<?php
/**
 * Created by PhpStorm.
 * User: tangmusen
 * Date: 2017/11/4
 * Time: 14:21
 */

namespace app\api\controller;

use think\Controller;
use think\Db;
use think\Request;

class Alipay extends Controller
{

    /**
     * 获取支付宝支付签名
     */
    public function get_signature(Request $request){
        $uid    = $request->post('uid')?$request->post('uid'):1;
        $body   = [
            'uid'=>$uid,
            'secret'=>'zcjy'
        ];
        $body         = json_encode($body);
        $total_amount = 0.1;
        $params = [
            'body'=>$body,
            'subject'=>'泽成教育',
            'out_trade_no'=>mt_rand().time(),
            'total_amount'=>$total_amount,
        ];
        $result = \alipay\Apppay::pay($params);
        json_return(200,'成功',$result);

    }

    /**
     * 通知回调
     */
    public function notify()
    {
        $out_trade_no   = $_POST['out_trade_no'];
        $total_amount   = $_POST['total_amount'];
        $params = [
            'out_trade_no' => $out_trade_no,
            'total_amount' => $total_amount,
        ];
        $result = \alipay\Notify::check($params);
        if($result) {//验证成功
            if($_POST['trade_status'] == 'TRADE_FINISHED') {

            }elseif ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                $is_trade = Db::name('pay')
                    ->where(array('out_trade_no'=>$out_trade_no))
                    ->value('id');
                if($is_trade){
                    exit("success");
                }
                $body = json_decode($_POST['body'],true);
                $uid = $body['uid'];
                //支付记录表加入数据
                $pay_data['uid']            = $uid;
                $pay_data['out_trade_no']   = $_POST['out_trade_no'];
                $pay_data['trade_no']       = $_POST['trade_no'];
                $pay_data['seller_email']   = $_POST['seller_email'];
                $pay_data['total_fee']      = $total_amount;
                $pay_data['body']           = $_POST['body'];
                $pay_data['gmt_create']     = $_POST['gmt_create'];
                $pay_data['trade_status']   = $_POST['trade_status'];
                $pay_data['buyer_logon_id'] = $_POST['buyer_logon_id'];
                $pay_data['subject']        = $_POST['subject'];
                $pay_data['seller_id']      = $_POST['seller_id'];
                Db::name('pay')->insert($pay_data);
                $ins = [
                    'uid'   => $uid,
                    'money' => $total_amount,
                    'type'  => 2,
                ];
                Db::name('recharge')->insert($ins);
                Db::name('member')->where('id',$uid)->update(['is_q'=>1]);
                echo "success";
            }
        }else {
            file_put_contents('alipay.txt','2222'.$result.'\r\n',8);
            //验证失败
            echo "fail";	//请不要修改或删除
        }

    }

}