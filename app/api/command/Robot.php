<?php
namespace app\api\command;

use think\Cache;
use think\Config;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use GatewayClient\Gateway;

class Robot extends Command
{
    protected function configure()
    {
        $this->setName('robot')->setDescription('机器人投注');
    }

    protected function execute(Input $input, Output $output)
    {
        /* 永不超时 */
        ini_set('max_execution_time', 0);
        $this->doCron();
    }

    /**
     * 回水、流水代理回水记录
     */

    public function doCron(){
        $start = strtotime(date('Y-m-d').' 09:10:00');
        $end   = strtotime(date('Y-m-d').' 02:00:00');
        if(time()>$start || time()<$end) {
            Gateway::$registerAddress = '127.0.0.1:1236';
            $content = $this->get_robots();
            if($content) {
                $data = [
                    'content' => $content,
                    'type' => 'say',
                    'to_client_id' => 'all',
                    'to_client_name' => '所有人',
                ];
                $data = json_encode($data);
                $number = getNumberCache('ssc');
                $stage = key($number); //获取最新一期的key（即期数）
                $numbers = $number[$stage]; //获取最新开奖号
                $data_time = $numbers['dateline']; //获取最新时间
                $ssc = setSscStageTimeNew($stage, $data_time);
                $cha = $ssc['dateline'] - 15 - time();
                $setting_info = Db::name('setting')->where('id', 1)->find();
                $feng_time = $setting_info['feng_time'];
                if ($cha > $feng_time) {
                    Gateway::sendToAll($data);
                }
            }
        }
    }

    /**
     * 获取机器人信息
     */
    public function get_robots(){

        $robot_info  = Db::name('robot')->where('status',1)->select();
        $rules_info  = Db::name('robot_way')->select();
        $robot_num   = count($robot_info);
        $rules_num   = count($rules_info );

        $s_robot   = rand(0,$robot_num-1);
        $s_rules   = rand(0,$rules_num-1);
        $robot_info_one = $robot_info[$s_robot];
        $rule_info_one  = $rules_info[$s_rules];
        //判断每个机器人每期值投一次
        $number     = getNumberCache('ssc');
        $stage      = key($number); //获取最新一期的key（即期数）
        $numbers    = $number[$stage]; //获取最新开奖号
        $data_time  = $numbers['dateline']; //获取最新时间
        $ssc        = setSscStageTimeNew($stage,$data_time);

        //判断每个机器人开盘前10秒不投
        $cha_stage = (int)substr($ssc['stage_next'],-3,3);
        if($cha_stage>=24 && $cha_stage<=96){
           if($ssc['dateline']-time()>580){
               return false;
           }
        }else{
            if($ssc['dateline']-time()>280){
                return false;
            }
        }

        $k_stage    = $stage+1;
        $has_bet = Cache::get('has_bet');
        if(!$has_bet){
            $has_bet = [];
            $bet_arr = [];
            $bet_arr[] = $robot_info_one['id'];
            $has_bet[$k_stage ] = $bet_arr;
            Cache::set('has_bet',$has_bet);
        }else{
            foreach ($has_bet as $k=>$v){
                if($k!=$k_stage){
                    $has_bet = [];
                    $bet_arr = [];
                    $bet_arr[]       = $robot_info_one['id'];
                    $has_bet[$k_stage ] = $bet_arr;
                    Cache::set('has_bet',$has_bet);
                }else{
                    if(in_array($robot_info_one['id'],$v)){
                       return false;
                    }
                    $bet_arr = $v;
                    $bet_arr[]       = $robot_info_one['id'];
                    $has_bet[$k_stage ] = $bet_arr;
                    Cache::set('has_bet',$has_bet);
                }
            }
        }


        $chat_room = Cache::get('chat_room');
        $cha        = $ssc['dateline']-15-time();
        $setting_info    = Db::name('setting')->where('id',1)->find();
        $feng_time       = $setting_info['feng_time'];
        if($cha>$feng_time) {
            if (!$chat_room) {
                $room['head']     = Config::get('img_url') . $robot_info_one['logo'];
                $room['content']  = $rule_info_one['content'];
                //$room['nickname'] = mb_substr($robot_info_one['nickname'], 0, 3, 'utf-8').'***';
                $room['nickname'] = $robot_info_one['nickname'];
                $room['uid']      = 999999999;
                $room['time']     = date('H:i:s');
                $room['msg_type'] = 1;
                $room['room'] = 5;
                $room['stage']    = $ssc['stage_next'];
                $room['sort']     = time();
                $room['balance'] = number_format(rand(100, 9999),2);
                $chat_room[]     = $room;
                Cache::set('chat_room', $chat_room);
            } else {
                $room['head'] = Config::get('img_url') . $robot_info_one['logo'];
                $room['content'] = $rule_info_one['content'];
                $room['nickname'] = $robot_info_one['nickname'];
                $room['uid'] = 999999999;
                $room['time'] = date('H:i:s');
                $room['msg_type'] = 1;
                $room['room'] = 5;
                $room['stage'] = $ssc['stage_next'];
                $room['sort'] = time();
                $room['balance'] = number_format(rand(100, 9999),2);
                $chat_room[] = $room;
                Cache::set('chat_room', $chat_room);
            }
        }
            $back = [
                'nickname' => $robot_info_one['nickname'],
                'head' => Config::get('img_url') . $robot_info_one['logo'],
                'content' => $rule_info_one['content'],
                'time' => date('H:i:s'),
                'msg_type' => 1,
                'room' => 5,
                'stage' => $ssc['stage_next'],
                'uid' => 999999999
            ];

            return json_encode($back);
        }


}