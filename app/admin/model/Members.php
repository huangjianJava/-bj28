<?php
/**
 * Created by PhpStorm.
 * User: tangmusen
 * Date: 2017/9/1
 * Time: 10:06
 */

namespace app\admin\model;


use think\Model;

class Members extends Base
{
    protected $table = 'dl_member';
    protected $autoWriteTimestamp = true;

    protected function setPasswordAttr($password)
    {
        return md5($password);
    }

    public function get_mobile_id($mobile){
        $result = Members::where(array('mobile'=>$mobile))->value('id');
        return $result;
    }
}