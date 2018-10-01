<?php
/**
 * Created by PhpStorm.
 * User: tangmusen
 * Date: 2017/10/30
 * Time: 11:08
 */
namespace app\home\controller;

use think\Controller;
use think\Db;
use think\Request;

class Notice extends Base
{
    /**
     * 邀请好友
     */
    public function index(){
        $id = session('uid');
        $url = 'http://103.115.44.136?parent_id='.$id;
        return view('',[
         'url'=>$url,
          'uid'=>$id
        ]);
    }

    /**
     * 具体消息
     */
    public function detail(Request $request){
        $id   = $request->param('id');
        $type = $request->param('type');
        if($type==1){
            $content = Db::name('notice')->where('id',$id)->find();
        }
        if($type==2){
            $content = Db::name('member_msg')->field('id,content as info,create_time,uid')->where('id',$id)->find();
            $content['content'] = "";
        }
        return view('',[
            'data' =>$content
        ]);


    }

    /**
     * 具体消息
     */
    public function detail_app(Request $request){
        $id   = $request->param('id');
        $type = $request->param('type')?$request->param('type'):1;
        if($type==1){
            $content = Db::name('notice')->where('id',$id)->find();
        }
        if($type==2){
            $content = Db::name('member_msg')->field('id,content as info,create_time,uid')->where('id',$id)->find();
            $content['content'] = "";
        }
        return view('',[
            'data' =>$content
        ]);


    }

    /**
     * 生成二维码
     */
    public function ewm(Request $request){
        $id = $request->param('uid');
        //$url = 'http://www.001s4.cn?parent_id='.$id;
		$url = 'http://103.115.44.136/home/login/register.html?rid='.$id;
        vendor('phpqrcode.phpqrcode');
        $size=4;    //图片大小
        $errorCorrectionLevel = "Q"; // 容错级别：L、M、Q、H
        $matrixPointSize = "8"; // 点的大小：1到10
        //实例化
        $qr = new \QRcode();

        //会清除缓冲区的内容，并将缓冲区关闭，但不会输出内容。
        ob_end_clean();
        $qr::png($url, false, $errorCorrectionLevel, $matrixPointSize);
    }


}