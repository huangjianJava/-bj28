<?php

namespace app\api\command;
use think\Cache;
use think\Config;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;


class Numbers extends Command
{
    protected function configure()
    {
        $this->setName('numbers')->setDescription('获取彩票开奖');
    }

    protected function execute(Input $input, Output $output)
    {
        /* 永不超时 */
        ini_set('max_execution_time', 0);
        $this->doCron();
    }

    public function doCron()
    {
        
        $api = Config::get('lottery_api');
        foreach ($api as $k => $v) {
            $url = $v;
            $res = httpGet($url);
            $number = json_decode($res, true);
            if (!isset($number['status'])) {
                foreach ($number as $kk => $vv) {
                    $data['stage'] = $kk;
                    $data['number'] = $vv['number'];
                    $data['dateline'] = $vv['dateline'];
                    $data['create_time'] = date('Y-m-d H:i:s');
                }
                switch ($k) {
                    case 'ssc':
                        $is_have = Db::name('at_ssc')->where(array('stage' => $data['stage']))->find();
                        if (!$is_have) {
                             Db::name('at_ssc')->insert($data);
                        }
                        break;
                }
                Cache::store('redis')->set('number_'.$k,$number);
            }
        }
    }

}