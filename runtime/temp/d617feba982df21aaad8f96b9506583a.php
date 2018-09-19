<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:57:"/home/wwwroot/dwj/public/../app/home/view/game/cqssc.html";i:1535126708;}*/ ?>
<!DOCTYPE html>
<html>
<head lang="en">
	<meta charset="UTF-8">
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport">
	<meta content="yes" name="apple-mobile-web-app-capable">
	<meta content="black" name="apple-mobile-web-app-status-bar-style">
	<meta name = "format-detection" content = "telephone=no">
	<title>天猫国际</title>
	<link rel="stylesheet" href="__CSS__/css.css"/>
	<script src="__JS__/jquery-2.1.0.js"></script>
	<style>
		.txta-main ul .msga .ts span .ti-l {
			width: 100%;
			color: #eba53e;
			font-style: normal;
		}
		i{
			font-style: normal;
		}
	</style>

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
		<p class="tex">长按识别二维码，加我微信</p>
	</div>
</div>
<div class="header remove">
	<a class="back" href="<?php echo url('index/index'); ?>">
		<img src="__IMG__/back.png"/>
	</a>
	重庆时时彩
	<a onclick="tanc()" class="lix">客服</a>
	<span class="a">
        <img src="__IMG__/mor.png" alt=""/>
    </span>
</div>
<div class="title remove">
	<div class="title-top">
		<p>
			<span>	 距离<?php echo $stage_data['stage_next']; ?>期开奖</span>
			<span>
                <img src="__IMG__/time.png" alt=""/>
                <b id="demo04">
					<input type="hidden" value="<?php echo $member_info['nickname']; ?>" id="nickname">
                    <input type="hidden" value="<?php echo $member_info['head']; ?>" id="head">
					<input type="hidden" value="<?php echo $uid; ?>" id="uid">
                    <input type="hidden" value="<?php echo $open; ?>" id="open">
					<input type="hidden" value="<?php echo $stage_data['stage_next']; ?>" id="next_stage">
					<input type="hidden" value="<?php echo $cha; ?>" id="cha">
					<input type="hidden" value="<?php echo $zhang_info['dao_time']; ?>" id="dao_time">
					<input type="hidden" value="<?php echo $zhang_info['feng_time']; ?>" id="feng_time">
					<input type="hidden" value="<?php echo $zhang_info['jing_time']; ?>" id="jing_time">
					<input type="hidden" value="<?php echo $notice0['call_time']; ?>" id="call_time0">
					<input type="hidden" value="<?php echo $notice1['call_time']; ?>" id="call_time1">
					<input type="hidden" value="<?php echo $notice2['call_time']; ?>" id="call_time2">
					<input type="hidden" value="<?php echo $notice3['call_time']; ?>" id="call_time3">
					<input type="hidden" value="<?php echo $notice4['call_time']; ?>" id="call_time4">
					<input type="hidden" value="<?php echo $notice0['info']; ?>" id="info0">
					<input type="hidden" value="<?php echo $notice1['info']; ?>" id="info1">
					<input type="hidden" value="<?php echo $notice2['info']; ?>" id="info2">
					<input type="hidden" value="<?php echo $notice3['info']; ?>" id="info3">
					<input type="hidden" value="<?php echo $notice4['info']; ?>" id="info4">
					<input type="hidden" value="<?php echo $notice0['content']; ?>" id="content0">
					<input type="hidden" value="<?php echo $notice1['content']; ?>" id="content1">
					<input type="hidden" value="<?php echo $notice2['content']; ?>" id="content2">
					<input type="hidden" value="<?php echo $notice3['content']; ?>" id="content3">
					<input type="hidden" value="<?php echo $notice4['content']; ?>" id="content4">
					<input type="hidden" value="<?php echo $zhang_info['zhang']; ?>" id="zhang">
					<input type="hidden" value="<?php echo $is_on; ?>" id="is_on">
					<input type="hidden" value="<?php echo $ten; ?>" id="ten">
                	<i class="minute">00</i>&nbsp;分
			        <i class="second">00</i>&nbsp;秒
					<i id="open_notice"></i>
                </b>

            </span>
		</p>
		<p>
			<a href="<?php echo url('mine/index'); ?>"><img class="simg" src="<?php echo $member_info['head']; ?>" alt="" /></a>
			<span class="sim">
           	<b class="name"><?php echo $member_info['nickname']; ?></b>
			<b>&nbsp;&nbsp;余芬: <i style="font-size:13px" id="balance"><?php echo $member_info['money']; ?></i></b>
           	<b>&nbsp;&nbsp;ID:<?php echo $member_info['id']; ?>&nbsp;&nbsp;&nbsp;在线:<?php echo $online_number; ?></b>
           </span>
			<b class="both"></b>
		</p>
		<b class="both"></b>
	</div>

	<div class="title-b">
		<div class="first">
			第<b><?php echo $stage_data['stage']; ?></b>期
			<span><a><?php echo $stage_data['number'][0]; ?></a>
				<a><?php echo $stage_data['number'][1]; ?></a>
				<a><?php echo $stage_data['number'][2]; ?></a>
				<a><?php echo $stage_data['number'][3]; ?></a>
				<a><?php echo $stage_data['number'][4]; ?></a>
				(<?php echo $stage_data['detail']; ?>)</span>
			<img src="__IMG__/input.png" alt=""/>
		</div>
		<div class="table"  style="height: 350px;overflow: auto;-webkit-overflow-scrolling: touch">
			<table cellspacing="0">
				<tr>
					<th>期号</th>
					<th>开奖结果</th>
				</tr>
				<?php foreach($number_history as $k=>$v): ?>
				<tr>
					<td> 第<b><?php echo $v['stage']; ?></b>期</td>
					<td><span>
						<a><?php echo $v['number'][0]; ?></a>
						<a><?php echo $v['number'][1]; ?></a>
						<a><?php echo $v['number'][2]; ?></a>
						<a><?php echo $v['number'][3]; ?></a>
						<a><?php echo $v['number'][4]; ?></a>
						(<?php echo $v['detail']; ?>)
					  </span>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>
		</div>

	</div>


</div>



<div style="max-width: 550px;margin: auto;left: 0;right: 0;position: absolute;top: 42px">
	<div class="t">
		<img src="__IMG__/t_01.png" alt=""/>
		<ul>
			<li><a href="<?php echo url('recharge/index'); ?>">在线上分</a></li>
			<li><a href="<?php echo url('mine/withdraw'); ?>">快速下分</a></li>
			<li><a href="<?php echo url('game/rule'); ?>">玩法规则</a></li>
			<li><a href="<?php echo url('mine/betting_record'); ?>">下注记录</a></li>
			<li><a href="<?php echo url('notice/index'); ?>">邀请好友</a></li>
		</ul>
	</div>
</div>

<div class="home remove game padd"  id="text">

	<div class="txta" id="txta">
		<div class="gg remove" style="background: #aeaeae;border-radius: 8px;width: 80%;margin: auto;margin-top: 2%;padding-bottom: 0">
			<marquee direction="left" class="left"style="width: 100%;margin: auto" >
				<p>
					<img src="__IMG__/gg.png"/>
					<span style="color: #fff">
				<?php echo $notice['info']; ?>
			</span>
				</p>
			</marquee>
		</div>
		<div class="txta-main">
			<ul id="show">
				<?php foreach($chat_room as $kk=>$vv): if($vv['msg_type']==1): ?>
					<li <?php if($uid==$vv['uid']): ?>class="my"<?php endif; ?> >
						<span class="head"><img style="border-radius: 50%" src="<?php echo $vv['head']; ?>"/></span>
						<p><span style="width: 100%">
										   <b><?php echo $vv['nickname']; ?></b>
										   <a><?php echo $vv['time']; ?></a>
										  </span>
							<b class="both"></b>
							<span class="take">
										<img style="width: 15px;" src="__IMG__/icon_time.png" alt=""/><?php echo $vv['stage']; ?>期
										<br/>
										投注：<?php echo $vv['content']; ?>
									</span>
						</p>
						<b class="both"></b>
					</li>
				<?php elseif($vv['msg_type']==2): ?>
					<li class="msga">
						<p class="ts">
							<span class="tile"><i style="float: none;margin: auto;text-align: center">★★★★★竞猜核对★★★★★</i></span>
							<span class="border"></span>
							<?php foreach($vv['content'] as $kkk=>$vvv): ?>
							<span class="fle">
										 <b><?php echo $vvv['nickname']; ?></b>
										 <i>投注 <?php echo $vvv['content']; ?></i>
										 <i>剩余 <?php echo $vvv['balance']; ?></i>
										 <label class="both"></label>
								   </span>
							<?php endforeach; ?>
							<span class="border"></span>
							<i class="tile">以系统投注记录显示为准</i>
						</p >
					</li>
				<?php elseif($vv['msg_type']==3): ?>
					<li class="msga">
						<p class="ts">
							<span class="tile"><i style="float: none;margin: auto;text-align: center"><?php echo $zhang_info['zhang']; ?></i></span>
							<span class="border"></span>
							<?php foreach($vv['content'] as $kkk=>$vvv): ?>
							<span class="fle">
											 <b><?php echo $vvv['nickname']; ?></b>
											 <i>余芬 <?php echo $vvv['balance']; ?></i>
											 <label class="both"></label>
									   </span>
							<?php endforeach; ?>
							<span class="border"></span>
							<i class="tile">上分点击右上角添加客服</i>
						</p >
					</li>
				<?php endif; endforeach; ?>
				<li class="msga">
					<p class="ts" style="background: #aeaeae">
						<span >
                			<i class="ti-r">仅显示前50条竞猜记录！</i>
                			<label class="both"></label>
                		</span>
					</p>
				</li>
			</ul>
			<b class="both"></b>
		</div>
	</div>
</div>
<div class="footer foot">

	<button class="red" onclick="refresh()">刷新</button>
	<input type="text" onclick="del(1)" placeholder="快速下注" readonly/>

	<button class="red" onclick="del(1)">投注</button>
</div>
<div class="start">
	<div class="start-top">
		<div class="top-con">
			<div class="zhu-all" id="div">
				<div class="zhu-sw" id="show_item">
					<h1>快速下注</h1>
					<div class="sw-head">
						<ul>
							<li>大<i>(1.8)</i>
								<input type="hidden" value="14" >
								<input type="hidden" value="大" >
							</li>
							<li>小<i>(1.8)</i>
								<input type="hidden" value="14" >
								<input type="hidden" value="小" >
							</li>
							<li>单<i>(1.8)</i>
								<input type="hidden" value="14" >
								<input type="hidden" value="单" >
							</li>
							<li>双<i>(1.8)</i>
								<input type="hidden" value="14" >
								<input type="hidden" value="双" >
							</li>
						</ul>
												<style>							.start-top .top-con .sw-head .ulm li{								width: 29%;							}							.start-top .top-con .sw-head .ulm{								width: 75%;								margin: auto;							}						</style>						<ul class="ulm">
							<li>龙<i>(1.8)</i>
								<input type="hidden" value="18" >
								<input type="hidden" value="龙" >
							</li>
							<li>虎<i>(1.8)</i>
								<input type="hidden" value="18" >
								<input type="hidden" value="虎" >
							</li>
							<li>和<i>(9)</i>
								<input type="hidden" value="18" >
								<input type="hidden" value="和" >
							</li>
						</ul>
						<b class="both"></b>
					</div>
					<ul>
						<li>0<i>(9)</i>
							<input type="hidden" value="13" >
							<input type="hidden" value="0" >
						</li>
						<li>1<i>(9)</i>
							<input type="hidden" value="13" >
							<input type="hidden" value="1" >
						</li>
						<li>2<i>(9)</i>
							<input type="hidden" value="13" >
							<input type="hidden" value="2" >
						</li>
						<li>3(9)
							<input type="hidden" value="13" >
							<input type="hidden" value="3" >
						</li>
						<li>4<i>(9)</i>
							<input type="hidden" value="13" >
							<input type="hidden" value="4" >
						</li>
						<b class="both"></b>
					</ul>
					<ul>
						<li>5<i>(9)</i>
							<input type="hidden" value="13" >
							<input type="hidden" value="5" >
						</li>
						<li>6<i>(9)</i>
							<input type="hidden" value="13" >
							<input type="hidden" value="6" >
						</li>
						<li>7<i>(9)</i>
							<input type="hidden" value="13" >
							<input type="hidden" value="7" >
						</li>
						<li>8<i>(9)</i>
							<input type="hidden" value="13" >
							<input type="hidden" value="8" >
						</li>
						<li>9<i>(9)</i>
							<input type="hidden" value="13" >
							<input type="hidden" value="9" >
						</li>
						<b class="both"></b>
					</ul>
					<div class="sw-head">
						<button onclick="window.location.href='<?php echo url('game/rule'); ?>'">规则说明</button>
						<button onclick="window.location.href='<?php echo url('recharge/index'); ?>'">上分</button>
						<button onclick="window.location.href='<?php echo url('mine/withdraw'); ?>'">下分</button>
						<button onclick="window.location.href='<?php echo url('mine/betting_record'); ?>'">投注记录</button>
					</div>
				</div>
			</div>
			<b class="both"></b>
			<p>投注积分：<input type="number" id="num" placeholder="请输入投注积分"/><button onclick="betting()">投注</button></p>
		</div>
	</div>

</div>



</body>
<script src="__JS__/js.js"></script>
<script src="__JS__/jquery-swiper.js"></script>
<script src="__JS__/layer_mobile/layer.js" ></script>
<script src="__JS__/rooms.js" ></script>
<script type="text/javascript">
    $(function(){
        var open = $('#open').val();
        countDown(open,null,"#demo04 .hour","#demo04 .minute","#demo04 .second");
        connect();
        get_balance();
    });
</script>
<script>
    $(function () {
        var isPageHide = false;
        window.addEventListener('pageshow', function () {
            if (isPageHide) {
                window.location.reload();
            }
        });
        window.addEventListener('pagehide', function () {
            isPageHide = true;
        });
    });
    //点击刷新
	function refresh() {
        window.location.reload();
    }
    //控制投注匡的显示
    function del(id){
        $('.start').show();
        if(id == 1){
            $('.zui').hide();
            $('.tou').show();
        }else{
            $('.tou').hide();
            $('.zui').show();
        }
    }
    //倒计时
    function countDown(time,day_elem,hour_elem,minute_elem,second_elem){
        var  sys_second = $('#cha').val();
        var  bb    = sys_second-7;
        var  cc    = sys_second-5;
        var  ten   = parseInt($('#ten').val())
        var dao_time   = parseInt($('#dao_time').val());
        var feng_time  = parseInt($('#feng_time').val());
        var jing_time  = parseInt($('#jing_time').val());
        var call_time0 = parseInt($('#call_time0').val());
        var call_time1 = parseInt($('#call_time1').val());
        var call_time2 = parseInt($('#call_time2').val());
        var call_time3 = parseInt($('#call_time3').val());
        var call_time4 = parseInt($('#call_time4').val());
        var timer = setInterval(function(){
            if (sys_second > 0) {
                sys_second -= 1;
                var day    = Math.floor((sys_second / 3600) / 24);
                var hour   = Math.floor((sys_second / 3600) % 24);
                var minute = Math.floor((sys_second / 60) % 60);
                var second = Math.floor(sys_second % 60);
                day_elem && $(day_elem).text(day);//计算天
                $(hour_elem).text(hour<10?"0"+hour:hour);//计算小时
                $(minute_elem).text(minute < 10 ? "0" + minute : minute);//计算分
                $(second_elem).text(second < 10 ? "0" + second : second);// 计算秒
                var aa = parseInt(sys_second);
                if (aa==bb){ //余芬统计
                    if(ten==0) {
                        if (sys_second > 240) {
                            feng_pan_zs();
                        }
                    }else{
                        if (sys_second > 540) {
                            feng_pan_zs();
                        }
					}
                }
                if (aa==cc){ //开盘提示
                    if(ten==0) {
                        if (sys_second > 240) {
                            come_in();
                        }
                    }else{
                        if (sys_second > 540) {
                            come_in();
                        }
					}
                }
                if(aa==dao_time){
                    feng_pan_notice();
                }
                if(aa==jing_time){
                    feng_pan_jchd();
                }
                if(aa==feng_time){
                    feng_pan_last() ;
                }
                if(call_time0>0) {
                    if (aa == call_time0) {
                        get_call(1);
                    }
                }
                if(call_time1>0) {
                    if (aa == call_time1) {
                        get_call(2);
                    }
                }
                if(call_time2>0) {
                    if (aa == call_time2) {
                        get_call(3);
                    }
                }
                if(call_time3>0) {
                    if (aa == call_time3) {
                        get_call(4);
                    }
                }
                if(call_time4>0) {
                    if (aa == call_time4) {
                        get_call(5);
                    }
                }

            } else {
                $('#open_notice').html('开奖中');
                var timer1 = setInterval(function () {
                    if (sys_second <= 0) {
                        location.reload();
                    } else {
                        clearInterval(timer1);
                    }
                }, 15000);
            }
        }, 1000);
    }
    //下注
    function betting() {
        var stage      = $('#next_stage').val();
        var nowTime    = CurentTime();
        var nickname   = $('#nickname').val();
        var head       = $('#head').val();
        var uid        = $('#uid').val();
        var b_number   = $('.top-con ul li.active').length;
        var is_on      = $('#is_on').val();
        if(is_on==0){
            my_error('封盘中，请开盘后再投注');
            return false;
		}
        if(b_number<=0){
            my_error('请选择投注项');
            return false;
        }
        var money = $('#num').val();
        if(money=="" || money==0){
            my_error('请输入投注积分');
            return false;
        }
        var time = $('#open').val();
        var time2 = time.replace(/\-/g, "/");
        var end_time = new Date(time2).getTime();
        var	 sys_second = (end_time-new Date().getTime())/1000;
        var  aa = parseInt(sys_second);
        var feng_time = parseInt($('#feng_time').val());
        if (aa<=feng_time){
            my_error('本期已封盘');
            return false;
        }
        var number_arr=[];
        var content= "";
        $('.top-con ul li.active').each(function () {
            var type   = $(this).find('input:eq(0)').val();
            var number = $(this).find('input:eq(1)').val();
            list = {};
            list['number']   = number;
            list['type']     = type;
            list['money']    = Math.ceil(money) ;
            content +=  number;
            number_arr.push(list);
        });
        content = content+'/'+Math.ceil(money);
        var bet_list = JSON.stringify(number_arr);
        $.ajax({
            type: "post",
            url: "<?php echo url('game/betting'); ?>",
            data: {'cate':5,'stage':stage,'hall':1,'list':bet_list,'content':content},
            dataType: "json",
            async: false,
            success: function(data) {
                var ret   = data.ret;
                var info   = data.msg;
                var balance = data.data.money;
                if(ret=='200'){
                    //定义消息 msg_type 1 投注消息
                    var s_content = {};
                    s_content['stage']   = stage;
                    s_content['content']  = content;
                    s_content['head']     = head;
                    s_content['time']     = nowTime;
                    s_content['uid']      = uid;
                    s_content['msg_type'] = 1;
                    s_content['room']     = 5;
                    s_content['nickname'] = nickname;
                    var s_d_content = JSON.stringify(s_content);
                    var send_msg = {'content':s_d_content,'type':'say','to_client_id':'all','to_client_name':'所有人','ext':'dasda'};
                    var last=JSON.stringify(send_msg);
                    ws.send(last);
                    $('#balance').html(balance);
                    $('.start').hide();
                    $('.top-con ul li.active').removeClass("active");
                    $('#num').val('');
                    my_error(info);
                }else{
                    my_error(info);
                }
            }
        });
    }
    //获取当前时间
    function CurentTime() {
        var now = new Date();
        var year = now.getFullYear();       //年
        var month = now.getMonth() + 1;     //月
        var day = now.getDate();            //日
        var hh = now.getHours();            //时
        var mm = now.getMinutes();          //分
        var ss = now.getSeconds();          //分
//				var clock = year + "-";
        var clock = "";
//				if(month < 10)
//					clock += "0";
//
//				clock += month + "-";
//
//				if(day < 10)
//					clock += "0";
//
//				clock += day + " ";

        if(hh < 10)
            clock += "0";

        clock += hh + ":";
        if (mm < 10) clock += '0';

        clock += mm+ ":";
        if (ss < 10) clock += '0';
        clock += ss;
        return(clock);
    }
    //封盘提示
    function feng_pan_notice() {
        var nowTime    = CurentTime();
        var stage      = $('#next_stage').val();
        var str = '<li class="msga">'
            +'<p>'+nowTime+'</p>'
            +'<p class="ts">'
            +'<span class="tile">【倒计时提示】</span>'
            +'<span>'
            // +'<i class="ti-l">'+stage+'期</i>'
            +'<i class="ti-l">距离封盘倒计时剩余50秒</i>'
            +'<i class="ti-l">请抓紧下注，上分请加客服</i>'
            +'<label class="both"></label>'
            +'</span>'
            +'</p>'
            +'</li>';
        $('#show').prepend(str);
    }
    //竞猜核对
    function feng_pan_jchd() {
        var nowTime    = CurentTime();
        var stage      = $('#next_stage').val();
        $.ajax({
            type: "post",
            url: "<?php echo url('game/get_record'); ?>",
            data: {'stage':stage},
            dataType: "json",
            async: true,
            success: function(data) {
                var ret   = data.ret;
                var info  = data.data;
                if(ret=='200'){
                    //定义消息 msg_type 1 投注消息
                    var str = '<li class="msga">'
                        +'<p>'+nowTime+'</p>'
                        +'<p class="ts">'
                        +'<span class="tile">★★★★★竞猜核对★★★★★</span>'
                        + '<span class="border"></span>'
                    for(var i=0;i<info.length;i++){
                        str += '<span class="fle">'
                            + '<b>'+info[i].nickname+'</b>'
                            + '<i>投注 '+info[i].content+'</i>'
                            +' <i>剩余 '+info[i].balance+'</i>'
                            +'<label class="both"></label>'
                            +'</span>'
                    }
                    str+= '<span class="border"></span>'
                        +'<i class="tile">以系统投注记录显示为准</i>'
                        +'</p>'
                        +'</li>';
                    $('#show').prepend(str);
                }else{
                    return false;
                }
            }
        });
    }
    //正式封盘
    function feng_pan_last() {
        var nowTime    = CurentTime();
        var str = '<li class="msga">'
            +'<p>'+nowTime+'</p>'
            +'<p class="ts">'
            +'<span class="tile">【本期封盘】</span>'
            +'<span>'
            +'<i class="ti-l">以下全接，以上不接</i>'
            +'<i class="tile">以投注记录显示为准</i>'
            +'<label class="both"></label>'
            +'</span>'
            +'</p>'
            +'</li>';
        $('#show').prepend(str);
    }
    //余芬统计
    function feng_pan_zs() {
        var nowTime    = CurentTime();
        var stage      = $('#next_stage').val();
        $.ajax({
            type: "post",
            url: "<?php echo url('game/get_record_yf'); ?>",
            data: {'stage':stage},
            dataType: "json",
            async: true,
            success: function(data) {
                var ret   = data.ret;
                var info = data.data;
                var zhang = $('#zhang').val();
                if(ret=='200'){
                    //定义消息 msg_type 1 投注消息
                    var str = '<li class="msga">'
                        +'<p>'+nowTime+'</p>'
                        +'<p class="ts">'
                        +'<span class="tile">'+zhang+'</span>'
                        + '<span class="border"></span>';
                    for(var i=0;i<info.length;i++){
                        str += '<span class="fle">'
                            + '<b>'+info[i].nickname+'</b>'
                            + '<i>余芬' +info[i].balance+'</i>'
                            +'<label class="both"></label>'
                            +'</span>'
                    }
                    str+= '<span class="border"></span>'
                        +'<i class="tile">上分点击右上角添加客服</i>'
                        +'</p>'
                        +'</li>';
                    $('#show').prepend(str);
                }else{
                    return false;
                }
            }
        });
    }
    //进入房间提示
    function come_in() {
        var nowTime    = CurentTime();
        var stage      = $('#next_stage').val();
        var str = '<li class="msga">'
            +'<p>'+nowTime+'</p>'
            +'<p class="ts">'
            +'<span class="tile">第'+stage+'期开放，请下注</span>'
            +'<span>'
        str +='<label class="both"></label>'
            +'</span>'
            +'</p>'
            +'</li>';
        $('#show').prepend(str);
    }
    //中奖
    function get_balance() {
        var timer1 = setInterval(function () {
			$.ajax({
				type: "post",
				url: "<?php echo url('get_balance'); ?>",
				data:"",
				dataType: "json",
				async: true,
				success: function(data) {
					var nowTime    = CurentTime();
					var is_zj           = data.data.is_zj;//中奖
					var balance         = data.data.balance;
					var zj_money        = data.data.zj_money;
					var tz_money        = data.data.tz_money;
					var str = '';
					if(is_zj==1) {
						str += '<li class="msga">'
							+ '<p>' + nowTime + '</p>'
							+ '<p class="ts" style="background: #007aff">'
							+ '<span class="tile" style="color: #fff">【结算提示】</span>'
							+ '<i class="ti-l" style="color: #fff">上期投注' + tz_money + '芬，中奖' + zj_money+' </i>'
							+ '<label class="both"></label>'
							+ '</span>'
							+ '</p>'
							+ '</li>';
					}
					var z_money = parseInt(zj_money);
					if(z_money>0) {
						$('#show').prepend(str);
					}
					$('#balance').html(balance);
				}
			});
        }, 20000);
    }
    //获取喊话消息
	function get_call(type) {
        if(type==1){
            var info    = $('#info0').val();
            var content = $('#content0').val();
		}else if(type==2){
            var info    = $('#info1').val();
            var content = $('#content1').val();
        }else if(type==3){
            var info    = $('#info2').val();
            var content = $('#content2').val();
        }else if(type==4){
            var info    = $('#info3').val();
            var content = $('#content3').val();
        }else if(type==5){
            var info    = $('#info4').val();
            var content = $('#content4').val();
        }
		var nowTime    = CurentTime();
		var str = '';
		str += '<li class="msga">'
			+ '<p>' + nowTime + '</p>'
			+ '<p class="ts" >'
			+ '<span>'
			+ '<span class="tile" >'+info+'</span>'
			+ '<i class="ti-l" >'+content+' </i>'
			+ '<label class="both"></label>'
			+ '</span>'
			+ '</p>'
			+ '</li>';
		$('#show').prepend(str);


    }

    document.onkeydown = function (event) {
        var e = event || window.event || arguments.callee.caller.arguments[0];
        if (e && e.keyCode == 13) {
            betting();
        }
    };
</script>

<script>
    // 连接服务端
    function connect() {
        // 创建websocket
        ws = new WebSocket("ws://"+'47.92.53.159'+":7272");
        // 当socket连接打开时，输入用户名
        ws.onopen = onopen;
        // 当有消息时根据消息类型显示不同信息
        ws.onmessage = onmessage;
        ws.onclose = function() {
            console.log("连接关闭，定时重连");
            connect();
        };
        ws.onerror = function() {
            console.log("出现错误");
        };
    }
    // 连接建立时发送登录信息
    function onopen() {
        var name      = $('#uid').val();
        // 登录
        var login_data = '{"type":"login","client_name":'+name+',"room_id":"5"}';
        //console.log("websocket握手成功，发送登录数据:"+login_data);
        ws.send(login_data);
    }
    // 服务端发来消息时
    function onmessage(e) {
        var data = JSON.parse(e.data);
        switch(data['type']){
            // 服务端ping客户端
            case 'ping':
                ws.send('{"type":"pong"}');
                break;
            // 发言
            case 'say':
                say(data['content']);
                break;
        }
    }
    // 发言
    function say(content){
        var data = JSON.parse(content);
        var minute = $('.minute').text();
        var second = $('.second').text();
        var uid    =  $('#uid').val();
        var time   = $('#open').val();
        var time2 = time.replace(/\-/g, "/");
        var end_time = new Date(time2).getTime();
        var	 sys_second = (end_time-new Date().getTime())/1000;
        var  aa = parseInt(sys_second);
        if (aa<=60){
            return false;
        }
        if(!(minute=='00' && second=='00')) {
            if (data.msg_type == '1' && data.room == '5' && data.uid==uid) {
                var str = '<li class="my">'
                    + '<span class="head"><img style="border-radius: 50%" src="' + data.head + '"/></span>'
                    + '<p><span style="width: 100%">'
                    +'<b>'+ data.nickname +'</b>'
                    +'<a>' + data.time + '</a>'
                    +'</span>'
                    +'<b class="both"></b>'
                    +'<span class="take">'
                    +'<img style="width: 15px;" src="__IMG__/icon_time.png" alt=""/>'+data.stage+'期'
                    +'<br/>投注：'+data.content
                    +'</span>'
                    +'</p>'
                    +'<b class="both"></b>'
                    +'</li>';
                $('#show').prepend(str);
            }else if(data.msg_type == '1' && data.room == '5' && data.uid!==uid){
                var str = '<li>'
                    + '<span class="head"><img style="border-radius: 50%" src="' + data.head + '"/></span>'
                    + '<p><span style="width: 100%">'
                    +'<b>'+ data.nickname +'</b>'
                    +'<a>' + data.time + '</a>'
                    +'</span>'
                    +'<b class="both"></b>'
                    +'<span class="take">'
                    +'<img style="width: 15px;" src="__IMG__/icon_time.png" alt=""/>'+data.stage+'期'
                    +'<br/>投注：'+data.content
                    +'</span>'
                    +'</p>'
                    +'<b class="both"></b>'
                    +'</li>';
                $('#show').prepend(str);
            }else if(data.msg_type == '4' && data.room == '5'){

                var info = JSON.parse(data.content);
                var str = '<li class="msga">'
                    + '<p class="ts">'
                    + '<span class="tile">上下分情报</span>'
                    + '<span>';
                //定义消息 msg_type 1 投注消息
                for (var i = 0; i < info.length; i++) {
                    if (info[i].exp == 1) {
                        str += '<i class="ti-l">' + info[i].nickname + '上芬：' + info[i].money + '  余芬：' + info[i].balance + '</i>'
                    }
                    if (info[i].exp == 6) {
                        str += '<i class="ti-l">' + info[i].nickname + '下芬：' + info[i].money + '  余芬：' + info[i].balance + '</i>'
                    }
                }
                str += '<label class="both"></label>'
                    + '</span>'
                    + '</p>'
                    + '</li>';
                $('#show').prepend(str);
            }
        }
    }
</script>
</html>