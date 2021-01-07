<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>快点CPS系统 - @yield('title')</title>
    <meta name="renderer" content="webkit">
    <meta name="x-csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    {{Html::style(layAsset('layui/css/layui.css'))}}
    {{Html::style(asset('resource/css/common.css'))}}
    @yield('style')
</head>
<body>

@yield('content')

{{Html::script(layAsset('layui/layui.js'))}}

<script>
    layui.config({
        base: '{{layAsset('')}}/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    });
</script>

@yield('script')

</body>
</html>
