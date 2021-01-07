@extends('manage::layouts.frame')

@section('content')

    <div class="layui-form" lay-filter="manager-role-form" id="manager-role-form" style="padding: 20px 30px 0 0;">
        <div class="layui-form-item">
            <label class="layui-form-label">角色名</label>
            <div class="layui-input-inline">
                <input type="text" name="name" lay-verify="required" placeholder="请输入角色名" autocomplete="off"
                       class="layui-input" value="">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">显示名称</label>
            <div class="layui-input-inline">
                <input type="text" name="guard_name" lay-verify="required" placeholder="请输入显示名称" autocomplete="off"
                       class="layui-input">
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
            var form = layui.form;
            var http = layui.http;
            var common = layui.common;

            form.on("submit(manager-role-submit)", function (data) {
                http.post('{{route('manager-role.store')}}', data.field).then(function (res) {
                    parent.location.reload();

                    common.success('添加角色成功');

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