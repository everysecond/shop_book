{{--@extends('manage::layouts.main')--}}
{{--@section("content")--}}


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

    <!-- 内容主体区域 -->
    <div id="mainbox" class="layui-row" style="margin: 15px;">
        <blockquote class="site-text layui-elem-quote">
            <a href="{{adminurl("/admins/add")}}" class="layui-btn loadHref"><i class="layui-icon">&#xe61f;</i>添加管理员</a>
        </blockquote>
        <table class="layui-table">
            <colgroup>
                <col width="200">
                <col width="200">
                <col width="150">
                <col>
                <col>
                <col width="150">
                <col width="150">
            </colgroup>
            <thead>
            <tr>
                <th>管理员名称</th>
                <th>管理员账号</th>
                <th>管理员角色</th>
                <th>添加时间</th>
                <th>修改时间</th>
                <th>是否启用</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($adminLists as $data)
                <tr>
                    <td>{{$data->realname}}</td>
                    <td>{{$data->username}}</td>
                    <td>{{$data->name}}</td>
                    <td>{{$data->admin_create_date}}</td>
                    <td>{{$data->admin_update_date}}</td>
                    <td>
                        @if($data->id != 1)
                            <div class="layui-form resetcss admin-action" data-method="showAdmin">
                                <input type="checkbox" name="is_show" lay-skin="switch" lay-text="是|否"
                                       data-id="{{$data->admin_id}}" @if($data->state==1) checked @endif>
                            </div>
                        @else
                            <div class="layui-form resetcss admin-action" data-method="showAdmin">
                                <input type="checkbox" name="is_show" lay-skin="switch" lay-text="是|否"
                                       data-id="{{$data->admin_id}}" checked disabled>
                            </div>
                        @endif
                    </td>
                    <td>
                        <a href="{{adminurl("/admins/edit/".$data->admin_id)}}"
                           class="layui-btn layui-btn-sm layui-btn-normal loadHref"><i class="layui-icon"></i>编辑</a>
                        @if($data->admin_id!=1)
                            <button data-method="deleteAdmin" class="layui-btn layui-btn-sm layui-btn-danger handle"
                                    data-id="{{$data->admin_id}}"><i class="layui-icon"></i>删除
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
    var _token = '<?php echo e(csrf_token()); ?>';
    var adminurl = "<?php echo e(adminurl()); ?>";
</script>

<script src="{{asseturl("lib/layuiadmin/layui/layui.js")}}"></script>
<script src="{{asseturl("js/manage/admin.js")}}"></script>
<script>
    layui.config({
        base: "{{asseturl("lib")}}" + '/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'console']);
</script>

</body>
</html>


{{--@stop--}}
{{--@section("javascript")--}}

{{--@stop--}}
