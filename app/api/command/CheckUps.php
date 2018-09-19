<?php
namespace app\api\command;

use think\Cache;
use think\Config;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use GatewayClient\Gateway;

class CheckUps extends Command
{
    protected function configure()
    {
        $this->setName('checkups')->setDescription('检测上分下分情况');
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



}