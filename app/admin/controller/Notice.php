<?php
/**
 * Created by PhpStorm.
 * User: tangmusen
 * Date: 2017/9/30
 * Time: 9:58
 */

namespace app\admin\controller;


use app\admin\model\Members;
use app\admin\model\PersonalMsg;
use think\Db;
use think\Request;

class Notice extends Admin
{
    /**
     * 个人公告
     */
    public function personal(Request $request){
        $mobile = $request->param('mobile');
        $w = [];
        if($mobile){
            $uid = Members::where(array('mobile'=>$mobile))->value('id');
            $w['uid'] = $uid;
        }
        $data = PersonalMsg::where($w)->order('id desc')->paginate(20);
        return view('personal',[
            'page'=>$data->render(),
            'data'=>$data,
        ]);
    }
    /**
     * 系统公告
     */
    public function system(){

        $data = Db::name('notice')->order('id desc')->paginate(20);
        return view('system',[
            'page'=>$data->render(),
            'data'=>$data,
        ]);
    }

    /**
     * 添加系统通知
     */
    public function add_system(Request $request){
        $info = $request->post('info');
        $data['info'] = $info;
        $data['create_time'] = date('Y-m-d H:i:s');
        $result = Db::name('notice')->insert($data);
        push_all($info);
        if ($result) {
            $this->success_new('添加成功',url('Admin/notice/system'));
        }else{
            $this->error_new('修改失败');
        }
    }
    /**
     * 编辑系统通知
     */
    public function system_show(Request $request){
            $cate     = $request->param('id');
            $data     = Db::name('notice')->where(array('id'=>$cate))->find();
            return view('system_edit',[
                'data'=>$data,
            ]);

    }
    /**
     * 编辑系统通知
     */
    public function system_edit(Request $request){
        if($request->isPost()){
            $data = $request->post();
            $map=array(
                'id'=>$data['id']
            );
            $result=Db::name('notice')->where($map)->update($data);
            if ($result) {
                $this->success_new('修改成功',url('Admin/notice/system'));
            }else{
                $this->error_new('修改失败');
            }
        }else{
            $cate     = $request->param('id');
            $data     = Db::name('notice')->where(array('id'=>$cate))->find();
            return view('system_edit',[
                'data'=>$data,
            ]);
        }
    }
    /**
     * 删除系统公告
     */
    public function system_del(Request $request){
        $cate     = $request->param('id');
        $result   = Db::name('notice')->where(array('id'=>$cate))->delete();
        if ($result) {
            $this->success_new('删除成功',url('Admin/notice/system'));
        }else{
            $this->error_new('删除失败');
        }
    }
    /**
     * 删除个人公告
     */
    public function personal_del(Request $request){
        $cate     = $request->param('id');
        $result   = Db::name('member_msg')->where(array('id'=>$cate))->delete();
        if ($result) {
            $this->success_new('删除成功',url('Admin/notice/personal'));
        }else{
            $this->error_new('删除失败');
        }
    }
    /**
     * 推送消息
     *
     */
    public function push_msg(){
        $data = Db::name('push')->select();
        return view('push_msg',[
            'data'=>$data,
        ]);
    }
    /**
     * 显示推送消息
     *
     */
    public function push_show(Request $request){
        $id     = $request->param('id');
        $data = Db::name('push')->where(array('id'=>$id))->find();
        return view('push_edit',[
            'data'=>$data,
        ]);
    }
    /**
     * 编辑推送消息
     */
    public function push_edit(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->post();
            $map = array(
                'id' => $data['id']
            );
            $result = Db::name('push')->where($map)->update($data);
            if ($result) {
                $this->success_new('修改成功', url('Admin/notice/push_msg'));
            } else {
                $this->error_new('修改失败');
            }
        }
    }
    /**
     * 删除推送消息
     */
    public function push_del(Request $request){
        $id     = $request->param('id');
        $result   = Db::name('push')->where(array('id'=>$id))->delete();
        if ($result) {
            $this->success_new('删除成功',url('Admin/notice/push_msg'));
        }else{
            $this->error_new('删除失败');
        }
    }
    /**
     * 是否首页显示
     */
    public function is_show(Request $request){
        $id      = $request->post('id');
        $is_show = $request->post('is_show');

        if($is_show){
            $up = [
                'is_show'=>1
            ];
        }else{
            $up = [
                'is_show'=>0
            ];
        }
        $result = Db::name('notice')->where('id',$id)->update($up);
        if($result){
            json_return(200,'成功');
        }else{
            json_return(201,'失败');
        }
    }




    /**
     * 系统公告
     */
    public function call(){

        $data = Db::name('call')->order('id desc')->paginate(20);
        return view('call',[
            'page'=>$data->render(),
            'data'=>$data,
        ]);
    }

    /**
     * 添加系统通知
     */
    public function add_call(Request $request){
        $info    = $request->post('info');
        $content = $request->post('content');
        $call_time = $request->post('call_time');
        $data['info']      = $info;
        $data['content']   = $content;
        $data['call_time'] = $call_time;
        $data['create_time'] = date('Y-m-d H:i:s');
        $result = Db::name('call')->insert($data);
        if ($result) {
            $this->success_new('添加成功',url('Admin/notice/call'));
        }else{
            $this->error_new('修改失败');
        }
    }
    /**
     * 编辑系统通知
     */
    public function call_show(Request $request){
        $cate     = $request->param('id');
        $data     = Db::name('call')->where(array('id'=>$cate))->find();
        return view('call_edit',[
            'data'=>$data,
        ]);

    }
    /**
     * 编辑系统通知
     */
    public function call_edit(Request $request){
        if($request->isPost()){
            $data = $request->post();
            $map=array(
                'id'=>$data['id']
            );
            $result=Db::name('call')->where($map)->update($data);
            if ($result) {
                $this->success_new('修改成功',url('Admin/notice/call'));
            }else{
                $this->error_new('修改失败');
            }
        }else{
            $cate     = $request->param('id');
            $data     = Db::name('call')->where(array('id'=>$cate))->find();
            return view('call_edit',[
                'data'=>$data,
            ]);
        }
    }
    /**
     * 删除系统公告
     */
    public function call_del(Request $request){
        $cate     = $request->param('id');
        $result   = Db::name('call')->where(array('id'=>$cate))->delete();
        if ($result) {
            $this->success_new('删除成功',url('Admin/notice/call'));
        }else{
            $this->error_new('删除失败');
        }
    }
    /**
     * 是否首页显示
     */
    public function is_call(Request $request){
        $id      = $request->post('id');
        $is_show = $request->post('is_show');

        if($is_show){
            $up = [
                'is_show'=>1
            ];
        }else{
            $up = [
                'is_show'=>0
            ];
        }
        $result = Db::name('call')->where('id',$id)->update($up);
        if($result){
            json_return(200,'成功');
        }else{
            json_return(201,'失败');
        }
    }
}