<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>layuiAdmin 主页示例模板一</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="{{asseturl("lib/layuiadmin/layui/css/layui.css")}}" media="all">
    <link rel="stylesheet" href="{{asseturl("lib/layuiadmin/style/admin.css")}}" media="all">
</head>
<body>
<div class="layui-body" style="left:0px">
    <div class=" layui-tab-brief">
        <div class="layui-breadcrumb-box">

        </div>
    </div>
    <!-- 内容主体区域 -->
    <div id="mainbox" class="layui-row">
        <blockquote class="site-text layui-elem-quote">
            密码修改
        </blockquote>
        <div class="layui-col-md6">
            <form class="layui-form" action="" lay-filter="repassForm">
                {{csrf_field()}}
                <div class="layui-col-md6 layui-col-space5">
                    <div class="layui-form-item">
                        <label class="layui-form-label">原密码</label>
                        <div class="layui-input-block">
                            <input type="password" name="oldpass" required lay-verify="required"
                                   placeholder="请输入用户名称" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">新密码</label>
                        <div class="layui-input-block">
                            <input type="password" name="password" required lay-verify="required"
                                   placeholder="请输入用户账号" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">确认密码</label>
                        <div class="layui-input-block">
                            <input type="password" name="repassword" required lay-verify="required"
                                   placeholder="请输入密码" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit lay-filter="formDemo">立即保存</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    var _token = '{{ csrf_token() }}';
    var adminurl = "{{adminurl()}}";
</script>
<script src="{{asseturl("lib/layuiadmin/layui/layui.js")}}"></script>
<script src="{{asseturl("js/manage/admin.js")}}"></script>

</body>

</html>
