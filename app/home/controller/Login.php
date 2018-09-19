<?php
/**
 * Created by PhpStorm.
 * User: tangmusen
 * Date: 2017/10/30
 * Time: 11:08
 */
namespace app\home\controller;

use app\api\controller\HX;
use think\Controller;
use think\Cookie;
use think\Db;
use think\Request;

class Login extends Controller
{
    public $uid; //会员id
    public $openid;
    public $unionid;
    public $appId;
    public $appSecret;

    /**
     * 微信相关
     */
    public function loginByOpenid() {

        if(session('web_access_token.time') < time() || !session('?web_access_token.value')){
            $this->getOpenid();
        }
        $access_token = session('web_access_token.value');
        $wxUser = $this->get_sns_info($access_token,$this->openid);
        session('wxUser',$wxUser);
        $uid = Db::name('member')->where(['unionid' => $wxUser['openid']])->value('id');
        if($uid){
            session('uid',$uid);
        }

    }
    /**
     * 微信相关
     */
    public function getOpenid(){
        $this->appId    = config('appId');
        $this->appSecret= config('appSecret');
        vendor('weichat.apiOauth');
        $api = new \apiOauth($this->appId,$this->appSecret);
        $res  = $api->getOpenid('snsapi_userinfo');
        if($res){
            session('member_openid',$res['openid']);
            session('web_access_token.value',$res['access_token']);
            session('web_access_token.time',time()+7000);
        }
    }
    /**
     * 微信相关
     */
    public function get_sns_info($access_token,$openid){
        $subscribe_msg = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        return json_decode(httpGet($subscribe_msg),true);
    }
    /**
     * 微信相关
     */
    public function get_refresh_web_access_token($web_refresh_token){
        $this->appId = config('appId');
        $tokenurl 	= 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.$this->appId.'&grant_type=refresh_token&refresh_token='.$web_refresh_token;
        $res 	= httpGet($tokenurl);
        return json_decode($res,true);

    }

    /**
     * 微信登陆
     */
    public function wx_login(Request $request){
        
        $this->getOpenid();
        $this->openid = session('member_openid');
        $uid = Db::name('member')->where(['unionid' => $this->openid])->value('id');
        if ($uid) {
            session('uid', $uid);
            $login_log['login_way'] = 3;
            $login_log['uid'] = $uid;
            $login_log['create_at'] = time();
            $login_log['ip'] = get_client_ip();
            $ip_address = Db::name('login_log')
                ->where(array('ip' => $login_log['ip']))
                ->value('ip_address');
            if (!$ip_address) {
                $ip_address = get_city_id($login_log['ip']);
            }
            $login_log['ip_address'] = $ip_address;
            Db::name('login_log')->insert($login_log);
            $this->setLoginNum();
            $this->redirect('index/index');
        } else {
            $this->loginByOpenid();
            $User     = Db::name('member');
            $parent_id = Cookie::get('parent_id');
            if($parent_id){
                $tid = $parent_id;
            }else{
                $tid = 0;
            }
            $wxUser = session('wxUser');

            if(!$wxUser['nickname']){
                $wxUser['nickname'] = 'dwj'.rand(0001,9999);
            }

            $data['tid']       = $tid;
            $data['nickname']  = $wxUser['nickname'];
            $data['mobile']    = $wxUser['nickname'];
            $data['password']  = data_md5_key('666666');
            $data['create_at'] = time();
            $data['ip']        = get_client_ip();
            $data['token']     = md5(rand(11111, 99999));
            $data['unionid']   = $wxUser['openid'];
            $data['head']      = $wxUser['headimgurl'];
            $User->insert($data);
            $uid = $User->getLastInsID();
            session('uid',$uid);
            $this->setLoginNum();
            $this->redirect('index/index');
        }

    }



    /**
     * 登陆界面
     */
    public function index(){

        return view();
    }
    /**
     * 账号登陆
     */
    public function login_index(){

        return view();
    }

    /*
     * 记录登入记录
     */
    public function setLoginNum(){
        $uid = session('uid');
        $login_array = cache('login_array');
        $login_array['uid_'.$uid] = time()+300;
        cache('login_array',$login_array);
    }

    /**

     * 绑定操作

     */
    public function login_bang(Request $request){

        $mobile     = $request->post('mobile');

        $code       = $request->post('code');

        $password   = $request->post('password')?$request->post('password'):0;

        $parent_id = Cookie::get('post_tid')? Cookie::get('post_tid'):0;

        if(!$mobile || !$code){
            json_return(204,'缺少参数');
        }

        $Captcha = Db::name('captcha');

        $captcha_info = $Captcha->where(array('mobile'=>$mobile))->find();

        $true_code = $captcha_info['code'];

        if($true_code!=$code){
            json_return(201,'验证码错误');
        }
        $wxUser = session('wxUser');

        $User = Db::name('member');
        $user_info = $User->where(array('mobile'=>$mobile))->find();
        if($user_info){
            $up['nickname']   = $wxUser['nickname'];
            $up['head']       = $wxUser['headimgurl'];
            $up['unionid']    = $wxUser['openid'];
            $re_up =  $User->where(array('mobile'=>$mobile))->update($up);
            if($re_up) {
                json_return(200, '绑定手机成功');
            }
        }
        if($parent_id){
            $re_result = $User->where(array('id'=>$parent_id))->find();
            if($re_result) {
                $data['tid']  = $parent_id;
                $data['level'] = $re_result['level']+1;
            }
        }
        //注册环信
        $Hx = new HX();
        $Hx->openRegister($mobile);
        if($password){
            $data['password']   = md5($password);
        }
        $data['nickname']   = $wxUser['nickname'];
        $data['mobile']     = $mobile;
        $data['create_at']  = time();
        $data['head']       = $wxUser['headimgurl'];
        $data['unionid']    = $wxUser['openid'];
        $data['ip'] = get_client_ip();
        $data['token'] = md5(rand(0000,9999));
        if ($User->insert($data)) {
            $uid = $User->getLastInsID();
            session('uid',$uid);
            $up['gm_name'] = 'ycgm'.$uid;
            $User->where('id',$uid)->update($up);
            json_return(200,'绑定手机成功');
        } else {
            json_return(201,'绑定手机成功');
        }
    }

    /**
     * 登陆
     */
    public function do_login(Request $request){

        $mobile      = $request->post('mobile');
        $password    = $request->post('password');
        if(!$mobile || !$password ){
            json_return(204,'缺少参数');
        };
        $User = Db::name('member');
        $user_info =$User
            ->field('id,nickname,head,money,password,gm_name,create_at')
            ->where(array('mobile'=>$mobile))
            ->find();
        if(!$user_info){
            json_return(201,'用户不存在');
        }
        if(data_md5_key($password)!=$user_info['password']){
            json_return(201,'用户名密码错误');
        }
        unset($user_info['password']);
        $this->do_login_log($user_info['id']);
        session('uid',$user_info['id']);
        $this->setLoginNum();
        json_return(200,'登录成功',$user_info);


    }

    /**
     * 根据ip获取地址
     */
    public function do_login_log($uid){
        $login_log['uid']             = $uid;
        $login_log['login_way']       = 3;
        $login_log['create_at']       = time();
        $login_log['ip']              = get_client_ip();
        $ip_address = Db::name('login_log')
            ->where(array('ip'=>$login_log['ip'] ))
            ->value('ip_address');
        if(!$ip_address){
            $ip_address = get_city_id($login_log['ip']);
        }
        $login_log['ip_address']     = $ip_address ;
        Db::name('login_log')->insert($login_log);
    }

    /**
     * 注册界面
     */
    public function register(){
        return view();
    }

    /**

     * 用户注册

     */
    public function do_register(Request $request){
        $mobile     = $request->post('mobile');
        $nickname   = $request->post('nickname');
        $password   = $request->post('password');
        if(!$mobile || !$nickname || !$password){
            json_return(204,'缺少参数');
        }
        $parent_id = Cookie::get('parent_id');
        if($parent_id){
            $tid = $parent_id;
        }else{
            $tid = 0;
        }
        $User      = Db::name('member');
        $user_info = $User->where(array('mobile'=>$mobile))->find();
        if($user_info){
            json_return(201,'账号已经注册');
        }
        $data['tid']       = $tid;
        $data['nickname']   = $nickname;
        $data['mobile']     = $mobile;
        $data['password']   = data_md5_key($password);
        $data['create_at']  = time();
        $data['ip']         = get_client_ip();
        $data['token']      = md5(rand(11111,9999));
        if ($User->insert($data)) {
            json_return(200,'注册成功');
        } else {
            json_return(201,'注册失败');

        }
    }

    /**
     * 注册界面
     */
    public function logout(){
        session('uid', null);
        $this->redirect('login/index');
    }

    /**
     * 忘记密码
     */
    public function forget_password(){

        return view();
    }

    /**
     * 忘记密码
     */
    public function forget(Request $request){
        $mobile     = $request->post('mobile');
        $code       = $request->post('code');
        $password   = $request->post('password');
        if(!$mobile || !$code){
            json_return(204,'缺少参数');
        }
        $Captcha      = Db::name('captcha');
        $captcha_info = $Captcha->where(array('mobile'=>$mobile))->find();
        $true_code    = $captcha_info['code'];
        if($true_code!=$code){
            json_return(201,'验证码错误');
        }
        $User            = Db::name('member');
        $up['password']  = data_md5_key($password);
        $re_up =  $User->where(array('mobile'=>$mobile))->update($up);
        if ($re_up) {
            json_return(200,'修改密码成功');
        } else {
            json_return(201,'修改密码失败');
        }
    }


    /**

     *  发送短信验证码

     * */
    public function sendSms(Request $request){
        $mobile     = $request->post('mobile');
        if(!$mobile){
            json_return(204,'缺少参数');

        }
        $mobile = trim($mobile);
        if( strlen($mobile)!=11){
            json_return(201,'手机格号码式不正确');
        }
        //生成的随机数
        $mobile_code = random(4,1);
        //短信接口地址
        $target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";
        $post_data = "account=C53947997&password=f53afbe8897259bffa85f9c98386fd76&mobile=".$mobile."&content=".rawurlencode("您的验证码是：".$mobile_code."。请不要把验证码泄露给其他人。");
        $gets =  xml_to_array(curlPost($post_data, $target));
        $Captcha = Db::name('captcha');
        $captcha_info = $Captcha->where(array('mobile'=>$mobile))->find();
        if($gets['SubmitResult']['code']==2){
            if($captcha_info){
                $save_data['code']      = $mobile_code;
                $save_data['create_at'] = time();
                $save_data['number']    = array("exp", "number+" . 1);
                $Captcha->where(array('mobile'=>$mobile))->update($save_data);
            }else{
                $add_data['code']      = $mobile_code;
                $add_data['create_at'] = time();
                $add_data['number']    = 1;
                $add_data['mobile']    = $mobile;
                $Captcha->insert($add_data);
            }
            json_return(200,'发送成功');
        }else{
            json_return(201,'发送失败');
        }
    }

    /**
     * 微信注册界面
     */
    public function wx_register(){
        return view();
    }

    /**

     * 微信用户注册

     */
    public function wx_do_register(Request $request)
    {
        $mobile   = $request->post('mobile');
        $password = $request->post('password');
        if (!$mobile|| !$password) {
            json_return(204, '缺少参数');
        }
        $parent_id = Cookie::get('parent_id');
        if($parent_id){
            $tid = $parent_id;
        }else{
            $tid = 0;
        }
        $User     = Db::name('member');
        $user_info = $User->where(array('mobile' => $mobile))->find();
        $wxUser = session('wxUser');
        if ($user_info) {
            $up['nickname'] = $wxUser['nickname'];
            $up['head']     = $wxUser['headimgurl'];
            $up['unionid']  = $wxUser['openid'];
            $re_up = $User->where(array('mobile' => $mobile))->update($up);
            if ($re_up) {
                session('uid', $user_info['id']);
                json_return(200, '注册成功');
            }
        } else {
            $data['tid']       = $tid;
            $data['nickname']  = $wxUser['nickname'];
            $data['mobile']    = $mobile;
            $data['password']  = data_md5_key($password);
            $data['create_at'] = time();
            $data['ip']        = get_client_ip();
            $data['token']     = md5(rand(11111, 9999));
            $data['unionid']   = $wxUser['openid'];
            $data['head']      = $wxUser['headimgurl'];
            if ($User->insert($data)) {
                $uid = $User->getLastInsID();
                session('uid',$uid);
                json_return(200, '注册成功');
            } else {
                json_return(201, '注册失败');
            }
        }
    }





}