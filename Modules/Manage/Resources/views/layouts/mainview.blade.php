<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>@yield('page_title', $title)|{{adminSetting("admin_title")}}</title>
    <link rel="stylesheet" href="{{url("layui/css/layui.css")}}">
    <link rel="stylesheet" type="text/css" href="{{ asseturl('css/style.css') }}">
    <link rel="stylesheet" href="{{asseturl("lib/layuiadmin/layui/css/layui.css")}}" media="all">
    <link rel="stylesheet" href="{{asseturl("lib/layuiadmin/style/admin.css")}}" media="all">
    @yield('css')
</head>
<body>
<div class="layui-layout layui-layout-admin">
    @yield('content')
</div>
<script>
    var _token = '{{ csrf_token() }}';
    var adminurl = "{{adminurl()}}";
</script>
<script src="{{url("layui/layui.js")}}"></script>
<!--
<script src="{{asseturl("js/Public/main.js")}}"></script>-->
@yield('javascript')
</body>
</html>