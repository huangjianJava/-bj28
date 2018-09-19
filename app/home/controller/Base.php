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
use think\Cookie;
use think\Db;
use think\Request;

class Base extends Controller
{

    public function _initialize()
    {
        $request = Request::instance();
        if ($postTid = $request->param('parent_id', 0)) {
            Cookie::set('parent_id', $postTid);
        }
        if(!session('uid')){
            $this->redirect('login/index');
        }
        $this->setLoginNum();

        $controllerName = $request->controller();
        $this->assign('controllerName',strtolower($controllerName));

    }

   /*
   * 记录登入记录
   */
    public function setLoginNum(){
        $uid = session('uid');
        $login_array = cache('login_array');
        $login_array['uid_'.$uid] = time()+300;
        cache('login_array',$login_array);
    }

    /**
     *添加提现记录
     */
    public function add_withdraw_detail($uid,$money,$balance){
        $explain = "提现申请";
        $save['uid']     = $uid;
        $save['type']    = 2;
        $save['money']   = $money;
        $save['balance'] = $balance;
        $save['exp']     = 6;
        $save['explain'] =  $explain;
        $save['create_at'] = time();
        $save['update_at'] = time();
        $result = Db::name('detail')->insert($save);
        return $result;

    }

    /**
     * @param $cate
     * 获取下一期的开奖时间
     */
    public function get_next($cate){
        $now_time = date('H:i:s');
        $data = Db::name('cate_time')
            ->field('stage,dateline,open')
            ->where('cate',$cate)
            ->where('dateline','>',$now_time)
            ->order('id asc')
            ->find();
        switch ($cate){
            case 5:
                if(!$data){
                    $back['stage']    = date('Ymd') . '120';
                    $back['dateline'] = date('Y-m-d',strtotime('+1 day')) . ' ' . '00:00:40';
                    $back['open']     = date('Y-m-d',strtotime('+1 day')) . ' ' . '00:01:00';

                }else {
                    $back = $data;
                    $change = strtotime(date('Y-m-d') . ' 00:01:00');
                    if (time() <= $change) {
                        $yesterday = strtotime('-1 day');
                        $back['stage'] = date('Ymd', $yesterday) . $back['stage'];
                    } else {
                        $back['stage'] = date('Ymd') . $back['stage'];
                    }
                    $back['dateline'] = date('Y-m-d') . ' ' . $back['dateline'];
                    $back['open'] = date('Y-m-d') . ' ' . $back['open'];
                }
                break;
        }
        return $back;
    }
    /**
     * 定义方法
     *
     * 重写thinkphp跳转成功方法
     *
     * @param string $message
     * @param string $uri
     * @param bool|mixed $param
     */
    public function success_new($message,$uri="",$param = [])
    {
        if ($uri) {
            $param_str = '';
            if($param) {
                foreach ($param as $k => $v) {
                    $param_str .= '/' . $k . '/' . $v;
                }
                echo '<script>alert(\'' . $message . '\');location=\'' . $uri . $param_str . '\'</script>';
            }else{
                echo '<script>alert(\'' . $message . '\');location=\'' . $uri .  '\'</script>';
            }
        } else {
            echo '<script>alert(\''.$message.'\');history.go(-1)</script>';
        }
    }
    /**
     * 定义方法
     *
     * 重写thinkphp跳转失败方法
     *
     * @param string $message
     * @param string $uri
     * @param bool|mixed $param
     */
    public function error_new($message,$uri ="",$param = [])
    {
        if ($uri) {
            if($param) {
                $param_str = '';
                foreach ($param as $k => $v) {
                    $param_str .= '/' . $k . '/' . $v;
                }
                echo '<script>alert(\'' . $message . '\');location=\'' . $uri . $param_str . '\'</script>';
            }else{
                echo '<script>alert(\'' . $message . '\');location=\'' . $uri .  '\'</script>';
            }
        } else {
            echo '<script>alert(\''.$message.'\');history.go(-1)</script>';
        }
        exit;
    }
    /**
     * 上传一张图片
     */
    public function upload_one(){
        $file = request()->file('file');
        if(!$file){
            $data = array("status" =>0,"error" => '请选择上传图片');
            echo json_encode($data);
            exit;
        }
        $path = ROOT_PATH . 'public' . DS . 'uploads'. DS .'cate_img';
        $info = $file->move($path);
        if($info){
            $picd = 'cate_img/'.$info->getSaveName();
            $pic = Config::get('img_url').$picd;
            $data = array("status" =>1,"pic" => $pic,'picd'=>$picd);
            echo json_encode($data);
            exit;
        }else{
            $data = array("status" =>0,"error" => '上传图片失败');
            echo json_encode($data);
            exit;
        }
    }

}