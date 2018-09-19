<?php
/**
 * Created by PhpStorm.
 * User: tangmusen
 * Date: 2017/10/30
 * Time: 11:08
 */
namespace app\home\controller;

use think\Config;
use think\Controller;
use think\Db;
use think\Request;

class Recharge extends Base
{
    /**
     * 充值
     */
    public function index(){
        $uid = session('uid');
        $member_info = Db::name('member')->field('mobile,nickname,head,money')->where('id',$uid)->find();
        $member_info['back'] = 0.00;
        $wx_url = Db::name('setting')->where('id',1)->value('wx');
        $wx_url = Config::get('img_url').$wx_url;
        return view('',[
            'member_info'=>$member_info,

            'wx_url'=>$wx_url
        ]);

    }

    /**
     * 微信充值
     */
    public function wx_pay(){
        //微信
        $w = [
            'type'=>1
        ];
        $wx_data = Db::name('qrcode')
            ->where($w)
            ->select();
        $key = array_rand($wx_data);
        $wx_data = $wx_data[$key];
        $wx_data['qrcode'] = Config::get('img_url').$wx_data['qrcode'];
        return view('',[
            'wx_data'=>$wx_data,
        ]);

    }

    /**
     * 支付宝值
     */
    public function ali_pay(){

        $w1 = [
            'type'=>2
        ];
        $ali_data = Db::name('qrcode')
            ->where($w1)
            ->select();
        $key = array_rand($ali_data);
        $ali_data = $ali_data[$key];
        $ali_data['qrcode'] = Config::get('img_url').$ali_data['qrcode'];
        return view('',[
            'ali_data'=>$ali_data,
        ]);

    }

    /**
     * 线下充值
     */
    public function offline_charge(Request $request){
        $uid      = session('uid');
        $money    = $request->post('money');
        $name     = $request->post('name')?$request->post('name'):'线下充值';
        $way      = $request->post('way');
        if(!$uid ||!$money||!$name ||!$way){
            json_return(201,'网络错误!');
        }
        if($way==1){
            $charge['remark'] = '微信充值';
        }
        if($way==2){
            $charge['remark']  = '支付宝充值';
        }
        $balance = Db::name('member')->where('id',$uid)->value('money');
        $charge['way']      = $way;
        $charge['uid']      = $uid;
        $charge['money']    = $money;
        $charge['balance']  = $balance;
        $charge['name']     = $name;
        $charge['type']     = 2;
        $charge['create_at'] = time();
        $result = Db::name('recharge')->insert($charge);
        if($result){
            json_return(200,'提交成功,请等待后台审核!');
        }else{
            json_return(201,'提交失败');
        }
    }

}