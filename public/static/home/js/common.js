function pop(msg,url) {
    //提示
    layer.open({
        content: msg,
        skin: 'msg',
        time: 2 ,//2秒后自动关闭
        end: function(elem){
            if(url){
                window.location.href=url;
            }
        }
    });
}

