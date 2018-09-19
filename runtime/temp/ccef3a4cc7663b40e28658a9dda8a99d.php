<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:67:"/home/wwwroot/dwj/public/../app/home/view/mine/withdraw_record.html";i:1530373550;}*/ ?>
<!DOCTYPE html>
<html>
<head lang="en">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
	<title>宝马国际</title>
	<link rel="stylesheet" href="__CSS__/css.css"/>
	<script type="text/javascript" src="__JS__/jquery-2.1.0.js"></script>
	<script type="text/javascript" src="__JS__/js.js"></script>
</head>
<body>

<div class="header">
	下芬记录
	<a onclick="window.history.go(-1)">
		<img src="__IMG__/back.png"/>
	</a>
</div>
<div class="home home-a">
	<div class="game-f">
		<ul>
			<?php foreach($data as $k=>$v): ?>
			<li>
				<?php echo date('Y-m-d H:i:s',$v['create_at']); ?>
				<span>
						<?php if($v['status']==1): ?>
							<b style="color:#2fa2de ">审核中</b>
							+<?php echo $v['money']; elseif($v['status']==2): ?>
							<b style="color:#cf3138 ">下芬成功</b>
							+<?php echo $v['money']; elseif($v['status']==3): ?>
							 <b style="color:#868686 ">下芬失败</b>
							+<?php echo $v['money']; endif; ?>
					</span>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>

</body>
</html>