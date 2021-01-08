
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>404 页面不存在</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    {{Html::style(layAsset('layui/css/layui.css'))}}
    {{Html::style(layAsset('style/admin.css'))}}
</head>
<body>


<div class="layui-fluid">
    <div class="layadmin-tips">
        <i class="layui-icon" face>&#xe664;</i>

        <div style="margin-top:20px;">
            <span style="font-size: 24px;color: #393D49;">
                {{$exception->getMessage()}}
            </span>
        </div>

        <div class="layui-text">
            <h1>
                <span class="layui-anim layui-anim-loop layui-anim-">4</span>
                <span class="layui-anim layui-anim-loop layui-anim-rotate">0</span>
                <span class="layui-anim layui-anim-loop layui-anim-">3</span>
            </h1>
        </div>
    </div>
</div>

{{Html::script(layAsset('layui/layui.js'))}}
<script>
    layui.config({
        base: '{{layAsset('')}}/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index']);
</script>
</body>
</html>