<?php
/**
+------------------------------------------------------------------------------
 * 基于用户的操作记录验证类
+------------------------------------------------------------------------------
 * @category   ORG
 * @package  ORG
 * @subpackage  Msj
 * @author    PHP@妖孽 <msj@yaonies.com>
 * @version   1.0
+------------------------------------------------------------------------------
 */
namespace Net;
// 配置文件增加设置
//  'OPERATION_ON'=>true,// 开启用户记录日志
//     'OPERATION_MEMBER'=>'learn_member',
//     'OPERATION_TYPE'=>'web',//分别为web,interface也就是网站,和接口
//     'OPERATION_MEMBER_ID'=>'member_id', //如果后台就取session,如果接口就直接取get,post请求的值
/*
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `_operation_log` (
  `operation_log` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '操作记录主键',
  `operation_uid` mediumint(4) NOT NULL DEFAULT '0' COMMENT '操作人/如果是接口返回-1暂不记录接口请求人',
  `operation_node` char(50) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '操作节点',
  `operation_ip` mediumtext COLLATE utf8_bin NOT NULL COMMENT '记录操作IP,省市,等信息',
  `operation_time` int(10) NOT NULL DEFAULT '0' COMMENT '操作时间',
  PRIMARY KEY (`operation_log`),
  KEY `index_uid_node` (`operation_uid`,`operation_node`,`operation_log`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='@author PHP@妖孽\r\n@since 2014-5-4'

*/
use think\Db;
use think\Request;

class Operation {

    private $operation_on;//操作记录开关
    public    $error;//错误信息

    /**
     * @todo  验证是否开启记录
     */
    public function __construct(){
        $this->operation_on = true;
        if($this->operation_on === false){
            return false;
        }
    }

    /**
     * @todo 写入操作日志
     */
    public function writeLog(){
        $request = Request::instance();
        $controllerName = $request->controller();
        $actionName     = $request->action();
        $ip             = get_client_ip();
        if($ip =="127.0.0.1"){
            $address = "本地";
        }else {
            $address = get_city_id($ip);
        }

        $operation['operation_uid']  = session('info')['id'];
        $operation['operation_node'] =$controllerName.'/'.$actionName ;
        $operation['operation_ip']   = $ip;
        $operation['operation_addr'] = $address;
        Db::name('operation_log')->insert($operation);

    }

    /**
     * @todo 查询操作日志
     * @param array $map 目前只支持用户id的查询.
     */
    public function logList($map='',$limit = ''){
        if($limit) {
            $data = Db::name('operation_log')
                ->alias('o')
                ->join(' dl_user d', 'd.id = o.operation_uid')
                ->field('o.*,d.username')
                ->where($map)
                ->order('o.id desc')
                ->limit($limit)
                ->select();
            }else{
            $data = Db::name('operation_log')
                ->alias('o')
                ->join(' dl_user d', 'd.id = o.operation_uid')
                ->where($map)
                ->select();
        }
        return $data;


    }

    public function __destruct(){
        $this->operation_on=false;
        $this->error ='';
    }
}