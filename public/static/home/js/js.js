$(function(){

    var a=0;
    $('.a').click(function(){
        if(a==0){
            $('.t').show();
            a=1;
        }else{
            $('.t').hide();
            a=0;
        }
    });
    var a=0
    var b=0

    $('.sele').click(function(){
        if(a==0){
            var h=$(this).siblings('ul').children('li').length;
            $(this).siblings('ul').stop().animate({height:h*40+'px'},300,function(){
                a=1;
            })
        }else{
            $(this).siblings('ul').stop().animate({height:'0'},300,function(){
                a=0;
            })
        }
    })
    $('.selet').click(function(){
        if(b==0){
            var h=$(this).siblings('ul').children('li').length;
            $(this).siblings('ul').stop().animate({height:h*40+'px'},300,function(){
                b=1;
            })
        }else{
            $(this).siblings('ul').stop().animate({height:'0'},300,function(){
                b=0;
            })
        }
    });
    $('#sho').on('click','.dle',function(){
        $(this).parents('li').remove();
    })
    $('.remove').click(function(){
        $('.start').hide();
        $('.games').hide();
        $("#sho").html('');
        $('.top-con ul li').removeClass('active');
    });
    $('.top-con ul li').click(function(){

        if($(this).hasClass('active')){
            $(this).removeClass('active');
        }else{
            $(this).addClass('active');
        }
     

    });
    var code=0;
    $('.first').click(function(){
        if(code==0){
            $('.title-b .table').show();
            code=1;
        }else{
            $('.title-b .table').hide();
            code=0;
        }

    })
    // $('#sun').click(function(){
    //     if($('#num').val()!=null&&$('#num').val()!=''){
    //         var html='<li>'+
    //             '<a class="dle">'+
    //             ' <img src="img/dele.png" alt="a"/>'+
    //             '</a>'+
    //             ' <p>'+
    //             '8998989'+
    //             '</p>'+
    //             '<p>大</p>'+
    //             ' <p><b>4元宝</b></p>'+
    //             '  </li>';
    //         $('#sho').append(html)
    //     }else{
    //         alert('请输入投注金额！')
    //     }
    // });
    $("#delet").click(function(){
        $("#sho").html('')
    })
});
function GetQueryString(name) {

    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)","i");

    var r = window.location.search.substr(1).match(reg);

    if (r!=null) return unescape(r[2]); return null;

}
var ida=GetQueryString("id");
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
var type=GetQueryString("type");

$('.out').click(function(){
    $('.sta').hide();
    $('.menga').hide();
    $('.table table tr td').removeClass('act');
    $('.games').css({ paddingBottom:'0'});
});
$('.fly-con-l ul li').click(function(){
    var index=$(this).index();
    $(this).addClass('active');
    $(this).siblings().removeClass('active');
    $(this).parents('.fly-con-l').siblings('.fly-con-r').children('.fly-table').eq(index).addClass('fla');
    $(this).parents('.fly-con-l').siblings('.fly-con-r').children('.fly-table').eq(index).siblings().removeClass('fla');
});
$('.table table tr td').click(function(){
   if($(this).hasClass('act')){
       $(this).removeClass('act');
   }else {
       $(this).addClass('act');
       var games=$(this).parents('.games').attr('data-i');
       $(this).parents('.games').css({
           paddingBottom:'120px'
       })
       if(games == 1){
           $('.menga').hide();
           $('.sta').show();
        }else{
           $('.menga').show();
           $('.sta').show();
       }

   }

});
$('.off').click(function(){
    $('.sta').hide();
    $('.menga').hide();
    $('.table table tr td').removeClass('act');
    $('.games').css({ paddingBottom:'0'});
});
var imo=0;
$('#re ').click(function(){
   if(imo==1){
      $('.imoa').show();
       $('.imob').hide();
       imo=0
   }else{
       $('.imob').show();
       $('.imoa').hide();
       imo=1
   }
})
//var ida=GetQueryString("id");
//console.log(ida)
var tancc=0;
function tanc(){
	if(tancc == 0){
		
	$('.tanc').show();
	tancc=1
	}else{
		$('.tanc').hide();
		tancc=0
	}
}
