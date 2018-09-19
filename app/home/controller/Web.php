<?php
/**
 * Created by PhpStorm.
 * User: tangmusen
 * Date: 2017/11/1
 * Time: 16:02
 */

namespace app\home\controller;


use app\admin\model\Members;
use app\api\controller\HX;
use think\Config;
use think\Controller;
use think\Cookie;
use think\Db;
use think\Request;

class Web extends Controller
{

    /**
     * 游戏规则
     */
    public function play_rule_web(Request $request){
        $cate = $request->param('cate')?$request->param('cate'):1;
        $name = Db::name('cate')->where(array('id'=>$cate))->value('name');
        $data = Db::name('play_rule')->where(array('cate'=>$cate))->value('play_rule');
        return view('',[
            'data'=>$data,
            'name'=>$name
        ]);
    }

    /**
     * 赔率规则
     */
    public function odds_rule_web(Request $request){
        $cate_id = $request->param('cate')?$request->param('cate'):1;
        $hall    = $request->param('hall')?$request->param('hall'):1;

        $data= Db::name('odds')
            ->alias('o')
            ->field('o.id,o.rule,o.rate,b.title')
            ->join('dl_bet b','o.cate=b.cate and o.type=b.type')
            ->where(array('o.cate'=>$cate_id,'o.hall'=>$hall))
            ->select();
        return view('',[
            'data'=>$data,
        ]);
    }

    /**
     * 回水规则
     */
    public function water_rule_web(Request $request){
        $cate = $request->param('cate')?$request->param('cate'):1;
        $hall = $request->param('hall')?$request->param('hall'):1;
        $w = [];
        $w['cate'] = $cate;
        $w['hall'] = $hall;
        $data = Db::name('water')->where($w)->select();
        return view('',[
            'data'=>$data,
        ]);
    }
    /**
     * 游戏规则
     */
    public function play_rule(Request $request){
      $cate = $request->param('cate')?$request->param('cate'):1;
      $name = Db::name('cate')->where(array('id'=>$cate))->value('name');
      $data = Db::name('play_rule')->where(array('cate'=>$cate))->value('play_rule');
      return view('',[
          'data'=>$data,
          'name'=>$name
      ]);
  }

    /**
     * 赔率规则
     */
    public function odds_rule(Request $request){
        $cate_id = $request->param('cate')?$request->param('cate'):1;
        $hall    = $request->param('hall')?$request->param('hall'):1;

        $data= Db::name('odds')
            ->alias('o')
            ->field('o.id,o.rule,o.rate,b.title')
            ->join('dl_bet b','o.cate=b.cate and o.type=b.type')
            ->where(array('o.cate'=>$cate_id,'o.hall'=>$hall))
            ->select();
        return view('',[
            'data'=>$data,
        ]);
    }

    /**
     * 回水规则
     */
    public function water_rule(Request $request){
        $cate = $request->param('cate')?$request->param('cate'):1;
        $hall = $request->param('hall')?$request->param('hall'):1;
        $w = [];
        $w['cate'] = $cate;
        $w['hall'] = $hall;
        $data = Db::name('water')->where($w)->select();
        return view('',[
            'data'=>$data,
        ]);
    }

    /**
     * 分享中心
     */

    public function ewm(Request $request){
        $id = $request->param('parent_id');
        $parent_id = 'yicai'.$id;
        $parent_id = base64_encode(base64_encode(base64_encode($parent_id)));
        $url = Config::get('host_url').'home/index/index?parent_id='.$parent_id;
        return view('',[
            'code_url'=>$url,
        ]);

    }
    /**
     * 分享注册
     */

    public function reg(Request $request){
        $parent_id = $request->param('parent_id');
        $p_mobile  = Db::name('member')->where(array('id'=>$parent_id))->value('mobile');
        return view('',[
            'p_mobile'=>$p_mobile,
        ]);

    }
    /**

     * 用户注册

     */

    public function register(Request $request){

        $mobile     = $request->post('mobile');

        $code       = $request->post('code');

        $password   = $request->post('password');

        $parent_id = Cookie::get('post_tid')? Cookie::get('post_tid'):0;

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

        $data['nickname']   = 'yicai' . rand(10000,99999);

        $data['mobile']     = $mobile;

        $data['password']   = md5($password);

        $data['create_at']  = time();

        $data['ip'] = get_client_ip();

        $data['token'] = md5(rand(11111,9999));

        if ($User->insert($data)) {
            $uid = $User->getLastInsID();
            session('uid',$uid);
            $up['gm_name'] = 'ycgm'.$uid;
            $User->where('id',$uid)->update($up);
            json_return(200,'恭喜您，注册成功');

        } else {

            json_return(201,'注册失败');

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
     * AG、BB登陆
     */
    public function login(Request $request){
        header("Content-type: text/html; charset=utf-8");
        $uid    = $request->param('uid');
        $game   = $request->param('game');
        if(!$uid){
            $message = '用户不存在';
            echo '<script>alert(\''.$message.'\');history.go(-1)</script>';
        }
        if($game!='AG' && $game!='BB'){
            $message = '游戏不存在';
            echo '<script>alert(\''.$message.'\');history.go(-1)</script>';
        }
        $mobile = Db::name('member')->where(array('id'=>$uid))->find();

        if(!$mobile){
            $message = '用户不存在';
            echo '<script>alert(\''.$message.'\');history.go(-1)</script>';
        }
            $username = $mobile['gm_name'];
        error_reporting(0);
            $API = new Biapi();
            if($game=="AG") {
                $url = $API->loginbi($game,$username);
            }
        if($game=="BB") {
            $url = $API->loginbi($game, $username,'ball');
        }
            return view('',[
                'url'=>$url,
            ]);
    }

    /**
     * AG、BB登陆
     */
    public function login_web(Request $request){
        header("Content-type: text/html; charset=utf-8");
        $game   = $request->param('game');
        $token  = $request->param('token');
        $type   = $request->param('type');
        $uid = substr(base64_decode(base64_decode(base64_decode($token))),5);
        
        if(!$uid){
            $message = '请先注册登陆';
            echo '<script>alert(\''.$message.'\');history.go(-1)</script>';
            exit();
        }
        if($game!='AG' && $game!='BB'){
            $message = '游戏不存在';
            echo '<script>alert(\''.$message.'\');history.go(-1)</script>';
            exit();
        }
        $mobile = Db::name('member')->where(array('id'=>$uid))->find();

        if(!$mobile){
            $message = '用户不存在';
            echo '<script>alert(\''.$message.'\');history.go(-1)</script>';
            exit();
        }
        $username = $mobile['gm_name'];
        error_reporting(0);
        $API = new Biapi();
        if($game=="AG") {
            $url = $API->loginbi($game,$username);
        }
        if($game=="BB") {
            if($type==1){
                $url = $API->loginbi($game, $username,'ball');
            }elseif($type==2){
                $url = $API->loginbi($game, $username,'live');
            }elseif($type==3){
                $url = $API->loginbi($game, $username,'ltlottery');
            }
        }
        return view('login',[
            'url'=>$url,
        ]);
    }


}

