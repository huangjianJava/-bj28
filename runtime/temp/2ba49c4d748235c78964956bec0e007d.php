<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:61:"/home/wwwroot/dwj/public/../app/home/view/login/register.html";i:1534861791;}*/ ?>
<!DOCTYPE html>
<html>
<head lang="en">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
	<title>天猫国际</title>
	<link rel="stylesheet" href="__CSS__/css.css"/>
</head>
<body>
<div class="header">
	注册
	<a onclick="window.history.go(-1)">
		<img src="__IMG__/back.png"/>
	</a>
</div>
<div class="login-num">
	<ul>
		<li>
			<img src="__IMG__/phone.png" alt=""/>
			<input type="text" id="mobile" placeholder="请输入账号"/>
		</li>
		<li>
			<img src="__IMG__/code.png" alt=""/>
			<input type="password" id="password_one" placeholder="请输入密码"/>
		</li>
		<li>
			<img src="__IMG__/code.png" alt=""/>
			<input type="password" id="password_two" placeholder="请确认密码"/>
		</li>
		<li>
			<img src="__IMG__/yan.png" alt=""/>
			<input type="text" id="nickname" placeholder="请输入昵称"/>
		</li>
	</ul>
	<button class="sub" id="doRegister">确认</button>
	<p>
		已有账号，
		<a href="<?php echo url('login/login_index'); ?>">去登陆</a>
	</p>

</div>
</body>
<script src="__JS__/jquery-1.7.1.min.js" type="text/javascript" charset="utf-8"></script>
<script src="__JS__/layer_mobile/layer.js" ></script>
<script src="__JS__/rooms.js" ></script>
<script type="text/javascript">
    $('#doRegister').click(function(){
        var mobile       = $('#mobile').val();
        var password_one = $('#password_one').val();
        var password_two = $('#password_two').val();
        var nickname     = $('#nickname').val();
        if(nickname == '' || nickname == null){
            my_error('请输入昵称');
            return false;
        }
        if(nickname.length>7){
            my_error('昵称长度在1-7位');
            return false;
		}
        if(password_one!=password_two){
            my_error('两次输入密码不一致');
            return false;
        }
        if(!mobile){
            my_error("请填写账号");
            return false;
        }
        $.ajax({
            type: "post",
            url: "<?php echo url('login/do_register'); ?>",
            data: {
                "mobile": mobile,
                'password': password_one,
                'nickname': nickname
            },
            dataType: "json",
            async: false,
            success: function(data) {
                var ret  = data.ret;
                var info = data.msg;
                if(ret=='200'){
                    var url = "<?php echo url('login/login_index'); ?>";
                    my_success(info,url);
                }else {
                    my_error(info);
                }
            }
        });

    });
</script>
</html>