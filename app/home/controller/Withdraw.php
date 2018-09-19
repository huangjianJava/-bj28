<?php
/**
 * Created by PhpStorm.
 * User: tangmusen
 * Date: 2017/10/30
 * Time: 11:08
 */
namespace app\home\controller;

use app\admin\model\Members;
use think\Config;
use think\Controller;
use think\Db;
use think\Request;

class Withdraw extends Base
{
    /**
     * 充值
     */
    public function index(){
        $uid      = session('uid');
        $member_info = Db::name('member')->field('mobile,nickname,head,money')->where('id',$uid)->find();
        $member_info['back'] = 0.00;
        return view('',[
            'member_info'=>$member_info,
        ]);

    }

    /**
     * 用户提现
     */
    public function do_withdraw(Request $request){

        $uid            = session('uid');
        $money          = $request->post('money');
        $name           = $request->post('name');
//        $funds_pass     = $request->post('funds_pass');

        if(!$name ||!$money){
            json_return(201,'网络错误!');
        }
        $user = Members::get($uid);
        $my_money      = $user['money'];
//        $my_funds_pass = $user['funds_pass'];
        $m_status      = $user['m_status'];
        if($m_status==1){
            json_return(201,'您的账号已被冻结!');
        }
        if($my_money<$money){
            json_return(207,'余额不足');
        }
//        if(data_md5_key($funds_pass)!=$my_funds_pass){
//            json_return(207,'资金密码有误');
//        }
        Db::startTrans();
        try {
            $new_money = bcsub(round($my_money,2), round($money,2));
            $up = [
                'money'=>$new_money
            ];
            $result0 = Db::name('member')
                ->where('id',$uid)
                ->update($up);
            $data['uid']          = $uid;
            $data['username']     = $name;
            $data['money']        = $money;
            $data['balance']      = round($new_money,2);
            $data['create_at']    = time();
            $result = Db::name('withdrawals')->insert($data);
            $this->add_withdraw_detail($uid,$money,$data['balance']);
            Db::commit();
        }catch (\Exception $e){
            Db::rollback();
            json_return(500,'提现失败');
        }
        if($result && $result0){
            json_return(200,'申请提现成功,后台将在最短时间内审核',$data);
        }else{
            json_return(500,'提现失败');
        }

    }

}