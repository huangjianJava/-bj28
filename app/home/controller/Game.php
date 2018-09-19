<?php
/**
 * Created by PhpStorm.
 * User: tangmusen
 * Date: 2017/10/30
 * Time: 11:08
 */
namespace app\home\controller;

use app\admin\model\Members;
use app\api\controller\HX;
use think\Cache;
use think\Config;
use think\Controller;
use think\Db;
use think\Request;
use GatewayClient\Gateway;

class Game extends Base
{


    /**
     * 游戏规则
     */
    public function rule(){
       $data = Db::name('play_rule')->where('cate',5)->find();
        return view('',[
            'data'=>$data
        ]);
    }

    /**
     * 二维数组排序
     */
    public function my_sort($arrays,$sort_key,$sort_order=SORT_ASC,$sort_type=SORT_NUMERIC ){
        if(is_array($arrays)){
            foreach ($arrays as $array){
                if(is_array($array)){
                    $key_arrays[] = $array[$sort_key];
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
        array_multisort($key_arrays,$sort_order,$sort_type,$arrays);
        return $arrays;
    }

    /**
     * 重庆时时彩
     */
    public function cqssc(){

        $uid         = session('uid');
        $start = strtotime(date('Y-m-d').' 02:00:00');
        $end   = strtotime(date('Y-m-d').' 10:00:00');
        $is_on = 1;
        if(time()>$start && time()<$end){
            $is_on = 0;
        }
        $member_info = Db::name('member')->field('id,nickname,mobile,money,head,is_m')->where(array('id' => $uid))->find();
        $number      = getNumberCache('ssc');
        $stage      = key($number); //获取最新一期的key（即期数）
        $numbers    = $number[$stage]; //获取最新开奖号
        $data_time  = $numbers['dateline']; //获取最新时间

        $number_arr = explode(',',$numbers['number']);
        //计算下次期数 和 开间时间
        $dx = sscDxWei($number_arr[4]); //判断大小
        $ds = sscDs($number_arr[4]);//判断单双
        $lf = sscLh($number_arr);
        $detail = $dx.",".$ds.",".$lf;
        $ssc = setSscStageTimeNew($stage,$data_time);
        $stage_data = [
            'stage'=>$stage,
            'stage_next'  =>$ssc['stage_next'],
            'number'      =>$number_arr,
            'number_sum'  =>array_sum($number_arr),
            'dateline'    =>date('Y-m-d H:i:s',$ssc['dateline']),
            'lottery_time'=>date('Y-m-d H:i:s',$ssc['dateline']),
            'detail'      =>$detail,
        ];
        $open = $stage_data['dateline'];
        $number_history = Db::name('at_ssc')
            ->limit(30)
            ->order('id desc')
            ->select();
        foreach ($number_history  as $k=>$v){
            $number_arr = explode(',',$v['number']);
            $dx = sscDxWei($number_arr[4]); //判断大小
            $ds = sscDs($number_arr[4]);//判断单双
            $lf = sscLh($number_arr);
            $number_history[$k]['detail']  = $dx.",".$ds.",".$lf;;
            $number_history[$k]['number']  = $number_arr;
            $number_history[$k]['dateline']  = date('m-d H:i:s',strtotime($v['dateline']));
        }

        $wx_url        = Db::name('setting')->where('id',1)->value('wx');
        $wx_url        = Config::get('img_url').$wx_url;
        $zhang_info    = Db::name('setting')->where('id',1)->find();
        $max_online_number = $zhang_info['x_numbers']+40;
        $min_online_number = $zhang_info['x_numbers']-40;
        $online_number = rand($min_online_number,$max_online_number);
        $notice        = Db::name('notice')->select()[0];
        $call_info     = Db::name('call')->select();

        //消息1
        if($call_info[0]['is_show']==1){
           $notice0 = $call_info[0];
        }else{
            $notice0 = $call_info[0];
            $notice0['call_time'] = 0;
        }
        //消息2
        if($call_info[1]['is_show']==1){
            $notice1 = $call_info[1];
        }else{
            $notice1 = $call_info[1];
            $notice1['call_time'] = 0;
        }
        //消息3
        if($call_info[2]['is_show']==1){
            $notice2 = $call_info[2];
        }else{
            $notice2 = $call_info[2];
            $notice2['call_time'] = 0;
        }
        //消息4
        if($call_info[3]['is_show']==1){
            $notice3 = $call_info[3];
        }else{
            $notice3 = $call_info[3];
            $notice3['call_time'] = 0;
        }
        //消息5
        if($call_info[4]['is_show']==1){
            $notice4 = $call_info[4];
        }else{
            $notice4 = $call_info[4];
            $notice4['call_time'] = 0;
        }
        
        $cha           = $ssc['dateline']-15-time();
        $chat_room = Cache::get('chat_room');
        $chat_room = array_slice($chat_room, -50);
        $chat_room = $this->my_sort($chat_room,'sort',3 );

        $cha_stage = (int)substr($ssc['stage_next'],-3,3);
       if($cha_stage>=24 && $cha_stage<=96){
           $ten = 1;
       }else{
           $ten = 0;
       }
        return view('',[
            'member_info'=>$member_info,
            'stage_data'=>$stage_data,
            'open'      =>$open,
            'number_history'=>$number_history,
            'next_time'     =>$ssc['dateline'],
            'date_time'     =>date('H:i:s'),
            'wx_url'        =>$wx_url,
            'online_number' =>$online_number,
            'uid'   =>$uid,
            'notice'=>$notice,
            'notice0'=>$notice0,
            'notice1'=>$notice1,
            'notice2'=>$notice2,
            'notice3'=>$notice3,
            'notice4'=>$notice4,
            'cha'=>$cha,
            'chat_room'=>$chat_room,
            'zhang_info'=>$zhang_info,
            'is_on'=>$is_on,
            'ten'=>$ten
        ]);

    }

    /**
     * 投注
     */
    public function betting(Request $request){

        $number51     = getNumberCache('ssc');
        $stage51      = key($number51); //获取最新一期的key（即期数）
        $numbers51    = $number51[$stage51]; //获取最新开奖号
        $data_time51  = $numbers51['dateline']; //获取最新时间
        $ssc51        = setSscStageTimeNew($stage51,$data_time51);
        $cha51        = $ssc51['dateline']-15-time();
        $setting_info    = Db::name('setting')->where('id',1)->find();
        $feng_time = $setting_info['feng_time'];
        if($cha51<$feng_time) {
            json_return(204,'本期已封盘'); 
        }
        $uid = session('uid');
        $cate   = $request->post('cate');
        $stage  = $request->post('stage');
        $hall   = $request->post('hall');
        $list   = $request->post('list');
        $content= $request->post('content');
        $ip     = get_client_ip();
        if(!$uid || !$stage  || !$hall ||!$list ||!$cate ){
            json_return(204,'缺少参数');
        }
        //获取会员信息
        $user = Members::get($uid);
        if($user['m_status']==1){
            json_return(201,'您的账号已被冻结!');
        }
        switch ($cate){
            case 5:
                $table_name = "at_ssc";
                break;
        }
        $is_stage = Db::name($table_name)->order('id desc')->find()['stage'];
        if($stage<=$is_stage){
            json_return(201,'已经封盘');
        }
        $list_d = json_decode($list, true);
        try {
            $list_d = json_decode($list, true);
        }catch (\Exception $e){
            json_return(201,'list格式不对');
        }
        if(!is_array($list_d)){
            json_return(201,'list格式不对');
        }
        foreach ($list_d as $k=>$v){
            $map['cate'] = $cate;
            $map['type'] = $v['type'];
            $bets = Db::name('bet')
                ->field('from,to,max')
                ->where($map)
                ->find();
            $map_s['stage']  = $stage;
            $map_s['number'] = $v['number'];
            $map_s['uid']    = $uid;
            $map_s['type']   = $v['type'];
            $now_money = Db::name('single')->where($map_s)->sum('money');
            $now_money = $now_money+$v['money'];
            if($now_money>$bets['max'] ){
                json_return(201,'当期投注金额超出限制');
            }
            if($v['money']<$bets['from']){
                json_return(201,'单注最低投注金额为'.$bets['from']);
            }
            if($v['money']>$bets['to']){
                json_return(201,'单注最高投注金额为'.$bets['to']);
            }
        }
        $time = time();
        $change_money = 0;
        foreach ($list_d as $k=>$v){
            $money[] = $v['money'];
            $change_money = $change_money+$v['money'];
            $data[] =[
                'uid'   =>$uid,
                'stage' =>$stage,
                'cate'  =>$cate,
                'type'  =>$v['type'],
                'hall'  =>$hall,
                'number'=>$v['number'],
                'money' =>$v['money'],
                'balance' =>$user->money-($change_money),
                'ip'      =>$ip,
                'create_at'=>$time,
                'content'=>$content,
                'login_way'=>3
            ];
        }
        $total_money = array_sum($money);
        if($total_money>$user->money){
            json_return(207,'余额不足');
        }
        $moneys =  $user->money-($total_money);
        if($cate==5) {
            $chat_room = Cache::get('chat_room');
            if(!$chat_room) {
                    $room['head']     = $user['head'];
                    $room['content']  = $content;
                  //  $room['nickname'] = mb_substr($user['nickname'], 0, 3, 'utf-8').'***';
                    $room['nickname'] = $user['nickname'];
                    $room['uid']      = $uid;
                    $room['time']     =  date('H:i:s');
                    $room['msg_type'] = 1;
                    $room['room']     = 5;
                    $room['stage']    = $stage;
                    $room['balance']  = $moneys;
                    $chat_room[]      = $room;
                    Cache::set('chat_room', $chat_room);
            }else{
                    $chat_room = array_slice($chat_room, -50);
                    $room['head']     = $user['head'];
                    $room['content']  = $content;
                    $room['nickname'] = $user['nickname'];
                    $room['uid']      = $uid;
                    $room['time']     =  date('H:i:s');
                    $room['msg_type'] = 1;
                    $room['room']     = 5;
                    $room['stage']    = $stage;
                    $room['sort']     = time();
                    $room['balance']  = $moneys;
                    $chat_room[]      = $room;
                    Cache::set('chat_room', $chat_room);
            }
        }
        Db::startTrans();
        try{
            Db('single')->insertAll($data);
            $moneys =  $user->money-($total_money);
            Db('member')->where('id',$uid)->update(['money'=>$moneys]);
            addDetail($uid,2,$total_money,$moneys,5,$cate,'期数'.$stage.',投注'.$content,$hall);
            $data['money'] = $moneys;
            Db::commit();
            json_return(200,'投注成功',$data);
        } catch (\Exception $e) {
            Db::rollback();
            json_return(500,'投注失败');
        }
    }

    /**
     * 获取机器人信息
     */
    public function get_robots(){
        $robot_info = Cache::get('robot_info');
        if(!$robot_info){
            $robot_info = Db::name('robot')->select();
        }
        $rules_info =  Cache::get('rules_info');
        if(!$rules_info ){
            $rules_info = Db::name('robot_way')->select();
        }
        $robot_num    =  count($robot_info);
        $rules_num   = count($rules_info );

        $s_robot   = rand(0,$robot_num-1);
        $s_rules   = rand(0,$rules_num-1);
        $robot_info_one = $robot_info[$s_robot];
        $rule_info_one  = $rules_info[$s_rules];
        $back = [
            'nickname'=>$robot_info_one['nickname'],
            'head'    => Config::get('img_url').$robot_info_one['logo'],
            'content' => $rule_info_one['content'],
            'time'    => date('H:i:s'),
        ];

        json_return(200,'成功',$back);


    }

    /**
     * 获取余芬
     */
    public function get_record_yf(){

        $chat_room = Cache::get('chat_room');
        $is_check  = Cache::get('is_check_yf');
        if(!$is_check) {
            $back = [];
            foreach ($chat_room as $kk => $vv) {
                if ($vv['msg_type'] != 2 && $vv['msg_type'] != 3) {
                    if($vv['uid']!='999999999') {
                        $balance = Db::name('member')->where('id',$vv['uid'])->value('money');
                        $back[$vv['nickname']] = number_format($balance,2);
                    }else{
                        $back[$vv['nickname']] = $vv['balance'];
                    }
                }
                if($vv['msg_type']==3){
                    unset($chat_room[$kk]);
                }
            }
            $back_room = [];
            foreach ($back as $kkk => $vvv) {
                $arr = [];
                $arr['nickname'] = $kkk;
                $arr['balance']  = $vvv;
                $back_room[]     =   $arr;
            }
            $room['content']  = $back_room;
            $room['time']     = date('H:i:s');
            $room['msg_type'] = 3;
            $room['room']     = 5;
            $room['sort']     = time();
            $chat_room[] = $room;
            Cache::set('chat_room', $chat_room);
            Cache::set('is_check_yf',1);
            json_return(200, '成功', $back_room);
        }else{
            $back = [];
            foreach ($chat_room as $kk => $vv) {
                if ($vv['msg_type'] != 2 && $vv['msg_type'] != 3) {
                    if($vv['uid']!='999999999') {
                        $balance = Db::name('member')->where('id',$vv['uid'])->value('money');
                        $back[$vv['nickname']] = number_format($balance,2);
                    }else{
                        $back[$vv['nickname']] = $vv['balance'];
                    }
                }
                if($vv['msg_type']==3){
                    unset($chat_room[$kk]);
                }
            }
            $back_room = [];
            foreach ($back as $kkk => $vvv) {
                $arr = [];
                $arr['nickname'] = $kkk;
                $arr['balance']  = $vvv;
                $back_room[]     =   $arr;
            }
            json_return(200,'成功', $back_room);
        }

    }

    /**
     * 取消订单
     */
    public function cancel_order(Request $request){
        $id     = $request->post('id');
        $uid    = session('uid');
        $is_c   = Db::name('single')->where('id',$id)->value('is_c');
        $number     = getNumberCache('ssc');
        $stage      = key($number); //获取最新一期的key（即期数）
        $ssc        = setSscStageTimeNew($stage);
        if($ssc['dateline']-time()<30){
            json_return(201,'即将开奖，不能撤单！');
        }
        if($is_c){
            json_return(201,'该订单已经撤单！');
        }
        Db::startTrans();
        try {
            $money = Db::name('single')->where('id',$id)->value('money');
            $up1 = [
                'is_c'=>1,
            ];
            $result1      = Db::name('single')->where('id',$id)->update($up1);
            $up2["money"] = array("exp", "money+" . $money);
            $result2 = Db::name('member')->where('id',$uid)->update($up2);
            $balance = Db::name('member')->where('id',$uid)->value('money');
            addDetail($uid,1,$money,$balance,8,5,'订单'.$id.'撤单',1);
            Db::commit();
            $back = [
                'balance' => $balance,
            ];
            json_return(200,'撤单成功',$back);
        }catch (\Exception $e){
            Db::rollback();
            json_return(201,'撤单失败');
        }
    }

    /**
     * 获取最新你充值信息
     */
    public function get_balance(){
        $uid = session('uid');
        $back = [];
        $back['is_recharge']=0;
        $back['is_withdraw']=0;
        $back['is_zj']      =0;
        //中奖消息
        $number     = getNumberCache('ssc');
        $stage      = key($number); //获取最新一期的key（即期数）
        $map0   = [
            'uid'  =>$uid,
            'stage'=>$stage,
            'state'=>1,
            'is_read'=>0
        ];
        $data1 = Db::name('single')->field('id,money,z_money')->where($map0)->select();
        $z_money = [];
        $t_money = [];
        if($data1){
            $back['is_zj']=1;
            foreach($data1 as $k=>$v) {
                Db::name('single')->where('id',$v['id'])->update(['is_read'=>1]);
                $z_money[] = $v['z_money'];
                $t_money[] = $v['money'];
            }
            array_sum($z_money);
            $back['zj_money']=array_sum($z_money);
            $back['tz_money'] =array_sum($t_money);
        }else{
            $back['zj_money']=0;
            $back['t_money'] =0;
        }

        $user_info = Db::name('member')->field('money,nickname')->where('id',$uid)->find();
        $back['balance'] = $user_info['money'];
        $back['nickname']= $user_info['nickname'];
        Cache::set('is_check',0);
        json_return(200,'成功',$back);

    }

    /**
     * 获取本期投注人数
     */
    public function get_record(){
        $number      = getNumberCache('ssc');
        $stage      = key($number); //获取最新一期的key（即期数）
        $chat_room = Cache::get('chat_room');
        $is_check  = Cache::get('is_check');
        if(!$is_check) {
            $chat_room = array_slice($chat_room, -50);
            foreach ($chat_room as $kk=>$vv){
                if($vv['msg_type']==2 || $vv['msg_type']==3){
                    unset($chat_room[$kk]);
                }
                if($vv['msg_type']==1){
                    if($vv['stage']!=$stage+1) {
                        unset($chat_room[$kk]);
                    }
                }
            }
            $room['content']  = $chat_room;
            $room['time']     = date('H:i:s');
            $room['msg_type'] = 2;
            $room['room']     = 5;
            $room['sort']     = time();
            $chat_room[] = $room;
            Cache::set('chat_room', $chat_room);
            Cache::set('is_check',1);
        }
        $chat_room = $this->my_sort($chat_room,'sort',3 );
        foreach ($chat_room as $kk=>$vv){
            if($vv['msg_type']==2 || $vv['msg_type']==3){
                unset($chat_room[$kk]);
            }
        }
        $chat_room = array_values($chat_room);
        Cache::set('is_check_yf',0);
        if($chat_room){
            json_return(200,'成功',$chat_room);
        }else{
            json_return(201,'暂无数据');
        }


    }

    /**
     * 获取上下分的情况
     */
    public function get_ups()
    {
        $map = [
            'd.is_read' => 0,
            'd.exp' => array('in', [1, 6])
        ];
        $data = Db::name('detail')
            ->alias('d')
            ->field('m.nickname,d.exp,d.money,d.balance,d.id')
            ->join('dl_member m', 'm.id=d.uid')
            ->where($map)
            ->select();
        if ($data) {
            foreach ($data as $kk => $vv) {
                Db::name('detail')->where('id', $vv['id'])->update(['is_read' => 1]);
            }
            $daa['content'] = json_encode($data);
            $daa['msg_type']=4;
            $daa['room']    =5;
            $daa['uid']     =1000000;
            Gateway::$registerAddress = '127.0.0.1:1236';
            $content = json_encode($daa);
            $send = [
                'content' => $content,
                'type' => 'say',
                'to_client_id' => 'all',
                'to_client_name' => '所有人',
            ];
            $send = json_encode($send);
            Gateway::sendToAll($send);

        }
    }

    /**
     * 获取喊话
     */
    public function get_call(){
        $data =  Db::name('call')->where('is_show',1)->order('id desc')->find();
        json_return(200,'成功',$data);
    }



    

}