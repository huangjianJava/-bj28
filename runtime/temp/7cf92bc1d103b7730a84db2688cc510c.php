<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:66:"/home/wwwroot/dwj/public/../app/home/view/mine/betting_record.html";i:1534861811;}*/ ?>
<!DOCTYPE html>
<html>
<head lang="en">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
	<title>天猫国际</title>
	<link rel="stylesheet" href="__CSS__/css.css"/>
	<script type="text/javascript" src="__JS__/jquery-2.1.0.js"></script>
	<script type="text/javascript" src="__JS__/js.js"></script>
</head>
<body>

<div class="header">
	下注记录
	<a onclick="window.history.go(-1)">
		<img src="__IMG__/back.png"/>
	</a>
</div>
<div class="home home-a">
	<div class="user-zu">
		<table cellspacing="0">
			<tr>
				<th>期号</th>
				<th>内容</th>
				<th>状态</th>
				<th>盈亏</th>
				<th>余芬</th>
				<th>时间</th>
			</tr>
			<?php foreach($data as $k=>$v): ?>
			<tr>
				<td><?php echo $v['stage']; ?></td>
				<td><?php echo $v['number']; ?>/<?php echo $v['money']; ?>芬</td>
				<?php if($v['state']==1): ?>
				<td style="color: #2caa00">已开奖</td>
				<?php else: ?>
				<td style="color: red">未开奖</td>
				<?php endif; if($v['code']==1): ?>
				<td>+<?php echo $v['z_money']; ?></td>
				<?php else: ?>
				<td>-<?php echo $v['money']; ?></td>
				<?php endif; ?>
				<td style="color: red"><?php echo $v['balance']; ?></td>
				<td><?php echo date('m-d H:i',$v['create_at']); ?></td>
			</tr>
			<?php endforeach; ?>
		</table>
	</div>
</div>
<script>

</script>
</body>
</html>