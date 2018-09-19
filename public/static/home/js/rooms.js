function my_success(info,url) {
    layer.open({
        content: info,
        skin: 'msg',
        time: 1 ,//2秒后自动关闭
        end: function(elem){
            window.location.href=url;
        }
    });
}
function my_error(info) {
    layer.open({
        content: info,
        skin: 'msg',
        time: 2
    });
}