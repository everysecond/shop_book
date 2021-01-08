layui.use(['jquery', 'layer', 'form'], function () {
    var layer = layui.layer;
    var $ = layui.jquery;
    var form = layui.form;
    form.on('submit(login)', function (data) {
        if ($("#username").val() == "") {
            layer.msg("用户名不能为空！");
            $("#username").focus();
            return false;
        }
        if ($("#password").val() == "") {
            layer.msg("密码不能为空！");
            $("#password").focus();
            return false;
        }
        var _username = $("#username").val();
        var _password = $("#password").val();

        $.ajax({
            url: "/login",
            data: {
                username: _username,
                password: _password,
                _token: _token,
            },
            type: "POST",
            dataType: "json",
            beforeSend: function (request) {
                index = layer.load(2, {
                    shade: [0.1, '#FFF'] //0.1透明度的白色背景
                });
            },
            success: function (res) {
                layer.close(index);
                if (res.code == 1) {
                    layer.msg("登录成功，正在跳转...", {time: 20000});
                    setTimeout(function () {
                        location.href = "/manage";
                    }, 1000)
                } else {
                    layer.msg(res.msg);
                    return false;
                }
            },
            error: function () {
                layer.close(index);
                layer.msg("网络请求错误，稍后重试！");
                return false;
            }
        });
        return false;
    });
});
