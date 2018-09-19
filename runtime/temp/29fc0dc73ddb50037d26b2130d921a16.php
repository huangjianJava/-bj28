<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:59:"/home/wwwroot/dwj/public/../app/home/view/notice/index.html";i:1534861910;s:57:"/home/wwwroot/dwj/public/../app/home/view/common/nav.html";i:1526706822;}*/ ?>
<!DOCTYPE html>
<html>
<head lang="en">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
	<meta name = "format-detection" content = "telephone=no">
	<title>天猫国际</title>
	<link rel="stylesheet" href="__CSS__/swiper.min.css"/>
	<link rel="stylesheet" href="__CSS__/css.css"/>
	<script src="__JS__/jquery-2.1.0.js"></script>
	<script src="__JS__/swiper.min.js"></script>
</head>
<body>
<div class="header">
	邀请好友
	<a onclick="window.history.go(-1)">
		<img src="__IMG__/back.png"/>
	</a>
</div>
<div class="home homea">
	<div class="img">
		<img  src="<?php echo url('notice/ewm',array('uid'=>$uid)); ?>" alt="" />
	</div>
	<!--<div class="gree fend">-->
		<!--<p>邀请链接</p>-->
		<!--<p>-->
			<!--<span><?php echo $url; ?></span>-->
		<!--</p>-->
	<!--</div>-->
	<div class="gree fend">
		<p>温馨提示：</p>
		<p>
			<span>1.长按图片，保存手机相册，分享给朋友</span>
			<span>2.分享朋友，还可以找客服领取福利哦</span>
		</p>
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
<script>
</script>
</html>