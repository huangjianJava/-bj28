<?php

namespace app\api\controller;


use app\admin\model\CateTime;
use Firebase\JWT\JWT;
use think\Cache;
use think\Config;
use think\Controller;
use think\Db;
use think\Request;
use GatewayClient\Gateway;

class Index extends Controller
{

    /**
     * 登陆
     */
    public function login(Request $request){

        $mobile      = $request->post('mobile');
        $password    = $request->post('password');
        $device_id   = $request->post('device_id');
        if(!$mobile || !$password || !$device_id){
            json_return(204,'缺少参数');
        }
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
        }else{
            unset($user_info['password']);
            if(!$user_info['gm_name']){
                $data['gm_name'] = 'ycgm'.$user_info['id'];
            }
            $data['device_id'] = $device_id;
            $User->where(array('mobile'=>$mobile))->update($data);
            $this->do_login_log($user_info['id']);
            $user_info['token']           =$this->tokenSign($user_info);
            json_return(200,'登录成功',$user_info);
        }

    }

    /**
     * 根据ip获取地址
     */
    public function do_login_log($uid){
        $login_log['uid']             = $uid;
        $login_log['login_way']       = is_ios();
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

     * 用户注册

     */
    public function register(Request $request){

        $mobile     = $request->post('mobile');
        $code       = $request->post('code');
        $password   = $request->post('password');
        $recommend_phone   = $request->post('recommend_phone');
        if(!$mobile || !$code || !$password){
            json_return(204,'缺少参数');
        }
        $Captcha = Db::name('captcha');
        $captcha_info = $Captcha->where(array('mobile'=>$mobile))->find();
        $true_code = $captcha_info['code'];
        if($true_code!=$code){
            json_return(201,'验证码错误');
        }
        $User = Db::name('member');
        $user_info = $User->where(array('mobile'=>$mobile))->find();
        if($user_info){
            json_return(201,'账号已经注册');
        }
        if($recommend_phone){
            $re_result_info = $User->field('id,level')->where(array('mobile'=>$recommend_phone))->find();
            if($re_result_info){
                $data['tid']     = $re_result_info['id'];
                $data['level']   = $re_result_info['level']+1;
            }else{
                json_return(201,'推荐人不存在');
            }
        }
        $data['nickname']   = 'yicai' . rand(10000,99999);
        $data['mobile']     = $mobile;
        $data['password']   = md5($password);
        $data['create_at']  = time();
        $data['ip']         = get_client_ip();
        $data['token']      = md5(rand(1111,9999));
        if ($User->insert($data)) {
            json_return(200,'注册成功,请登录');
        } else {
            json_return(201,'注册失败');
        }
    }

    /**
     * 找回密码
     */
    public function forget_password(Request $request){

        $mobile     = $request->post('mobile');

        $code       = $request->post('code');

        $password   = $request->post('password');


        if(!$mobile || !$code || !$password){
            json_return(204,'缺少参数');
        }

        $Captcha = Db::name('captcha');

        $captcha_info = $Captcha->where(array('mobile'=>$mobile))->find();

        $true_code = $captcha_info['code'];

        if($true_code!=$code){
            json_return(201,'验证码错误');
        }

        $up['password'] = md5($password);

        $result = Db::name('member')->where('mobile',$mobile)->update($up);

        if ($result) {

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
        $mobile_code = rand(1111,9999);
        //短信接口地址
        $target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";
        $post_data = "account=C53947997&password=f53afbe8897259bffa85f9c98386fd76&mobile=".$mobile."&content=".rawurlencode("您的验证码是：".$mobile_code."。请不要把验证码泄露给其他人。");
        $gets =  xml_to_array(curlPost($post_data, $target));
        $Captcha = Db::name('captcha');
        $captcha_info = $Captcha->where(array('mobile'=>$mobile))->find();
        if($gets['SubmitResult']['code']==2){
            if($captcha_info){
                $save_data['code'] = $mobile_code;
                $save_data['time'] = time();
                $save_data['number'] = array("exp", "number+" . 1);
                $Captcha->where(array('mobile'=>$mobile))->update($save_data);
            }else{
                $add_data['code'] = $mobile_code;
                $add_data['time'] = time();
                $add_data['number'] = 1;
                $add_data['mobile'] = $mobile;
                $Captcha->insert($add_data);
            }
            json_return(200,'发送成功');
        }else{
            json_return(201,'发送失败');
        }
    }

    /**
     * 版本控制
     */
    public function get_version(Request $request){
        $app_id       = $request->post('app_id');
        $version_code = $request->post('version_code');
        if(!$app_id || !$version_code){
            json_return(204, '缺少参数');
        }
        $condition  = array(
            'app_id'=>$app_id,
        );
        $condition0 = array(
            'app_id'=>$app_id,
            'version_code'=>$version_code
        );
        $version_info0 = Db::name('version')
            ->where($condition0)
            ->find();
        $version_info = Db::name('version')
            ->where($condition)
            ->order('id desc')
            ->find();
        if($version_info0['version_code']==$version_info['version_code']){
            $return_data['is_new']    = 1;
        }else{
            $return_data['is_new']    = 0;
        }
        $return_data['version_info'] = $version_info;
        json_return(200, '成功',$return_data);
    }

    /**
     * JWT验签方法
     */
    public function tokenSign($member)
    {
        $key = Config::get('API_KEY').Config::get('JWT_KEY');
        $jwt_data = ['uid' => $member['id'], 'nickname' => $member['nickname'], 'create_at' => $member['create_at']];
        $token = [
            "iss"   => "PADMIN JWT",          // 签发者
            "iat"   => time(),                // 签发时间
            "exp"   => time()+ 7200,          // 过期时间
            "aud"   => 'padmin',              // 接收方
            "sub"   => 'padmin',              // 面向的用户
            "data"  => $jwt_data
        ];
        $jwt = JWT::encode($token, $key);
        return $jwt ;
    }

    /**
     * 测试
     */
    public function get_user_info($token){
        $uid = get_member_by_token($token)->uid;
        $data = input('post.');

    }

    /**
     * 回水、流水代理回水记录
     */

    public function doCron(){
        Gateway::$registerAddress = '127.0.0.1:1236';
        $content = $this->get_robots();
        $data = [
            'content'=>$content,
            'type'=>'say',
            'to_client_id'=>'all',
            'to_client_name'=>'所有人',

        ];
        $data = json_encode($data);
        Gateway::sendToAll($data);
    }

    /**
     * 获取机器人信息
     */
    public function get_robots(){
        $robot_info = Cache::get('robot_info');
        if(!$robot_info){
            $robot_info = Db::name('robot')->select();
        }
        $rules_info =  Cache::get('rules_info');
        if(!$rules_info ){
            $rules_info = Db::name('robot_way')->select();
        }
        $robot_num    =  count($robot_info);
        $rules_num   = count($rules_info );

        $s_robot   = rand(0,$robot_num-1);
        $s_rules   = rand(0,$rules_num-1);
        $robot_info_one = $robot_info[$s_robot];
        $rule_info_one  = $rules_info[$s_rules];
        $back = [
            'nickname'=>$robot_info_one['nickname'],
            'head'    => Config::get('img_url').$robot_info_one['logo'],
            'content' => $rule_info_one['content'],
            'time'    => date('H:i:s'),
            'msg_type'=>1,
            'room'=>5
        ];
        return json_encode($back);


    }



}