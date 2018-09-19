<?php
/**
 * Created by PhpStorm.
 * User: tangmusen
 * Date: 2017/9/1
 * Time: 10:04
 */

namespace app\admin\controller;
use app\admin\model\Cash;
use app\admin\model\LoginLog;
use app\admin\model\Members;
use app\admin\model\Recharge;
use app\api\controller\HX;
use think\Db;
use think\Request;

class Member extends Admin
{
    /**
     * 会员信息首页
     */
    public function index(Request $request){
        $mobile = $request->param('mobile');
        $from   = $request->param('from');
        $to     = $request->param('to');
        $uid    = $request->param('uid');
        $is_m    = $request->param('is_m');
        $on_line = $request->param('online');
        $w=[];
        if($from && $to){
            $from = strtotime($from);
            $to = strtotime($to);
            $w['create_at'] = [['>=',$from],['<=',$to]];
        }
        if($mobile){
            $w['mobile']=$mobile;
        }
        if($uid){
            $w['id']    =$uid;
        }

        if($is_m==1){
            $w['is_m']    =1;
        }elseif ($is_m==2){
            $w['is_m']    =0;
        }
        if($on_line){
            $on_line_array =  cache('login_array');
            foreach ($on_line_array as $k2=>$v2){
                if($v2<time()){
                   unset($on_line_array[$k2]);
                }
            }
            if($on_line_array){
                $uid_arr = [];
                foreach ($on_line_array as $k=>$v){
                    $uid_arr[] = $k;
                }
                $uid_s = [];
                foreach ($uid_arr as $kk=>$vv){
                    $uid_s[] = explode('_',$vv)[1];
                }
                $w['id'] = array('in',$uid_s);
            }else{
                $w['id']    =0;
            }

        }
        $list = Members::where($w)->order('id desc')->paginate(20);
        $data = $list->all();
        foreach ( $data as $k=>$v) {
            if ($v['tid']) {
                $up_name = Db::name('member')->where('id', $v['tid'])->value('mobile');
            } else {
                $up_name = '无';
            }
            $down_number = Db::name('member')->where('tid', $v['id'])->count();
            $v['up_name'] = $up_name;
            $v['down_number'] = $down_number;
            $login_ip = Db::name('login_log')->where('uid', $v['id'])->order('id desc')->value('ip');
            $v['login_ip'] = $login_ip;
            $list[$k] = $v;
        }


        $login_array =  cache('login_array');
        $on_line_number = 0;
        foreach ($login_array as $k22=>$v22){
            if($v22>time()){
                $on_line_number ++;
            }
        }
        return view('index',[
            'list'=>$list,
            'page'=>$list->render(),
            'on_line'=>$on_line_number
        ]);
    }

    /**
     * 下级会员信息
     */
    public function member_down(Request $request){
        $mobile = $request->param('mobile');
        $from   = $request->param('from');
        $to     = $request->param('to');
        $uid    = $request->param('uid');
        $tid    = $request->param('tid');
        $w=[];
        if($from && $to){
            $from = strtotime($from);
            $to = strtotime($to);
            $w['create_at'] = [['>=',$from],['<=',$to]];
        }
        if($mobile){
            $w['mobile']=$mobile;
        }
        if($uid){
            $w['id']    =$uid;
        }
        $w['tid'] = $tid;

        $list = Members::where($w)->order('id desc')->paginate(20);

        $data = $list->all();
        foreach ( $data as $k=>$v){
            $money_info    = Db::name('single')
                ->field('sum(money) as money,sum(z_money) as z_money')
                ->where('uid',$v['id'])
                ->find();
            if(!$money_info['money']){
                $money_info['money'] = '0.00';
            }
            if(!$money_info['z_money']){
                $money_info['z_money'] = '0.00';
            }
            $v['betting_money']     =  $money_info['money'];
            $v['betting_zmoney']    =  $money_info['z_money'];
            $v['win_lose']          =  $money_info['z_money']-$money_info['money'];
            $list[$k] = $v;
        }
        return view('',[
            'list'=>$list,
            'page'=>$list->render(),
        ]);
    }

    /**
     * 查看会员信息
     */
    public function show_list(Request $request){
        $id = $request->param('id');
        $info = Members::get($id);
        return view('edit',[
            'info'=>$info
        ]);
    }

    /**
     * 修改会员信息
     */
    public function edit(Request $request){

        if($request->isPost()){
            $data = $request->post();
        
            $Member = new Members();
            $map=array(
                'id'=>$data['id']
            );
            $result=$Member->editData($map,$data);
            if ($result) {
                $this->error_new('修改成功');
            }else{
                $this->error_new('修改失败');
            }
        }
    }

    /**
     * 登陆日志
     */
    public function login_log(Request $request){
        $mobile        = $request->param('mobile');
        $from          = $request->param('from');
        $to            = $request->param('to');
        $login_way     = $request->param('login_way');
        $w = [];
        if($from && $to){
            $from = strtotime($from);
            $to = strtotime($to);
            $w['create_at'] = [['>=',$from],['<=',$to]];
        }
        if($mobile){
            $uid = Members::where(array('mobile'=>$mobile))->value('id');
            $w['uid'] = $uid;
        }
        if($login_way){
            $w['login_way'] = $login_way;
        }
        $list = LoginLog::where($w)->order('id desc')->paginate(20,false,['query' => request()->param()]);
        $data = $list->all();
        foreach ( $data as $k=>$v){
            $v['minute'] = ceil((time()-strtotime($v['create_at']))/3600);
            $list[$k] = $v;
        }
        return view('login_log',[
            'list'=>$list,
            'page'=>$list->render(),
        ]);
    }

    /**
     * 后台上分
     */
    public function edit_frozen(Request $request){
        $uid    = $request->post('id');
        $money = $request->post('money');
        $remark = '后台上分';
        if(!is_numeric($money)){
            json_return(201,'金额格式不对');
        }
        Db::startTrans();
        try {
            $up_data["money"] = array("exp", "money+" . $money);
            $result0 = Db::name('member')->where(array('id'=>$uid))->update($up_data);
            $Recharge = new Recharge();
            $data['uid'] = $uid;
            $data['remark'] = $remark;
            $data['money'] = $money;
            $data['status'] = 2;
            $data['type'] = 3;
            $data['create_at'] = time();
            $result1 = $Recharge->addData($data);
            $this->add_charge_detail($uid,$money,1);
        }catch (\Exception $e){
            Db::rollback();
            json_return(201,'充值失败');
        }
        if ($result0 && $result1 ) {
            Db::commit();
            json_return(200,'充值成功');
        }else{
            json_return(201,'充值失败');
        }

    }
    
    /**
     * 后台下分
     */
    public function edit_unfreeze(Request $request){

        $uid    = $request->post('id');
        $money = $request->post('money');
        $remark = '后台下分';
        if(!is_numeric($money)){
            json_return(201,'金额格式不对');
        }
        if(!$remark){
            $remark = '后台下分';
        }
        $user = Members::get($uid);
        $my_money      = $user['money'];
        if($my_money<$money){
            json_return(201,'余额不足');
        }
        Db::startTrans();
        try {
            $new_money = bcsub(round($my_money,2), round($money,2));
            $up = [
                'money'=>$new_money
            ];
            $result0 = Db::name('member')
                ->where('id',$uid)
                ->update($up);
            $data['uid']          = $uid;
            $data['username']     = $remark;
            $data['money']        = $money;
            $data['status']       = 2;
            $data['balance']      = round($new_money,2);
            $data['create_at']    = time();
            $data['update_at']    = time();
            $result = Db::name('withdrawals')->insert($data);
            $this->add_withdraw_detail($uid,$money,$data['balance']);
            Db::commit();
        }catch (\Exception $e){
            Db::rollback();
            json_return(201,'下分失败');
        }
        if($result && $result0){
            json_return(200,'下分成功');
        }else{
            json_return(201,'下分失败');
        }

    }

    /**
     *添加提现记录
     */
    public function add_withdraw_detail($uid,$money,$balance){
        $explain = "后台下分";
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
     * 删除用户
     */
    public function del(Request $request){
        $id          = $request->param('id');
        $result      = Db::name('member')->where(array('id'=>$id))->delete();
        if ($result) {
            $this->success_new('删除成功',url('Admin/member/index'));
        }else{
            $this->error_new('删除失败');
        }
    }

    /**
     * 机器人信息
     */
    public function robot(Request $request){
        
        $list = Db::name('robot')
            ->alias('r')
            ->field('r.*,c.name as cate_name')
            ->join('dl_cate c','r.cate=c.id')
            ->order('r.id desc')
            ->paginate(20);
        $cates = Db::name('cate')->select();
        return view('robot',[
            'cates'=>$cates,
            'list'=>$list,
            'page'=>$list->render(),
        ]);
    }

    /**
     * 改变机器人开启关闭状态
     */
    public function edit_open(Request $request){
        $id     = $request->post('id');
        $status = $request->post('status');
        $result = Db::name('robot')
            ->where(array('id'=>$id))
            ->update(array('status'=>$status));
        if($result){
            json_return(200,'成功');
        }else{
            json_return(500,'失败');
        }
    }

    /**
     * 添加机器人信息
     */
    public function add_robot(Request $request){
        $data = $request->post();
        $result=Db::name('robot')->insert($data);
        if ($result) {
            $this->success_new('添加成功',url('Admin/member/robot'));
        }else{
            $this->error_new('添加失败');
        }
    }

    /**
     * 查看机器人信息
     */
    public function robot_show(Request $request){
        $id = $request->param('id');
        $info = Db::name('robot')->where(array('id'=>$id))->find();
        $cates = Db::name('cate')->select();
        return view('robot_edit',[
            'cates'=>$cates,
            'data'=>$info
        ]);
    }

    /**
     * 修改机器人信息
     */
    public function robot_edit(Request $request){
        if($request->isPost()){
            $data = $request->post();
            $map=array(
                'id'=>$data['id']
            );
            $result=Db::name('robot')->where($map)->update($data);
            if ($result) {
                $this->success_new('修改成功',url('Admin/member/robot'));
            }else{
                $this->error_new('修改失败');
            }
        }
    }

    /**
     * 成为平台代理
     */
    public function proxy_edit(Request $request){
        $id = $request->post('id');
        $result = Db::name('member')
            ->where('id',$id)
            ->update(['is_proxy'=>1]);
        if ($result) {
            json_return(200,'修改成功');
        }else{
            json_return(500,'修改失败');
        }

    }

    /**
     * 取消平台代理
     */
    public function proxy_cancel(Request $request){
        $id = $request->post('id');
        $result = Db::name('member')
            ->where('id',$id)
            ->update(['is_proxy'=>0]);
        if ($result) {
            json_return(200,'修改成功');
        }else{
            json_return(500,'修改失败');
        }

    }

    /**
     * 代理、佣金用户
     */
    public function proxy_member(Request $request){
        $t_uid    = $request->param('t_uid');
        $w['tid']   =$t_uid;
        $proxy_info = Db::name('member')->field('is_proxy,proxy_level')->where('id',$t_uid)->find();
        $is_proxy      = $proxy_info['is_proxy'];
        $proxy_level   = $proxy_info['proxy_level'];
        if($is_proxy) {//代理
            $field = 'id,nickname,head,mobile,money,remark,create_at,level,m_status';
            $list1 = Db::name('member')->field($field)->where($w)->order('id desc')->select();
            $level1 = [];
            foreach ($list1 as $k => $v) {
                $level1[] = $v['id'];
                $daa[] = $v;
            }
            if ($level1 && $proxy_level>=2) {
                $list2 = Db::name('member')->field($field)->where('tid', 'in', $level1)->order('id desc')->select();
                $level2 = [];
                foreach ($list2 as $kk => $vv) {
                    $level2[] = $vv['id'];
                    $daa[] = $vv;
                }
                if ($level2 && $proxy_level>=3) {
                    $list3 = Db::name('member')->field($field)->where('tid', 'in', $level2)->order('id desc')->select();
                    $level3 = [];
                    foreach ($list3 as $kkk => $vvv) {
                        $level3[] = $vvv['id'];
                        $daa[] = $vvv;
                    }
                    if ($level3 && $proxy_level>=4) {
                        $list4 = Db::name('member')->field($field)->where('tid', 'in', $level3)->order('id desc')->select();
                        $level4 = [];
                        foreach ($list4 as $kkkk => $vvvv) {
                            $level4[] = $vvvv['id'];
                            $daa[] = $vvvv;
                        }
                        if ($level4 & $proxy_level>=5) {
                            $list5 = Db::name('member')->field($field)->where('tid', 'in', $level4)->order('id desc')->select();
                            foreach ($list5 as $kkkkk => $vvvvv) {
                                $daa[] = $vvvvv;
                            }
                        }
                    }
                }
            }
        }else{//佣金
            $field = 'id,nickname,head,mobile,money,remark,create_at,level,m_status';
            $daa = Db::name('member')->field($field)->where($w)->order('id desc')->select();
            foreach ($daa as $k0=>$v0){
                $daa[$k0]['level'] = 0;
                $y_money = Db::name('detail')->where('proxy_uid',$v0['id'])->sum('money');
                $daa[$k0]['m_status'] = $y_money;

            }
        }
        $count = count($daa);
        return view('proxy_member',[
            'list'=>$daa,
            'count'=>$count
        ]);
    }

    /**
     * 添加用户
     */
    public function add_member(Request $request){
        $mobile     = $request->post('mobile');
        $password   = $request->post('password');
        $recommend_phone   = $request->post('recommend_phone');
        $User = Db::name('member');
        $user_info = $User->where(array('mobile'=>$mobile))->find();
        if($user_info){
            $this->error_new('账号已经注册');
        }
        if($recommend_phone){
            $re_result_info = $User->field('id,level')->where(array('mobile'=>$recommend_phone))->find();
            if($re_result_info){
                $data['tid']     = $re_result_info['id'];
                $data['level']   = $re_result_info['level']+1;
            }else{
                $this->error_new('推荐人不存在');
            }
        }
        //注册环信
        $Hx = new HX();
        $Hx->openRegister($mobile);
        $data['nickname']   = 'yicai'.rand(10000,99999);
        $data['mobile']     = $mobile;
        $data['password']   = md5($password);
        $data['create_at']  = time();
        $data['ip'] = get_client_ip();
        $data['token'] = md5(rand(0000,9999));
        if ($User->insert($data)) {
            $this->success_new('删除成功',url('Admin/member/index'));
        } else {
            $this->error_new('添加用户失败');
        }
    }

    /**
     * 机器人规则信息
     */
    public function robot_way(){

        $list = Db::name('robot_way')
            ->paginate(20);
        return view('',[
            'list'=>$list,
            'page'=>$list->render(),
        ]);
    }

    /**
     * 机器人规则信息
     */
    public function del_robot_way(Request $request){

        $id          = $request->param('id');
        $result      = Db::name('robot_way')->where(array('id'=>$id))->delete();
        if ($result) {
            $this->success_new('删除成功',url('Admin/member/robot_way'));
        }else{
            $this->error_new('删除失败');
        }
    }

    /**
     * 机器人规则信息
     */
    public function robot_way_add(Request $request){

        $data = $request->post();
        $result=Db::name('robot_way')->insert($data);
        if ($result) {
            $this->success_new('添加成功',url('Admin/member/robot_way'));
        }else{
            $this->error_new('添加失败');
        }
    }

    /**
     * 假人真人
     */
    public function edit_jia(Request $request){
        $uid  = $request->post('id');
        $type = $request->post('type');
        if($type==1){
            Db::name('member')->where('id',$uid)->update(['is_m'=>1]);
        }else{
            Db::name('member')->where('id',$uid)->update(['is_m'=>0]);
        }
        json_return(200,'成功');
    }

}