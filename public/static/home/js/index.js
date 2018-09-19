$(function(){
	$("#index_icon").css("width",$("#index_icon").height());
	//充值方式点击事件
	var index;
	$(".recharge_box li").click(function(){
		index=$(this).index();
		$(this).addClass("active").siblings().removeClass("active");
		$(".recharge_div").eq(index).addClass("active").siblings().removeClass("active");
	})
	
	$(".tab_box table").css("max-height",$(document).height()-100 +"px");
	
	//投注记录
	$(".betting_box1 li").click(function(){
		index=$(this).index();
		$(this).addClass("active").siblings().removeClass("active");
		$(".betting_box1_div").eq(index).addClass("active").siblings().removeClass("active");
		$(".betting_name").text($(this).find("a").text());
	})
	$(".betting_box2 li").click(function(){
		index=$(this).index();
		$(this).addClass("active").siblings().removeClass("active");
        $(".betting_box1_div.active .betting_box2_div").eq(index).addClass("active").siblings().removeClass("active");


    })
	
	//弹窗效果
	$(".dell_img").click(function(){
		$(this).parent().parent().fadeOut(200);
		$(".overlay").hide();
	})
	//
	$(".exchange").click(function(){
		$(".spring_wx").fadeIn(200);
		$(".overlay").css({"width":$(document).width(),"height":$(document).height()});
		$(".overlay").show();
		$(".overlay").click(function(){
			$(".overlay").hide();
			$(".spring_wx").fadeOut(200);
		})
	})
    $(".alipay_exchange").click(function(){
        $(".spring_alipay").fadeIn(200);
        $(".overlay").css({"width":$(document).width(),"height":$(document).height()});
        $(".overlay").show();
        $(".overlay").click(function(){
            $(".overlay").hide();
            $(".spring_alipay").fadeOut(200);
        })
    })
	//六合彩切换
	$(".list_two li").click(function(){
		index=$(this).index();
		$(this).addClass("active").siblings().removeClass("active");
		$(".six_table_box").eq(index).addClass("active").siblings().removeClass("active");
	})
	 $(".key_img").click(function(){
	   	if($(this).hasClass("red")){
	   		$(this).removeClass("red");
	   		$(".key_a_box").hide();
	   	}else{
	   		$(this).addClass("red");
	   		$(".key_a_box").show();
	   	}
	})
	 //彩种页面切换
	//  $(".game_box3_left li").click(function(){
	// 	index=$(this).index()-1;
	// 	$(this).addClass("active").siblings().removeClass("active");
	// 	$(".game_box3_list ").eq(index).addClass("active").siblings().removeClass("active");
	//
	// })
	 //记录切换
    $(".record_time li").click(function(){
    	 $(this).addClass("active").siblings().removeClass('active');
    	 index=$(this).index();
    	$(".record_table").eq(index).addClass('active').siblings().removeClass('active');
    	 
    });
	$(".record_a").click(function () {
        $(".trend ").hide();
        $(".rule ").hide();
		if($(".record ").is(":hidden")){
            $(".record ").fadeIn(200);
		}else {
            $(".record ").fadeOut(200);
		}
    })
    $(".trend_a").click(function () {
        $(".record ").hide();
        $(".rule ").hide();
        if($(".trend ").is(":hidden")){
            $(".trend ").fadeIn(200);
        }else {
            $(".trend ").fadeOut(200);
        }
    })
    $(".rule_a").click(function () {
        $(".record ").hide();
        $(".trend ").hide();
        if($(".rule ").is(":hidden")){
            $(".rule ").fadeIn(200);
        }else {
            $(".rule ").fadeOut(200);
        }
    })
    $(".guess").click(function () {
        $(".record ").hide();
        $(".trend ").hide();
        $(".rule ").hide();
    })
	
})