<!DOCTYPE html>
<html class="full-height">
<head>
    <meta charset="utf-8">
    <title>报表主页</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="{{asseturl("lib/layuiadmin/layui/css/layui.css")}}" media="all">
    <link rel="stylesheet" href="{{asseturl("lib/layuiadmin/style/admin.css")}}" media="all">
    <style>
        .full-height {
            height: 100%;
        }
        .layadmin-shortcut li .layui-icon {
            height: 100px;
            line-height: 100px;
            font-size: 45px;
        }
    </style>
</head>
<body class="full-height">

<div class="layui-fluid full-height">
    <div class="layui-row layui-col-space15 full-height" style="max-width: 1200px;margin: 0 auto;">
        <div class="layui-col-md12 full-height">
            <div class="layui-card" style="overflow-y: scroll;height: 97%">
                <div class="layui-card-header">报表系统</div>
                <div class="layui-card-body">
                    <div class=" layadmin-shortcut full-height">
                        <div carousel-item class="full-height">
                            <ul class="layui-row layui-col-space10 full-height">
                                <li class="layui-col-xs6">
                                    <a href="/kood">
                                        <i class="layui-icon layui-icon-console"></i>
                                        <cite>快点</cite>
                                    </a>
                                </li>
                                <li class="layui-col-xs6">
                                    <a lay-href="home/homepage2.html">
                                        <i class="layui-icon layui-icon-chart"></i>
                                        <cite>租点</cite>
                                    </a>
                                </li>
                                <li class="layui-col-xs6">
                                    <a lay-href="component/layer/list.html">
                                        <i class="layui-icon layui-icon-template-1"></i>
                                        <cite>弹层</cite>
                                    </a>
                                </li>
                                <li class="layui-col-xs6">
                                    <a layadmin-event="im">
                                        <i class="layui-icon layui-icon-chat"></i>
                                        <cite>聊天</cite>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{asseturl("lib/layuiadmin/layui/layui.js")}}"></script>
<script>

</script>
</body>
</html>