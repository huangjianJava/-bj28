<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:63:"/home/wwwroot/dwj/public/../app/home/view/mine/member_down.html";i:1534861848;}*/ ?>
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
	我的下级(总计：<?php echo $count; ?>人)
	<a onclick="window.history.go(-1)">
		<img src="__IMG__/back.png"/>
	</a>
</div>
<div class="home home-a">
	<div class="user-zu">
		<table cellspacing="0">
			<tr>
				<th>ID</th>
				<th>用户名</th>
				<th>时间</th>
			</tr>
			<?php foreach($list as $k=>$v): ?>
			<tr>
				<td><?php echo $v['id']; ?></td>
				<td><?php echo $v['mobile']; ?></td>
				<td><?php echo $v['create_at']; ?></td>
			</tr>
			<?php endforeach; ?>
		</table>
	</div>
</div>
<script>

</script>
</body>
</html>