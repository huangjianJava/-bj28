<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Cache;

/*------------公用函数start----------------------*/

/**
 * @param $code 返回码
 * @param $data 返回数据主题
 * @param $msg  返回消息
 */
function json_return($code,$msg,$data=""){
    exit(json_encode(array('ret'=>$code,'msg'=>$msg,'data'=>$data,)));
}
/**
 * curl 请求
 */
function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
    // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
}

/**
 * php将字符串分割成数组实现中文分词
 */
function math($string,$code ='UTF-8'){
    if ($code == 'UTF-8') {
        $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
    } else {
        $pa = "/[\x01-\x7f]|[\xa1-\xff][\xa1-\xff]/";
    }
    preg_match_all($pa, $string, $t_string);
    $math=[];
    foreach($t_string[0] as $k=>$s){
        $math[]=$s;
    }
    return $math;
}

/**
 * 添加详细记录
 */
function addDetail($uid,$type,$money,$balance,$exp,$cate,$explain='',$hall){
    $data=[
        'uid'=>$uid,
        'type'=>$type,
        'money'=>$money,
        'balance'=>$balance,
        'cate'   =>$cate,
        'exp'    =>$exp,
        'explain' =>$explain,
        'hall'   =>$hall,
        'create_at'=>time()
    ];
//    $detail = new \app\admin\model\Detail($data);
//    $detail->allowField(true)->save();
    \think\Db::name('detail')->insert($data);
    
}

/**
 * 检测是后台登入
 */
function isAdminLogin(){
    $user = session('admin_login');
    if (empty($user)) {
        return 0;
    } else {
        return session('admin_login_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

/**
 * sha1签名
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)){
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
 * 判断时间戳是不是为0
 */
function isTimeZero($time){
    if(strtotime($time)>0){
        return true;
    }else{
        return false;
    }
}

/**
 * 检查是否微信
 */
function is_weixin() {
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    } return false;
}

/**
 * web检查
 */
function web_check()
{
    $uri  = url('login/login');
    $uri0 = url('login/bang');
    $uri1 = url('index/index');
    $uid  = session('uid');
    if (!$uid) {
        if (!is_weixin()) {
            echo "<script>if(confirm('您还没有登陆,确定前往登陆？')){
                   location=\" " . $uri . "\";
                 }else{
                  location=\" " . $uri1 . "\";
                 }</script>";
        }else{
            echo "<script>if(confirm('您还没有绑定手机,前往绑定？')){
                   location=\" " . $uri0 . "\";
                 }else{
                  location=\" " . $uri1 . "\";
                 }</script>";

        }
    }

}

/**
 * 判断客服端是否为ｉｏｓ
 */
function is_ios(){
    if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){
           return 2;
    }else{
           return 1;
    }
}

/**
 * curl POST
 * @param $curlPost
 * @param $url
 * @return mixed
 */
function curlPost($curlPost,$url){

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);

    curl_setopt($curl, CURLOPT_HEADER, false);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($curl, CURLOPT_NOBODY, true);

    curl_setopt($curl, CURLOPT_POST, true);

    curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);

    $return_str = curl_exec($curl);

    curl_close($curl);

    return $return_str;

}

/**
 * 返回随机整数
 * @param int $length
 * @param int $numeric
 * @return string
 *
 */
function random($length = 6 , $numeric = 0) {

    PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);

    if($numeric) {

        $hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));

    } else {

        $hash = '';

        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';

        $max = strlen($chars) - 1;

        for($i = 0; $i < $length; $i++) {

            $hash .= $chars[mt_rand(0, $max)];

        }

    }

    return $hash;

}

/**
 * 根据ip获取城市
 */
function get_city_id($ip){
//    if(empty($ip)){
//        $ip=get_client_ip();
//    }
//    $url='http://ip.taobao.com/service/getIpInfo.php?ip='.$ip;
//    $result = file_get_contents($url);
//    $result = json_decode($result,true);
//    if($result['code']!==0) {
//        return false;
//    }
    $result = '本地';
    return $result;
}

/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string
 */
function data_md5($str, $key = 'PADMIN')
{
    return '' === $str ? '' : md5(sha1($str) . $key);
}

/**
 * 使用上面的函数与系统加密KEY完成字符串加密
 * @param  string $str 要加密的字符串
 * @return string
 */
function data_md5_key($str, $key = '')
{

    if (is_array($str)) {

        ksort($str);

        $data = http_build_query($str);

    } else {

        $data = (string) $str;
    }
    return empty($key) ? data_md5($data, \think\Config::get('API_KEY')) : data_md5($data, $key);
}

/**
 * 获取手机类型
 */
function get_mobile_vision()
{
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if (stripos($user_agent, "iPhone") !== false) {
        $brand = 'iPhone';
    } else if (stripos($user_agent, "SAMSUNG") !== false || stripos($user_agent, "Galaxy") !== false || strpos($user_agent, "GT-") !== false || strpos($user_agent, "SCH-") !== false || strpos($user_agent, "SM-") !== false) {
        $brand = '三星';
    } else if (stripos($user_agent, "Huawei") !== false || stripos($user_agent, "Honor") !== false || stripos($user_agent, "H60-") !== false || stripos($user_agent, "H30-") !== false) {
        $brand = '华为';
    } else if (stripos($user_agent, "Lenovo") !== false) {
        $brand = '联想';
    } else if (strpos($user_agent, "MI-ONE") !== false || strpos($user_agent, "MI 1S") !== false || strpos($user_agent, "MI 2") !== false || strpos($user_agent, "MI 3") !== false || strpos($user_agent, "MI 4") !== false || strpos($user_agent, "MI-4") !== false) {
        $brand = '小米';
    } else if (strpos($user_agent, "HM NOTE") !== false || strpos($user_agent, "HM201") !== false) {
        $brand = '红米';
    } else if (stripos($user_agent, "Coolpad") !== false || strpos($user_agent, "8190Q") !== false || strpos($user_agent, "5910") !== false) {
        $brand = '酷派';
    } else if (stripos($user_agent, "ZTE") !== false || stripos($user_agent, "X9180") !== false || stripos($user_agent, "N9180") !== false || stripos($user_agent, "U9180") !== false) {
        $brand = '中兴';
    } else if (stripos($user_agent, "OPPO") !== false || strpos($user_agent, "X9007") !== false || strpos($user_agent, "X907") !== false || strpos($user_agent, "X909") !== false || strpos($user_agent, "R831S") !== false || strpos($user_agent, "R827T") !== false || strpos($user_agent, "R821T") !== false || strpos($user_agent, "R811") !== false || strpos($user_agent, "R2017") !== false) {
        $brand = 'OPPO';
    } else if (strpos($user_agent, "HTC") !== false || stripos($user_agent, "Desire") !== false) {
        $brand = 'HTC';
    } else if (stripos($user_agent, "vivo") !== false) {
        $brand = 'vivo';
    } else if (stripos($user_agent, "K-Touch") !== false) {
        $brand = '天语';
    } else if (stripos($user_agent, "Nubia") !== false || stripos($user_agent, "NX50") !== false || stripos($user_agent, "NX40") !== false) {
        $brand = '努比亚';
    } else if (strpos($user_agent, "M045") !== false || strpos($user_agent, "M032") !== false || strpos($user_agent, "M355") !== false) {
        $brand = '魅族';
    } else if (stripos($user_agent, "DOOV") !== false) {
        $brand = '朵唯';
    } else if (stripos($user_agent, "GFIVE") !== false) {
        $brand = '基伍';
    } else if (stripos($user_agent, "Gionee") !== false || strpos($user_agent, "GN") !== false) {
        $brand = '金立';
    } else if (stripos($user_agent, "HS-U") !== false || stripos($user_agent, "HS-E") !== false) {
        $brand = '海信';
    } else if (stripos($user_agent, "Nokia") !== false) {
        $brand = '诺基亚';
    } else {
        $brand = '其他手机';
    }

    return $brand;
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0,$adv=false) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if($adv){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * 检查银行卡
 */
function is_card($s){
    $n = 0;
    $ns = strrev($s); // 倒序
    for ($i=0; $i <strlen($s) ; $i++) {
        if ($i % 2 ==0) {
            $n += $ns[$i]; // 偶数位，包含校验码
        }else{
            $t = $ns[$i] * 2;
            if ($t >=10) {
                $t = $t - 9;
            }
            $n += $t;
        }
    }
    return ( $n % 10 ) == 0;
}

/**
 * 检查身份证号码
 */
function is_id_card($number) {
    //检查是否是身份证号
    // 转化为大写，如出现x
    $number = strtoupper($number);
    //加权因子
    $wi = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
    //校验码串
    $ai = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
    //按顺序循环处理前17位
    $sigma = 0;
    for($i = 0;$i < 17;$i++){
        //提取前17位的其中一位，并将变量类型转为实数
        $b = (int) $number{$i};      //提取相应的加权因子
        $w = $wi[$i];     //把从身份证号码中提取的一位数字和加权因子相乘，并累加
        $sigma += $b * $w;
    }
    //计算序号
    $snumber = $sigma % 11;
    //按照序号从校验码串中提取相应的字符。
    $check_number = $ai[$snumber];
    if($number{17} == $check_number){
        return true;
    }else{
        return false;
    }
}

/**
 * 将xml转化为数组
 * @param $xml
 * @return mixed
 */
function xml_to_array($xml){

    $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";

    if(preg_match_all($reg, $xml, $matches)){

        $count = count($matches[0]);

        for($i = 0; $i < $count; $i++){

            $subxml= $matches[2][$i];

            $key = $matches[1][$i];

            if(preg_match( $reg, $subxml )){

                $arr[$key] = xml_to_array( $subxml );

            }else{

                $arr[$key] = $subxml;

            }

        }

    }

    return $arr;

}

//从缓存中获取开奖号
function getNumberCache($name){
    $cache_name = 'number_'.$name;
    $number = Cache::get($cache_name);
    if($number){
        return $number;
    }
}

/*------------重庆时时彩start------------------*/
//获取三个号码
function take_three($array,$type){
    if($type==0){
        $return = array_slice($array,0,3);
        return $return;
    }elseif ($type==1){
        $return = array_slice($array,1,3);
        return $return;
    }elseif ($type==2){
        $return = array_slice($array,2,3);
        return $return;
    }

}
//判断三个号码的状态
function judge_three($number_arr){
    $arr_num = array_count_values($number_arr);

    if($number_arr[0]==$number_arr[1] && $number_arr[1] ==$number_arr[2]  ){
        $first_three= "豹";
        return  $first_three;
    }elseif ($number_arr[0]+1==$number_arr[1] && $number_arr[1]+1 ==$number_arr[2]){
        $first_three= "顺";
        return  $first_three;
    }elseif ($number_arr[0]-1==$number_arr[1] && $number_arr[1]-1 ==$number_arr[2]){
        $first_three= "顺";
        return  $first_three;
    }elseif ($number_arr[0]==9 && $number_arr[1]==0 && $number_arr[1]==1){
        $first_three= "顺";
        return  $first_three;
    }elseif(in_array(2,$arr_num)){
        $first_three= "对";
        return  $first_three;
    }elseif (abs($number_arr[0]-$number_arr[1]) == 1
        ||abs($number_arr[0]-$number_arr[2]) == 1
        ||abs($number_arr[1]-$number_arr[2]) == 1 ){
        $first_three= "半";
        return  $first_three;
    }elseif (abs($number_arr[0]-$number_arr[1]) >1
        &&abs($number_arr[0]-$number_arr[2]) > 1
        &&abs($number_arr[1]-$number_arr[2]) > 1 ){
        $first_three= "杂";
        return  $first_three;
    }
}
//计算（时间和期数）
function setSscStageTime($stage){
    //计算下期的数字
    $stage_num = (int)substr($stage,-3);
    if($stage_num<120){
        $stage_next = $stage+1;
    }else{
        $stage_next = date('Ymd')."001";
        if($stage_next<$stage){
            $stage_next =  date("Ymd",strtotime("+1 day"))."001";
        }
    }
    //计算下次开奖时间
    $dateline = reckonSscTime((int)substr($stage_next,-3));
    return ['stage_next'=>$stage_next,'dateline'=>$dateline];
}

//计算（时间和期数）
function setSscStageTimeNew($stage,$dateline){
    //计算下期的数字
    $stage_num = (int)substr($stage,-3);
    if($stage_num<120){
        $stage_next = $stage+1;
    }else{
        $stage_next = date('Ymd')."001";
        if($stage_next<$stage){
            $stage_next =  date("Ymd",strtotime("+1 day"))."001";
        }
    }
    //计算下次开奖时间
    $num = ((int)substr($stage_next,-3));
    if($num>=1 && $num<=23){
        $time = strtotime($dateline)+300;
    }elseif($num == 24){
        $second = date('Y-m-d').' 10:01:21';
        $time   = strtotime($second);
    }elseif ($num>=25 && $num<=96){
        $time   = strtotime($dateline)+600;
    }elseif($num>=97 && $num<=120){
        $time = strtotime($dateline)+300;
    }


    return ['stage_next'=>$stage_next,'dateline'=>$time];
}
//计算每期时间间隔（时时彩）
function reckonSscTime($num){
    if($num>=1 && $num<=23){
        $datelines = strtotime(date('Y').'-'.date('m').'-'.date('d').' 02:00:00');
        $time =$datelines - (24-$num) * 300;
    }elseif($num == 24){
        $datelines = strtotime(date('Y').'-'.date('m').'-'.date('d').' 09:30:00');
        $time = 1800 +$datelines;
    }elseif ($num>=25 && $num<=96){
        $datelines = strtotime(date('Y').'-'.date('m').'-'.date('d').' 09:30:00');
        $time = 1800 + (600*($num-24)) +$datelines;
    }elseif($num>=97 && $num<=120){
        $datelines = strtotime(date('Y').'-'.date('m').'-'.date('d').' 09:30:00');
        $time =  1800 +  (600*(96-24)) + 300*($num-96)+$datelines;
    }
    return $time;
}
//判断龙虎 $number 第一期开奖号（array）
function sscLh($number){
    $start = $number[0];
    $end   = $number[4];
    if($start>$end){
        return '龙';
    }elseif ($start<$end){
        return '虎';
    }elseif ($start == $end){
        return '和';
    }
}
//判断大小
function sscDx($number){
    if($number>=0 && $number<=22){
        return '小';
    }elseif ($number>=23 && $number<=45){
        return '大';
    }
}
//判断每个位子大小
function sscDxWei($number){
    if($number>=0 && $number<=4){
        return '小';
    }elseif ($number>=5 && $number<=9){
        return '大';
    }
}
//判断单双
function sscDs($number){
    if($number%2 ==0) {
        return '双';
    }else{
        return '单';
    }
}
//判断总大
function zSscDs($number){
    if($number%2 ==0) {
        return '双';
    }else{
        return '单';
    }
}
//判断总小
function zSscDx($number){
    if($number>=0 && $number<=22){
        return '小';
    }elseif ($number>=23 && $number<=45){
        return '大';
    }
}
/*------------重庆时时彩end-------------------*/

//获取倍率
function getOdds($cate,$hall,$type,$rule){
   $map = [
       'cate'=>$cate,
       'hall'=>$hall,
       'type'=>$type,
       'rule'=>$rule
   ];
    $odds = Db('odds')
        ->where($map)
        ->find(); //获取赔率
    return number_format($odds['rate'],3);
}








