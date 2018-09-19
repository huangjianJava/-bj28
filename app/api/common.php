<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/8 0008
 * Time: 15:18
 */

/**
 * @param $uid   用户ID
 * @param $token 用户token
 * 用户接口验证
 */
function check_token($uid,$token){

    if(!$uid || !$token){
        json_return(204,'缺少参数');
    }

    $true_token = \think\Db::name('member')->where(array('id'=>$uid))->value('token');

    if($token!=$true_token){
        json_return(401,'token有误');
    }
}


// 解密user_token
function decoded_user_token($token = '')
{
    $key     = \think\Config::get('API_KEY').\think\Config::get('JWT_KEY');

    $decoded = \Firebase\JWT\JWT::decode($token, $key, array('HS256'));

    return (array) $decoded;
}

// 获取解密信息中的data
function get_member_by_token($token = '')
{
    try {
        $result = decoded_user_token($token);
    }catch (\Exception $e){
         json_return(401,'非法访问');
    }
    return $result['data'];
}



