<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:58:"/home/wwwroot/dwj/public/../app/home/view/login/index.html";i:1534861777;}*/ ?>
<!DOCTYPE html>
<html>
<head lang="en">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
	<title>天猫国际</title>
	<link rel="stylesheet" href="__CSS__/css.css"/>
</head>
<body>
<div class="login">
	<div class="login-but">
		<a class="wx" href="<?php echo url('login/login_index'); ?>">
			<img src="__IMG__/number.png" alt=""/>
			<span>账号登录</span>
		</a>
		<a class="num" href="<?php echo url('login/wx_login'); ?>">
			<img src="__IMG__/wx.png" alt=""/>
			<span>微信登录</span>
		</a>
	</div>
</div>
</body>
</html>