<?php
namespace app\admin\controller;



use app\home\controller\Biapi;
use think\Db;
use think\Request;
use app\admin\model\Detail;
use app\admin\model\Members;
use app\admin\model\Recharge;

class Index extends Admin
{
    public function index()
    {
        $info    = session('info');

        $member_number = Db::name('member')->count();

        $uids = Db::name('member')->where('is_m',1)->column('id');

        $total_number = $member_number;
        $recharge_map = [
            'uid'=>array('in',$uids),
            'status'=>2,
            'update_at'=>array('gt',strtotime(date('Y-m-d')))
        ];
        $win_lose_map = [
            'uid'=>array('in',$uids),
            'create_at'=>array('gt',strtotime(date('Y-m-d')))
        ];
        $withdraw_map = [
            'uid'=>array('in',$uids),
            'status'=>2,
            'update_at'=>array('gt',strtotime(date('Y-m-d')))
        ];
        $today_recharge = Db::name('recharge')->where($recharge_map)->sum('money');
        $today_withdraw = Db::name('withdrawals')->where($withdraw_map)->sum('money');
        $balance_total  = Db::name('member')->where('is_m',1)->sum('money');
        $win_lose      = Db::name('single')->where($win_lose_map)->field('sum(money-z_money) as win_lose')->find()['win_lose'];
        $recharge_wait = Db::name('recharge')->where('status',1)->count();
        $withdraw_wait = Db::name('withdrawals')->where('status',1)->count();
        $back = [
            'member_number' =>$member_number,
            'total_number'  =>$total_number,
            'today_recharge'=>$today_recharge,
            'today_withdraw'=>$today_withdraw,
            'balance_total' =>$balance_total,
            'win_lose'      =>$win_lose,
            'recharge_wait'=>$recharge_wait,
            'withdraw_wait'=>$withdraw_wait
        ];
        $this->assign('back',$back);
        $this->assign('info',$info);
        return view();
    }

    /**
     * 获取系统信息
     */
    public function get_sys_info(){
        $sys_info['os']             = PHP_OS;
        $sys_info['zlib']           = function_exists('gzclose') ? 'YES' : 'NO';//zlib
        $sys_info['safe_mode']      = (boolean) ini_get('safe_mode') ? 'YES' : 'NO';//safe_mode = Off
        $sys_info['timezone']       = function_exists("date_default_timezone_get") ? date_default_timezone_get() : "no_timezone";
        $sys_info['curl']           = function_exists('curl_init') ? 'YES' : 'NO';
        $sys_info['web_server']     = $_SERVER['SERVER_SOFTWARE'];
        $sys_info['phpv']           = phpversion();
        $sys_info['ip']             = GetHostByName($_SERVER['SERVER_NAME']);
        $sys_info['fileupload']     = @ini_get('file_uploads') ? ini_get('upload_max_filesize') :'unknown';
        $sys_info['max_ex_time']    = @ini_get("max_execution_time").'s'; 
        $sys_info['set_time_limit'] = function_exists("set_time_limit") ? true : false;
        $sys_info['domain']         = $_SERVER['HTTP_HOST'];
        $sys_info['memory_limit']   = ini_get('memory_limit');
        $sys_info['version']        = '1.1.0';
        //$mysqlinfo = Db::query("SELECT VERSION() as version");
        $sys_info['mysql_version']  = 5.4;
        if(function_exists("gd_info")){
            $gd = gd_info();
            $sys_info['gdinfo']     = $gd['GD Version'];
        }else {
            $sys_info['gdinfo']     = "δ֪";
        }
        return $sys_info;
    }
}
