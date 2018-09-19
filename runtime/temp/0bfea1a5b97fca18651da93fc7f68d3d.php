<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:61:"/home/wwwroot/dwj/public/../app/home/view/recharge/index.html";i:1534861932;s:57:"/home/wwwroot/dwj/public/../app/home/view/common/nav.html";i:1526706822;}*/ ?>
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
	<script src="__JS__/js.js"></script>
</head>
<body>
<div class="tanc">
	<div class="cc">
		<a class="clos" onclick="tanc()">
			<img src="__IMG__/close.png"/>
		</a>
		<h1>
			<img src="__IMG__/1.jpg"/>
			<p>
				<span>天猫国际客服</span>
				<!--<span>-->
						<!--<b>上海 金山</b>-->
				<!--</span>-->
			</p>
			<b class="both"></b>
		</h1>
		<img src="<?php echo $wx_url; ?>" class="er"/>
		<p class="tex">扫一扫上方二维码，加我微信</p>
	</div>
</div>
<div class="header">
	上芬
	<a onclick="window.history.go(-1)">
		<img src="__IMG__/back.png"/>
	</a>
</div>
<div class="home homea">
	<div class="top">
		<p>
			余芬：<?php echo $member_info['money']; ?>
		</p>
	</div>
	<div class="ul">
		<a href="<?php echo url('recharge/wx_pay'); ?>">
			<img src="__IMG__/wxpay.png"/>
			<span>微信支付</span>
		</a>
		<a href="<?php echo url('recharge/ali_pay'); ?>">
			<img src="__IMG__/zfbpay.png"/>
			<span>支付宝支付</span>
		</a>
	</div>
	<div class="button">
		<button class="gree" onclick="window.location.href='<?php echo url('mine/recharge_record'); ?>'">查看上芬记录</button>
		<button onclick="tanc()"  class="blue">联系在线客服</button>
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