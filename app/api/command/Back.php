<?php
namespace app\api\command;

use think\Cache;
use think\Config;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use GatewayClient\Gateway;

class Back extends Command
{
    protected function configure()
    {
        $this->setName('back')->setDescription('投注回水');
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
        $date = strtotime('-1 days');
        $map  = [
            'create_at' => array('gt',$date),
            'state' => 1,
            'is_c' => 0
        ];
        $data = Db::name('single')
            ->field('sum(money) as total,uid,cate')
            ->where($map)
            ->group('uid')
            ->select();
        $setting = Db::name('setting')->select()[0];
        $percent = $setting['re_back'];
        if($data){
            foreach ($data as $k=>$v){
                $user_data = Db::name('member')->where(array('id' => $v['uid']))->find();
                $back_money = $v['total'] * $percent;
                $my_money   = $user_data['money'] + round($back_money, 3);
                $this->add_back_detail($v['uid'], $back_money, $my_money, $v['cate'], 3);
            }
        }
    }

    /**
     * 添加记录
     */
    public function add_back_detail($uid,$money,$my_money,$cate,$exp){
        $date = date("Ymd");
        if($exp==3) {
            $explain = $date . '投注回水';
        }
        $up_data["money"] = $my_money;
        Db::name('member')->where(array('id' => $uid))->update($up_data);
        $save['uid']     =  $uid;
        $save['type']    = 1;
        $save['money']   = $money;
        $save['balance'] = $my_money;
        $save['exp']     = $exp;
        $save['explain'] =  $explain;
        $save['cate']    =  $cate;
        $save['hall']    =  1;
        $save['create_at'] = time();
        Db::name('detail')->insert($save);
    }



}