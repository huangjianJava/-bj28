<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:58:"/home/wwwroot/dwj/public/../app/home/view/index/index.html";i:1534861766;s:57:"/home/wwwroot/dwj/public/../app/home/view/common/nav.html";i:1526706822;}*/ ?>
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
<div class="heada">
	游戏大厅
	<span>余芬：<?php echo $member_info['money']; ?></span>
	<a onclick="tanc()">客服</a>
</div>
<div class="home">
	<div class="banner">
		<div class="swiper-container">
			<div class="swiper-wrapper">
				<div class="swiper-slide">
					<img src="__IMG__/banner@2x.png" alt=""/>
				</div>
				<div class="swiper-slide">
					<img src="__IMG__/banner@2x.png" alt=""/>
				</div>
				<div class="swiper-slide">
					<img src="__IMG__/banner@2x.png" alt=""/>
				</div>
			</div>
		</div>
	</div>
	<div class="room">
		<div class="a">
			<img src="__IMG__/ssc.png" alt="" />
			<!--<p>-->
				<!--在线人数：<?php echo $online_number; ?>-->
			<!--</p>-->
			<a href="<?php echo url('game/cqssc'); ?>">进入房间</a>
		</div>
		<div class="a no-a">
			<img src="__IMG__/car.png" alt="" />
			<!--<p>-->
				<!--在线人数：123-->
			<!--</p>-->
			<a href="">进入房间</a>
			<span>
    				<b>敬请期待</b>
    		</span>
		</div>
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
    var swiper = new Swiper('.swiper-container',{
        loop:true,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
    });
    var swipera = new Swiper('.swiper-containera', {
        direction: 'vertical',
        loop:true,
        autoplay: {
            delay: 2000,
            disableOnInteraction: false,
        },
    });
</script>
</html>