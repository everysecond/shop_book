layui.use(['element', 'jquery', 'layer'], function () {
    $ = layui.jquery;
    var layer = layui.layer;
    $(".loginout").on('click', function () {
        $.ajax({
            url: "/logout",
            data: {
                _token: _token,
            },
            type: "POST",
            dataType: "json",
            success: function (res) {
                layer.msg(res.msg);
                window.location.href = adminurl + "/../login";
            },
            error: function () {
                layer.close(index);
                layer.msg("网络请求错误，稍后重试！");
                return false;
            }
        });
    });

});