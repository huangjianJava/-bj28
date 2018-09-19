$(function(){

	countDown("2018/8/10 23:59:59","#demo01 .day","#demo01 .hour","#demo01 .minute","#demo01 .second");

	countDown("2019/8/10 22:59:59",null,"#demo02 .hour","#demo02 .minute","#demo02 .second");
	countDown("2019/8/10 22:59:59",null,"#demo03 .hour","#demo03 .minute","#demo03 .second");
	countDown("2019/8/10 22:59:59",null,"#demo04 .hour","#demo04 .minute","#demo04 .second");
	countDown("2019/8/10 22:59:59",null,"#demo05 .hour","#demo05 .minute","#demo05 .second");
	countDown("2019/8/10 22:59:59",null,"#demo06 .hour","#demo06 .minute","#demo06 .second");
	countDown("2019/8/10 22:59:59",null,"#demo07 .hour","#demo07 .minute","#demo07 .second");
	countDown("2019/8/10 22:59:59",null,"#demo08 .hour","#demo08 .minute","#demo08 .second");
	countDown("2019/8/10 22:59:59",null,"#demo09 .hour","#demo09 .minute","#demo09 .second");

});
function countDown(time,day_elem,hour_elem,minute_elem,second_elem){



	var end_time = new Date(time).getTime(),//月份是实际月份-1


	sys_second = (end_time-new Date().getTime())/1000;

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

			clearInterval(timer);

		}

	}, 1000);

}
