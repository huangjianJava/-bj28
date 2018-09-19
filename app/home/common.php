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





