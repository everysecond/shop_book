@extends('manage::layouts.frame')

@section('style')
    <style>
        .layui-form-select dl{
            max-height: none;
        }
    </style>
@endsection

@section('content')

    <div class="layui-form" lay-filter="layuiadmin-form-admin" id="layuiadmin-form-admin"
         style="padding: 20px 30px 0 0;">
        <div class="layui-form-item">
            <label class="layui-form-label"><span class="red">*</span>&nbsp;&nbsp;&nbsp;字典类型</label>
            <div class="layui-input-inline">
                <input type="text" name="dict_type" lay-verify="required" placeholder="字典类型" autocomplete="off"
                       class="layui-input" value="">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><span class="red">*</span>&nbsp;&nbsp;&nbsp;类型名称</label>
            <div class="layui-input-inline">
                <input type="text" name="type_means" lay-verify="required" placeholder="类型名称" autocomplete="off"
                       class="layui-input" value="">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><span class="red">*</span>&nbsp;&nbsp;&nbsp;键</label>
            <div class="layui-input-inline">
                <input type="text" name="code" lay-verify="required" placeholder="键" autocomplete="off"
                       class="layui-input" value="">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><span class="red">*</span>&nbsp;&nbsp;&nbsp;值</label>
            <div class="layui-input-inline">
                <input type="text" name="means" lay-verify="required" placeholder="值" autocomplete="off"
                       class="layui-input" value="">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">排序</label>
            <div class="layui-input-inline">
                <input type="text" name="sort" placeholder="" autocomplete="off"
                       class="layui-input" value="">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">备注</label>
            <div class="layui-input-inline">
                <input type="text" name="memo" placeholder="" autocomplete="off" class="layui-input" value="">
            </div>
        </div>
        <div class="layui-form-item layui-hide">
            <input type="button" lay-submit lay-filter="LAY-user-back-submit" id="LAY-user-back-submit" value="确认">
        </div>
    </div>
@endsection

@section('script')
    <script>
        var layerId = 0;
        layui.use(['jquery','index', 'form', 'http','common'], function () {
            var form = layui.form, http = layui.http, $ = layui.$;

            form.on("submit(LAY-user-back-submit)", function (data) {
                http.post('{{route('dict.store')}}', data.field).then(function (res) {
                    parent.table.reload('LAY-user-back-manage', {
                        where: {
                            searchStr: $('#searchStr').val()
                        }
                    });
                    layerId && parent.layer.close(layerId);
                });
            });
        });


        function layerYesCallback(index) {
            layerId = index;

            layui.$('#LAY-user-back-submit').trigger('click');
        }

        function layerNoCallback(index) {

        }
    </script>
@endsection