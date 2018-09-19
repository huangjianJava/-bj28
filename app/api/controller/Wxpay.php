<?php
/**
 * Created by PhpStorm.
 * User: tangmusen
 * Date: 2017/11/4
 * Time: 14:21
 */

namespace app\api\controller;


use app\admin\model\Members;
use think\Controller;
use think\Db;
use think\Request;

class Wxpay extends Controller
{
    protected $config;

    public function __construct()
    {
        parent::__construct();

    }


    /**
     * 获取微信预支付签名
     */
    public function get_signature(Request $request){

        $uid    = $request->post('uid')?$request->post('uid'):1;
        $attach =   [
            'uid'=>$uid,
            'secret'=>'zcjy'
        ];
        $total_fee = 0.01*100;
        $attach = json_encode($attach);
        $params = [
            'attach'=>$attach,
            'body' => '泽成教育',
            'out_trade_no' => mt_rand().time(),
            'total_fee' => $total_fee,
        ];
        $result = \wxpay\AppPay::getPayUrl($params);
        $back = [
            'appid'    =>$result['appid'],
            'partnerid'=>$result['mch_id'],
            'prepayid' =>$result['prepay_id'],
            'package'  =>"Sign=WXPay",
            'noncestr' =>random(20),
            'timestamp'=>time(),
        ];
        $key  = \WxPayConfig::KEY;
        $sign = $this->getSign($back,$key);
        $back['sign'] = $sign;
       json_return(200,'成功',$back);

    }

    /**
     * 通知回调
     */
    public function notify()
    {

        $xml        = $GLOBALS['HTTP_RAW_POST_DATA'];
        $xml_data   = $this->xmlToArray($xml);
        $attach     = json_decode($xml_data['attach'], true);
        $money      = $xml_data['total_fee'];
        if($attach['secret']=='zcjy') {
            if ($xml_data['result_code'] == 'SUCCESS' && $xml_data['return_code'] == 'SUCCESS') {
                $outTradeNo = $xml_data['out_trade_no'];
                $pay_id = Db::name("wxpay")
                    ->where(array('out_trade_no' => $outTradeNo))
                    ->value("id");
                if ($pay_id) {
                   exit('SUCCESS');
                }
                $attach = json_decode($xml_data['attach'], true);
                $uid = $attach['uid'];
                $xml_data['uid'] = $uid;
                Db::name("wxpay")->insert($xml_data);
                $ins = [
                    'uid'   => $uid,
                    'money' => $money/100,
                    'type'  => 1,
                ];
                Db::name('recharge')->insert($ins);
                Db::name('member')->where('id', $uid)->update(['is_q' => 1]);
                exit('SUCCESS');
            }
        }
    }


    /**
     *    作用：array转xml
     *
     */
    public function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml = $xml . "<" . $key . ">" . $val . "</" . $key . ">";
            } else
                $xml = $xml . "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
        $xml = $xml . "</xml>";
        return $xml;
    }

    /**
     *  作用：将xml转为array
     */
    public function xmlToArray($xml)
    {
        //将XML转为array
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }

    /**
     *  作用：格式化参数，签名过程需要使用
     */
    private function formatBizQueryParaMap($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if ($urlencode) {
                $v = urlencode($v);
            }
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar = '';
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }

    /**
     *  作用：生成签名
     */
    private function getSign($Obj, $key = '')
    {
        foreach ($Obj as $k => $v) {
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        if (!empty($key)) {//判断是否加入key
            $String = $String . "&key=" . $key;
        }
        $String = md5($String);
        $result_ = strtoupper($String);
        return $result_;
    }

}