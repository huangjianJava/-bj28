<?php
namespace app\api\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class Ssc extends Command
{
    protected function configure()
    {
        $this->setName('ssc')->setDescription('重庆时时彩兑奖');
    }

    protected function execute(Input $input, Output $output)
    {
        /* 永不超时 */
        ini_set('max_execution_time', 0);
        $this->doCron();
    }

    /**
     *开奖
     */
    public function doCron()
    {
        $fileName = __DIR__ . '/ssc_lock.txt';
        $fp = fopen($fileName, "r");
        if (flock($fp, LOCK_EX | LOCK_NB)){
            $number = getNumberCache('ssc'); //获取最新开奖号
            if ($number) {
            $stage_num = key($number);//获取最新一期号
            $value = $number[$stage_num];
            $numbers = $value['number'];//获取最新开奖号
            $number_arr = explode(',', $numbers);
            $w['stage'] = $stage_num;
            $w['cate']  = 5;
            $w['state'] = 0;
            $w['code']  = 0;
            $w['is_c']  = 0;
            $list = Db::name('single')->where($w)->limit(1000)->select();
            $explain = '';
            if ($list) {
                $time = time();
                foreach ($list as $v) {
                    $pl = 0;
                    if ($v['type'] == 1) {
                        $he = array_sum($number_arr);
                        $dx = zSscDx($he);
                        $ds = zSscDs($he);
                        $longfu = sscLh($number_arr);
                        if ($v['number'] == $dx || $v['number'] == $ds || $v['number'] == $longfu) {
                            $pl = 1;
                            $explain = $stage_num . '期总大小单双龙虎和中' . $v['number'];
                        }
                        if ($v['number'] == '龙' || $v['number'] == '虎') {
                            if ($longfu == '和') {
                                $pl = 2;
                                $explain = $stage_num . '期龙虎出和返本金' . $v['number'];
                            }

                        }
                    } elseif ($v['type'] >= 5 && $v['type'] <= 14) { //特码
                        $target = "";
                        if ($v['type'] == 5 || $v['type'] == 6) {
                            $target = $number_arr[0];
                        } elseif ($v['type'] == 7 || $v['type'] == 8) {

                            $target = $number_arr[1];
                        } elseif ($v['type'] == 9 || $v['type'] == 10) {

                            $target = $number_arr[2];
                        } elseif ($v['type'] == 11 || $v['type'] == 12) {

                            $target = $number_arr[3];
                        } elseif ($v['type'] == 13 || $v['type'] == 14) {
                            $target = $number_arr[4];
                        }
                        $dx = sscDxWei($target); //判断大小
                        $ds = sscDs($target);   //判断单双
                        if ($v['number'] == $target || $v['number'] == $dx || $v['number'] == $ds || $v['number'] == $dx .$ds) {
                            $pl = 1;
                            $explain = $stage_num . '期,中' . $v['number'] . ',订单' . $v['id'];
                        }
                    } elseif (in_array($v['type'], array(15, 16, 17))) { //前三中三后三
                        $first_three = "";
                        if ($v['type'] == 15) {
                            $first_three = take_three($number_arr, 0);
                        }
                        if ($v['type'] == 16) {
                            $first_three = take_three($number_arr, 1);
                        }
                        if ($v['type'] == 17) {
                            $first_three = take_three($number_arr, 2);
                        }
                        $r_first_three = judge_three($first_three);
                        if ($v['number'] == $r_first_three) {
                            $pl = 1;
                            $explain = $stage_num . '期前中后中' . $v['number'];
                        }
                    }elseif ($v['type']==18) { //龙虎和
                        if($number_arr[0]>$number_arr[4]){
                            $target = '龙';
                        }elseif ($number_arr[0]<$number_arr[4]){
                            $target = '虎';
                        }else{
                            $target = '和';
                        }
                        if ($v['number'] == $target) {
                            $pl = 1;
                            $explain = $stage_num . '期龙虎和中' . $v['number'];
                        }
                    }
                    $open_number = $numbers;
                    if ($pl) {
                        if ($pl == 1) {
                            $odd = getOdds($v['cate'], $v['hall'], $v['type'], $v['number']);
                            if (!$odd) {
                                $content = $v['cate'] . '---' . $v['type'] . '---' . $v['hall'] . '---' . $v['number'] . '没有赔率';
                                file_put_contents('my_log.txt', $content . PHP_EOL, 8);
                            }
                        }
                        if ($pl == 2) {
                            $explain = '重庆时时彩' . $explain;
                            $odd = 1;
                        }
                        Db::startTrans();
                        try {
                            $z_money = $v['money'] * $odd;
                            $ups = [
                                'state' => 1,
                                'code' => 1,
                                'update_at' => $time,
                                'open_number' => $open_number,
                                'z_money' => $z_money
                            ];
                            Db::name('single')->where('id', $v['id'])->update($ups);
                            Db::name('member')->where('id', $v['uid'])->setInc('money', $z_money);
                            $balance = Db::name('member')->where('id', $v['uid'])->value('money');
                            addDetail($v['uid'], 1, $z_money, $balance, 2, $v['cate'], $explain, $v['hall']);
                            Db::commit();
                        } catch (\Exception $e) {
                            file_put_contents('my_error_log.txt', $e . PHP_EOL, 8);
                            Db::rollback();
                        }
                    } else {
                        Db::name('single')->where('id', $v['id'])->update(['state' => 1, 'update_at' => $time, 'open_number' => $open_number]);
                    }
                }
            }
        }
            flock($fp,LOCK_UN);
        }
        fclose($fp);
    }
}