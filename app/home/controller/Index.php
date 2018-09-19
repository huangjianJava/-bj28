<?php
/**
 * Created by PhpStorm.
 * User: tangmusen
 * Date: 2017/10/30
 * Time: 11:08
 */
namespace app\home\controller;

use think\Cache;
use think\Config;
use think\Controller;
use think\Cookie;
use think\Db;
use think\Request;

class Index extends Base
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
     * 首页
     */
    public function index(Request $request){

        $uid = session('uid');
        if ($postTid = $request->param('parent_id', 0)) {
            Cookie::set('parent_id', $postTid);
        }
        if(!$uid) {
            if (is_weixin()) {
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
                } else {
                    $uri = url('login/register');
                    echo "<script>if(confirm('系统检测到该微信号未注册账号,前往注册？')){
                           location=\" " . $uri . "\";
                         }</script>";
                    exit;
                }
            }
        }
        $member_info = Db::name('member')->field('mobile,head,money')->where('id',$uid)->find();
        $wx_url = Db::name('setting')->where('id',1)->value('wx');
        $wx_url = Config::get('img_url').$wx_url;
        $online_number = rand(100,200);
        return view('',[
            'member_info'=>$member_info,
            'wx_url'=>$wx_url,
            'online_number'=>$online_number
        ]);
    }
    





}