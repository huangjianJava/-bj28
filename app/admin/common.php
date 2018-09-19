<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/8 0008
 * Time: 15:18
 */

/**
 * 推送个人消息
 */
function push_one($target,$msg){
    $client = new \JPush\Client('4a998fe41bb56c6adfc6c2a3','00382559bc931004e9dd2d71');
    $client->push()
        ->setPlatform('all')
        ->addRegistrationId($target)
        ->setNotificationAlert($msg)
        ->iosNotification($msg, array(
            'sound' => 'default',
        ))
        ->send();
}
/**
 * 推送公告消息
 */
function push_all($msg){
     $client = new \JPush\Client('4a998fe41bb56c6adfc6c2a3','00382559bc931004e9dd2d71');
    $client->push()
        ->setPlatform('all')
        ->addAllAudience()
        ->setNotificationAlert($msg)
        ->send();
}



