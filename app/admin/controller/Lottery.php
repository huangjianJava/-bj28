<?php
/**
 * Created by PhpStorm.
 * User: tangmusen
 * Date: 2017/9/4
 * Time: 9:36
 */

namespace app\admin\controller;

use app\admin\model\CateTime;
use app\admin\model\Members;
use app\admin\model\Odds;
use app\admin\model\Water;
use think\Cache;
use think\Config;
use think\Request;
use think\Db;

class Lottery extends Admin
{
    /**
     * 彩种管理
     */
    public function index(){
        $data = Db::name('cate')->select();
        return view('index',[
            'data'=>$data,
        ]);

    }

    /**
     * 玩法界面
     */
    public function eight_index(Request $request){
        $cate     = $request->param('cate');
        $w['cate'] = $cate;
        $data = Db::name('hall')
            ->where($w)
            ->select();
        foreach ($data as $k=>$v){
            $data[$k]['play_rules'] = Db::name('bet')
                ->where($w)
                ->where('open',1)
                ->select();
        }
        $cate_name = Db::name('cate')->where(array('id'=>$cate))->value('name');

        return view('eight_index',[
            'data'=>$data,
            'cate_name'=>$cate_name,
        ]);
    }

    /**
     * 赔率设置
     */
    public function eight_pei(Request $request){
        $type     = $request->param('type');
        $hall     = $request->param('hall');
        $cate     = $request->param('cate');
        $w['type'] = $type;
        $w['hall'] = $hall;
        $w['cate'] = $cate;
        $data = Db::name('odds')
            ->where($w)
            ->select();
        $data_d = [];
        $data_s = [];
        foreach ($data as $k=>$v){
            if($k%2!=0){
                $data_d[] = $data[$k];
            }else{
                $data_s[] = $data[$k];
            }
        }

        $cate_name = Db::name('cate')->where(array('id'=>$cate))->value('name');
        $type_name = Db::name('bet')->where(array('cate'=>$cate,'type'=>$type))->value('title');
        $hall_name = Db::name('hall')->where(array('cate'=>$cate,'hall'=>$hall))->value('name');
        return view('eight_pei',[
            'data_d'=>$data_d,
            'data_s'=>$data_s,
            'cate_name'=>$cate_name,
            'type_name'=>$type_name,
            'hall_name'=>$hall_name,
        ]);
    }

    /**
     * 编辑赔率
     */
    public function edit_pei(Request $request){
        $data = $request->post();
        $odds=[];
        if($data){
            foreach ($data as $k=>$v){
                    $odds[$k]['id']   = $k;
                    $odds[$k]['rate'] = $v;
                }
        }
     
        $odd = new Odds();
        $odd->saveAll($odds);
        $this->success_new('修改成功');

    }

    /**
     * 大厅界面
     */
    public function hall(Request $request){
        $cate     = $request->param('cate');
        if($cate) {
            $w['h.cate'] = $cate;
        }
        $data = Db::name('hall')
            ->alias('h')
            ->field('h.*,c.name as cate_name')
            ->join('dl_cate c','h.cate=c.id')
            ->where($w)
            ->select();
        return view('hall',[
            'data'=>$data,
        ]);


    }

    /**
     * 编辑彩种资料
     */
    public function show(Request $request){
            $cate     = $request->param('id');
            $data     = Db::name('cate')->where(array('id'=>$cate))->find();
            return view('edit',[
                'data'=>$data,
            ]);

    }

    /**
     * 编辑彩种资料
     */
    public function edit(Request $request){
        if($request->isPost()){
            $data = $request->post();
            $map=array(
                'id'=>$data['id']
            );
            $result=Db::name('cate')->where($map)->update($data);
            if ($result) {
                $this->success_new('修改成功');
            }else{
                $this->error_new('修改失败');
            }
        }else{
            $cate     = $request->param('id');
            $data     = Db::name('cate')->where(array('id'=>$cate))->find();
            return view('edit',[
                'data'=>$data,
            ]);
        }
    }

    /**
     * 游戏规则
     */
    public function play_rule(Request $request){
        if($request->isPost()){
            $data = $request->post();
            $map=array(
                'id'=>$data['id']
            );
            $result=Db::name('play_rule')->where($map)->update($data);
            if ($result) {
                $this->success_new('修改成功');
            }else{
                $this->error_new('修改失败');
            }
        }else{
            $cate     = $request->param('cate');
            $data     = Db::name('play_rule')->where(array('cate'=>$cate))->find();
            $cate_name = Db::name('cate')->where(array('id'=>$cate))->value('name');
            return view('play_rule',[
                'data'=>$data,
                'cate_name'=>$cate_name,

            ]);
        }
    }

    /**
     * 编辑大厅
     */
    public function hall_edit(Request $request){
        if($request->isPost()){
            $data = $request->post();
            $map=array(
                'id'=>$data['id']
            );
            $result=Db::name('hall')->where($map)->update($data);
            if ($result) {
                $this->success_new('修改成功');
            }else{
                $this->error_new('修改失败');
            }
        }else{
            $cate     = $request->param('id');
            $data     = Db::name('hall')->where(array('id'=>$cate))->find();
            return view('hall_edit',[
                'data'=>$data,
            ]);
        }
    }

    /**
     * 编辑大厅
     */
    public function hall_show(Request $request){
            $cate     = $request->param('id');
            $data     = Db::name('hall')->where(array('id'=>$cate))->find();
            return view('hall_edit',[
                'data'=>$data,
            ]);

    }

    /**
     * 编辑回水、流水
     */
    public function water_edit(Request $request){
        if($request->isPost()){
            $data = $request->post();
            foreach ($data as $k=>$v) {
                if ($str = strstr($k, '_')) {
                    $str = substr($str, 1);
                    $bet[$str]['id'] = $str;
                    if (strstr($k, 'from')) {
                        $bet[$str]['from'] = $v;
                    } elseif (strstr($k, 'to')) {
                        $bet[$str]['to'] = $v;
                    } elseif (strstr($k, 'back')) {
                        $bet[$str]['back_water'] = $v;
                    } elseif (strstr($k, 'flow')) {
                        $bet[$str]['flow_water'] = $v;
                    }elseif (strstr($k, 'dl')) {
                        $bet[$str]['dl_water'] = $v;
                    }
                }
            }

            $Water = new Water();
            $result=$Water->saveall($bet);
            if ($result) {
                $this->success_new('修改成功');
            }else{
                $this->error_new('修改失败');
            }
        }else{
            $cate     = $request->param('cate');
            $hall     = $request->param('hall');
            $w['cate'] = $cate;
            $w['hall'] = $hall;
            $data      = Db::name('water')->where($w)->select();
            $cate_name = Db::name('cate')->where(array('id'=>$cate))->value('name');
            $hall_name = Db::name('hall')->where(array('cate'=>$cate,'hall'=>$hall))->value('name');
            return view('water_edit',[
                'data'=>$data,
                'cate_name'=>$cate_name,
                'hall_name'=>$hall_name,
            ]);
        }
    }

    /**
     * 显示回水、流水
     */
    public function water_show(Request $request){
            $cate     = $request->param('cate');
            $hall     = $request->param('hall');
            $w['cate'] = $cate;
            $w['hall'] = $hall;
            $data      = Db::name('water')->where($w)->select();
            $cate_name = Db::name('cate')->where(array('id'=>$cate))->value('name');
            $hall_name = Db::name('hall')->where(array('cate'=>$cate,'hall'=>$hall))->value('name');
            return view('water_edit',[
                'data'=>$data,
                'cate_name'=>$cate_name,
                'hall_name'=>$hall_name,
            ]);
    }

    /**
     * 历史记录
     */
    public function history(Request $request){
        $cate      = $request->param('cate');
        $from      = $request->param('from');
        $to        = $request->param('to');
        $w = [];
        if($from && $to){
            $w['create_time'] = [['>=',$from],['<=',$to]];
        }
        switch ($cate){
            case 1:
                $table_name = "at_eight";
                break;
            case 2:
                $table_name = "at_canada";
                break;
            case 3:
                $table_name = "at_car";
                break;
            case 4:
                $table_name = "at_airship";
                break;
            case 5:
                $table_name = "at_ssc";
                break;
            case 6:
                $table_name = "at_tjssc";
                break;
            case 7:
                $table_name = "at_gd10";
                break;
            case 8:
                $table_name = "at_cq10";
                break;
            case 9:
                $table_name = "at_fast";
                break;
            case 10:
                $table_name = "at_gd11";
                break;
            case 11:
                $table_name = "at_hk";
                break;

        }
        $list = Db::name($table_name)->where($w)->order('id desc')->paginate(20);
        if($cate==1){
            $data = $list->all();
            foreach ($data as $k=>$v){
                $number_arr_s = explode(',',$v['number']);
                $number_arr   = getEightNum($number_arr_s);
                $v['number']  = implode(',',$number_arr).'('.$v['number'].')';
                $list[$k]     = $v;
            }
        }
        if($cate==2){
            $data = $list->all();
            foreach ($data as $k=>$v){
                $number_arr_s = explode(',',$v['number']);
                $number_arr = getCanadaNum($number_arr_s);
                $v['number']  = implode(',',$number_arr).'('.$v['number'].')';
                $list[$k]     = $v;
            }
        }
        $cate_name = Db::name('cate')->where(array('id'=>$cate))->value('name');
        return view('',[
            'list'=>$list,
            'page'=>$list->render(),
            'cate'=>$cate,
            'cate_name'=>$cate_name
        ]);
    }

    /**
     * 开奖时间
     */
    public function open_time(){
        $data = Db::name('cate')->where('status',1)->select();
        return view('open_time',[
            'data'=>$data,
        ]);
    }

    /**
     * 开奖时间
     */
    public function open_time_show(Request $request){
         $cate      = $request->param('id');
         if($cate!=11) {
             $w['t.cate'] = $cate;
             $data = Db::name('cate_time')
                 ->alias('t')
                 ->field('t.id,t.dateline,t.open,c.name')
                 ->join('dl_cate c', 't.cate=c.id')
                 ->where($w)
                 ->paginate(20);
         }
        if($cate==11) {
            $w['t.cate'] = $cate;
            $data = Db::name('cate_hk')
                ->alias('t')
                ->field('t.id,t.dateline,t.open,c.name')
                ->join('dl_cate c', 't.cate=c.id')
                ->where($w)
                ->paginate(20);
        }

        return view('open_time_show',[
            'data'=>$data,
            'page'=>$data->render(),
        ]);
    }

    /**
     * 开奖时间编辑
     */
    public function open_time_edit(Request $request){
        $data        = $request->post();
        $list = $data['num'];
        $pl=0;
        foreach ($list as $k=>$v){
            if($v['id']==1111){
                $pl=1;
                $result = Db::name('cate_hk')
                    ->where('id', $v['id'])
                    ->update(['open' => $v['open'], 'dateline' => $v['dateline']]);
            }else{
                continue;
            }
        }
        if(!$pl) {
            $user = new CateTime();
            $result = $user->saveAll($list);
        }
        if ($result) {
            json_return(200,'修改成功');
        }else{
            json_return(500,'修改失败');
        }

    }

    /**
     * 开奖时间编辑
     */
    public function open_time_edit1(Request $request){
        $id        = $request->param('id');
        if($id!=1111) {
            $open = $request->param('open');
            $dateline = $request->param('dateline');
            $result = Db::name('cate_time')
                ->where('id', $id)
                ->update(['open' => $open, 'dateline' => $dateline]);
        }else{
            $open     = $request->param('open');
            $dateline = $request->param('dateline');
            $result = Db::name('cate_hk')
                ->where('id', $id)
                ->update(['open' => $open, 'dateline' => $dateline]);
        }
        if ($result) {
            json_return(200,'修改成功');
        }else{
            json_return(500,'修改失败');
        }

    }

    /**
     * 删除开奖历史
     */
    public function del_history(Request $request){
        $id   =$request->param('id');
        $cate = $request->param('cate');
        switch ($cate){
            case 1:
                $table_name = "at_eight";
                break;
            case 2:
                $table_name = "at_canada";
                break;
            case 3:
                $table_name = "at_car";
                break;
            case 4:
                $table_name = "at_airship";
                break;
            case 5:
                $table_name = "at_ssc";
                break;
            case 6:
                $table_name = "at_tjssc";
                break;
            case 7:
                $table_name = "at_gd10";
                break;
            case 8:
                $table_name = "at_cq10";
                break;
            case 9:
                $table_name = "at_fast";
                break;
            case 10:
                $table_name = "at_gd11";
                break;
            case 11:
                $table_name = "at_hk";
                break;
        }
        $result = Db::name($table_name)->where(array('id'=>$id))->delete();
        if ($result) {
            $this->success_new('删除成功');
        }else{
            $this->error_new('删除失败');
        }
    }

    /**
     * 添加手动开奖界面
     */
    public function open_result(){
        $cates = Db::name('cate')
            ->field('id,name')
            ->where('id','<',13)
            ->select();
        return view('',[
            'cates'=>$cates,
        ]);
    }

    /**
     * 添加开奖结果
     */
    public function add_open(Request $request){
        $data = $request->post();
        $cate = $data['cate'];
        switch ($cate){
            case 1:
                $table_name = "at_eight";
                break;
            case 2:
                $table_name = "at_canada";
                break;
            case 3:
                $table_name = "at_car";
                break;
            case 4:
                $table_name = "at_airship";
                break;
            case 5:
                $table_name = "at_ssc";
                break;
            case 6:
                $table_name = "at_tjssc";
                break;
            case 7:
                $table_name = "at_gd10";
                break;
            case 8:
                $table_name = "at_cq10";
                break;
            case 9:
                $table_name = "at_fast";
                break;
            case 10:
                $table_name = "at_gd11";
                break;
            case 11:
                $table_name = "at_hk";
                break;

        }
        unset($data['cate']);
        $data['dateline'] = date('Y-m-d H:i:s');
        $result=Db::name($table_name)->insert($data);
        if ($result) {
            $this->success_new('添加成功');
        }else{
            $this->error_new('添加失败');
        }
    }

    /**
     * 手动开奖界面
     */
    public function open(){
        $cates = Db::name('cate')
            ->field('id,name')
            ->where('id','<',13)
            ->select();
        return view('',[
            'cates'=>$cates,
        ]);
    }
    
    /**
     * 执行开奖
     */
    public function open_edit(Request $request){
        $cate  = $request->post('cate');
        $stage = $request->post('stage');
        switch ($cate){
            case 1:
                $this->eight_doCron($stage);
                break;
            case 2:
                $this->canada_doCron($stage);
                break;
            case 3:
                $this->car_doCron($stage);
                break;
            case 4:
                $this->ship_doCron($stage);
                break;
            case 5:
                $this->ssc_doCron($stage);
                break;
            case 6:
                $this->tjssc_doCron($stage);
                break;
            case 7:
                $this->gd10_doCron($stage);
                break;
            case 8:
                $this->cq10_doCron($stage);
                break;
            case 9:
                $this->fast_doCron($stage);
                break;
            case 10:
                $this->gd11_doCron($stage);
                break;
            case 11:
                $this->hk_doCron($stage);
                break;
        }
        $this->success_new('开奖成功！',url('lottery/open'));

    }

    /**
     * 开奖
     */
    public function eight_doCron($stage){
        $info = Db::name('at_eight')->where(array('stage'=>$stage))->find();
        if(!$info){
            $this->success_new('开奖期号不存在',url('lottery/open'));
        }
        if($info) {
            $stage_num =  $info['stage'];
            $numbers   =  $info['number'];
            $number_arr_s = explode(',',$numbers);
            $number_arr = getEightNum($number_arr_s);
            $he = array_sum($number_arr);
            $w['stage'] = $stage_num;
            $w['cate']  = 1;
            $w['state'] = 0;
            $w['code']  = 0;
            $list = Db::name('single')->where($w)->select();
            $explain ='';
            if($list) {
                $time = time();
                foreach ($list as $v){
                    $pl =0;
                    //计算赔率
                    switch ($v['type']){
                        case 1://大小单双
                            $dx = eightDx($he); //判断大小
                            $ds = eightDs($he);//判断单双
                            if($he!=13 && $he!=14) {
                                if ($v['number'] == $dx || $v['number'] == $ds) {
                                    $pl = 1;
                                }
                                $explain = $stage_num . '期大小单双中' . $v['number'];
                                break;
                            }else{
                                if ($v['number'] == $dx || $v['number'] == $ds) {
                                    $pl = 2;
                                }
                                $explain = $stage_num . '期大小单双中' . $v['number'];
                                break;
                            }
                        case 2://猜极大极小
                            if($he >=22 && $he<=27 && $v['number']=='极大'){
                                $pl = 1;
                            }elseif ($he >=0 && $he<=5 && $v['number']=='极小'){
                                $pl = 1;
                            }
                            $explain = $stage_num.'期极大极小中'.$v['number'];
                            break;
                        case 3://大单小单大双小双
                            $dx = eightDx($he); //判断大小
                            $ds = eightDs($he);//判断单双
                            if($he!=13 && $he!=14) {
                                $dx = eightDx($he); //判断大小
                                $ds = eightDs($he);//判断单双
                                if ($v['number'] == $dx.$ds) {
                                    $pl = 1;
                                    $explain = $stage_num . '期猜组合中' . $v['number'];
                                }
                                break;
                            }else{
                                if ($v['number'] == $dx.$ds) {
                                    $pl = 3;
                                    $explain = $stage_num . '期猜组合中' . $v['number'];
                                }
                                break;
                            }
                        case 4://特码
                            if($he == $v['number']){
                                $pl = 1;
                                $explain = $stage_num.'期猜特码中'.$v['number'];
                            }
                            break;
                        case 5://豹子
                            if($number_arr[0]==$number_arr[1] && $number_arr[1]==$number_arr[2] && $number_arr[0]==$number_arr[2]){
                                $pl = 1;
                            }
                            $explain = $stage_num.'期猜豹子中'.$v['number'];
                            break;
                        case 6://对子
                            $arr_num = array_count_values($number_arr);
                            if(in_array(2,$arr_num)) {
                                $pl = 1;
                            }
                            $explain = $stage_num.'期猜对子中'.$v['number'];
                            break;
                        case 7://色波
                            $sb = eightBo($he);
                            if($v['number']==$sb) {
                                $pl = 1;
                                $explain = $stage_num.'期猜红黄绿中'.$v['number'];
                            }
                            break;
                    }
                    $open_number = implode(',',$number_arr);

                    if($pl){
                        $explain = 'PC蛋蛋'.$explain;
                        if($pl==2) {
                            $odd = getOdds_1314($v['cate'], $v['hall'], 1);
                        }elseif($pl==3){
                            $odd = getOdds_1314($v['cate'], $v['hall'], 2);
                        } else{
                            $odd = getOdds($v['cate'], $v['hall'], $v['type'], $v['number']);
                        }
                        if(!$odd){
                            $content = $v['cate'].'---'.$v['type'].'---'.$v['hall'].'---'.$v['number'].'没有赔率';
                            file_put_contents('my_log.txt',$content.PHP_EOL,8);
                        }
                        Db::startTrans();
                        try{
                            $z_money = $v['money'] * $odd;
                            Db::name('single')->where('id',$v['id'])->update(['state'=>1,'code'=>1,'update_at'=>$time,'open_number'=>$open_number,'z_money'=>$z_money]);
                            Db::name('member')->where('id', $v['uid'])->setInc('money', $v['money'] * $odd);
                            $balance = Db::name('member')->where('id', $v['uid'])->value('money');
                            addDetail($v['uid'], 1, $v['money'] * $odd, $balance, 2, $v['cate'], $explain,$v['hall']);
                            Db::commit();
                        }catch (\Exception $e) {
                            Db::rollback();
                        }
                    }else{
                        Db::name('single')->where('id',$v['id'])->update(['state'=>1,'update_at'=>$time,'open_number'=>$open_number]);
                    }
                }
            }
        }
    }

    /**
     * 开奖
     */
    public function canada_doCron($stage){
        $info = Db::name('at_canada')->where(array('stage'=>$stage))->find();
        if(!$info){
            $this->success_new('开奖期号不存在',url('lottery/open'));
        }
        if($info) {
            $stage_num =  $info['stage'];
            $numbers   =  $info['number'];
            $number_arr_s = explode(',',$numbers);
            $number_arr = getCanadaNum($number_arr_s);
            $he = array_sum($number_arr);
            $w['stage'] = $stage_num;
            $w['cate']  = 2;
            $w['state'] = 0;
            $w['code']  = 0;
            $list = Db::name('single')->where($w)->select();
            $explain ='';
            if($list) {
                $time = time();
                foreach ($list as $v){
                    $pl =0;
                    //计算赔率
                    switch ($v['type']){
                        case 1://大小单双
                            $dx = eightDx($he); //判断大小
                            $ds = eightDs($he);//判断单双
                            if($he!=13 && $he!=14) {
                                if ($v['number'] == $dx || $v['number'] == $ds) {
                                    $pl = 1;
                                }
                                $explain = $stage_num . '期大小单双中' . $v['number'];
                                break;
                            }else{
                                if ($v['number'] == $dx || $v['number'] == $ds) {
                                    $pl = 2;
                                }
                                $explain = $stage_num . '期大小单双中' . $v['number'];
                                break;
                            }
                        case 2://猜极大极小
                            if($he >=22 && $he<=27 && $v['number']=='极大'){
                                $pl = 1;
                            }elseif ($he >=0 && $he<=5 && $v['number']=='极小'){
                                $pl = 1;
                            }
                            $explain = $stage_num.'期极大极小中'.$v['number'];
                            break;
                        case 3://大单小单大双小双
                            $dx = eightDx($he); //判断大小
                            $ds = eightDs($he);//判断单双
                            if($he!=13 && $he!=14) {
                                $dx = eightDx($he); //判断大小
                                $ds = eightDs($he);//判断单双
                                if ($v['number'] == $dx.$ds) {
                                    $pl = 1;
                                    $explain = $stage_num . '期猜组合中' . $v['number'];
                                }
                                break;
                            }else{
                                if ($v['number'] == $dx.$ds) {
                                    $pl = 3;
                                    $explain = $stage_num . '期猜组合中' . $v['number'];
                                }
                                break;
                            }

                        case 4://特码
                            if($he == $v['number']){
                                $pl = 1;
                                $explain = $stage_num.'期猜特码中'.$v['number'];
                            }
                            break;
                        case 5://豹子
                            if($number_arr[0]==$number_arr[1] && $number_arr[1]==$number_arr[2] && $number_arr[0]==$number_arr[2]){
                                $pl = 1;
                            }
                            $explain = $stage_num.'期猜豹子中'.$v['number'];
                            break;
                        case 6://对子
                            $arr_num = array_count_values($number_arr);
                            if(in_array(2,$arr_num)) {
                                $pl = 1;
                            }
                            $explain = $stage_num.'期猜对子中'.$v['number'];
                            break;
                        case 7://色波
                            $sb = eightBo($he);
                            if($v['number']==$sb) {
                                $pl = 1;
                                $explain = $stage_num.'期猜红黄绿中'.$v['number'];
                            }
                            break;
                    }
                    $open_number = implode(',',$number_arr);

                    if($pl){
                        $explain = '加拿大28'.$explain;
                        if($pl==2) {
                            $odd = getOdds_1314($v['cate'], $v['hall'], 1);
                        }elseif($pl==3){
                            $odd = getOdds_1314($v['cate'], $v['hall'], 2);
                        } else{
                            $odd = getOdds($v['cate'], $v['hall'], $v['type'], $v['number']);
                        }
                        if(!$odd){
                            $content = $v['cate'].'---'.$v['type'].'---'.$v['hall'].'---'.$v['number'].'没有赔率';
                            file_put_contents('my_log.txt',$content.PHP_EOL,8);
                        }
                        Db::startTrans();
                        try{
                            $z_money = $v['money'] * $odd;
                            Db::name('single')->where('id',$v['id'])->update(['state'=>1,'code'=>1,'update_at'=>$time,'open_number'=>$open_number,'z_money'=>$z_money]);
                            Db::name('member')->where('id', $v['uid'])->setInc('money', $v['money'] * $odd);
                            $balance = Db::name('member')->where('id', $v['uid'])->value('money');
                            addDetail($v['uid'], 1, $v['money'] * $odd, $balance, 2, $v['cate'], $explain,$v['hall']);
                            Db::commit();
                        }catch (\Exception $e) {
                            Db::rollback();
                        }
                    }else{
                        Db::name('single')->where('id',$v['id'])->update(['state'=>1,'update_at'=>$time,'open_number'=>$open_number]);
                    }
                }
            }
        }
    }

    /**
     * 开奖
     */
    public function car_doCron($stage){
        $info = Db::name('at_car')->where(array('stage'=>$stage))->find();
        if(!$info){
            $this->success_new('开奖期号不存在',url('lottery/open'));
        }
        if($info) {
            $stage_num =  $info['stage'];
            $numbers   =  $info['number'];
            $number_arr = explode(',', $numbers);
            $w['stage'] = $stage_num;
            $w['cate'] = 3;
            $w['state'] = 0;
            $w['code'] = 0;
            $list = Db::name('single')->where($w)->select();
            $explain ='';
            if($list) {
                $time = time();
                foreach ($list as $v){
                    $pl =0;
                    //计算赔率
                    if($v['type']>=1 && $v['type']<=5){
                        $wei_number = $number_arr[$v['type']-1];
                        $bj_number  = $number_arr[10-$v['type']];
                        $dx = carDxWei($wei_number); //判断大小
                        $ds = carDs($wei_number);//判断单双
                        $longfu = longfu($wei_number,$bj_number);//判断龙虎
                        if($v['number']==$dx || $v['number']==$ds ||$v['number']==$longfu){
                            $pl = 1;
                            $explain = $stage_num.'期大小单双龙虎中'.$v['number'];
                        }
                    }elseif ($v['type']>=6 && $v['type']<=10){
                        $wei_number = $number_arr[$v['type']-1];
                        $dx = carDxWei($wei_number); //判断大小
                        $ds = carDs($wei_number);//判断单双
                        if($v['number']==$dx || $v['number']==$ds){
                            $pl = 1;
                            $explain = $stage_num.'期大小单双中'.$v['number'];
                        }

                    } elseif ($v['type']==11){
                        $wei_number = $number_arr[0]+$number_arr[1];
                        $dx = carDx($wei_number); //判断大小
                        $ds = carDs($wei_number);//判断单双
                        if($v['number']==$dx || $v['number']==$ds){
                            $pl = 1;
                            $explain = $stage_num.'期大小单双中'.$v['number'];
                        }
                    } elseif ($v['type']==12){
                        $wei_number = $number_arr[0]+$number_arr[1];
                        if($v['number']==$wei_number){
                            $pl = 1;
                            $explain = $stage_num.'期冠亚和值中'.$v['number'];
                        }
                    }elseif ($v['type']>12 && $v['type']<=22){
                        $wei_number = $number_arr[$v['type']-13];
                        if($v['number']==$wei_number){
                            $pl = 1;
                            $explain = $stage_num.'期冠特码中'.$v['number'];
                        }
                    }
                    $open_number = $numbers;
                    if($pl){
                        $explain = '北京赛车'.$explain;
                        $odd = getOdds($v['cate'],$v['hall'],$v['type'],$v['number']);
                        if(!$odd){
                            $content = $v['cate'].'---'.$v['type'].'---'.$v['hall'].'---'.$v['number'].'没有赔率';
                            file_put_contents('my_log.txt',$content.PHP_EOL,8);
                        }
                        Db::startTrans();
                        try{
                            $z_money = $v['money'] * $odd;
                            Db::name('single')->where('id',$v['id'])->update(['state'=>1,'code'=>1,'update_at'=>$time,'open_number'=>$open_number,'z_money'=>$z_money]);
                            Db::name('member')->where('id', $v['uid'])->setInc('money', $v['money'] * $odd);
                            $balance = Db::name('member')->where('id', $v['uid'])->value('money');
                            addDetail($v['uid'], 1, $v['money'] * $odd, $balance, 2, $v['cate'], $explain,$v['hall']);
                            Db::commit();
                        }catch (\Exception $e) {
                            Db::rollback();
                        }
                    }else{
                        Db::name('single')->where('id',$v['id'])->update(['state'=>1,'update_at'=>$time,'open_number'=>$open_number]);
                    }
                }
            }
        }
    }

    /**
     * 开奖
     */
    public function ship_doCron($stage){
        $info = Db::name('at_airship')->where(array('stage'=>$stage))->find();
        if(!$info){
            $this->success_new('开奖期号不存在',url('lottery/open'));
        }
        if($info) {
            $stage_num =  $info['stage'];
            $numbers   =  $info['number'];
            $number_arr = explode(',', $numbers);
            $w['stage'] = $stage_num;
            $w['cate']  = 4;
            $w['state'] = 0;
            $w['code']  = 0;
            $list = Db::name('single')->where($w)->select();
            $explain ='';
            if($list) {
                $time = time();
                foreach ($list as $v){
                    $pl =0;
                    //计算赔率
                    if($v['type']>=1 && $v['type']<=5){
                        $wei_number = $number_arr[$v['type']-1];
                        $bj_number  = $number_arr[10-$v['type']];
                        $dx = carDxWei($wei_number); //判断大小
                        $ds = carDs($wei_number);//判断单双
                        $longfu = longfu($wei_number,$bj_number);//判断龙虎
                        if($v['number']==$dx || $v['number']==$ds ||$v['number']==$longfu){
                            $pl = 1;
                            $explain = $stage_num.'期大小单双龙虎中'.$v['number'];
                        }
                    }elseif ($v['type']>=6 && $v['type']<=10){
                        $wei_number = $number_arr[$v['type']-1];
                        $dx = carDxWei($wei_number); //判断大小
                        $ds = carDs($wei_number);//判断单双
                        if($v['number']==$dx || $v['number']==$ds){
                            $pl = 1;
                            $explain = $stage_num.'期大小单双中'.$v['number'];
                        }

                    } elseif ($v['type']==11){
                        $wei_number = $number_arr[0]+$number_arr[1];
                        $dx = carDx($wei_number); //判断大小
                        $ds = carDs($wei_number);//判断单双
                        if($v['number']==$dx || $v['number']==$ds){
                            $pl = 1;
                            $explain = $stage_num.'期冠亚和值中'.$v['number'];
                        }
                    } elseif ($v['type']==12){
                        $wei_number = $number_arr[0]+$number_arr[1];
                        if($v['number']==$wei_number){
                            $pl = 1;
                            $explain = $stage_num.'期冠亚和值中'.$v['number'];
                        }
                    }elseif ($v['type']>12 && $v['type']<=22){
                        $wei_number = $number_arr[$v['type']-13];
                        if($v['number']==$wei_number){
                            $pl = 1;
                            $explain = $stage_num.'期冠特码中'.$v['number'];
                        }
                    }
                    $open_number = $numbers;
                    if($pl){
                        $explain = '幸运飞艇'.$explain;
                        $odd = getOdds($v['cate'],$v['hall'],$v['type'],$v['number']);
                        if(!$odd){
                            $content = $v['cate'].'---'.$v['type'].'---'.$v['hall'].'---'.$v['number'].'没有赔率';
                            file_put_contents('my_log.txt',$content.PHP_EOL,8);
                        }
                        Db::startTrans();
                        try{
                            $z_money = $v['money'] * $odd;
                            Db::name('single')->where('id',$v['id'])->update(['state'=>1,'code'=>1,'update_at'=>$time,'open_number'=>$open_number,'z_money'=>$z_money]);
                            Db::name('member')->where('id', $v['uid'])->setInc('money', $v['money'] * $odd);
                            $balance = Db::name('member')->where('id', $v['uid'])->value('money');
                            addDetail($v['uid'], 1, $v['money'] * $odd, $balance, 2, $v['cate'], $explain,$v['hall']);
                            Db::commit();
                        }catch (\Exception $e) {
                            Db::rollback();
                        }
                    }else{
                        Db::name('single')->where('id',$v['id'])->update(['state'=>1,'update_at'=>$time,'open_number'=>$open_number]);
                    }
                }
            }
        }
    }

    /**
     *开奖
     */
    public function ssc_doCron($stage){
        $info = Db::name('at_ssc')->where(array('stage'=>$stage))->find();
        if(!$info){
            $this->success_new('开奖期号不存在',url('lottery/open'));
        }
        if($info) {
            $stage_num =  $info['stage'];
            $numbers   =  $info['number'];
            $number_arr = explode(',', $numbers);
            $w['stage'] = $stage_num;
            $w['cate']  = 5;
            $w['state'] = 0;
            $w['code']  = 0;
            $list = Db::name('single')->where($w)->select();
            $explain ='';
            if($list) {
                $time = time();
                foreach ($list as $v){
                    $pl =0;
                    if($v['type']==1){
                        $he = array_sum($number_arr);
                        $dx = zSscDx($he);
                        $ds = zSscDs($he);
                        $longfu = sscLh($number_arr);
                        if($v['number']==$dx ||$v['number']==$ds ||$v['number']==$longfu){
                            $pl = 1;
                            $explain = $stage_num.'期龙虎和中'.$v['number'];
                        }
                    }elseif($v['type']==2) {
                        $result_niu = niuNiu($number_arr);

                        if ($v['number'] == $result_niu) {
                            $pl = 1;
                            $explain = $stage_num . '期牛牛中' . $v['number'];
                        }
                    }elseif($v['type']==3) {
                        $result_niu = niuNiu($number_arr);
                        if($result_niu!="无牛") {
                            $sm_dx = sm_dx($result_niu);
                            $sm_ds = sm_ds($result_niu);
                            $sm_zh = sm_zh($result_niu);
                            if ($v['number'] == $sm_dx || $v['number'] == $sm_ds || $v['number'] == $sm_zh) {
                                $pl = 1;
                                $explain = $stage_num . '期牛牛双面中' . $v['number'];
                            }
                        }
                    }elseif($v['type']==4) {
                        $result_niu = nn_sh($number_arr);

                        if ($v['number'] == $result_niu) {
                            $pl = 1;
                            $explain = $stage_num . '期牛牛梭哈中' . $v['number'];
                        }
                    }elseif($v['type']>=5 && $v['type']<=14) {
                        $target = "";
                        if($v['type']==5 || $v['type']==6){

                            $target = $number_arr[0];
                        }elseif($v['type']==7 ||$v['type']==8 ){

                            $target = $number_arr[1];
                        }elseif($v['type']==9 || $v['type']==10 ){

                            $target = $number_arr[2];
                        }elseif($v['type']==11 || $v['type']==12 ){

                            $target = $number_arr[3];
                        }elseif($v['type']==13 ||$v['type']==14 ){

                            $target = $number_arr[4];
                        }
                        $dx = sscDxWei($target); //判断大小
                        $ds = sscDs($target);//判断单双
                        if ($v['number'] == $target ||$v['number'] == $dx ||$v['number'] == $ds) {
                            $pl = 1;
                            $explain = $stage_num . '期数字盘中' . $v['number'];
                        }
                    }elseif(in_array($v['type'],array(15,16,17))){
                        $first_three = "";
                        if($v['type']==15){
                            $first_three = take_three($number_arr,0);
                        }
                        if($v['type']==16){
                            $first_three = take_three($number_arr,1);
                        }
                        if($v['type']==17){
                            $first_three = take_three($number_arr,2);
                        }
                        $r_first_three = judge_three($first_three);
                        if ($v['number'] == $r_first_three ) {
                            $pl = 1;
                            $explain = $stage_num . '期前中后三中' . $v['number'];
                        }
                    }
                    $open_number = $numbers;
                    if($pl){
                        $explain = '重庆时时彩'.$explain;
                        $odd = getOdds($v['cate'],$v['hall'],$v['type'],$v['number']);
                        if(!$odd){
                            $content = $v['cate'].'---'.$v['type'].'---'.$v['hall'].'---'.$v['number'].'没有赔率';
                            file_put_contents('my_log.txt',$content.PHP_EOL,8);
                        }
                        Db::startTrans();
                        try{
                            $z_money = $v['money'] * $odd;
                            Db::name('single')->where('id',$v['id'])->update(['state'=>1,'code'=>1,'update_at'=>$time,'open_number'=>$open_number,'z_money'=>$z_money]);
                            Db::name('member')->where('id', $v['uid'])->setInc('money', $v['money'] * $odd);
                            $balance = Db::name('member')->where('id', $v['uid'])->value('money');
                            addDetail($v['uid'], 1, $v['money'] * $odd, $balance, 2, $v['cate'], $explain,$v['hall']);
                            Db::commit();
                        }catch (\Exception $e) {
                            Db::rollback();
                        }
                    }else{
                        Db::name('single')->where('id',$v['id'])->update(['state'=>1,'update_at'=>$time,'open_number'=>$open_number]);
                    }
                }
            }
        }
    }

    /**
     *开奖
     */
    public function tjssc_doCron($stage){
        $info = Db::name('at_tjssc')->where(array('stage'=>$stage))->find();
        if(!$info){
            $this->success_new('开奖期号不存在',url('lottery/open'));
        }
        if($info) {
            $stage_num =  $info['stage'];
            $numbers   =  $info['number'];
            $number_arr = explode(',', $numbers);
            $w['stage'] = $stage_num;
            $w['cate']  = 6;
            $w['state'] = 0;
            $w['code']  = 0;
            $list = Db::name('single')->where($w)->select();
            $explain ='';
            if($list) {
                $time = time();
                foreach ($list as $v){
                    $pl =0;
                    if($v['type']==1){
                        $he = array_sum($number_arr);
                        $dx = zSscDx($he);
                        $ds = zSscDs($he);
                        $longfu = sscLh($number_arr);
                        if($v['number']==$dx ||$v['number']==$ds ||$v['number']==$longfu){
                            $pl = 1;
                            $explain = $stage_num.'期龙虎和中'.$v['number'];
                        }
                    }elseif($v['type']==2) {
                        $result_niu = niuNiu($number_arr);

                        if ($v['number'] == $result_niu) {
                            $pl = 1;
                            $explain = $stage_num . '期牛牛中' . $v['number'];
                        }
                    }elseif($v['type']==3) {
                        $result_niu = niuNiu($number_arr);
                        if($result_niu!="无牛") {
                            $sm_dx = sm_dx($result_niu);
                            $sm_ds = sm_ds($result_niu);
                            $sm_zh = sm_zh($result_niu);
                            if ($v['number'] == $sm_dx || $v['number'] == $sm_ds || $v['number'] == $sm_zh) {
                                $pl = 1;
                                $explain = $stage_num . '期牛牛双面中' . $v['number'];
                            }
                        }
                    }elseif($v['type']==4) {
                        $result_niu = nn_sh($number_arr);

                        if ($v['number'] == $result_niu) {
                            $pl = 1;
                            $explain = $stage_num . '期牛牛梭哈中' . $v['number'];
                        }
                    }elseif($v['type']>=5 && $v['type']<=14) {
                        $target = "";
                        if($v['type']==5 || $v['type']==6){

                            $target = $number_arr[0];
                        }elseif($v['type']==7 ||$v['type']==8 ){

                            $target = $number_arr[1];
                        }elseif($v['type']==9 || $v['type']==10 ){

                            $target = $number_arr[2];
                        }elseif($v['type']==11 || $v['type']==12 ){

                            $target = $number_arr[3];
                        }elseif($v['type']==13 ||$v['type']==14 ){

                            $target = $number_arr[4];
                        }
                        $dx = sscDxWei($target); //判断大小
                        $ds = sscDs($target);//判断单双
                        if ($v['number'] == $target ||$v['number'] == $dx ||$v['number'] == $ds) {
                            $pl = 1;
                            $explain = $stage_num . '期数字盘中' . $v['number'];
                        }
                    }elseif(in_array($v['type'],array(15,16,17))){
                        $first_three = "";
                        if($v['type']==15){
                            $first_three = take_three($number_arr,0);
                        }
                        if($v['type']==16){
                            $first_three = take_three($number_arr,1);
                        }
                        if($v['type']==17){
                            $first_three = take_three($number_arr,2);
                        }
                        $r_first_three = judge_three($first_three);
                        if ($v['number'] == $r_first_three ) {
                            $pl = 1;
                            $explain = $stage_num . '期前中后三中' . $v['number'];
                        }
                    }
                    $open_number = $numbers;
                    if($pl){
                        $explain = '天津时时彩'.$explain;
                        $odd = getOdds($v['cate'],$v['hall'],$v['type'],$v['number']);
                        if(!$odd){
                            $content = $v['cate'].'---'.$v['type'].'---'.$v['hall'].'---'.$v['number'].'没有赔率';
                            file_put_contents('my_log.txt',$content.PHP_EOL,8);
                        }
                        Db::startTrans();
                        try{
                            $z_money = $v['money'] * $odd;
                            Db::name('single')->where('id',$v['id'])->update(['state'=>1,'code'=>1,'update_at'=>$time,'open_number'=>$open_number,'z_money'=>$z_money]);
                            Db::name('member')->where('id', $v['uid'])->setInc('money', $v['money'] * $odd);
                            $balance = Db::name('member')->where('id', $v['uid'])->value('money');
                            addDetail($v['uid'], 1, $v['money'] * $odd, $balance, 2, $v['cate'], $explain,$v['hall']);
                            Db::commit();
                        }catch (\Exception $e) {
                            Db::rollback();
                        }
                    }else{
                        Db::name('single')->where('id',$v['id'])->update(['state'=>1,'update_at'=>$time,'open_number'=>$open_number]);
                    }
                }
            }
        }
    }

    /**
     *开奖
     */
    public function gd10_doCron($stage){
        $info = Db::name('at_gd10')->where(array('stage'=>$stage))->find();
        if(!$info){
            $this->success_new('开奖期号不存在',url('lottery/open'));
        }
        if($info) {
            $stage_num =  $info['stage'];
            $numbers   =  $info['number'];
            $number_arr = explode(',', $numbers);
            $w['stage'] = $stage_num;
            $w['cate']  = 7;
            $w['state'] = 0;
            $w['code']  = 0;
            $list = Db::name('single')->where($w)->select();
            $explain ='';
            if($list) {
                $time = time();
                foreach ($list as $v){
                    $pl =0;
                    if($v['type']>=1 && $v['type']<=8){
                        $xu = $v['type']-1;
                        $dx = gd10dx($number_arr[$xu]);
                        $ds = gd10ds($number_arr[$xu]);
                        $weidx = Zgd10weids($number_arr[$xu]);
                        $weids = Zgd10weidx($number_arr[$xu]);
                        if($v['type']>=1 && $v['type']<=4) {
                            $lf = gd10lh($number_arr,$v['type']);
                        }else{
                            $lf = "无";
                        }
                        if($v['number']==$dx ||$v['number']==$ds ||$v['number']==$weidx || $v['number']==$weids || $v['number']==$lf){
                            $pl = 1;
                            $explain = $stage_num.'期大小单双龙虎中'.$v['number'];
                        }
                    }elseif(in_array($v['type'],array(9,11,13,15,17,19,21,23))) {
                        $xz_array = ['9'=>0, '11'=>1, '13'=>2, '15'=>3, '17'=>4, '19'=>5, '21'=>6, '23'=>7];
                        $xu_hao = $xz_array[$v['type']];
                        $tm_number = $number_arr[$xu_hao];
                        if ($v['number'] == $tm_number) {
                            $pl = 1;
                            $explain = $stage_num . '期特码中' . $v['number'];
                        }
                    }elseif(in_array($v['type'],array(10,12,14,16,18,20,22,24))) {
                        $xz_array = ['10'=>0, '12'=>1, '14'=>2, '16'=>3, '18'=>4, '20'=>5, '22'=>6, '24'=>7];
                        $xu_hao = $xz_array[$v['type']];
                        $tm_number = $number_arr[$xu_hao];
                        $gd10fx  = gd10fx($tm_number);
                        $gd10zfb = gd10zfb($tm_number);
                        if ($v['number'] == $gd10fx || $v['number'] == $gd10zfb) {
                            $pl = 1;
                            $explain = $stage_num . '期东西南北中' . $v['number'];

                        }
                    }elseif($v['type']==25) {
                        $he = array_sum($number_arr);
                        $Zgd10dx    = Zgd10dx($he);
                        $Zgd10ds    = Zgd10ds($he);
                        $Zgd10weids = Zgd10weids($he);
                        $Zgd10weidx = Zgd10weidx($he);
                        $Zgd10lh    = Zgd10lh($he);
                        if ($v['number'] == $Zgd10dx || $v['number'] == $Zgd10ds || $v['number'] == $Zgd10weids ||$v['number']== $Zgd10weidx ||$v['number']== $Zgd10lh) {
                            $pl = 1;
                            $explain = $stage_num . '期总和龙虎中' . $v['number'];
                        }
                    }

                    $open_number = $numbers;
                    if($pl){
                        $explain = '广东快乐十分'.$explain;
                        $odd = getOdds($v['cate'],$v['hall'],$v['type'],$v['number']);
                        if(!$odd){
                            $content = $v['cate'].'---'.$v['type'].'---'.$v['hall'].'---'.$v['number'].'没有赔率';
                            file_put_contents('my_log.txt',$content.PHP_EOL,8);
                        }
                        Db::startTrans();
                        try{
                            $z_money = $v['money'] * $odd;
                            Db::name('single')->where('id',$v['id'])->update(['state'=>1,'code'=>1,'update_at'=>$time,'open_number'=>$open_number,'z_money'=>$z_money]);
                            Db::name('member')->where('id', $v['uid'])->setInc('money', $v['money'] * $odd);
                            $balance = Db::name('member')->where('id', $v['uid'])->value('money');
                            addDetail($v['uid'], 1, $v['money'] * $odd, $balance, 2, $v['cate'], $explain,$v['hall']);
                            Db::commit();
                        }catch (\Exception $e) {
                            Db::rollback();
                        }
                    }else{
                        Db::name('single')->where('id',$v['id'])->update(['state'=>1,'update_at'=>$time,'open_number'=>$open_number]);
                    }
                }
            }
        }
    }

    /**
     *开奖
     */
    public function cq10_doCron($stage){
        $info = Db::name('at_canada')->where(array('stage'=>$stage))->find();
        if(!$info){
            $this->success_new('开奖期号不存在',url('lottery/open'));
        }
        if($info) {
            $stage_num =  $info['stage'];
            $numbers   =  $info['number'];
            $number_arr = explode(',', $numbers);
            $w['stage'] = $stage_num;
            $w['cate']  = 8;
            $w['state'] = 0;
            $w['code']  = 0;
            $list = Db::name('single')->where($w)->select();
            $explain ='';
            if($list) {
                $time = time();
                foreach ($list as $v){
                    $pl =0;
                    if($v['type']>=1 && $v['type']<=8){
                        $xu = $v['type']-1;
                        $dx = gd10dx($number_arr[$xu]);
                        $ds = gd10ds($number_arr[$xu]);
                        $weidx = Zgd10weids($number_arr[$xu]);
                        $weids = Zgd10weidx($number_arr[$xu]);
                        if($v['type']>=1 && $v['type']<=4) {
                            $lf = gd10lh($number_arr,$v['type']);
                        }else{
                            $lf = "无";
                        }
                        if($v['number']==$dx ||$v['number']==$ds ||$v['number']==$weidx || $v['number']==$weids || $v['number']==$lf){
                            $pl = 1;
                            $explain = $stage_num.'期大小单双龙虎中'.$v['number'];
                        }
                    }elseif(in_array($v['type'],array(9,11,13,15,17,19,21,23))) {
                        $xz_array = ['9'=>0, '11'=>1, '13'=>2, '15'=>3, '17'=>4, '19'=>5, '21'=>6, '23'=>7];
                        $xu_hao = $xz_array[$v['type']];
                        $tm_number = $number_arr[$xu_hao];
                        if ($v['number'] == $tm_number) {
                            $pl = 1;
                            $explain = $stage_num . '期特码中' . $v['number'];
                        }
                    }elseif(in_array($v['type'],array(10,12,14,16,18,20,22,24))) {
                        $xz_array = ['10'=>0, '12'=>1, '14'=>2, '16'=>3, '18'=>4, '20'=>5, '22'=>6, '24'=>7];
                        $xu_hao = $xz_array[$v['type']];
                        $tm_number = $number_arr[$xu_hao];
                        $gd10fx  = gd10fx($tm_number);
                        $gd10zfb = gd10zfb($tm_number);
                        if ($v['number'] == $gd10fx || $v['number'] == $gd10zfb) {
                            $pl = 1;
                            $explain = $stage_num . '期东西南北中' . $v['number'];

                        }
                    }elseif($v['type']==25) {
                        $he = array_sum($number_arr);
                        $Zgd10dx    = Zgd10dx($he);
                        $Zgd10ds    = Zgd10ds($he);
                        $Zgd10weids = Zgd10weids($he);
                        $Zgd10weidx = Zgd10weidx($he);
                        $Zgd10lh    = Zgd10lh($he);
                        if ($v['number'] == $Zgd10dx || $v['number'] == $Zgd10ds || $v['number'] == $Zgd10weids ||$v['number']== $Zgd10weidx ||$v['number']== $Zgd10lh) {
                            $pl = 1;
                            $explain = $stage_num . '期总和龙虎中' . $v['number'];
                        }
                    }
                    $open_number = $numbers;
                    if($pl){
                        $explain = '重庆快乐十分'.$explain;
                        $odd = getOdds($v['cate'],$v['hall'],$v['type'],$v['number']);
                        if(!$odd){
                            $content = $v['cate'].'---'.$v['type'].'---'.$v['hall'].'---'.$v['number'].'没有赔率';
                            file_put_contents('my_log.txt',$content.PHP_EOL,8);
                        }
                        Db::startTrans();
                        try{
                            $z_money = $v['money'] * $odd;
                            Db::name('single')->where('id',$v['id'])->update(['state'=>1,'code'=>1,'update_at'=>$time,'open_number'=>$open_number,'z_money'=>$z_money]);
                            Db::name('member')->where('id', $v['uid'])->setInc('money', $v['money'] * $odd);
                            $balance = Db::name('member')->where('id', $v['uid'])->value('money');
                            addDetail($v['uid'], 1, $v['money'] * $odd, $balance, 2, $v['cate'], $explain,$v['hall']);
                            Db::commit();
                        }catch (\Exception $e) {
                            Db::rollback();
                        }
                    }else{
                        Db::name('single')->where('id',$v['id'])->update(['state'=>1,'update_at'=>$time,'open_number'=>$open_number]);
                    }
                }
            }
        }
    }

    /**
     *开奖
     */
    public function fast_doCron($stage){
        $info = Db::name('at_fast')->where(array('stage'=>$stage))->find();
        if(!$info){
            $this->success_new('开奖期号不存在',url('lottery/open'));
        }
        if($info) {
            $stage_num =  $info['stage'];
            $numbers   =  $info['number'];
            $number_arr = explode(',', $numbers);
            $w['stage'] = $stage_num;
            $w['cate'] = 9;
            $w['state'] = 0;
            $w['code'] = 0;
            $list = Db::name('single')->where($w)->select();
            $explain ='';
            if($list) {
                $time = time();
                foreach ($list as $v){
                    $pl =0;
                    //计算赔率
                    switch ($v['type']){
                        case 1://大小单双
                            $he = array_sum($number_arr);

                            $dx = FastDx($he);
                            if($v['number']==$dx) {
                                $pl=1;
                                $explain = $stage_num . '期大小单双中' . $v['number'];
                            }
                            break;
                        case 2://点数
                            $he = array_sum($number_arr);
                            if($v['number'] == $he){
                                $pl = 1;
                                $explain = $stage_num.'期点数选中'.$v['number'];
                            }
                            break;
                        case 3://围骰

                            $number_str = implode('', $number_arr);
                            if($v['number']==$number_str){
                                $pl = 1;
                                $explain = $stage_num.'期围骰中'.$v['number'];
                            }
                            break;
                        case 4://短牌
                            sort($number_arr);
                            $number_str = implode('', $number_arr);
                            if(stripos($number_str,$v['number']) != false){
                                $pl = 1;
                                $explain = $stage_num.'期短牌中'.$v['number'];
                            }
                            break;
                        case 5://长牌
                            $first_two  = $number_arr[0].$number_arr[1];
                            $second_two = $number_arr[1].$number_arr[2];
                            if($v['number']==$first_two || $v['number']==$second_two){
                                $pl = 1;
                                $explain = $stage_num.'期长牌中'.$v['number'];
                            }
                            break;
                    }
                    $open_number = $numbers;
                    if($pl){
                        $explain = '江苏快三'.$explain;
                        $odd = getOdds($v['cate'],$v['hall'],$v['type'],$v['number']);
                        if(!$odd){
                            $content = $v['cate'].'---'.$v['type'].'---'.$v['hall'].'---'.$v['number'].'没有赔率';
                            file_put_contents('my_log.txt',$content.PHP_EOL,8);
                        }
                        Db::startTrans();
                        try{
                            $z_money = $v['money'] * $odd;
                            Db::name('single')->where('id',$v['id'])->update(['state'=>1,'code'=>1,'update_at'=>$time,'open_number'=>$open_number,'z_money'=>$z_money]);
                            Db::name('member')->where('id', $v['uid'])->setInc('money', $v['money'] * $odd);
                            $balance = Db::name('member')->where('id', $v['uid'])->value('money');
                            addDetail($v['uid'], 1, $v['money'] * $odd, $balance, 2, $v['cate'], $explain,$v['hall']);
                            Db::commit();
                        }catch (\Exception $e) {
                            Db::rollback();
                        }
                    }else{
                        Db::name('single')->where('id',$v['id'])->update(['state'=>1,'update_at'=>$time,'open_number'=>$open_number]);
                    }
                }
            }
        }
    }

    /**
     *开奖
     */
    public function gd11_doCron($stage){
        $info = Db::name('at_gd11')->where(array('stage'=>$stage))->find();
        if(!$info){
            $this->success_new('开奖期号不存在',url('lottery/open'));
        }
        if($info) {
            $stage_num =  $info['stage'];
            $numbers   =  $info['number'];
            $number_arr = explode(',', $numbers);
            $w['stage'] = $stage_num;
            $w['cate']  = 10;
            $w['state'] = 0;
            $w['code']  = 0;
            $list = Db::name('single')->where($w)->select();
            $explain ='';
            if($list) {
                $time = time();
                foreach ($list as $v){
                    $pl =0;
                    if($v['type']>=1 && $v['type']<=5){
                        $xu = $v['type']-1;
                        $dx = gd11dx($number_arr[$xu]);
                        $ds = gd11ds($number_arr[$xu]);
                        if($v['number']==$dx ||$v['number']==$ds){
                            $pl = 1;
                            $explain = $stage_num.'期大小单双中'.$v['number'];
                        }
                    }elseif($v['type']==6) {
                        $he = array_sum($number_arr);
                        $dx = gd11zdx($he);
                        $ds = gd11ds($he);
                        $lf = gd11lf($number_arr);
                        if ($v['number']==$dx ||$v['number']==$ds || $v['number']==$lf) {
                            $pl = 1;
                            $explain = $stage_num . '期总大小龙虎中' . $v['number'];
                        }
                    }elseif($v['type']>=7 && $v['type']<=10) {

                        $c_number = $v['number'];
                        $c_array  = explode(',',$c_number);
                        if (is_in($number_arr,$c_array)) {
                            $pl = 1;
                            $explain = $stage_num . '期任选中' . $v['number'];

                        }
                    }elseif($v['type']>=11 && $v['type']<=13) {
                        $c_number = $v['number'];
                        $c_array = explode(',', $c_number);
                        if (is_in($c_array,$number_arr)) {
                            $pl = 1;
                            $explain = $stage_num . '期任选中' . $v['number'];

                        }
                    }elseif($v['type']==14) {
                        $first_two[] = $number_arr[0];
                        $first_two[] = $number_arr[1];
                        $c_number = $v['number'];
                        $c_array = explode(',', $c_number);
                        if (in_array($c_array[0],$first_two) && in_array($c_array[1],$first_two)) {
                            $pl = 1;
                            $explain = $stage_num . '期前二组中' . $v['number'];

                        }
                    }elseif($v['type']==15) {
                        $first_two[] = $number_arr[0];
                        $first_two[] = $number_arr[1];
                        $c_number = $v['number'];
                        $c_array = explode(',', $c_number);
                        if ($c_array[0]==$first_two[0] && $c_array[1]==$first_two[1]) {
                            $pl = 1;
                            $explain = $stage_num . '期前二直中' . $v['number'];

                        }
                    }elseif($v['type']==16) {
                        $first_three[] = $number_arr[0];
                        $first_three[] = $number_arr[1];
                        $first_three[] = $number_arr[2];
                        $c_number = $v['number'];
                        $c_array = explode(',', $c_number);
                        if (in_array($c_array[0],$first_three) && in_array($c_array[1],$first_three) && in_array($c_array[2],$first_three[2])) {
                            $pl = 1;
                            $explain = $stage_num . '期前三组中' . $v['number'];

                        }
                    }elseif($v['type']==17) {
                        $first_three[] = $number_arr[0];
                        $first_three[] = $number_arr[1];
                        $first_three[] = $number_arr[2];
                        $c_number = $v['number'];
                        $c_array = explode(',', $c_number);
                        if ($c_array[0]==$first_three[0] && $c_array[1]==$first_three[1] && $c_array[2]==$first_three[2]) {
                            $pl = 1;
                            $explain = $stage_num . '期前三直中' . $v['number'];

                        }
                    }elseif($v['type']>=18 && $v['type']<=22 ) {
                        $wei = $v['type']-18;
                        $wei_number = $number_arr[$wei];
                        if ($wei_number==$v['number']) {
                            $pl = 1;
                            $explain = $stage_num . '期特码中' . $v['number'];
                        }
                    }

                    $open_number = $numbers;
                    if($pl){
                        $explain = '广东11选5'.$explain;
                        if($v['type']>=7 && $v['type']<=17){
                            $odd = getOdds($v['cate'],$v['hall'],$v['type'],1);
                        }else {
                            $odd = getOdds($v['cate'], $v['hall'], $v['type'], $v['number']);
                        }
                        if(!$odd){
                            $content = $v['cate'].'---'.$v['type'].'---'.$v['hall'].'---'.$v['number'].'没有赔率';
                            file_put_contents('my_log.txt',$content.'\r\n"',8);
                        }
                        Db::startTrans();
                        try{
                            $z_money = $v['money'] * $odd;
                            Db::name('single')->where('id',$v['id'])->update(['state'=>1,'code'=>1,'update_at'=>$time,'open_number'=>$open_number,'z_money'=>$z_money]);
                            Db::name('member')->where('id', $v['uid'])->setInc('money', $v['money'] * $odd);
                            $balance = Db::name('member')->where('id', $v['uid'])->value('money');
                            addDetail($v['uid'], 1, $v['money'] * $odd, $balance, 2, $v['cate'], $explain,$v['hall']);
                            Db::commit();
                        }catch (\Exception $e) {
                            Db::rollback();
                        }
                    }else{
                        Db::name('single')->where('id',$v['id'])->update(['state'=>1,'update_at'=>$time,'open_number'=>$open_number]);
                    }
                }
            }
        }
    }

    /**
     *开奖
     */
    public function hk_doCron($stage){
        $info = Db::name('at_hk')->where(array('stage'=>$stage))->find();
        if(!$info){
            $this->success_new('开奖期号不存在',url('lottery/open'));
        }
        if($info) {
            $stage_num =  $info['stage'];
            $numbers   =  $info['number'];
            $number_arr = explode(',', $numbers);
            $w['stage'] = $stage_num;
            $w['cate']  = 11;
            $w['state'] = 0;
            $w['code']  = 0;
            $list = Db::name('single')->where($w)->select();
            $explain ='';
            if($list) {
                $time = time();
                foreach ($list as $v){
                    $pl =0;
                    if($v['type']>=1 && $v['type']<=4){
                        $dx   = hkdx($number_arr[6]);
                        $ds   = hkds($number_arr[6]);
                        $sebo = hkhll($number_arr[6]);
                        $zuhe = hkdx($number_arr[6]).hkds($number_arr[6]);
                        $wei  = hkwei($number_arr[6]);
                        if($v['number']==$dx ||$v['number']==$ds ||$v['number']==$sebo || $v['number']==$zuhe || $v['number']==$wei){
                            $pl = 1;
                            $explain = $stage_num.'期双面盘中'.$v['number'];
                        }
                    }elseif($v['type']==6) {
                        if ($v['number'] == $number_arr[6]) {
                            $pl = 1;
                            $explain = $stage_num . '期特码中' . $v['number'];
                        }
                    }elseif($v['type']==7) {
                        $number_arr_7 = $number_arr;
                        array_pop( $number_arr_7);
                        if (in_array($v['number'],$number_arr_7)) {
                            $pl = 1;
                            $explain = $stage_num . '期正码中' . $v['number'];

                        }
                    }elseif($v['type']>=8 && $v['type']<=12) {
                        $wei = $v['type']-8;
                        $wei_numner = $number_arr[$wei];
                        $dx   = hkdx($wei_numner);
                        $ds   = hkds($wei_numner);
                        $hdx  = hkhdx($wei_numner);
                        $hds  = hkhds($wei_numner);
                        $sebo = hkhll($wei_numner);
                        $weidx = hkwei($wei_numner);
                        if ($v['number'] == $dx || $v['number'] == $ds || $v['number'] == $hdx ||$v['number']== $hds ||$v['number']== $sebo||$v['number']== $weidx) {
                            $pl = 1;
                            $explain = $stage_num . '期正码1-6中' . $v['number'];
                        }
                    }elseif($v['type']==13) {
                        $five = five($number_arr[6]);
                        if ($v['number'] == $five) {
                            $pl = 1;
                            $explain = $stage_num . '期五行中' . $v['number'];
                        }
                    }elseif($v['type']==14) {
                        $zodiacs = get_zodiac($number_arr);
                        $zodiacs_arr = explode(',',$zodiacs);
                        $zodiacs_arr = array_unique($zodiacs_arr);
                        if (in_array($v['number'],$zodiacs_arr)) {
                            $pl = 1;
                            $explain = $stage_num . '期平特一肖中' . $v['number'];
                        }
                    }elseif($v['type']==15) {
                        $zodiacs = zodiac($number_arr[6]);
                        if ($v['number']==$zodiacs){
                            $pl = 1;
                            $explain = $stage_num . '期特码生效中' . $v['number'];
                        }
                    }elseif($v['type']==16) {
                        $head = head($number_arr[6]);
                        if ($v['number']==$head){
                            $pl = 1;
                            $explain = $stage_num . '期特码头数中' . $v['number'];
                        }
                    }elseif($v['type']==17) {
                        $head = tail($number_arr[6]);
                        if ($v['number']==$head){
                            $pl = 1;
                            $explain = $stage_num . '期特码尾数中' . $v['number'];
                        }
                    }elseif($v['type']==18) {
                        $zodiacs = get_zodiac($number_arr);
                        $zodiacs_arr = explode(',',$zodiacs);
                        $zodiacs_arr = array_unique($zodiacs_arr);
                        $number = count($zodiacs_arr);
                        if ($v['number']==$number){
                            $pl = 1;
                            $explain = $stage_num . '期总肖中' . $v['number'];
                        }
                    }elseif($v['type']==19) {
                        $wei = 5;
                        $wei_numner = $number_arr[$wei];
                        $dx    = hkdx($wei_numner);
                        $ds    = hkds($wei_numner);
                        $hdx   = hkhdx($wei_numner);
                        $hds   = hkhds($wei_numner);
                        $sebo  = hkhll($wei_numner);
                        $weidx = hkwei($wei_numner);
                        if ($v['number'] == $dx || $v['number'] == $ds || $v['number'] == $hdx || $v['number'] == $hds || $v['number'] == $sebo || $v['number'] == $weidx) {
                            $pl = 1;
                            $explain = $stage_num . '期正码1-6中' . $v['number'];
                        }
                    }elseif($v['type']==20) {
                        $t_number = explode(',',$v['number']);
                        $number_arr_20 = $number_arr;
                        array_pop($number_arr_20);
                        if (is_in($t_number,$number_arr_20)) {
                            $pl = 1;
                            $explain = $stage_num . '三全中中' . $v['number'];
                        }
                    }elseif($v['type']==21) {
                        $t_number = explode(',',$v['number']);
                        $t_number_one   = [$t_number[0],$t_number[1]];
                        $t_number_two   = [$t_number[1],$t_number[2]];
                        $t_number_three = [$t_number[0],$t_number[2]];
                        $number_arr_21 = $number_arr;
                        array_pop($number_arr_21);
                        if(is_in($t_number_one, $number_arr_21) || is_in($t_number_two, $number_arr_21)|| is_in($t_number_three, $number_arr_21)){
                            $pl = 1;
                            $explain = $stage_num . '三中二中' . $v['number'];
                        }
                        if (is_in($t_number,$number_arr_21)) {
                            $pl = 2;
                            $explain = $stage_num . '三中二之中三中' . $v['number'];
                        }
                    }elseif($v['type']==22) {
                        $t_number = explode(',',$v['number']);
                        $number_arr_22 = $number_arr;
                        array_pop($number_arr_22);

                        if(is_in($t_number,$number_arr_22)){
                            $pl = 1;
                            $explain = $stage_num . '二全中' . $v['number'];
                        }
                    }elseif($v['type']==24) {
                        $t_number = explode(',',$v['number']);
                        $t_number_one = $t_number[0];
                        $t_number_two = $t_number[1];
                        $number_arr_temp = $number_arr;
                        $number_arr_24   = $number_arr;
                        array_pop($number_arr_24);
                        if(in_array($t_number_one, $number_arr_24) && $t_number_two = $number_arr_temp[6] ){
                            $pl = 1;
                            $explain = $stage_num . '特串中' . $v['number'];
                        }elseif(in_array($t_number_two,  $number_arr_24) && $t_number_one = $number_arr_temp[6] ){
                            $pl = 1;
                            $explain = $stage_num . '特串中' . $v['number'];
                        }
                    }elseif($v['type']==25) {
                        $t_number = explode(',',$v['number']);
                        $number_arr_25   = $number_arr;
                        array_pop($number_arr_25);
                        if(is_in($t_number,$number_arr_25 )){
                            $pl = 1;
                            $explain = $stage_num . '四全中' . $v['number'];
                        }
                    }elseif($v['type']==26) {
                        $t_number = explode(',',$v['number']);
                        $zodiacs = get_zodiac($number_arr);
                        $zodiacs_arr = explode(',',$zodiacs);
                        $zodiacs_arr = array_unique($zodiacs_arr);
                        if(is_in($t_number,$zodiacs_arr)){
                            $pl = 1;
                            $explain = $stage_num . '二连肖中' . $v['number'];
                        }
                    }elseif($v['type']==27) {
                        $t_number = explode(',',$v['number']);
                        $zodiacs = get_zodiac($number_arr);
                        $zodiacs_arr = explode(',',$zodiacs);
                        $zodiacs_arr = array_unique($zodiacs_arr);
                        if(is_in($t_number,$zodiacs_arr)){
                            $pl = 1;
                            $explain = $stage_num . '三连肖中' . $v['number'];
                        }
                    }elseif($v['type']==28) {
                        $t_number = explode(',',$v['number']);
                        $zodiacs = get_zodiac($number_arr);
                        $zodiacs_arr = explode(',',$zodiacs);
                        $zodiacs_arr = array_unique($zodiacs_arr);
                        if(is_in($t_number,$zodiacs_arr)){
                            $pl = 1;
                            $explain = $stage_num . '四连肖中' . $v['number'];
                        }
                    }elseif($v['type']==29) {
                        $t_number = explode(',',$v['number']);
                        $zodiacs = get_zodiac($number_arr);
                        $zodiacs_arr = explode(',',$zodiacs);
                        $zodiacs_arr = array_unique($zodiacs_arr);
                        if(is_in($t_number,$zodiacs_arr)){
                            $pl = 1;
                            $explain = $stage_num . '五连肖中' . $v['number'];
                        }
                    }elseif($v['type']==30) {
                        $t_number = explode(',',$v['number']);
                        $zodiacs = zodiac1($number_arr[6]);

                        if(in_array($zodiacs,$t_number)){
                            $pl = 1;
                            $explain = $stage_num . '六肖中' . $v['number'];
                        }
                    }
                    $open_number = $numbers;
                    if($pl){
                        $explain = '香港六合彩'.$explain;
                        if($v['type']>=20 && $v['type']<=25){
                            if($pl==1) {
                                $odd = getOdds($v['cate'], $v['hall'], $v['type'], 1);
                            }elseif ($pl==2){
                                $odd = getOdds($v['cate'], $v['hall'], 20, 1);
                            }
                        }elseif($v['type']>=26 && $v['type']<=30){
                            $odd = getOdds($v['cate'], $v['hall'], $v['type'], "鼠");
                        }else{
                            $odd = getOdds($v['cate'],$v['hall'],$v['type'],$v['number']);
                        }
                        if(!$odd){
                            $content = $v['cate'].'---'.$v['type'].'---'.$v['hall'].'---'.$v['number'].'没有赔率';
                            file_put_contents('my_log.txt',$content.PHP_EOL,8);
                        }
                        Db::startTrans();
                        try{
                            $z_money = $v['money'] * $odd;
                            Db::name('single')->where('id',$v['id'])->update(['state'=>1,'code'=>1,'update_at'=>$time,'open_number'=>$open_number,'z_money'=>$z_money]);
                            Db::name('member')->where('id', $v['uid'])->setInc('money', $v['money'] * $odd);
                            $balance = Db::name('member')->where('id', $v['uid'])->value('money');
                            addDetail($v['uid'], 1, $v['money'] * $odd, $balance, 2, $v['cate'], $explain,$v['hall']);
                            Db::commit();
                        }catch (\Exception $e) {
                            Db::rollback();
                        }
                    }else{
                        Db::name('single')->where('id',$v['id'])->update(['state'=>1,'update_at'=>$time,'open_number'=>$open_number]);
                    }
                }
            }
        }
    }

    /**
     * 修改大厅人数
     */
    public function edit_hall_number(Request $request){
        $id        = $request->param('id');
        $number    = $request->param('number');
        $result = Db::name('hall')
            ->where('id',$id)
            ->update(['online'=>$number]);
        if ($result) {
            json_return(200,'修改成功');
        }else{
            json_return(500,'修改失败');
        }

    }

    /**
     * 修改每种玩法限制投注
     */
    public function edit_bet_number(Request $request){
        $id        = $request->param('id');
        $from      = $request->param('from');
        $to        = $request->param('to');
        $max        = $request->param('max');

        $up_data = [
            'from'=>$from,
            'to'=>$to,
            'max'=>$max
        ];
        $result = Db::name('bet')
            ->where('id',$id)
            ->update($up_data);
        if ($result) {
            json_return(200,'修改成功');
        }else{
            json_return(500,'修改失败');
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
        $uids = Db::name('member')->where('is_m',1)->column('id');
        $w['s.uid']     = ['in',$uids];
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
            ->paginate(20,false,['query' => request()->param()]);
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
     *投注记录
     */
    public function betting_ben(Request $request){
        $from    = $request->param('from');
        $to      = $request->param('to');
        $id      = $request->param('id');
        $stage   = $request->param('stage');
        $mobile  = $request->param('mobile');
        $w =[];
        $uids = Db::name('member')->where('is_m',1)->column('id');
        $w['s.uid']     = ['in',$uids];
        if($stage){
            $w['stage'] = $stage;
        }else{
            $number      = getNumberCache('ssc');
            $stage       = key($number); //获取最新一期的key（即期数）
            $stage       = $stage+1;
            $w['stage'] = $stage;
        }
        if($from && $to){
            $from = strtotime($from);
            $to   = strtotime($to);
            $w['s.create_at'] = [['>=',$from],['<=',$to]];
        }
        if($id){
            $w['s.uid']  = $id;
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
            ->paginate(20,false,['query' => request()->param()]);
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
            'total_number'=>$total_number,
            'total_money' =>$total_money,
            'zj_money' =>$zj_money,
            'stage'=>$stage
        ]);
    }

//    /**
//     *投注记录本期
//     */
//    public function betting_ben(Request $request){
//        $stage  = $request->param('stage');
//        $w =[];
//        if($stage){
//            $w['stage'] = $stage;
//        }else{
//            $number      = getNumberCache('ssc');
//            $stage       = key($number); //获取最新一期的key（即期数）
//            $stage       = $stage+1;
//            $w['stage'] = $stage;
//        }
//        $list = Db::name('single')
//            ->alias('s')
//            ->field('stage,number,sum(money) as money,sum(z_money) as z_money')
//            ->where($w)
//            ->group('number')
//            ->paginate(20,false,['query' => request()->param()]);
//        $total_number =  Db::name('single')
//            ->alias('s')
//            ->where($w)
//            ->sum('money');
//        $total_money =  Db::name('single')
//            ->alias('s')
//            ->where($w)
//            ->sum('z_money');
//
//        $zj_money = $total_money-$total_number;
//        return view('',[
//            'list' =>$list,
//            'page' =>$list->render(),
//            'total_number'=>$total_number,
//            'total_money' =>$total_money,
//            'zj_money' =>$zj_money,
//            'stage'=>$stage
//        ]);
//    }

    /**
     * 1314赔率
     */
    public function special_odd(){
        $data = Db::name('odds_more')->select();
        foreach ($data as $k=>$v){
            if($v['cate']==1){
                $data[$k]['name'] = 'PC蛋蛋';
            }
            if($v['cate']==2){
                $data[$k]['name'] = '加拿大28';
            }
        }
        return view('',[
           'data'=>$data
        ]);

    }

    /**
     * 1314赔率编辑
     */
    public function special_odd_edit(Request $request){
        $id        = $request->param('id');
        $rate      = $request->param('rate');
        $result = Db::name('odds_more')
            ->where('id', $id)
            ->update(['rate' => $rate]);

        if ($result) {
            json_return(200,'修改成功');
        }else{
            json_return(500,'修改失败');
        }
    }



}