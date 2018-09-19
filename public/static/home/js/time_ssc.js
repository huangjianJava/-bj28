function countDown(time,day_elem,hour_elem,minute_elem,second_elem){
    time = time.replace(/\-/g, "/");
    var end_time = new Date(time).getTime(),//月份是实际月份-1
        sys_second = (end_time-new Date().getTime())/1000;
    if (sys_second <= 0) {
        $(minute_elem).text('00');//计算分
        $(second_elem).text('00');// 计算秒
        var str =  '<dl class="mui-clearfix game_box3_right2">'
                +'<dt class="mui-pull-left"><img src="__IMG__/user.png" alt=""></dt>'
                +'<dd class="mui-pull-right padding_10 mui-text-left">'
                +'<p style="font-size: 12px">【管理员】<span class="mg_left10">10:28:19</span></p>'
                +'<span class="font_box mui-text-left  dialogue text_right">'
                +'本期已封盘敬请期待'
                +'</span>'
                +'</dd>'
                +'</dl>';
        $('#chat_content').append(str);
        $('#chat_content').scrollTop( $('#chat_content')[0].scrollHeight );
    }
    var timer = setInterval(function(){
        if (sys_second > 0) {
            sys_second -= 1;
            var day = Math.floor((sys_second / 3600) / 24);
            var hour = Math.floor((sys_second / 3600) % 24);
            var minute = Math.floor((sys_second / 60) % 60);
            var second = Math.floor(sys_second % 60);
            day_elem && $(day_elem).text(day);//计算天
            $(hour_elem).text(hour<10?"0"+hour:hour);//计算小时
            $(minute_elem).text(minute<10?"0"+minute:minute);//计算分
            $(second_elem).text(second<10?"0"+second:second);// 计算秒
        } else {
            if(sys_second <= 0) {
                $(minute_elem).text('00');//计算分
                $(second_elem).text('00');// 计算秒){
            }
            var timer1 = setInterval(function () {
                if (sys_second <= 0) {
                    location.reload();
                } else {
                    clearInterval(timer1);
                }
            }, 10000);
        }
    }, 1000);
}