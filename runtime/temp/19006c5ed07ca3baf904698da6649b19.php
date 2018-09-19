<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:63:"/home/wwwroot/dwj/public/../app/admin/view/lottery/betting.html";i:1529840921;s:59:"/home/wwwroot/dwj/public/../app/admin/view/common/base.html";i:1535125972;s:58:"/home/wwwroot/dwj/public/../app/admin/view/common/nav.html";i:1524635392;}*/ ?>
<!DOCTYPE html>
<html lang="zh_ch">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="ThemeBucket">
    <link rel="shortcut icon" href="#" type="image/png">
    <title>后台管理系统</title>
    <link href="/yicai/favicon.ico" rel="icon" type="image/x-icon" />
    <link href="__CSS__/style.css" rel="stylesheet">
    <!--<link href="__CSS__/bootstrap.min.css" rel="stylesheet">-->
    <link href="__CSS__/style-responsive.css" rel="stylesheet">
    
<!--pickers css-->
<link rel="stylesheet" type="text/css" href="__JS__/bootstrap-datepicker/css/datepicker-custom.css" />
<link rel="stylesheet" type="text/css" href="__JS__/bootstrap-timepicker/css/timepicker.css" />
<link rel="stylesheet" type="text/css" href="__JS__/bootstrap-colorpicker/css/colorpicker.css" />
<link rel="stylesheet" type="text/css" href="__JS__/bootstrap-daterangepicker/daterangepicker-bs3.css" />
<link rel="stylesheet" type="text/css" href="__JS__/bootstrap-datetimepicker/css/datetimepicker-custom.css" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="__JS__/html5shiv.js"></script>
    <script src="__JS__/respond.min.js"></script>
    <![endif]-->
</head>

<body class="sticky-header">

<section>
    <!-- left side start-->
    <div class="left-side sticky-left-side">

        <!--logo start-->
        <div class="logo">
            <a href="<?php echo url('Index/index'); ?>"><img src="__IMG__/logo.png" alt=""></a>
        </div>

        <div class="logo-icon text-center">
            <a href="<?php echo url('Index/index'); ?>"><img src="__IMG__/logo_icon.png" alt=""></a>
        </div>
        <!--logo  end-->


        <div class="left-side-inner">

            <!-- visible to small devices only -->
            <div class="visible-xs hidden-sm hidden-md hidden-lg">
                <div class="media logged-user">
                    <img alt="" src="__IMG__/photos/user-avatar.png" class="media-object">
                    <div class="media-body">
                        <h4><a><?php echo session('admin_login.username'); ?></a></h4>
                    </div>
                </div>
                <ul class="nav nav-pills nav-stacked custom-nav">
                    <li><a  data-toggle="modal" href="#myModal"><i class="fa fa-cog"></i> <span>修改密码</span></a></li>
                    <li><a href="<?php echo url('Login/out'); ?>"><i class="fa fa-sign-out"></i> <span>退出</span></a></li>
                </ul>
            </div>

            <!--左侧菜单 start-->
<ul class="nav nav-pills nav-stacked custom-nav">

    <?php if(is_array($menu_list) || $menu_list instanceof \think\Collection || $menu_list instanceof \think\Paginator): if( count($menu_list)==0 ) : echo "" ;else: foreach($menu_list as $key=>$vo): if(!(empty($vo['sub_menu']) || (($vo['sub_menu'] instanceof \think\Collection || $vo['sub_menu'] instanceof \think\Paginator ) && $vo['sub_menu']->isEmpty()))): ?>
            <li class="menu-list <?php if(($controllerName == $vo['control'])): ?>nav-active<?php endif; ?>"><a href="#"><i class="fa <?php echo $vo['icon']; ?>"></i><span><?php echo $vo['name']; ?></span></a>
                <ul class="sub-menu-list">
                    <?php if(is_array($vo['sub_menu']) || $vo['sub_menu'] instanceof \think\Collection || $vo['sub_menu'] instanceof \think\Paginator): if( count($vo['sub_menu'])==0 ) : echo "" ;else: foreach($vo['sub_menu'] as $kk=>$vv): ?>
                    <li <?php if(($actionName == $vv['act'])): ?> class="active" <?php endif; ?>><a href="<?php echo url("$vv[control]/$vv[act]"); ?>"><?php echo $vv['name']; ?></a></li>
                   <?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
            </li>
             <?php else: ?>
            <li class="menu-list}">
            <a href="<?php echo url("$vo[control]/$vo[act]"); ?>""><i class="fa <?php echo $vo['icon']; ?>"></i><span><?php echo $vo['name']; ?></span></a>
            </li>
      <?php endif; endforeach; endif; else: echo "" ;endif; ?>

</ul>
<!--左侧菜单 end-->

        </div>
    </div>
    <!-- left side end-->

    <!-- main content start-->
    <div class="main-content" >

        <!-- header section start-->
        <div class="header-section">

            <!--toggle button start-->
            <a class="toggle-btn"><i class="fa fa-bars"></i></a>
            <!--toggle button end-->

            <!--notification menu start -->
            <div class="menu-right">
                <ul class="notification-menu">

                    <li>
                        <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <img src="__IMG__/photos/user-avatar.png" alt="" />
                            <?php echo session('admin_login.username'); ?>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-usermenu pull-right">
                            <li><a  data-toggle="modal" href="#myModal"><i class="fa fa-cog"></i> <span>修改密码</span></a></li>
                            <li><a href="<?php echo url('Login/out'); ?>"><i class="fa fa-sign-out"></i>退出</a></li>
                        </ul>
                    </li>

                </ul>
            </div>
            <!--notification menu end -->

        </div>
        <!-- header section end-->
        <!--提示信息-->
        <div id="top-alert" class="fixed alert alert-error">
            <button class="close fixed">&times;</button>
            <div class="alert-content">提示信息</div>

        </div>
        <!--提示信息end-->
        
<!--body wrapper start-->
<div class="wrapper">
    <div class="pull-left">
        <a href="javascript:history.go(-1)" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="返回"><i class="fa fa-reply"></i></a>
    </div>
    <div class="row">
        <div class="col-md-12">
            <!--breadcrumbs start -->
            <ul class="breadcrumb panel">
                <li><a href="<?php echo url('Index/index'); ?>"><i class="fa fa-home"></i> 控制台</a></li>
                <li><a href="<?php echo url('index'); ?>">彩票管理</a></li>
                <li class="active">投注记录</li>
            </ul>
            <!--breadcrumbs end -->
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <section class="panel">
                <header class="panel-heading col-xs-12 ">
                    <form class="form-inline" action="<?php echo url('',array('id'=>$id)); ?>">
                        <div class="form-group">
                            <div class="col-md-5">
                                <div class="input-group  custom-date-range">
                                    <input type="text" class="form-control form_datetime" value="<?php echo isset($_GET['from']) ? $_GET['from'] :  ''; ?>" name="from" placeholder="开始时间">
                                    <span class="input-group-addon">到</span>
                                    <input type="text" class="form-control form_datetime" value="<?php echo isset($_GET['to']) ? $_GET['to'] :  ''; ?>" name="to" placeholder="结束时间">
                                </div>
                            </div>
                            <select class="form-control" name="code">
                                <option value="-1">中奖状态</option>
                                <option value="0" <?php if((isset($_GET['code']) && $_GET['code'] == 0)): ?>selected<?php endif; ?>>未中奖</option>
                                <option value='1' <?php if((isset($_GET['code']) && $_GET['code'] == 1)): ?>selected<?php endif; ?>>中奖</option>
                            </select>
                            <input type="text" name="mobile" class="form-control" value="<?php echo isset($_GET['mobile']) ? $_GET['mobile'] :  ''; ?>" placeholder="请输用户名">
                            <input type="text" name="id" class="form-control" value="<?php echo isset($_GET['id']) ? $_GET['id'] :  ''; ?>" placeholder="请输入用户ID">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
                </header>

                <div class="panel-body">
                    <form class="ids" action="<?php echo url('del'); ?>" method="post">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>用户名</th>
                                <th>期数</th>
                                <th>彩种</th>
                                <th>玩法</th>
                                <th>开奖号码</th>
                                <th>号码</th>
                                <th>投注</th>
                                <th>中奖</th>
                                <th>余芬</th>
                                <th>盈亏</th>
                                <th>IP</th>
                                <th >状态</th>
                                <th >时间</th>
                            </tr>
                            </thead>
                            <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                            <tr>
                                <td><?php echo $v['uid']; ?></td>
                                <td><?php echo $v['mobile']; ?></td>
                                <td><?php echo $v['stage']; ?></td>
                                <td><?php echo $v['name']; ?></td>
                                <td><?php echo $v['title']; ?></td>
                                <td><?php echo $v['open_number']; ?></td>
                                <td><?php echo $v['number']; ?></td>
                                <td><?php echo $v['money']; ?></td>
                                <td><?php echo $v['z_money']; ?></td>
                                <td><?php echo $v['balance']; ?></td>
                                <?php if($v['z_money']-$v['money']>0): ?>
                                <td style="color: blue">+<?php echo $v['z_money']-$v['money']; ?></td>
                                <?php else: ?>
                                <td style="color: red"><?php echo $v['z_money']-$v['money']; ?></td>
                                <?php endif; ?>
                                <td><?php echo $v['ip']; ?></td>
                                <?php if($v['is_c']==0): if(($v['state'] == 0)): ?>
                                            <td style="color:red">未开奖</td>
                                    <?php elseif(($v['state'] == 1 && $v['code'] == 1)): ?>
                                            <td style="color:#65CEA7">中奖</td>
                                    <?php else: ?>
                                            <td>未中奖</td>
                                    <?php endif; else: ?>
                                    <td>已撤销</td>
                                <?php endif; ?>
                                <td><?php echo date('Y-m-d H:i:s',$v['create_at']); ?></td>
                            </tr>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </table>

                    </form>
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="dataTables_info" id="dynamic-table_info">
                                总投注次数：<?php echo $total_number; ?>&nbsp;&nbsp; 总投注金额：<?php echo $total_money; ?>&nbsp;&nbsp;总中奖金额：<?php echo (isset($zj_money) && ($zj_money !== '')?$zj_money:"0.00"); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <?php echo $page; ?>
        </div>

    </div>
</div>
<!--body wrapper end-->

        <!--footer section start-->
        <footer class="sticky-footer">
            2014-2017 ©
            天猫国际出品
        </footer>
        <!--footer section end-->

    </div>
    <!-- main content end-->
</section>

<!-- Modal -->
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                <h4 class="modal-title">修改密码</h4>
            </div>
            <form class="form-horizontal form-post" action="<?php echo url('Users/psd'); ?>" method="post">
            <div class="modal-body">
                <div class="form-group">
                    <label  class="col-lg-2 col-sm-2 control-label">原密码</label>
                    <div class="col-lg-10">
                        <input type="password" name="old" class="form-control"  placeholder="">
                    </div>
                </div>
                <div class="form-group">
                    <label  class="col-lg-2 col-sm-2 control-label">新密码</label>
                    <div class="col-lg-10">
                        <input type="password" name="password" class="form-control"  placeholder="">
                    </div>
                </div>
                <div class="form-group">
                    <label  class="col-lg-2 col-sm-2 control-label">确认密码</label>
                    <div class="col-lg-10">
                        <input type="password" name="passwords" class="form-control"  placeholder="">
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">取消</button>
                <button class="btn btn-primary ajax-post" type="submit" target-form="form-post">提交</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- modal -->

<!-- Placed js at the end of the document so the pages load faster -->
<script src="__JS__/jquery-1.10.2.min.js"></script>
<script src="__JS__/jquery-ui-1.9.2.custom.min.js"></script>
<script src="__JS__/jquery-migrate-1.2.1.min.js"></script>
<script src="__JS__/bootstrap.min.js"></script>
<script src="__JS__/modernizr.min.js"></script>
<script src="__JS__/jquery.nicescroll.js"></script>
<script src="__JS__/layer/layer.js"></script>


<!--pickers plugins-->
<script type="text/javascript" src="__JS__/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="__JS__/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="__JS__/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="__JS__/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="__JS__/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="__JS__/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>

<!--pickers initialization-->
<script src="__JS__/pickers-init.js"></script>

<script>
    $(document).ready(function(){
        setInterval(is_recharge,8000);
    });
    function is_recharge() {
        $.ajax({
            type: "post",
            url: "<?php echo url('money/is_recharge'); ?>",
            data: "",
            dataType: "json",
            async: false,
            success: function(data) {
                var ret = data.ret;
                var type = data.data.type;
                var name = data.data.name;
                if(ret=="200"){
                    var audio = document.createElement("audio");
                    audio.src = "URL_IMG/recharge.mp3";
                    audio.play();
                    if(type==1){
                        var msg = "您有一个新的充值订单:"+name;
                        layer.alert(msg, {
                            skin: 'layui-layer-molv' //样式类名
                            ,closeBtn: 0
                            ,anim: 4 //动画类型
                        });
                    }
                    if(type==2){
                        var msg = "您有一个新的提现订单:"+name;
                        layer.alert(msg, {
                            skin: 'layui-layer-molv' //样式类名
                            ,closeBtn: 0
                            ,anim: 4 //动画类型
                        });
                    }
                }
            }
        });
    }
    function showNotice(msg,name) {
        Notification.requestPermission(function (perm) {
            if (perm == "granted") {
                var notification = new Notification(msg, {
                    dir: "auto",
                    lang: "hi",
                    tag: "易彩",
                    icon: "/yicai/favicon.ico",
                    body: name
                });
            }
        })
    }
</script>

<!--common scripts for all pages-->
<script src="__JS__/scripts.js"></script>
<script src="__JS__/common.js"></script>

</body>
</html>
