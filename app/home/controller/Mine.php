<?php
/**
 * Created by PhpStorm.
 * User: tangmusen
 * Date: 2017/10/30
 * Time: 11:08
 */
namespace app\home\controller;

use app\admin\model\Members;
use think\Config;
use think\Controller;
use think\Db;
use think\Request;

class Mine extends Base
{
    /**
     * 个人中心
     */
    public function index(){
        $uid = session('uid');
        $member_info = Db::name('member')->field('mobile,nickname,mobile,money,head')->where(array('id' => $uid))->find();
        return view('',[
            'member_info'=>$member_info,
        ]);
    }

    /**
    * QQ客服
    */
    public function sever_qq(){
       return view('',[
       ]);
   }

    /**
     * 微信客服
     */
    public function sever_wechat(){
        $wx_url = Db::name('setting')->where('id',1)->value('wx');
        $wx_url = Config::get('img_url').$wx_url;
        return view('',[
            'wx_url'=>$wx_url
        ]);
    }

    /**
     * 投注记录
     */
    public function betting_record (){
        $uid    = session('uid');
        $time_tj = strtotime(date('Y-m-d'));
        $today_data = Db::name('single')
            ->where('uid',$uid)
            ->where('create_at','gt',$time_tj)
            ->field('id,stage,number,money,z_money,state,code,create_at,balance')
            ->limit(100)
            ->order('id desc')
            ->select();
        $back = [
            'data'=>$today_data,
        ];

        return view('',$back);
    }

    /**
     * 资金密码
     */
    public function funds_password (){
        $uid = session('uid');
        $member_info = Db::name('member')->field('nickname,mobile,money,head,funds_pass')->where(array('id' => $uid))->find();

        return view('',[
            'member_info'=>$member_info,
        ]);
    }

    /**
     * 设置资金密码
     */
    public function change_funds_pass(Request $request){
        $uid         = session('uid');
        $funds_pass  = $request->post('funds_pass');
        $funds_pass  = data_md5_key($funds_pass);
        $result      = Db::name('member')
            ->where(array('id' => $uid))
            ->update(['funds_pass'=>$funds_pass]);
        if($result){
            json_return(200,'设置成功');
        }else{
            json_return(500,'设置失败');
        }

    }

    /**
     * 修改资金密码
     */
    public function set_funds_pass(Request $request){
        $uid = session('uid');
        $funds_pass     = $request->post('funds_pass');
        $funds_pass_one = $request->post('funds_pass_one');
        $funds_pass     = data_md5_key($funds_pass);
        $my_funds_pass = Db::name('member')->where('id',$uid)->value('funds_pass');

        if($funds_pass!=$my_funds_pass){
            json_return(201,'原密码不正确');
        }
        $funds_pass_one     = data_md5_key($funds_pass_one);
        $result = Db::name('member')
            ->where(array('id' => $uid))
            ->update(['funds_pass'=>$funds_pass_one]);
        if($result){
            json_return(200,'修改成功');
        }else{
            json_return(201,'修改失败');
        }

    }

    /**
     * 充值记录
     */
    public function recharge_record (){
        $uid  = session('uid');
        $data = Db::name('recharge')
            ->field('id,money,status,create_at')
            ->where('uid',$uid)
            ->limit(50)
            ->order('id desc')
            ->select();
        return view('',[
            'data'=>$data
        ]);
    }

    /**
     * 提现记录
     */
    public function withdraw_record (){
        $uid  = session('uid');
        $data = Db::name('withdrawals')
            ->field('id,money,status,create_at')
            ->where('uid',$uid)
            ->limit(50)
            ->order('id desc')
            ->select();
        return view('',[
            'data'=>$data
        ]);
    }

    /**
     * 修改密码
     */
    public function funds_psd_revise(){
        $uid = session('uid');
        $member_info = Db::name('member')->field('nickname,mobile,money,head')->where(array('id' => $uid))->find();
        return view('',[
            'member_info'=>$member_info,
        ]);

    }

    /**
     * 修改个人资料
     */
    public function personal(){
        $uid = session('uid');
        $data = Db::name('member')->field('nickname,head')->where('id',$uid)->find();
        return view('',[
            'data'=>$data
        ]);

    }

    /**
     * 修改头像
     */
    public function upload_head(){
        $uid        = session('uid');
        $file = request()->file('file');
        if(!$file){
            $data = array("status" =>0,"error" => '请选择上传图片');
            echo json_encode($data);
            exit;
        }
        $path = ROOT_PATH . 'public' . DS . 'uploads'. DS .'cate_img';
        $info = $file->move($path);
        if($info){
            $pri_path =   $imgpath = 'uploads/cate_img/'.$info->getSaveName();
            $image = \think\Image::open($pri_path);
            $date_path = 'uploads/cate_img/thumb/'.date('Ymd');
            if(!file_exists($date_path)){
                mkdir($date_path,0777,true);
            }
            $thumb_path = $date_path.'/'.$info->getFilename();
            $image->thumb(150,150,\think\Image::THUMB_CENTER)->save($thumb_path);
            $picd = 'cate_img/thumb/'.$info->getSaveName();
            $pic = Config::get('img_url').$picd;
            $up = [
                'head'=>$pic
            ];
            Db::name('member')->where('id',$uid)->update($up);
            $data = array("status" =>1,"pic" => $pic,'picd'=>$picd);
            echo json_encode($data);
            exit;
        }else{
            $data = array("status" =>0,"error" => '上传图片失败');
            echo json_encode($data);
            exit;
        }
    }

    /**
     * 修改昵称
     */
    public function do_nickname(Request $request){
        $uid        = session('uid');
        $nickname   = $request->post('nickname');
        $result     = Db::name('member')->where('id',$uid)->update(['nickname'=>$nickname]);
        if($result){
            json_return(200,'修改成功');
        }else{
            json_return(201,'修改失败');
        }
    }


    /**
     * 在线下分
     */
    public function withdraw(){
        $uid         = session('uid');
        $member_info = Db::name('member')->field('mobile,nickname,head,money,wx,alipay')->where('id',$uid)->find();
        return view('',[
            'member_info'=>$member_info
        ]);
    }

    /**
     * 用户提现
     */
    public function do_withdraw(Request $request){

        $uid            = session('uid');
        $money          = $request->post('money');
        $name           = $request->post('name');
        $way            = $request->post('way');

        if(!$name ||!$money){
            json_return(201,'网络错误!');
        }
        $user = Members::get($uid);
        $my_money      = $user['money'];
        $m_status      = $user['m_status'];
        if($m_status==1){
            json_return(201,'您的账号已被冻结!');
        }
        if($my_money<$money){
            json_return(207,'余额不足');
        }
        if($way==1){
           Db::name('member')->where('id',$uid)->update(['wx'=>$name]);
        }
        if($way==2){
            Db::name('member')->where('id',$uid)->update(['alipay'=>$name]);
        }
        Db::startTrans();
        try {
            $new_money = bcsub(round($my_money,2), round($money,2));
            $up = [
                'money'=>$new_money
            ];
            $result0 = Db::name('member')
                ->where('id',$uid)
                ->update($up);
            $data['uid']          = $uid;
            $data['username']     = $name;
            $data['way']          = $way;
            $data['money']        = $money;
            $data['balance']      = round($new_money,2);
            $data['create_at']    = time();
            $result = Db::name('withdrawals')->insert($data);
            $this->add_withdraw_detail($uid,$money,$data['balance']);
            Db::commit();
        }catch (\Exception $e){
            Db::rollback();
            json_return(500,'提现失败');
        }
        if($result && $result0){
            json_return(200,'申请提现成功,后台将在最短时间内审核',$data);
        }else{
            json_return(500,'提现失败');
        }

    }

    /**
     * 下级会员信息
     */
    public function member_down(Request $request){
        $tid    = $request->session('uid');
        $w=[];
        $w['tid'] = $tid;

        $list = Members::where($w)->order('id desc')->select();
        $count = count($list);
        return view('',[
            'list'=>$list,
            'count'=>$count
        ]);
    }


}