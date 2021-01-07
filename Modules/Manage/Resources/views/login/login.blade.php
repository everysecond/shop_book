<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>登入 - 中台系统</title>
    <meta name="renderer" content="webkit">
    {{--    <meta name="x-csrf-token" content="{{ csrf_token() }}">--}}
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    {{Html::style(layAsset('layui/css/layui.css'))}}
    {{Html::style(layAsset('style/admin.css'))}}
    {{Html::style(layAsset('style/login.css'))}}
    <style>
        body {
            background: url({{layAsset('image/login2.jpg')}}) no-repeat;
            height: 100%;
            width: 100%;
            overflow: hidden;
            background-size: cover;
        }

        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        .absolute-div {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }

        .login-div {
            min-width: 870px;
            min-height: 435px;
            width: 52.08%;
            height: 52.3%;
        }

        .login-back {
            background: rgb(116, 170, 255);
            opacity: 0.3;
            height: 100%;
            width: 100%;
            min-width: 435px;
            min-height: 435px;
        }

        .login-div-child {
            width: 50%;
            height: 100%;
            min-width: 435px;
            min-height: 435px;
        }

        .welcome-msg {
            width: 200px;
            height: 89px;
            font-size: 28px;
            font-family: PingFang-SC-Medium;
            font-weight: 500;
            color: rgba(255, 255, 255, 1);
            line-height: 60px;
            opacity: 1;
            letter-spacing: 2px;
        }

        .font-login {
            width: 109px;
            height: 26px;
            font-size: 26px;
            font-family: FZY3JW--GB1-0;
            font-weight: 400;
            color: rgba(57, 134, 255, 1);
            line-height: 72px;
            letter-spacing: 2px;
        }

        .btn-login {
            background: rgba(61, 136, 255, 1);
            border-radius: 4px;
            color: rgba(255, 255, 255, 1);
        }

        .copyright {
            width: 114px;
            height: 14px;
            font-size: 14px;
            font-family: ArialMT;
            font-weight: 400;
            color: rgba(255, 255, 255, 1) !important;
            line-height: 35px;
        }

        .layadmin-user-login-icon {
            color: rgba(61, 136, 255, 1);
            line-height: 38px;
        }

        .layui-icon-ok:hover {
            border-color: rgba(61, 136, 255, 1) !important;
            color: rgba(61, 136, 255, 1) !important;
        }

        .layui-form-checkbox[lay-skin=primary]:hover i {
            border-color: rgba(61, 136, 255, 1) !important
        }

        .layui-form-checked[lay-skin=primary] i {
            border-color: rgba(61, 136, 255, 1) !important;
            background-color: rgba(61, 136, 255, 1) !important;
            color: #fff;
        }

        .layui-form-checked[lay-skin=primary] i:hover {
            border-color: rgba(61, 136, 255, 1) !important;
            color: rgba(61, 136, 255, 1) !important;
            color: #fff !important;
        }

        input:-webkit-autofill, textarea:-webkit-autofill, select:-webkit-autofill {
            -webkit-box-shadow: 0 0 0px 1000px white inset
        }

        .layui-form-item :hover {
            border-color: rgba(61, 136, 255, 1) !important;
        }

        .layui-form-item {
            margin-bottom: 20px;
        }

        .layui-input {
            height: 40px;
        }

        input::-webkit-input-placeholder {
            color: rgb(170, 170, 170);
        }

        input::-moz-input-placeholder {
            color: rgb(170, 170, 170);
        }

        input::-ms-input-placeholder {
            color: rgb(170, 170, 170);
        }
    </style>
    <script>
        if (window.top !== window.self) {
            window.top.location = window.location;
        }
    </script>
</head>
<body>

<div class="layadmin-user-display-show" id="LAY-user-login" style="display: none;">
    <img src="{{layAsset('image/logo.png')}}" style="position: absolute;top: 7%;left: 3.3%;width: 20%"{{-- width="25.36%"
         height="4.814%"--}}>
    <div class="login-div absolute-div">
        <div class="layui-col-md6 login-div-child">
            <div class="login-back">
            </div>
            <span class="welcome-msg absolute-div" style="top: 46%;">
                    <span>&nbsp;&nbsp;&nbsp;欢迎使用</span><br>
                    <span>CPS中台系统</span>
            </span>
        </div>
        <div class="layui-col-md6 login-div-child" style="background:rgba(255,255,255,1);">
            <div class="layadmin-user-login-main" style="height: 100%">
                <div class="layadmin-user-login-box layadmin-user-login-header"
                     style="padding: 7% 20px 2% 20px;height: 14%">
                    <span class="font-login">后台登录</span>
                </div>
                <div class="layadmin-user-login-box layadmin-user-login-body layui-form">
                    <div class="layui-form-item">
                        <label class="layadmin-user-login-icon layui-icon layui-icon-username"
                               for="LAY-user-login-username"></label>
                        <input type="text" name="mobile" id="LAY-user-login-username" lay-verify="required"
                               placeholder="用户名"
                               class="layui-input" value="">
                    </div>
                    <div class="layui-form-item">
                        <label class="layadmin-user-login-icon layui-icon layui-icon-password"
                               for="LAY-user-login-password"></label>
                        <input type="password" name="password" id="LAY-user-login-password" lay-verify="required"
                               placeholder="密码" class="layui-input" value="">
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-row">
                            <div class="layui-col-xs7">
                                <label class="layadmin-user-login-icon layui-icon layui-icon-vercode"
                                       for="LAY-user-login-vercode"></label>
                                <input type="text" name="captcha" id="LAY-user-login-vercode" lay-verify="required"
                                       placeholder="图形验证码" class="layui-input" value="">
                            </div>
                            <div class="layui-col-xs5">
                                <div style="margin-left: 10px;">
                                    <img src="{{captcha_src()}}"
                                         onclick="this.src='{{captcha_src()}}-'+parseInt(Math.random()*1000)"
                                         class="layadmin-user-login-codeimg" id="LAY-user-get-vercode"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item" style="margin-bottom: 38px;">
                        <input type="checkbox" name="remember" lay-skin="primary" title="记住密码">
                        <a href="javascript:;" class="layadmin-user-jump-change layadmin-link" title="请联系管理员"
                           style="margin-top: 7px;color: #3986FF !important;font-weight: 500">忘记密码？</a>
                    </div>
                    <div class="layui-form-item">
                        <button class="layui-btn layui-btn-fluid btn-login" lay-submit
                                lay-filter="LAY-user-login-submit">登 入
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-trans layadmin-user-login-footer">
        <span class="copyright">© 2019 <a class="copyright" href="javascript:;" target="_blank">快点科技</a></span>
    </div>
</div>

{{Html::script(layAsset('layui/layui.js'))}}

<script>
    layui.config({
        base: '{{layAsset('')}}/', //静态资源所在路径
        version: '{{time()}}'
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'form', 'http', 'setter'], function () {
        var form = layui.form, http = layui.http, config = layui.setter, $ = layui.$;
        var _token = '{{csrf_token()}}';
        var option = $.extend({
            showError: true,
        }, option);
        //提交
        form.on('submit(LAY-user-login-submit)', function (form) {
            form.field._token = _token;
            {{--http.post('{{route('login.submit')}}', form.field).then(function (res) {--}}
            {{--    location.href = '/';--}}
            {{--});--}}
            $.ajax({
                type: 'POST',
                data: form.field,
                dataType: 'json',
                url: '{{route('login.submit')}}',
                success: function (res) {
                    var r = config.response;
                    if (res[r.statusName] == r.statusCode.ok) {
                        location.href = '/';
                    } else {
                        if (option.showError) {
                            var msg = "<cite>错误：</cite> " + (res[r.msgName] || "返回状态码异常");
                            top.layer.open({title: '错误', content: msg, icon: 5, anim: 6});
                            if (vercode = $("#LAY-user-get-vercode")) {//如果有验证码，自动刷新一次验证码
                                vercode.click();
                            }
                        }
                    }
                },
                error: function (e, type) {
                    var msg = '请求异常，请重试<br><cite>错误信息：</cite>';
                    if (type == 'error') {
                        msg += '[' + e.status + ']' + e.statusText;
                    } else if (type == 'timeout') {
                        msg += '请求超时';
                    } else if (type == 'parsererror') {
                        msg += '服务器响应内容无法解析';
                    } else if (type == 'notmodified') {
                        msg += 'notmodified';
                    }

                    top.layer.open({title: '错误', content: msg, icon: 5, anim: 6});
                }
            });
        });
    });
</script>
</body>
</html>
