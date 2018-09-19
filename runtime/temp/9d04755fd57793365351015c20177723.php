<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:56:"/home/wwwroot/dwj/public/../app/home/view/game/rule.html";i:1534861753;}*/ ?>
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
<div class="header">
	游戏规则
	<a class="b" onclick="window.history.go(-1)">
		<img src="__IMG__/back.png" alt=""/>
	</a>
</div>
<div  class="home rom">
	<div class="n">
		<?php echo $data['play_rule']; ?>
	</div>
</div>
</body>
</html>