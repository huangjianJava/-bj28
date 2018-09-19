<?php
/**
 * Created by PhpStorm.
 * User: tangmusen
 * Date: 2017/9/4
 * Time: 9:36
 */

namespace app\admin\controller;


use app\admin\model\Cash;
use app\admin\model\Detail;
use app\admin\model\Members;
use app\admin\model\Recharge;
use app\home\controller\Biapi;
use think\Db;
use think\Request;
use GatewayClient\Gateway;

class Money extends Admin
{
    /**
     * 充值记录
     */
    public function recharge(Request $request){

        $from   = $request->param('from');
        $to     = $request->param('to');
        $status = $request->param('status');
        $mobile = $request->param('mobile');
        $uid    = $request->param('uid');
        $w='';
        if($from && $to){
            $from = strtotime($from);
            $to = strtotime($to);
            $w['create_at'] = [['>=',$from],['<=',$to]];
        }
        if($status){
            $w['status']=$status;
        }
        if($mobile){
            $uid = Members::where(array('mobile'=>$mobile))->value('id');
            $w['uid'] = $uid;
        }
        if($uid){
            $w['uid'] = $uid;
        }

        $list = Recharge::where($w)->order('id desc')->paginate(20,false,['query' => request()->param()]);



        $renshu      = Recharge::where($w)->count();
        $totle_money = Recharge::where($w)->sum('money');

        return view('',[
            'list'=>$list,
            'page'=>$list->render(),
            'renshu'=>$renshu,
            'total_money'=>$totle_money,
        ]);
    }
    /**
     *  后台充值
     */
    public function addmoney(Request $request){
        $mobile   = trim($request->post('mobile'));
        $money    = trim($request->post('money'));
        $remark   = $request->post('remark');
        if(!$remark){
            $remark = '后台充值';
        }
        $Members = new Members();
        $uid = $Members->get_mobile_id($mobile);
        if(!$uid){
            $this->error_new('用户不存在');
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
            $this->error_new('充值失败');
        }
        if ($result0 && $result1 ) {
            Db::commit();

            $this->success_new('充值成功',url('admin/money/recharge'));
        }else{
            $this->error_new('充值失败');
        }

    }
    /**
     * 修改充值状态
     */
    public function edit_status(Request $request){
        $type   = $request->post('type');
        $uid    = $request->post('uid');
        $id     = $request->post('id');
        Db::startTrans();
        try{
            if($type==1) {
                $result_up = Db::name('recharge')->where(array('id' =>$id))->update(array('status' => 2,'update_at'=>time()));
                $money     = Db::name('recharge')->where(array('id' =>$id))->value('money');
                $up_data["money"] = array("exp", "money+" . $money);
                $result = Db::name('member')->where(array('id'=>$uid))->update($up_data);
                $this-> add_charge_detail($uid,$money,2);
           }
            if($type==2) {
                $result_up = Db::name('recharge')->where(array('id' =>$id))->update(array('status' => 3,'update_at'=>time()));
            }
        }catch (\Exception $e){
            Db::rollback();
            json_return(500,'失败');
        }
        if($result_up){
            Db::commit();
            json_return(200,'成功');
        }else{
            Db::rollback();
            json_return(500,'失败');
        }
    }

    /**
     *充值成功后发消息
     */
    public function send_message($uid,$money){
        Gateway::$registerAddress = '127.0.0.1:1236';
        $back = [
            'uid'=>$uid,
            'nickname'=>'后台',
            'head'    => '',
            'content' => '本次充值'.$money.'元，后台已审核成功,游戏愉快',
            'time'    => date('H:i:s'),
            'msg_type'=> 2,
            'room'=>5
        ];
        $data = [
            'content'=>json_encode($back),
            'type'=>'say',
            'to_client_id'=>'all',
            'to_client_name'=>'所有人',
        ];
        $data = json_encode($data);
        Gateway::sendToAll($data);
    }
    /**
     * 提现
     */
    public function cash(Request $request){
        $mobile    = $request->param('mobile');
        $status    = $request->param('status');
        $from      = $request->param('from');
        $to        = $request->param('to');
        $uid    = $request->param('uid');
        $w = [];
        if($mobile){
            $Members = new Members();
            $uid = $Members->get_mobile_id($mobile);
            if(!$uid){
                $this->error_new('用户不存在');
            }
            $w['uid']=$uid;
        }
        if($from && $to){
            $from = strtotime($from);
            $to   = strtotime($to);
            $w['create_at'] = [['>=',$from],['<=',$to]];
        }
        if($status){
            $w['status']=$status;
        }
        if($uid){
            $w['uid']=$uid;
        }
        $list        = Cash::where($w)->order('id desc')->paginate(20,false,['query' => request()->param()]);
        $renshu      = Cash::where($w)->count();
        $totle_money = Cash::where($w)->sum('money');
        return view('',[
            'list'=>$list,
            'page'=>$list->render(),
            'renshu'=>$renshu,
            'total_money'=>$totle_money,
        ]);
    }
    /**
     * 修改提现记录
     */
    public function edit_withdrawal_status(Request $request){
        $type   = $request->post('type');
        $uid    = $request->post('uid');
        $id     = $request->post('id');
        Db::startTrans();
        try{
            if($type==1) {
                $result_up = Db::name('withdrawals')->where(array('id' =>$id))->update(array('status' => 2,'update_at'=>time()));
            }
            if($type==2) {
                $result_up = Db::name('withdrawals')->where(array('id' =>$id))->update(array('status' => 3,'update_at'=>time()));
                $money     = Db::name('withdrawals')->where(array('id' =>$id))->value('money');
                $up_data["money"] = array("exp", "money+" . $money);
                $result = Db::name('member')->where(array('id'=>$uid))->update($up_data);
                $this->add_withdraw_detail_back($uid,$money);
            }
        }catch (\Exception $e){
            Db::rollback();
            json_return(500,'失败');
        }
        if($result_up){
            Db::commit();
            json_return(200,'成功');
        }else{
            Db::rollback();
            json_return(500,'失败');
        }

    }
    /**
     *投注记录
     */
    public function betting(Request $request){
        $from    = $request->param('from');
        $to      = $request->param('to');
        $id      = $request->param('id');
        $cate    = $request->param('cate');
        $code    = $request->param('code');
        $mobile  = $request->param('mobile');
        $w =[];
        if($from && $to){
            $from = strtotime($from);
            $to   = strtotime($to);
            $w['s.create_at'] = [['>=',$from],['<=',$to]];
        }
        if($cate){
            $w['s.cate'] = $cate;
        }
        if($id){
            $w['s.uid']  = $id;
        }
        if($code!=null && $code>=0){
            $w['s.code']  = $code;
        }
        if($mobile){
            $Members = new Members();
            $uid = $Members->get_mobile_id($mobile);
            if(!$uid){
                $this->error_new('用户不存在');
            }else{
                $w['s.uid']  = $uid;
            }
        }
        $list = Db::name('single')
            ->alias('s')
            ->field('s.*,c.name,b.title,m.mobile,m.nickname')
            ->join('dl_cate c','c.id=s.cate')
            ->join('dl_bet b','s.type=b.type and s.cate=b.cate')
            ->join('dl_member m','s.uid=m.id')
            ->where($w)
            ->order('s.id desc')
            ->paginate(20);
        $total_number =  Db::name('single')
            ->alias('s')
            ->where($w)
            ->count();
        $total_money =  Db::name('single')
            ->alias('s')
            ->where($w)
            ->sum('money');
        $w['code'] = 1;
        $zj_money =  Db::name('single')
            ->alias('s')
            ->where($w)
            ->sum('z_money');
        $cates = Db::name('cate')->select();
        return view('',[
            'cates'=>$cates,
            'list' =>$list,
            'page' =>$list->render(),
            'id'   =>$id,
            'cate' =>$cate,
            'total_number'=>$total_number,
            'total_money' =>$total_money,
            'zj_money' =>$zj_money
        ]);
    }
    /**
     * 回水流水
     */
    public function back_water(Request $request){
        $from    = $request->param('from');
        $to      = $request->param('to');
        $id      = $request->param('uid');
        $mobile  = $request->param('mobile');
        $exp     = $request->param('exp');
        $w =[];
        if($exp) {
            $w['exp'] = $exp;
        }else{
            $w['exp'] = 3;
        }
        if($from && $to){
            $from = strtotime($from);
            $to   = strtotime($to);
            $w['create_at'] = [['>=',$from],['<=',$to]];
        }
        if($id){
            $w['uid']  = $id;
        }
        if($mobile){
            $Members = new Members();
            $uid = $Members->get_mobile_id($mobile);
            if(!$uid){
                $this->error_new('用户不存在');
            }else{
                $w['uid']  = $uid;
            }
        }
        $list = Detail::alias('d')
            ->field('d.*,c.name')
            ->where($w)
            ->join('dl_cate c','d.cate=c.id','left')
            ->order('d.id desc')
            ->paginate(20,false,['query' => request()->param()]);

        $total_money = Detail::where($w)->sum('money');
        return view('',[
            'list' =>$list,
            'page' =>$list->render(),
            'total_money'=>$total_money,
        ]);
    }
    /**
     * 金额记录
     */
    public function transaction(Request $request){
        $from    = $request->param('from');
        $to      = $request->param('to');
        $id      = $request->param('id');
        $cate    = $request->param('exp');
        $mobile  = $request->param('mobile');
        $w =[];
        $uids = Db::name('member')->where('is_m',1)->column('id');
        $w['uid']     = ['in',$uids];
        if($from && $to){
            $from = strtotime($from);
            $to   = strtotime($to);
            $w['create_at'] = [['>=',$from],['<=',$to]];
        }
        if($cate){
            $w['exp'] = $cate;
        }
        if($id){
            $w['uid']  = $id;
        }
        if($mobile){
            $Members = new Members();
            $uid = $Members->get_mobile_id($mobile);
            if(!$uid){
                $this->error_new('用户不存在');
            }else{
                $w['uid']  = $uid;
            }
        }
        $w['status']  = 2;
        $list = Detail::where($w)->order('id desc')->paginate(20,false,['query' => request()->param()]);
        return view('',[
            'list' =>$list,
            'id'   =>$id,
            'page' =>$list->render(),
        ]);

    }
    /**
     * 修改流水状态
     */
    public function edit_water_status(Request $request){
        $type   = $request->post('type');
        $uid    = $request->post('uid');
        $id     = $request->post('id');

        Db::startTrans();
        try{
            if($type==1) {
                $result_up = Db::name('detail')->where(array('id' =>$id))->update(['status' => 2,'update_at'=>time()]);
                $money     = Db::name('detail')->where(array('id' =>$id))->value('money');
                $up_data["money"]    = array("exp", "money+" . $money);
                $result = Db::name('member')->where(array('id'=>$uid))->update($up_data);
            }
            if($type==2) {
                $result_up = Db::name('detail')->where(array('id' =>$id))->update(['status' => 3,'update_at'=>time()]);
            }
        }catch (\Exception $e){
            Db::rollback();
            json_return(500,'失败');
        }
        if($result_up){
            Db::commit();
            json_return(200,'成功');
        }else{
            Db::rollback();
            json_return(500,'失败');
        }
    }

    /**
     * 会员盈亏
     */
    public function win_lose_member(Request $request){
        $from    = $request->param('from')?$request->param('from'):date('Y-m-d').' 00:00:00';
        $to      = $request->param('to')?$request->param('to'):date('Y-m-d').' 23:59:59';
        $id      = $request->param('uid');
        $mobile  = $request->param('mobile');
        $w =[];
        $uid = "";
        if($from && $to){
            $from = strtotime($from);
            $to   = strtotime($to);
            $w['create_at'] = [['>=',$from],['<=',$to]];
            $w1['update_at'] = [['>=',$from],['<=',$to]];
        }
        if($id){
            $w['uid']  = $id;
            $w1['uid']  = $id;
            $uid  = $id;
        }
        if($mobile){
            $Members = new Members();
            $uid = $Members->get_mobile_id($mobile);
            if(!$uid){
                $this->error_new('用户不存在');
            }else{
                $w['uid']  = $uid;
            }
        }
        //总投注
        $betting_total = Detail::where($w)
            ->where(array('exp'=>5))
            ->sum('money');
        //总中奖
        $winning_total = Detail::where($w)
            ->where(array('exp'=>2))
            ->sum('money');
        //总回水
        $back_total = Detail::where($w)
            ->where(array('exp'=>3))
            ->sum('money');

        //总充值
        $recharge_total = Recharge::where($w1)
            ->where('status',2)
            ->sum('money');

        //总提现
        $withdrawals_total = Db::name('withdrawals')
            ->where($w1)
            ->where('status',2)
            ->sum('money');
        if($uid) {
            $profit = $winning_total + $back_total  - $betting_total;
        }else{
            $profit = $betting_total - $winning_total - $back_total ;
        }
        $profit            = number_format($profit,2);
        $betting_total     = number_format($betting_total,2);
        $winning_total     = number_format($winning_total,2);
        $back_total        = number_format($back_total,2);
        $recharge_total    = number_format($recharge_total,2);
        $withdrawals_total = number_format($withdrawals_total,2);
        if($profit>0){
            $now_profit = "+".$profit;
        }elseif ($profit<0){
            $now_profit = $profit;
        }else{
            $now_profit=0.00;
            $now_profit            = number_format($now_profit,2);
        }
        return view('',[
            'uid'=>$id,
            'profit'=> $now_profit,
            'betting_total' =>$betting_total,
            'winning_total'=>$winning_total,
            'back_total' =>$back_total,
            'recharge_total'=>$recharge_total,
            'withdrawals_total'=> $withdrawals_total,
        ]);
    }

    /**
     * 会员盈亏列表
     */
    public function member_win(Request $request){
        $from    = $request->param('from')?$request->param('from'):date('Y-m-d'). '00:00:00';
        $to      = $request->param('to')?$request->param('to'):date('Y-m-d').' 23:59:59';
        $id      = $request->param('id');
        $mobile  = $request->param('mobile');
        $w =[];
        
        if($from && $to){
            $from = strtotime($from);
            $to   = strtotime($to);
            $w['s.create_at'] = [['>=',$from],['<=',$to]];
        }
        if($id){
            $w['uid']  = $id;
        }
        if($mobile){
            $Members = new Members();
            $uid = $Members->get_mobile_id($mobile);
            if(!$uid){
                $this->error_new('用户不存在');
            }else{
                $w['s.uid']  = $uid;
            }
        }
        $w['m.is_m']     = 1;
        $log_list= Db::name('single')
            ->alias('s')
            ->field('uid,sum(s.money) as money,sum(z_money) as z_money,m.mobile,m.nickname')
            ->join('dl_member m','s.uid=m.id')
            ->where($w)
            ->group('uid')
            ->paginate(20,false,['query' => request()->param()]);



        $list = $log_list->all();
        $all_money = 0;
        foreach ( $list as $k=>$v){
            $win = $v['z_money']-$v['money'];
            $all_money = $all_money+$win;
        }

        return view('',[
            'list' =>$log_list,
            'page' =>$log_list->render(),
            'all_money'=>$all_money
        ]);
    }



    /**
     * 盈亏
     */
    public function win_lose(Request $request){
        $from    = $request->param('from')?$request->param('from'):date('Y-m-d').' 00:00:00';
        $to      = $request->param('to')?$request->param('to'):date('Y-m-d').' 23:59:59';;
        $id      = $request->param('uid');
        $mobile  = $request->param('mobile');
        $w =[];
        $uid = "";
        if($from && $to){
            $from = strtotime($from);
            $to   = strtotime($to);
            $w['create_at'] = [['>=',$from],['<=',$to]];
        }
        if($id){
            $w['uid']   = $id;
            $w1['uid']  = $id;
            $uid  = $id;
        }
        if($mobile){
            $Members = new Members();
            $uid = $Members->get_mobile_id($mobile);
            if(!$uid){
                $this->error_new('用户不存在');
            }else{
                $w['uid']  = $uid;
            }
        }
        $uids = Db::name('member')->where('is_m',1)->column('id');
        $w['uid']     = ['in',$uids];
        //总投注
        $betting_total = Db::name('single')
            ->where($w)
            ->sum('money');
        //总中奖
        $winning_total = Db::name('single')
            ->where($w)
            ->sum('z_money');
        //总回水
        $back_total = 0;

        //总充值
        $recharge_total = Recharge::where($w)
            ->where('status',2)
            ->where($w)
            ->sum('money');

        //总提现
        $withdrawals_total = Db::name('withdrawals')
            ->where('status',2)
            ->where($w)
            ->sum('money');
        if($uid) {
            $profit = $winning_total + $back_total  - $betting_total;
        }else{
            $profit = $betting_total - $winning_total - $back_total ;
        }
        $profit            = number_format($profit,2);
        $betting_total     = number_format($betting_total,2);
        $winning_total     = number_format($winning_total,2);
        $back_total        = number_format($back_total,2);
        $recharge_total    = number_format($recharge_total,2);
        $withdrawals_total = number_format($withdrawals_total,2);
        if($profit>0){
            $now_profit = "+".$profit;
        }elseif ($profit<0){
            $now_profit = $profit;
        }else{
            $now_profit=0.00;
            $now_profit            = number_format($now_profit,2);
        }
        return view('',[
            'profit'=> $now_profit,
            'betting_total' =>$betting_total,
            'winning_total'=>$winning_total,
            'back_total' =>$back_total,
            'recharge_total'=>$recharge_total,
            'withdrawals_total'=> $withdrawals_total,
        ]);
    }
    /**
     * 查看提现二维码
     */
    public function qrcode(Request $request){
        $id     = $request->param('id');
        $data   = Db::name('withdrawals')->where(array('id'=>$id))->find();
        return view('',[
            'data' =>$data,
        ]);

    }
    /**
     * 检查是否有新的充值
     */
    public function is_recharge(){
        $re = Db::name('recharge')
            ->where('is_read',0)
            ->order('id desc')
            ->find();
        $rs = Db::name('withdrawals')
            ->where('is_read',0)
            ->order('id desc')
            ->find();
        if($re || $rs){
            if($re) {
                $name = Db::name('member')->where('id',$re['uid'])->value('mobile');
                Db::name('recharge')
                    ->where(array('is_read' => 0))
                    ->update(array('is_read' => 1));
                $data['type'] = 1;
                $data['name'] = $name;
            }
            if($rs){
                $name = Db::name('member')->where('id',$rs['uid'])->value('mobile');
                Db::name('withdrawals')
                    ->where(array('is_read' => 0))
                    ->update(array('is_read' => 1));
                $data['type'] = 2;
                $data['name'] = $name;
            }
            json_return(200, '成功',$data);
        }else{
            $data['type'] = 0;
            $data['name'] = '无';
            json_return(500,'成功',$data);
        }
    }
    /**
     * 具体盈亏
     */
    public function detail(Request $request){
        $uid      = $request->param('uid');
        $from     = $request->param('from');
        $to       = $request->param('to');
        $w =[];
        $from_ag = $from;
        if($from && $to){
            $from = strtotime($from);
            $to   = strtotime($to);
            $w['create_at'] = [['>=',$from],['<=',$to]];
        }
        $today  = strtotime(date('Y-m-d'));

        if(!$w) {
            $w['create_at'] = array('>', $today);
            $w['uid'] = $uid;
        }
        $w1['update_at'] = array('>', $today);
        $w1['uid'] = $uid;
        //今日佣金
        $today_yj =  Db::name('detail')
            ->where($w)
            ->where(array('exp'=>4))
            ->sum('money');
        $back['today_yj'] = $today_yj ;

        //今日退水
        $today_ts =  Db::name('detail')
            ->where($w1)
            ->where(array('status'=>2))
            ->where('exp','in','3,6')
            ->sum('money');
        $back['today_ts'] = $today_ts;
        //PC蛋蛋
        $pc = Db::name('single')
            ->field('cate,sum(z_money-money) as total')
            ->where($w)
            ->group('cate')
            ->select();
        $back['pc_money']     = '0.00';
        $back['canada_money'] = '0.00';
        $back['car_money']    = '0.00';
        $back['ship_money']   = '0.00';
        $back['ssc_money']    = '0.00';
        $back['tjssc_money']  = '0.00';
        $back['gd10_money']   = '0.00';
        $back['cq10_money']   = '0.00';
        $back['fast_money']   = '0.00';
        $back['gd11_money']   = '0.00';
        $back['hk_money']     = '0.00';

        foreach($pc as $k=>$v){
            switch($v['cate']){
                case 1:
                    $back['pc_money']   = $v['total'];
                    break;
                case 2:
                    $back['canada_money'] = $v['total'];
                    break;
                case 3:
                    $back['car_money'] = $v['total'];
                    break;
                case 4:
                    $back['ship_money'] = $v['total'];
                    break;
                case 5:
                    $back['ssc_money'] = $v['total'];
                    break;
                case 6:
                    $back['tjssc_money'] = $v['total'];
                    break;
                case 7:
                    $back['gd10_money'] = $v['total'];
                    break;
                case 8:
                    $back['cq10_money'] = $v['total'];
                    break;
                case 9:
                    $back['fast_money'] = $v['total'];
                    break;
                case 10:
                    $back['gd11_money'] = $v['total'];
                    break;
                case 11:
                    $back['hk_money'] = $v['total'];
                    break;
            }
        }
        if($from){
            $wag['create_at']       = ['gt',$from_ag];
            $wbb['create_at']       = ['gt',$from_ag];
            $wss['create_at']       = ['gt',$today];
        }else{
            $today = date('Y-m-d');
            $wag['create_at']      = ['gt',$today];
            $wbb['create_at']      = ['gt',$today];
            $wss['create_at']      = ['gt',$today];
        }
        if($uid){
            $gm_name = Db::name('member')->where('id',$uid)->value('gm_name');
            $wag['username'] = $gm_name;
            $wbb['UserName'] = $gm_name;
            $wss['account_code'] = $gm_name;
        }
        $back['ag_money'] = Db::name('at_ag')->where($wag)->sum('netPnl');
        $back['bb_money'] = Db::name('at_bb')->where($wbb)->sum('Payoff');
        $back['ss_money'] = Db::name('at_ss')->where($wss)->sum('win_amt');
        //$back['all_money'] =   $back['pc_money'] +$back['canada_money'] + $back['car_money'] + $back['ship_money'] + $back['ssc_money'] + $back['tjssc_money'] + $back['gd10_money'] + $back['cq10_money'] + $back['fast_money'] + $back['gd11_money'] + $back['hk_money'] +$back['today_yj']+$back['today_ts']+$back['ag_money'] +$back['bb_money'];
        return view('',[
            'back'=>$back
        ]);

    }
    /**
     * 代理明细
     */
    public function agent(Request $request){
        $start_p    = $request->param('from');
        $end_p      = $request->param('to');
        $uid        = $request->param('id');
        $mobile     = $request->param('mobile');
        $w =[];
        if($mobile){
            $Members = new Members();
            $uid = $Members->get_mobile_id($mobile);
            if(!$uid){
                $this->error_new('用户不存在');
            }
        }
        if($start_p && $end_p){
            $start_p_other = strtotime($start_p);
            $end_p_other   = strtotime($end_p);
            $w['create_at'] = ['between',[$start_p_other,$end_p_other] ];
            $wag['transactionTime']   = ['between', [$start_p,$end_p]];
            $wbb['WagersDate']        = ['between', [$start_p,$end_p]];
            $wss['create_at']         = ['between', [$start_p,$end_p]];
        }

        $user_info  = Db::name('member')->field('nickname,mobile')->where('id',$uid)->find();
        $back       = $this->get_uids($uid);
        $uids       = $back['uids'];
        $game_names = $back['gm_name'];
        $uids_yj    = $this->get_uids_yj($uid)['uids'];
        $halls      = Db::name('hall')->select();
        $percent     = Db::name('member')->where('id',$uid)->value('proxy_percent');
        $total_money = '0.00';
        $all_my_win  = '0.00';
        foreach ($halls as $k=>$v){
            $w['uid'] = ['in', $uids];
            $w['cate'] = $v['cate'];
            $w['hall'] = $v['hall'];
            if(!in_array($v['cate'],[13,14,15])) {
                $data = Db::name('single')
                    ->where($w)
                    ->sum('z_money-money');
                if($data){
                    $total_money = $data;
                }else{
                    $total_money = '0.00';
                }
            }
            if($v['cate']==13) {
                $wag['username']          = ['in', $game_names];
                $wag['hall']              = $v['hall'];
                $data = Db::name('at_ag')
                    ->where($wag)
                    ->sum('netPnl');
                if($data){
                    $total_money = $data ;
                }else{
                    $total_money = '0.00';
                }
            }
            if($v['cate']==14) {
                $wbb['UserName']   = ['in', $game_names];
                $wbb['hall']       = $v['hall'];
                $data = Db::name('at_bb')
                    ->where($wbb)
                    ->sum('Payoff');
                if($data){
                    $total_money = $data ;
                }else{
                    $total_money = '0.00';
                }
            }
            if($v['cate']==15) {
                $wss['account_code']   = ['in', $game_names];
                $wss['hall']           = $v['hall'];
                $data = Db::name('at_ss')
                    ->where($wss)
                    ->sum('win_amt');
                if($data){
                    $total_money = $data ;
                }else{
                    $total_money = '0.00';
                }
            }
            $back_money = Db::name('detail')
                ->where($w)
                ->where('exp','in','3,6')
                ->where('status',2)
                ->sum('money');
            $w0['uid']  = ['in', $uids_yj];
            $w0['cate'] = $v['cate'];
            $w0['hall'] = $v['hall'];
            $back_yj = Db::name('detail')
                ->where($w0)
                ->where('exp',4)
                ->where('status',2)
                ->sum('money');
            $back_money = $back_money+$back_yj;
//            $ww['cate'] = $v['cate'];
//            $ww['hall'] = $v['hall'];
//            $back_data = Db::name('water')->where($ww)->find();
//            $percent    = $back_data['dl_water'];
            $fact_money = $total_money+$back_money;
            $my_win     = $fact_money * $percent;
            $my_win     = round($my_win,2);
            if($my_win>0){
                $my_win = -$my_win;
            }elseif ($my_win<=0){
                $my_win = abs($my_win);
            }
            $all_back_data[$k]['total_money'] = number_format($total_money,2);
            $all_back_data[$k]['back_money']  = number_format($back_money,2);
            $all_back_data[$k]['fact_money']  = number_format($fact_money,2);
            $all_back_data[$k]['my_win']      = number_format($my_win,2);
            $all_back_data[$k]['room']        = $v['name'];
            $all_my_win = $all_my_win + $my_win;
        }
        $return['all_my_win'] = round($all_my_win,2);
        $return['data']       = $all_back_data;
        $return['id']         = $uid;
        $return['nickname']   = $user_info['nickname'];
        return view('',$return);
    }
    /**
     * 获取代理人员
     */
    public function get_uids($uid){
        $w['tid'] = $uid;
        $proxy_level = Db::name('member')->where('id',$uid)->value('proxy_level');
        $field = 'id,gm_name';
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
       foreach($daa as $k=>$v){
           $uids[]   = $v['id'];
           $gm_name[] = $v['gm_name'];
       }
        $back['uids'] = $uids;
        $back['gm_name'] = $gm_name;
        return $back;
    }
    /**
     * 获取代理人员
     */
    public function get_uids_yj($uid){
        $w['tid'] = $uid;
        $proxy_level = Db::name('member')->where('id',$uid)->value('proxy_level');
        $proxy_level = $proxy_level-1;
        $field = 'id,gm_name';
        $list1 = Db::name('member')->field($field)->where($w)->order('id desc')->select();
        $level1 = [];
        foreach ($list1 as $k => $v) {
            $level1[] = $v['id'];
            $daa    [] = $v;
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
        foreach($daa as $k=>$v){
            $uids[]   = $v['id'];
            $gm_name[] = $v['gm_name'];
        }
        $back['uids']    = $uids;
        $back['gm_name'] = $gm_name;
        return $back;
    }
    /**
     * 财务统计
     */
    public function financial(Request $request){
            $from    = $request->param('from');
            $to      = $request->param('to');
            $w =[];
            $from_ag = $from;
            if($from && $to){
                $from = strtotime($from);
                $to   = strtotime($to);
                $w['create_at'] = [['>=',$from],['<=',$to]];
            }
            $today  = strtotime(date('Y-m-d'));

            if(!$w) {
                $w['create_at'] = array('>', $today);
            }
            $w1['update_at'] = array('>', $today);
            //昨日留盘
            $yesterday = date("Y-m-d",strtotime("-1 day"));
            $yesterday_money = Db::name('recording')
                ->where(array('date'=>$yesterday))
                ->value('balance');
            $back['yesterday_money'] = $yesterday_money;
            //今日留盘
            $today_money = Db::name('member')
                ->sum('money');
            $back['today_money'] = $today_money;
            //今日充值
            $today_recharge = Db::name('recharge')
                ->where($w1)
                ->where('status','in','2')
                ->sum('money');
            $back['today_recharge'] = $today_recharge;
            //今日提现
            $today_tx = Db::name('withdrawals')
                ->where($w1)
                ->where('status','in','2')
                ->sum('money');
            $back['today_tx'] = $today_tx;
            //今日佣金
            $today_yj =  Db::name('detail')
                ->where($w)
                ->where(array('exp'=>4))
                ->sum('money');
            $back['today_yj'] = $today_yj ;

            //今日退水
            $today_ts =  Db::name('detail')
                ->where($w)
                ->where(array('status'=>2))
                ->where('exp','in','3,6')
                ->sum('money');
            $back['today_ts'] = $today_ts;
            //今日微信
            $today_wx_charge = Db::name('recharge')
                ->where($w1)
                ->where(array('way'=>1))
                ->where('status','in','2')
                ->sum('money');
            $today_wx_tx = Db::name('withdrawals')
                ->where($w1)
                ->where(array('way'=>1))
                ->where('status','in','2')
                ->sum('money');
            $today_wx = $today_wx_charge-$today_wx_tx;
            $back['today_wx'] = $today_wx;
            //今日支付宝
            $today_pay_charge = Db::name('recharge')
                ->where($w1)
                ->where(array('way'=>2))
                ->where('status','in','1,2')
                ->sum('money');
            $today_pay_tx = Db::name('withdrawals')
                ->where($w1)
                ->where(array('way'=>2))
                ->where('status','in','1,2')
                ->sum('money');
            $today_pay = $today_pay_charge-$today_pay_tx;
            $back['today_pay'] = $today_pay;
            //今日银行
            $today_bank_charge = Db::name('recharge')
                ->where($w1)
                ->where(array('way'=>3))
                ->where('status','in','2')
                ->sum('money');
            $today_bank_tx = Db::name('withdrawals')
                ->where($w)
                ->where(array('way'=>3))
                ->where('status','in','2')
                ->sum('money');
            $today_bank = $today_bank_charge-$today_bank_tx;
            $back['today_bank'] = $today_bank;
            //今日上分
            $today_up = Db::name('detail')
                ->where($w)
                ->where(array('exp'=>7,'type'=>2))
                ->sum('money');
            $back['today_up'] = $today_up;
            //今日下分
            $today_down = Db::name('detail')
                ->where($w)
                ->where(array('exp'=>7,'type'=>1))
                ->sum('money');
            $back['today_down'] = $today_down;

            //今日盈利
            $win_lose = $today_recharge-$today_tx-($today_money-$yesterday_money);
            $back['win_lose'] = round($win_lose,2);
            //平台AG/BB余额
            $API = new Biapi();
            $agbb = $API->BusinessBalance();
            if($agbb['retCode']!=-1){
                $back['ag1_money'] = $agbb['retMsg'][0]['AG'];
                $back['bb1_money'] = $agbb['retMsg'][0]['BB'];
                $back['ss1_money'] = $agbb['retMsg'][0]['SS'];
            }else {
                $back['ag1_money'] = 0.00;
                $back['bb1_money'] = 0.00;
                $back['ss1_money'] = 0.00;
            }
            //PC蛋蛋
            $pc = Db::name('single')
                ->field('cate,sum(money-z_money) as total')
                ->where($w)
                ->group('cate')
                ->select();
            $back['pc_money'] = 0.00;
            $back['canada_money'] = 0.00;
            $back['car_money'] = 0.00;
            $back['ship_money'] = 0.00;
            $back['ssc_money'] = 0.00;
            $back['tjssc_money'] = 0.00;
            $back['gd10_money'] = 0.00;
            $back['cq10_money'] = 0.00;
            $back['fast_money'] = 0.00;
            $back['gd11_money'] = 0.00;
            $back['hk_money'] = 0.00;
            foreach($pc as $k=>$v){
                switch($v['cate']){
                    case 1:
                        $back['pc_money']   = $v['total'];
                        break;
                    case 2:
                        $back['canada_money'] = $v['total'];
                        break;
                    case 3:
                        $back['car_money'] = $v['total'];
                        break;
                    case 4:
                        $back['ship_money'] = $v['total'];
                        break;
                    case 5:
                        $back['ssc_money'] = $v['total'];
                        break;
                    case 6:
                        $back['tjssc_money'] = $v['total'];
                        break;
                    case 7:
                        $back['gd10_money'] = $v['total'];
                        break;
                    case 8:
                        $back['cq10_money'] = $v['total'];
                        break;
                    case 9:
                        $back['fast_money'] = $v['total'];
                        break;
                    case 10:
                        $back['gd11_money'] = $v['total'];
                        break;
                    case 11:
                        $back['hk_money'] = $v['total'];
                        break;
                }
            }
            if($from){
                $wag['transactionTime'] = ['gt',$from_ag];
                $wbb['WagersDate']      = ['gt',$from_ag];
                $wss['create_at']       = ['gt',$from_ag];
            }else{
                $today_d = date('Y-m-d');
                $wag['transactionTime'] = ['gt',$today_d];
                $wbb['WagersDate']      = ['gt',$today_d];
                $wss['create_at']    = ['gt',$today_d];
            }
            $back['ag_money'] = Db::name('at_ag')->where($wag)->sum('netPnl');
            if($back['ag_money']>0){
                $back['ag_money'] = -$back['ag_money'];
            }else{
                $back['ag_money'] = abs($back['ag_money']);
            }


            $back['bb_money'] = Db::name('at_bb')->where($wbb)->sum('Payoff');
            if($back['bb_money']>0){
                $back['bb_money'] = -$back['bb_money'];
            }else{
                $back['bb_money'] = abs($back['bb_money']);
            }

            $back['ss_money'] = Db::name('at_ss')->where($wss)->sum('win_amt');
            if($back['ss_money']>0){
                $back['ss_money'] = -$back['ss_money'];
            }else{
                $back['ss_money'] = abs($back['ss_money']);
            }
            $back['all_money'] =   $back['pc_money'] +$back['canada_money'] + $back['car_money'] + $back['ship_money'] + $back['ssc_money'] + $back['tjssc_money'] + $back['gd10_money'] + $back['cq10_money'] + $back['fast_money'] + $back['gd11_money'] + $back['hk_money']+$back['ag_money']+$back['bb_money']+$back['ss_money'];
            return view('',[
                'back'=>$back
            ]);
    }
    /**
     * 返还金额界面
     */
    public function back_to_client(){
        $cates = Db::name('cate')
            ->field('id,name')
            ->where('id','<',13)
            ->select();
        return view('',[
            'cates'=>$cates,
        ]);
    }
    /**
     * 返还金额
     */
    public function add_back_money(Request $request){
        header("Content-type: text/html; charset=utf-8");
        $cate  = $request->post('cate');
        $stage = $request->post('stage');
        $map = [
          'cate' =>$cate,
          'stage'=>$stage
        ];
        $data1 = Db::name('back_client')->where($map)->select();
        if($data1){
            $this->error_new('已经返还过了');
        }
        $map['stage'] = 0;
        $data  = Db::name('single')->where($map)->select();
        $data0 = Db::name('single')->field('uid,sum(money) as money')->where($map)->group('uid')->select();

        if(!$data){
            $this->error_new('该期数不存在');
        }
        if($data){
            if($data[0]['state']==1){
                $this->error_new('该期已开奖');
            }
            Db::startTrans();
            try{
                $daa   = [];
                foreach ($data as $k=>$v){
                    $daa[$k]['uid']   = $v['uid'];
                    $daa[$k]['cate']  = $v['cate'];
                    $daa[$k]['stage'] = $v['stage'];
                    $daa[$k]['oid']   = $v['id'];
                    $daa[$k]['money'] = $v['money'];
                }
                $result0 = Db::name('back_client')->insertAll($daa);
                if(!$result0){
                    throw new \Exception("插入数据失败");
                }
                foreach ($data0 as $kk=>$vv){
                    Db::name('member')->where('id',$vv['uid'])->setInc('money',$vv['money']);
                }
                Db::commit();
                $this->error_new('返还成功');
            }catch (\Exception $e){
                Db::rollback();
                $msg = $e->getMessage();
                $this->error_new($msg);
            }
        }

    }
    /**
     * 返还客户记录
     */
    public function back_client_detail(Request $request){
        $nickname = $request->param('nickname');
        $w = [];
        if($nickname){
            $w['m.nickname'] = $nickname;
        }
        $data = Db::name('back_client')
                   ->alias('b')
                   ->field('b.*,m.nickname,c.name')
                   ->join('dl_member m','m.id=b.uid')
                   ->join('dl_cate c','c.id=b.cate')
                   ->where($w)
                   ->order('b.id desc')
                   ->paginate(20);
        return view('',[
            'data'=>$data,
            'page'=>$data->render(),
        ]);
    }

    /**
     * 后台提现
     */
    public function withdraw_money(Request $request){
        $mobile   = trim($request->post('mobile'));
        $money    = trim($request->post('money'));
        $remark   = $request->post('remark');
        if(!$remark){
            $remark = '后台下分';
        }
        $Members = new Members();
        $uid = $Members->get_mobile_id($mobile);
        if(!$uid){
            $this->error_new('用户不存在');
        }
        if(!$money){
            $this->error_new('请输入提现金额');
        }
        $user = Members::get($uid);
        $my_money      = $user['money'];
        if($my_money<$money){
            $this->error_new('余额不足');
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
            $this->error_new('下分失败');
        }
        if($result && $result0){
            $this->success_new('下分成功');
        }else{
            $this->error_new('下分失败');
        }
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

}