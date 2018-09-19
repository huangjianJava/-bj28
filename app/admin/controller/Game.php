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

class Game extends Admin
{

    /**
     * AG/BB记录
     */
    public function index(Request $request){
        $from    = $request->param('from');
        $to      = $request->param('to');
        $id      = $request->param('id');
        $mobile  = $request->param('mobile');
        $cate    = $request->param('cate');
        $w =[];
        if($from && $to){
            $from = strtotime($from);
            $to   = strtotime($to);
            $w['create_at'] = [['>=',$from],['<=',$to]];
        }
        $w['exp'] = 7;
        if($id){
            $w['uid']  = $id;
        }
        if($cate){
            $w['cate']  = $cate;
        }
        if($mobile){
            if(is_numeric($mobile)) {
                $Members = new Members();
                $uid = $Members->get_mobile_id($mobile);
                if (!$uid) {
                    $this->error_new('用户不存在');
                } else {
                    $w['uid'] = $uid;
                }
            }else{
                $uid = Db::name('member')->where(array('gm_name'=>$mobile))->value('id');
                if (!$uid) {
                    $this->error_new('用户不存在');
                } else {
                    $w['uid'] = $uid;
                }
            }
        }
        $list    = Detail::where($w)->order('id desc')->paginate(20);
        if(isset($w['cate'])){
            unset($w['cate']);
        }
        $ag_up   = Detail::where($w)->where(['type'=>2,'cate'=>13])->sum('money');
        $ag_down = Detail::where($w)->where(['type'=>1,'cate'=>13])->sum('money');
        $bb_up   = Detail::where($w)->where(['type'=>2,'cate'=>14])->sum('money');
        $bb_down = Detail::where($w)->where(['type'=>1,'cate'=>14])->sum('money');
        $ss_up   = Detail::where($w)->where(['type'=>2,'cate'=>15])->sum('money');
        $ss_down = Detail::where($w)->where(['type'=>1,'cate'=>15])->sum('money');
        return view('',[
            'list' =>$list,
            'page' =>$list->render(),
            'ag_up'=>$ag_up,
            'ag_down'=>$ag_down,
            'bb_up'=>$bb_up,
            'bb_down'=>$bb_down,
            'ss_up'=>$ss_up,
            'ss_down'=>$ss_down
        ]);

    }

    /**
     * AG记录
     */
    public function game_ag(Request $request){
        $from    = $request->param('from');
        $to      = $request->param('to');
        $mobile  = $request->param('mobile');
        $w = [];
        if($from && $to){
            $w['a.betTime'] = [['>=',$from],['<=',$to]];
        }
        if($mobile){
            $w['m.mobile'] = $mobile;
        }
        $log_list = Db::name('at_ag')
            ->alias('a')
            ->field('m.nickname,m.mobile,m.gm_name,a.i_id,a.betAmount,a.netPnl,a.betOrderNo,a.betTime,a.gameCode')
            ->join('dl_member m','m.gm_name=a.username')
            ->where($w)
            ->order('a.i_id desc')
            ->paginate(20,false,['query' => request()->param()]);

        $list = $log_list->all();
        foreach ( $list as $k=>$v){
            $ag_zh = ag_type($v['gameCode']);
            $v['name'] = $ag_zh['name'];
            $v['big'] = $ag_zh['big'];
            $log_list[$k] = $v;
        }
         $total_money = Db::name('at_ag')
             ->alias('a')
             ->join('dl_member m','m.gm_name=a.username')
             ->where($w)
             ->sum('netPnl');
           return view('',[
               'list' =>$log_list,
               'total_money'=>$total_money,
               'page' =>$log_list->render(),
           ]);
    }

    /**
     * BB记录
     */
    public function game_bb(Request $request){
        $from    = $request->param('from');
        $to      = $request->param('to');
        $mobile  = $request->param('mobile');
        $w = [];
        if($from && $to){
            $w['a.betTime'] = [['>=',$from],['<=',$to]];
        }
        if($mobile){
            $w['m.mobile'] = $mobile;
        }
        $log_list = Db::name('at_bb')
            ->alias('a')
            ->field('m.nickname,m.mobile,m.gm_name,a.i_id,a.BetAmount,a.Payoff,a.WagersID,a.WagersDate,a.GameType')
            ->join('dl_member m','m.gm_name=a.UserName')
            ->where($w)
            ->order('a.i_id desc')
            ->paginate(20,false,['query' => request()->param()]);

        $list = $log_list->all();
        foreach ( $list as $k=>$v){
            $ag_zh = bb_type($v['GameType']);
            $v['name'] = $ag_zh['name'];
            $v['big'] = $ag_zh['big'];
            $log_list[$k] = $v;
        }
        $total_money = Db::name('at_bb')
            ->alias('a')
            ->join('dl_member m','m.gm_name=a.UserName')
            ->where($w)
            ->sum('Payoff');
        return view('',[
            'list' =>$log_list,
            'page' =>$log_list->render(),
            'total_money'=>$total_money,
        ]);
    }

    /**
     * AG记录
     */
    public function game_ss(Request $request){
        $from    = $request->param('from');
        $to      = $request->param('to');
        $mobile  = $request->param('mobile');
        $w = [];
        if($from && $to){
            $w['a.betTime'] = [['>=',$from],['<=',$to]];
        }
        if($mobile){
            $w['m.mobile'] = $mobile;
        }
        $log_list = Db::name('at_ss')
            ->alias('a')
            ->field('m.nickname,m.mobile,m.gm_name,a.id,a.wager_stake as betAmount,a.win_amt as netPnl,a.transactionid as betOrderNo,a.date_created as betTime,a.play_type as gameCode')
            ->join('dl_member m','m.gm_name=a.account_code')
            ->where($w)
            ->order('a.id desc')
            ->paginate(20,false,['query' => request()->param()]);

        $list = $log_list->all();
        foreach ( $list as $k=>$v){
            $ag_zh = ss_type($v['gameCode']);
            $v['name'] = $ag_zh['name'];
            $v['big'] = $ag_zh['big'];
            $log_list[$k] = $v;
        }
        $total_money = Db::name('at_ss')
            ->alias('a')
            ->join('dl_member m','m.gm_name=a.account_code')
            ->where($w)
            ->sum('win_amt');
        return view('',[
            'list' =>$log_list,
            'total_money'=>$total_money,
            'page' =>$log_list->render(),
        ]);
    }

    /**
     * AG转回显示
     */
    public function ag_back(){
        $data = Db::name('member')
            ->field('id,mobile,gm_name')
            ->where('is_ag',1)
            ->order('id desc')
            ->paginate(20);
        $list = $data->all();
        foreach ($list as $k=>$v){
            $last_time =  Db::name('at_ag')
                ->where('username',$v['gm_name'])
                ->order('transactionTime desc')
                ->value('transactionTime');
            if(!$last_time){
                $v['last_time'] = '还没有投注';
            }else{
                $v['last_time'] = ceil((time()-strtotime($last_time))/3600);
            }
            error_reporting(0);
            $API = new Biapi();
            $my_money = $API->balances('AG',$v['gm_name']);
            $v['game_money'] = $my_money;
            $data[$k] = $v;
        }
        return view('back_ag',[
            'list' =>$data,
            'page' =>$data->render(),
        ]);
    }

    /**
     * AG转回
     */
   public function edit_back_ag(Request $request)
   {
          $uid   = $request->post('uid');
          $money = $request->post('game_money');
          $money = floor($money);
          //转出AG、BB平台
           $API = new Biapi();
           $user_info = Db::name('member')->where(array('id' => $uid))->field('gm_name,mobile,money')->find();
           $yc_money  = $user_info['money'];
           $mobile    = $user_info['gm_name'];
           $my_money  = $money;
           Db::startTrans();
           try {
               $new_money = $yc_money + $money;
               $result0 = Db::name('member')
                   ->where(array('id' => $uid))
                   ->update(array('money' => $new_money));
               $data['uid'] = $uid;
               $data['type'] = 1;
               $data['money'] = $money;
               $data['balance'] = $new_money;
               $data['exp'] = 7;
               $data['cate'] = 13;
               $data['g_money'] = $my_money - $money;
               $data['explain'] =  'AG下分';
               $data['create_at'] = time();
               $result1 = Db::name('detail')->insert($data);
               $result2 = $API->zzmoney('AG', $mobile, 'OUT', $money);
           } catch (\Exception $e) {
               Db::rollback();
               json_return(500, '转出失败');
           }
           if ($result0 && $result1 && $result2) {
               Db::commit();
               json_return(200, '转出成功');
           }
       }

    /**
     * BB转回显示
     */
    public function bb_back(){
        $data = Db::name('member')
            ->field('id,mobile,gm_name')
            ->where('is_bb',1)
            ->order('id desc')
            ->paginate(20);
        $list = $data->all();
        foreach ($list as $k=>$v){
            $last_time =  Db::name('at_bb')
                ->where('username',$v['gm_name'])
                ->order('WagersDate desc')
                ->value('WagersDate');
             if(!$last_time){
                 $v['last_time'] = '还没有投注';
             }else{
                 $v['last_time'] = ceil((time()-strtotime($last_time))/3600);
             }
            error_reporting(0);
            $API = new Biapi();
            $my_money = $API->balances('BB',$v['gm_name']);
            $v['game_money'] = $my_money;
            $data[$k] = $v;
        }
        return view('back_bb',[
            'list' =>$data,
            'page' =>$data->render(),
        ]);
    }

    /**
     * BB转回
     */
    public function edit_back_bb(Request $request)
    {
        $uid = $request->post('uid');
        $money = $request->post('game_money');
        $money = floor($money);
        //转出AG、BB平台
        $API = new Biapi();
        $user_info = Db::name('member')->where(array('id' => $uid))->field('gm_name,mobile,money')->find();
        $yc_money = $user_info['money'];
        $mobile = $user_info['gm_name'];
        $my_money = $money;
        Db::startTrans();
        try {
            $new_money = $yc_money + $money;
            $result0 = Db::name('member')
                ->where(array('id' => $uid))
                ->update(array('money' => $new_money));
            $data['uid'] = $uid;
            $data['type'] = 1;
            $data['money'] = $money;
            $data['balance'] = $new_money;
            $data['exp'] = 7;
            $data['cate'] = 14;
            $data['g_money'] = $my_money - $money;
            $data['explain'] =  'BB下分';
            $data['create_at'] = time();
            $result1 = Db::name('detail')->insert($data);
            $result2 = $API->zzmoney('BB', $mobile, 'OUT', $money);
        } catch (\Exception $e) {
            Db::rollback();
            json_return(500, '转出失败');
        }
        if ($result0 && $result1 && $result2) {
            Db::commit();
            json_return(200, '转出成功');
        }
    }

    /**
     * SS转回显示
     */
    public function ss_back(){
        $data = Db::name('member')
            ->field('id,mobile,gm_name')
            ->where('is_ss',1)
            ->order('id desc')
            ->paginate(20);
        $list = $data->all();
        foreach ($list as $k=>$v){
            $last_time =  Db::name('at_ss')
                ->where('account_code',$v['gm_name'])
                ->order('date_created desc')
                ->value('date_created');
            if(!$last_time){
                $v['last_time'] = '还没有投注';
            }else{
                $v['last_time'] = ceil((time()-strtotime($last_time))/3600);
            }
            error_reporting(0);
            $API = new Biapi();
            $my_money = $API->balances('SS',$v['gm_name']);
            $v['game_money'] = $my_money;
            $data[$k] = $v;
        }
        return view('back_ss',[
            'list' =>$data,
            'page' =>$data->render(),
        ]);
    }

    /**
     * SS转回
     */
    public function edit_back_ss(Request $request)
    {
        $uid  = $request->post('uid');
        $money = $request->post('game_money');
        $money = floor($money);
        //转出AG、BB平台
        $API       = new Biapi();
        $user_info = Db::name('member')->where(array('id' => $uid))->field('gm_name,mobile,money')->find();
        $yc_money  = $user_info['money'];
        $mobile    = $user_info['gm_name'];
        $my_money  = $money;
        Db::startTrans();
        try {
            $new_money = $yc_money + $money;
            $result0 = Db::name('member')
                ->where(array('id' => $uid))
                ->update(array('money' => $new_money));
            $data['uid']     = $uid;
            $data['type']    = 1;
            $data['money']   = $money;
            $data['balance'] = $new_money;
            $data['exp']     = 7;
            $data['cate']    = 15;
            $data['g_money'] = $my_money - $money;
            $data['explain'] =  'SS下分';
            $data['create_at'] = time();
            $result1 = Db::name('detail')->insert($data);
            $result2 = $API->zzmoney('SS', $mobile, 'OUT', $money);
        } catch (\Exception $e) {
            Db::rollback();
            json_return(500, '转出失败');
        }
        if ($result0 && $result1 && $result2) {
            Db::commit();
            json_return(200, '转出成功');
        }
    }



}