<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:62:"/home/wwwroot/dwj/public/../app/home/view/recharge/wx_pay.html";i:1534861938;}*/ ?>
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
	充值
	<a onclick="window.history.go(-1)">
		<img src="__IMG__/back.png"/>
	</a>
</div>
<div class="home home-a">
	<div class="er">
		<img src="<?php echo $wx_data['qrcode']; ?>" alt="" />
	</div>
	<div class="pay-text">
		<p class="wx">
			<img src="__IMG__/wap.png" alt="" />
			微信支付
		</p>
		<p>
			<span>充值用户：<?php echo $wx_data['k_name']; ?></span>
		</p>
		<p class="wx">
			<a>
				请使用微信扫描二维码完成支付
			</a>
		</p>
		<p class="input">
			充值金额：
			<input type="number" id="wx_money" placeholder="请输入充值金额"/>
		</p>

		<p class="l">
			<a>温馨提示</a>
		</p>
		<p class="l">
			识别二维码<a>付款后</a>再填写金额进行提交
		</p>
		<p>
			<button class="sub" id="wx_tj">提交</button>
		</p>
	</div>
</div>
</body>
<script src="__JS__/jquery-1.7.1.min.js" type="text/javascript" charset="utf-8"></script>
<script src="__JS__/layer_mobile/layer.js" ></script>
<script src="__JS__/rooms.js" ></script>
<script>
    window.onload=function(){
        $('.wx').show();
    }
    $('#wx_tj').click(function () {
        var money    = $('#wx_money').val();
        if(money==""){
            my_error('请填写充值金额');
            return false;
        }
        $.ajax({
            type: "post",
            url: "<?php echo url('recharge/offline_charge'); ?>",
            data: {
                'money': money,
                'way':1
            },
            dataType: "json",
            async: false,
            success: function(data) {
                var ret = data.ret;
                var msg = data.msg;
                if(ret=='200'){
                    var url = "<?php echo url('game/cqssc'); ?>"
                    my_success(msg,url);
                }else{
                    my_error(msg);
                }
            }
        });
    });
</script>
</html>