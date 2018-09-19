<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:57:"/home/wwwroot/dwj/public/../app/home/view/mine/index.html";i:1534861834;s:57:"/home/wwwroot/dwj/public/../app/home/view/common/nav.html";i:1526706822;}*/ ?>
<!DOCTYPE html>
<html>
<head lang="en">
	<meta charset="UTF-8">
	<meta name = "format-detection" content = "telephone=no">
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
	<title>天猫国际</title>
	<link rel="stylesheet" href="__CSS__/swiper.min.css"/>
	<link rel="stylesheet" href="__CSS__/css.css"/>
	<script src="__JS__/jquery-2.1.0.js"></script>
	<script src="__JS__/swiper.min.js"></script>
</head>
<body>
<div class="header">
	个人中心
	<a onclick="window.history.go(-1)">
		<img src="__IMG__/back.png"/>
	</a>
</div>
<div class="home rom homea">
	<div class="user">
		<div class="head" id="my_head">
			<a href="javascript:void(0)"><img id="change_head" src="<?php echo $member_info['head']; ?>" alt=""/></a>
			<p>
				<span>
					<b>账号：</b><?php echo $member_info['mobile']; ?>
				</span>
				<span>
					<b>余芬：</b><?php echo $member_info['money']; ?>
                </span>
			</p>
			<b class="both"></b>
		</div>
	</div>
	<div class="user-li money">
		<ul>
			<li>
				<a href="<?php echo url('notice/index'); ?>">
					<img src="__IMG__/user-yao.png" alt=""/>
					<span>邀请好友</span>
					<b>
						<img src="__IMG__/r.png" alt=""/>
					</b>
				</a>
			</li>
			<li>
				<a href="<?php echo url('recharge/index'); ?>">
					<img src="__IMG__/user-tz.png" alt=""/>
					<span>在线上芬</span>
					<b>
						<img src="__IMG__/r.png" alt=""/>
					</b>
				</a>
			</li>
			<li>
				<a href="<?php echo url('mine/withdraw'); ?>">
					<img src="__IMG__/user-zd.png" alt=""/>
					<span>快速下芬</span>
					<b>
						<img src="__IMG__/r.png" alt=""/>
					</b>
				</a>
			</li>
			<li>
				<a href="<?php echo url('mine/recharge_record'); ?>">
					<img src="__IMG__/user-fx.png" alt=""/>
					<span>上芬记录</span>
					<b>
						<img src="__IMG__/r.png" alt=""/>
					</b>
				</a>
			</li>
			<li>
				<a href="<?php echo url('mine/withdraw_record'); ?>">
					<img src="__IMG__/user-tx.png" alt=""/>
					<span>下芬记录</span>
					<b>
						<img src="__IMG__/r.png" alt=""/>
					</b>
				</a>
			</li>
			<li>
				<a href="<?php echo url('mine/betting_record'); ?>">
					<img src="__IMG__/user-xzjl.png" alt=""/>
					<span>下注记录</span>
					<b>
						<img src="__IMG__/r.png" alt=""/>
					</b>
				</a>
			</li>
			<li>
				<a href="<?php echo url('mine/member_down'); ?>">
					<img src="__IMG__/user-yao.png" alt=""/>
					<span>我的下级</span>
					<b>
						<img src="__IMG__/r.png" alt=""/>
					</b>
				</a>
			</li>
		</ul>
	</div>
	<div class="user-li money">
		<button onclick="location.href='<?php echo url('login/index'); ?>'">安全退出</button>
	</div>
</div>
<div class="footer">
    <a href="<?php echo url('index/index'); ?>"  <?php if($controllerName=='index'): ?> class="active" <?php endif; ?>>
        <?php if($controllerName=='index'): ?>
          <img src="__IMG__/home-h.png" alt=""/>
        <?php else: ?>
           <img src="__IMG__/home.png" alt=""/>
        <?php endif; ?>
        <span>首页</span>
    </a>
    <a href="<?php echo url('recharge/index'); ?>" <?php if($controllerName=='recharge'): ?> class="active" <?php endif; ?>>
        <?php if($controllerName=='recharge'): ?>
        <img src="__IMG__/money-h.png" alt=""/>
        <?php else: ?>
        <img src="__IMG__/money.png" alt=""/>
        <?php endif; ?>
        <span>上芬</span>
    </a>
    <a href="<?php echo url('notice/index'); ?>" <?php if($controllerName=='notice'): ?> class="active" <?php endif; ?>>
        <?php if($controllerName=='notice'): ?>
        <img src="__IMG__/fuwu-h.png" alt=""/>
        <?php else: ?>
        <img src="__IMG__/fuwu.png" alt=""/>
        <?php endif; ?>
        <span>邀请好友</span>
    </a>
    <a href="<?php echo url('mine/index'); ?>" <?php if($controllerName=='mine'): ?> class="active" <?php endif; ?>>
        <?php if($controllerName=='mine'): ?>
        <img src="__IMG__/my-h.png" alt=""/>
        <?php else: ?>
        <img src="__IMG__/my.png" alt=""/>
        <?php endif; ?>
        <span>我的</span>
    </a>
</div>


</body>
<script src="__JS__/jquery-1.7.1.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="__JSA__/Plupload/plupload.full.min.js"></script>
<script type="text/javascript">
    var ids = new Array("my_head");
    $.each(ids,function(i,n){
        var self = this.toString();
        var uploader_avatar = new plupload.Uploader({
            runtimes: 'gears,html5,html4,silverlight,flash', //上传插件初始化选用那种方式的优先级顺序
            browse_button: self, // 上传按钮
            url: "<?php echo url('upload_head'); ?>", //远程上传地址
            flash_swf_url: '__DTSC__/Moxie.swf',//flash文件地址
            silverlight_xap_url: '__DTSC__/Moxie.xap', //silverlight文件地址
            filters: {
                max_file_size: '100mb', //最大上传文件大小（格式100b, 10kb, 10mb, 1gb）
                mime_types: [//允许文件上传类型
                    {title: "files", extensions: "jpg,png,gif,jpeg"}
                ]
            },
            multi_selection: false, //true:ctrl多文件上传, false 单文件上传
            init: {
                FilesAdded: function(up, files) { //文件上传前
                    uploader_avatar.start();
                },
                UploadProgress: function(up, file) { //上传中，显示进度条

                },
                FileUploaded: function(up, file, info) { //文件上传成功的时候触发
                    var data = eval("(" + info.response + ")");//解析返回的json数据
                    var pic  = data.pic;
                    if(data.status == 1){
                        $('#change_head').attr('src',pic);
                        my_error("修改成功");
                    }else{
                        my_error("修改失败");
                    }
                },
                Error: function(up, err) { //上传出错的时候触发
                    alert(err.message);
                }
            }
        });
        uploader_avatar.init();

    });

</script>
<script>
</script>
</html>