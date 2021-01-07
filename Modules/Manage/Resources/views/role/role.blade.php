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
            <button class="layui-btn handle" data-method="addRole"><i class="layui-icon">&#xe61f;</i>添加角色</button>
        </blockquote>
        <table class="layui-table">
            <colgroup>
                <col width="200">
                <col>
                <col width="200">
                <col width="200">
            </colgroup>
            <thead>
            <tr>
                <th>角色名称</th>
                <th>角色描述</th>
                <th>添加时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($roleLists as $data)
                <tr>
                    <td>{{$data->name}}</td>
                    <td>{{$data->remark}}</td>
                    <td>{{$data->create_date}}</td>
                    <td>
                        <a href="{{adminurl("/roles/auth/".$data->id)}}"
                           class="layui-btn layui-btn-sm layui-btn loadHref"><i class="layui-icon"></i>授权</a>
                        <button data-method="editRole" class="layui-btn layui-btn-sm layui-btn-normal handle"
                                data-id="{{$data->id}}"
                                data-title="{{$data->name}}"
                                data-remark="{{$data->remark}}"><i class="layui-icon"></i>编辑
                        </button>
                        @if($data->id!=1)
                            <button data-method="deleteRole" class="layui-btn layui-btn-sm layui-btn-danger handle"
                                    data-id="{{$data->id}}"><i class="layui-icon"></i>删除
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<!----添加角色----->
<div class="layui-form">
    <form id="addRole" class="layui-form" style="display: none">
        <div class="layui-form-item" style="padding:0px 10px;">
            {{ csrf_field() }}
            <label class="layui-label">角色名称：</label>
            <input id="roleId" type="hidden" name="id" value=""/>
            <input id="rolename" type="text" name="rolename" required lay-verify="required" placeholder="请输入角色名称"
                   autocomplete="off" class="layui-input layui-form-danger">
            <label class="layui-label">角色描述：</label>
            <textarea id="remark" class="layui-textarea" name="remark" placeholder="请输入角色描述"></textarea>
        </div>
    </form>
</div>
<!----添加角色----->
<script>
    var _token = '<?php echo e(csrf_token()); ?>';
    var adminurl = "<?php echo e(adminurl()); ?>";
</script>
<script src="{{asseturl("lib/layuiadmin/layui/layui.js")}}"></script>
<script src="{{asseturl("js/manage/role.js")}}"></script>
{{--<script src="{{asseturl("js/manage/admin.js")}}"></script>--}}


</body>
</html>
