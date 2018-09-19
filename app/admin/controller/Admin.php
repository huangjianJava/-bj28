<?php
namespace app\admin\controller;

use think\Config;
use think\Controller;
use think\Db;
use think\Request;

class admin extends Controller
{
    public function _initialize()
    {
        //判断是否登入成功
        if(!isAdminLogin()){
            $this->redirect('Login/index');
        }
        $request = Request::instance();
        $moduleName     = $request->module();
        $controllerName = $request->controller();
        $actionName     = $request->action();
        $menu_list      = $this->getMenuList();

        //判断权限
//        $auth=new \Auth\Auth() or die('加载auth类失败');
//        $rule_name=$moduleName.'/'.$controllerName.'/'.$actionName;
//
//
//        $result=$auth->check($rule_name,session('info.id'));
//        if(!$result){
//            echo '<script>alert(\'对不起,您的权限不足!\');history.go(-1)</script>';exit;
//        }
        //记录日志
        $operation_obj = new \Net\Operation();
        if (preg_match('/add|save|saveEdit|delete|edit|del|dj(.*)?/', $actionName)) {
            $operation_obj->writeLog();
        }

        $this->assign('menu_list',$menu_list);
        $this->assign('controllerName',strtolower($controllerName));
        $this->assign('actionName',strtolower($actionName));

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

    /**
     * 上传一张图片CK
     */
    public function upload_one_ck(){
        $cb = $_GET['CKEditorFuncNum']; //获得ck的回调id
        try {
            if(isset($_FILES['upload'])) { //上传的图片的信息存在$_FILES['upload']
                $file = request()->file('upload');
                if(!$file){
                    throw new Exception("上传文件不存在");
                }
                $path = ROOT_PATH . 'public' . DS . 'uploads'. DS .'ck_img';
                $info = $file->move($path);
                if($info) {
                    $picd = 'ck_img/' . $info->getSaveName();
                    $pic = Config::get('img_url') . $picd;
                    echo "<script>window.parent.CKEDITOR.tools.callFunction($cb, '$pic', '');</script>" ;
                }
            }
        }catch (\Exception $e) {
            $erro = $e->getMessage();
            echo "<script>window.parent.CKEDITOR.tools.callFunction($cb, '', '$erro');</script>" ;//图片上传失败，通知ck失败消息
        }
    }

    /**
     * 获取菜单列表
     * @return array
     */
    public function getMenuList(){
        $uid = session('info.id');
        if($uid==1){
            $menu_list = $this->getAllMenu();
        }else {

            $group_ids = Db::name('auth_group_access')->where(array('uid' => $uid))->column('group_id');

            $group_rules = Db::name('auth_group')->where('id', 'in', $group_ids)->select();

            $rules = "";
            foreach ($group_rules as $k => $v) {
                if ($k == 0) {
                    $rules = $v['rules'];
                }
                $rules .= "," . $v['rules'];
            }
            $rules = explode(',', $rules);
            $rules = array_unique($rules);
            $rule_name = Db::name('auth_rule')->where('id', 'in', $rules)->column('name');
            $rule_use = [];
            foreach ($rule_name as $vv) {
                $rule_use[] = strtolower($vv);
            }

            $menu_list = $this->getAllMenu();

            foreach ($menu_list as $k => $v) {
                $url = 'admin/' . $v['control'] . '/' . $v['act'];
                if (!in_array($url, $rule_use)) {
                    unset($menu_list[$k]);
                }
            }

        }
        return $menu_list;
    }
    
    /**
     * 菜单列表详情
     * @return array
     */
    public function getAllMenu(){
        $data = Db::name('right')->where(array('pid'=>0,'status'=>1))->order('sort asc')->select();
        foreach($data as $k=>$v){
            $child = Db::name('right')->where(array('pid'=>$v['id'],'status'=>1))->order('sort asc')->select();
            if($child){
                $data[$k]['sub_menu'] = $child;
            }
        }
        return $data;
    }

    /**
     *添加充值记录
     */
    public function add_charge_detail($uid,$money,$type=1){
        if($type==1){
           $explain = "后台充值";
        }elseif($type==2){
           $explain = "线下充值";
        }
        $my_money = Db::name('member')->where(array('id'=>$uid))->value('money');
            $save['uid']     = $uid;
            $save['type']    = 1;
            $save['money']   = $money;
            $save['balance'] = $my_money;
            $save['exp']     = 1;
            $save['explain'] =  $explain;
            $save['create_at'] = time();
            $save['update_at'] = time();
        $result = Db::name('detail')->insert($save);
        return $result;

    }

    /**
     *添加充值记录
     */
    public function add_withdraw_detail_back($uid,$money){
        $my_money = Db::name('member')->where(array('id'=>$uid))->value('money');
        $explain = "提现拒绝，返回金额";
        $save['uid']     = $uid;
        $save['type']    = 1;
        $save['money']   = $money;
        $save['balance'] = $my_money;
        $save['exp']     = 7;
        $save['explain'] =  $explain;
        $save['create_at'] = time();
        $save['update_at'] = time();
        $result = Db::name('detail')->insert($save);
        return $result;

    }

    /**
     * 哭脸跳转
     */
    public function error_new($msg = '', $url = null, $time = 1)
    {
        $str = '<script type="text/javascript" src="' . config('admin_static') . '/js/jquery-1.10.2.min.js"></script><script type="text/javascript" src="' . config('admin_static') . '/js/layer/layer.js"></script>';//加载jquery和layer

        if (is_null($url)) {
            $url = Request::instance()->isAjax() ? '' : 'javascript:history.back(-1);';
        } elseif ('' !== $url) {
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : Url::build($url);
        }

        $str .= '<script>$(function(){layer.msg("' . $msg . '",{icon:5,time:' . ($time * 1000) . '});setTimeout(function(){self.location.href="' . $url . '"},1000)});</script>';//主要方法
        echo $str;
        exit();
    }

    /**
     * 笑脸跳转
     */
    public function success_new($msg = '', $url = null, $time = 1)
    {
        $str = '<script type="text/javascript" src="' . config('admin_static') . '/js/jquery-1.10.2.min.js"></script><script type="text/javascript" src="' . config('admin_static') . '/js/layer/layer.js"></script>';//加载jquery和layer

        if (is_null($url)) {
            $url = Request::instance()->isAjax() ? '' : 'javascript:history.back(-1);';
        } elseif ('' !== $url) {
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : Url::build($url);
        }

        $str .= '<script>$(function(){layer.msg("' . $msg . '",{icon:6,time:' . ($time * 1000) . '});setTimeout(function(){self.location.href="' . $url . '"},1000)});</script>';//主要方法
        echo $str;
        exit();
    }

    /**
     * @param $id
     * @return mixed
     * 获取推送消息模板
     */
    public function get_push_msg($id){
        $content = Db::name('push')
            ->where(array('id'=>$id))
            ->value('content');
        return $content;
    }
}
