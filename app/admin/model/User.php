<?php

namespace app\admin\model;



class User extends Base
{
    protected $auto = [];
    protected $update = [];

    public function get_mobile_id($mobile){
        $result = User::where(array('phone'=>$mobile))->value('id');
        return $result;
    }


    protected function setPasswordAttr($password)
    {
        return md5($password);
    }
    protected function setStimeAttr()
    {
        return time();
    }

    //检验登入
    public function checkLogin($username,$password){
        $rs = $this->get(['username' => $username]);

        session('info',$rs);
        if(!$rs){
            $this->error='用户不存在或已被禁用！';
            return false;
        }else{
            if($rs['password']!== md5($password)){
                $this->error='用户名或密码错误！';
                return false;
            }
        }
        //更行数据
        $data =array(
            'id'=>$rs['id'],
            'lg_ip'=>request()->ip(),
            'lg_time'=>time(),
        );

        $this->update($data);
        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'uid'             => $rs['id'],
            'username'        => $rs['username'],
            'lg_time'         => $rs['lg_time'],
        );
        session('admin_login', $auth);
        session('admin_login_sign',data_auth_sign($auth));
        return true;
    }
}