<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:64:"/home/wwwroot/dwj/public/../app/home/view/login/login_index.html";i:1534861785;}*/ ?>
<!DOCTYPE html>
<html>
<head lang="en">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
	<title>天猫国际</title>
	<link rel="stylesheet" href="__CSS__/css.css"/>
</head>
<body>
<div class="head">
	<img src="__IMG__/login-bac.png" alt=""/>
</div>
<div class="login-num">
	<ul>
		<li>
			<img src="__IMG__/phone.png" alt=""/>
			<input type="text" id="mobile" placeholder="请输入账号"/>
		</li>
		<li>
			<img src="__IMG__/code.png" alt=""/>
			<input type="password" id="password" placeholder="请输入密码"/>
		</li>
	</ul>
	<button class="sub" onclick="do_login()">确认</button>
	<p>
		<a href="<?php echo url('login/register'); ?>">立即注册</a>
	</p>
</div>

</body>
<script src="__JS__/jquery-1.7.1.min.js" type="text/javascript" charset="utf-8"></script>
<script src="__JS__/layer_mobile/layer.js" ></script>
<script src="__JS__/rooms.js" ></script>
<script>
    function do_login() {
        var mobile   = $('#mobile').val();
        var password = $('#password').val();
        if(!mobile){
            my_error("请填写账号");
            return false;
        }
        if(!password){
            my_error("请填写密码");
            return false;
        }
        $.ajax({
            type: "post",
            url: "<?php echo url('do_login'); ?>",
            data: {'mobile':mobile,'password':password},
            dataType: "json",
            async: false,
            success: function(data) {
                var ret  = data.ret;
                var info = data.msg;
                if(ret=='200'){
                    var url = "<?php echo url('index/index'); ?>";
                    my_success(info,url);
                }else {
                    my_error(info);
                }
            }
        });
    }

</script>
</html>