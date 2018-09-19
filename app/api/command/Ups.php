<?php
namespace app\api\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class Ups extends Command
{
    protected function configure()
    {
        $this->setName('ups')->setDescription('更新每一天的期数');
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
       $this->up_eight();
       $this->up_car();
       $this->up_canada();
    }

    public function up_eight(){

        $stage = Db::name('cate_time')
            ->where('cate',1)
            ->order('id desc')
            ->value('stage');
        $cate_time = Db::name('cate_time')
            ->field('id,stage')
            ->where('cate',1)
            ->select();
        $i = 1;
        foreach($cate_time as $k=>$v){
            $data[$k]['stage'] = $stage+$i;
            $data[$k]['id'] = $v['id'];
            $i++;
        }
        $CateTime = new CateTime();
        $CateTime->saveAll($data);

    }
    public function up_canada(){

        $end   = date('Y-m-d');
        $stage = Db::name('at_canada')
            ->where('dateline','<',$end)
            ->order('id desc')
            ->value('stage');
        $cate_time = Db::name('cate_time')
            ->field('id,stage')
            ->where('cate',2)
            ->select();
        $i = 1;
        foreach($cate_time as $k=>$v){
            $data[$k]['stage'] = $stage+$i;
            $data[$k]['id'] = $v['id'];
            $i++;
        }
        $CateTime = new CateTime();
        $CateTime->saveAll($data);

    }
    public function up_car(){
        $stage = Db::name('cate_time')
            ->where('cate',3)
            ->order('id desc')
            ->value('stage');
        $cate_time = Db::name('cate_time')
            ->field('id,stage')
            ->where('cate',3)
            ->select();
        $i = 1;
        foreach($cate_time as $k=>$v){
            $data[$k]['stage'] = $stage+$i;
            $data[$k]['id'] = $v['id'];
            $i++;
        }
        $CateTime = new CateTime();
        $CateTime->saveAll($data);

    }
}