var regular = {
    showRegular:function (str,play,txt) {
        var code=[];
        $.each(play,function (i,k) {
            if(k.role.test(str)){
                if(txt == 'ssc'){
                    code = regular.assemble_ssc(str,k.id);
                }
                return false;
            }
        })
        return code;
    },

    assemble_ssc:function (str,type) {
        var code        =[];
        var number_arr  =[];
        var big_type    ="";
        code['type'] =  type;
        if(type ==2){//定位数字玩法
            var arr = str.split('/');
            code['money']   = arr[2];
            code['number']  = arr[1].split('');
            code['wei']     = arr[0].split('');
            var wei_c    = code['wei'].length;
            var number_c = code['number'].length;
            if(wei_c ==1 && number_c==1){ //只有一个位置
                code['wei'] = arr[0].split('');
                big_type    = regular.getbigtype(code['wei'][0]);
                list = {};
                list['type']   = big_type;
                list['number'] = code['number'][0];
                list['money']  = code['money'];
                number_arr.push(list);
            }else{ //多个位置
                for(var i=1;i<=wei_c;i++){
                    var s = code['wei'][i-1];
                    for(j=1;j<=number_c;j++) {
                        list = {};
                        list['type']   = regular.getbigtype(s);
                        list['number'] = code['number'][j-1];
                        list['money']  = code['money'];
                        number_arr.push(list);
                    }
                }
            }
        }else if(type ==1){//龙虎和玩法
            code['money']  = str.replace(/[^0-9]/ig,"");
            code['number'] = str.replace(/[^龙,虎,和]/ig,"").split('');
            var number_c   = code['number'].length;
            if(number_c ==1){ //只有一个位置
                list = {};
                list['type']   = 1;
                list['number'] = code['number'][0];
                list['money']  = code['money'];
                number_arr.push(list);
            }else{ //多个位置
                for(var i=1;i<=number_c;i++){
                    list = {};
                    list['type']   = 1;
                    list['number'] = code['number'][i-1];
                    list['money']  = code['money'];
                    number_arr.push(list);
                }
            }
        }else if(type ==5){//总和大小单双玩法
            code['money']  = str.replace(/[^0-9]/ig,"");
            code['number'] = str.replace(/[^大,小,单,双]/ig,"").split('');
            list = {};
            list['type']   = 1;
            list['number'] = code['number'][0];
            list['money']  = code['money'];
            number_arr.push(list);
        }else if(type ==4){//花样玩法
            code['money'] = str.replace(/[^0-9]/ig,"");
            var arr = str.replace(/[^前,中,后,豹,顺,对,半,杂,/ ]/ig,"").split('/');
            var obj1  = {'1':'前','2':'中','3':'后'};
            var obj2 = {'1':'豹','2':'顺','3':'对','4':'半','5':'杂'};
            var num1,num2,num3=[];
            $.each(arr,function (i,k) {
                $.each(obj1,function (ii,kk) {
                    if(k.replace(/[^前,中,后]/ig,"") == kk){
                       big_type=regular.getbigtype_1(kk)
                    }
                }),
                $.each(arr,function (i,k) {
                    $.each(obj2,function (iii,kkk) {
                        if(k.replace(/[^豹,顺,对,半,杂]/ig,"") == kkk){
                            num2=kkk;
                        }
                    })
                })
            })
            list = {};
            list['type']   = big_type;
            list['number'] = num2;
            list['money']  = code['money'];
            number_arr.push(list);
        }else if(type ==6) {//定位大小单双玩法
            var arr = str.split('/');
            var num_type = arr.length;
            if(num_type==1){
                var nums = str.replace(/[^大,小,单,双]/ig, "");
                var sss  = str.split(nums);
                var bet_num = sss[0];
                var bet_num_c = bet_num.length;
                if (bet_num_c == 1) {
                    var s = bet_num[0];
                    list = {};
                    list['type'] = regular.getbigtype_2(s);
                    list['number'] = [nums][0];
                    list['money'] = sss[1].replace(/[^0-9]/ig, "");
                    number_arr.push(list);
                } else {
                    for (var i = 1; i <= bet_num_c; i++) {
                        var s = bet_num[i - 1];
                        list = {};
                        list['type'] = regular.getbigtype(s);
                        list['number'] = [nums][0];
                        list['money'] = sss[1].replace(/[^0-9]/ig, "");
                        number_arr.push(list);
                    }
                }

            }
            if(num_type==3){
                var bet_num  = arr[0].split('');
                var bet_num_c = bet_num.length;
                if (bet_num_c == 1) {
                    var s = bet_num[0];
                    list = {};
                    list['type']   = regular.getbigtype_2(s);
                    list['number'] = arr[1];
                    list['money']  = arr[2];
                    number_arr.push(list);
                } else {
                    for (var i = 1; i <= bet_num_c; i++) {
                        var s = bet_num[i - 1];
                        list = {};
                        list['type'] = regular.getbigtype(s);
                        list['number'] = arr[1];
                        list['money']  = arr[2];
                        number_arr.push(list);
                    }
                }
            }
        }
        return number_arr;
    },

    ssc:function (str) {
        var play = [
            {role : /(^[1-5]{1,5}\/[0-9]{1,9}\/[1-9]\d*)/,id : 2}, //定位数字玩法
            {role : /(^[龙,虎,和]{1,3}[1-9]\d*$|^[龙,虎,和]{1,3}\/[1-9]\d*$)/,id : 1},          //龙虎和玩法
            {role : /(^总[大,小,单,双]{1}[1-9]\d*$|^总[大,小,单,双]{1}\/[1-9]\d*$)/,id : 5},   //总和大小单双玩法
            {role : /(^[1-5]{1,5}[大,小,单,双][1-9]\d*$|^[1-5]{1,5}\/[大,小,单,双]\/[1-9]\d*$|^[1-5]{1,5}[大,小][单,双][1-9]\d*$|^[1-5]{1,5}\/[大,小][单,双]\/[1-9]\d*$)/,id : 6}, //定位大小单双玩法
            {role : /^([前,中,后][豹,顺,对,半,杂][1-9]\d*$|[前,中,后][豹,顺,对,半,杂]\/[1-9]\d*$)/,id : 4}                  //花样玩法
        ];
        var code = regular.showRegular(str,play,'ssc');

        if(code){
            return code;
        }else{
            return '投注的格式有误,请参考规则';
        }
    },
    getbigtype:function (code) {
        switch (code){
            case '1':
                return '5';
                break;
            case '2':
                return '7';
                break;
            case '3':
                return '9';
                break;
            case '4':
                return '11';
                break;
            case '5':
                return '13';
                break;
        }
    },
    getbigtype_1:function (code) {
        switch (code){
            case '前':
                return '15';
                break;
            case '中':
                return '16';
                break;
            case '后':
                return '17';
                break;
        }
    },
    getbigtype_2:function (code) {
        switch (code){
            case '1':
                return '6';
                break;
            case '2':
                return '8';
                break;
            case '3':
                return '10';
                break;
            case '4':
                return '12';
                break;
            case '5':
                return '14';
                break;
        }
    },



}