@extends('manage::layouts.frame')

@section('content')

    <div class="layui-form" style="padding: 20px 30px 0 0;">
        <div class="layui-form-item">
            <label class="layui-form-label">角色名</label>
            <div class="layui-input-inline">
                <input type="text" name="name" lay-verify="required" placeholder="请输入角色名" autocomplete="off"
                       class="layui-input" value="{{$model->name}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">显示名称</label>
            <div class="layui-input-inline">
                <input type="text" name="guard_name" lay-verify="required" placeholder="请输入显示名称" autocomplete="off"
                       class="layui-input" value="{{$model->guard_name}}">
            </div>
        </div>
        <div class="layui-form-item layui-hide">
            <input type="button" lay-submit lay-filter="manager-role-submit" id="manager-role-submit" value="确认">
        </div>
    </div>
@endsection

@section('script')
    <script>
        var layerId = 0;
        layui.use(['index', 'form', 'http', 'common'], function () {
            var form = layui.form, http = layui.http, common = layui.common;

            form.on("submit(manager-role-submit)", function (data) {
                http.put('{{route('manager-role.update',$model->id)}}', data.field).then(function () {
                    parent.location.reload();

                    common.success('保存角色成功');

                    layerId && parent.layer.close(layerId);
                });
            });
        });

        function layerYesCallback(index) {
            layerId = index;

            layui.$('#manager-role-submit').trigger('click');
        }
    </script>
@endsection