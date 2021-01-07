@extends('manage::layouts.frame')

@section('style')
    <style>
        .layui-form-select dl {
            max-height: none;
        }
        input[readonly],input[readonly]:hover{color:#666 !important;opacity:0.6}
    </style>
@endsection

@section('content')

    <div class="layui-form" lay-filter="layuiadmin-form-admin" id="layuiadmin-form-admin"
         style="padding: 20px 30px 0 0;">
        <div class="layui-form-item">
            <label class="layui-form-label">公海</label>
            <div class="layui-input-block" style="width: 276px;">
                {{Form::select("sea_id",$positions,request("sea_id"),["disabled"])}}
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">请选择人员</label>
            <div class="layui-input-block" style="width: 276px;">
                {{Form::select("staff_id",$options,"",["lay-verify","lay-search","placeholder"=>"请选择或输入搜索"])}}
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">权限</label>
            <div class="layui-input-block" style="width: 276px;">
                <input type="checkbox" name="can_get" title="认领" lay-skin="primary">
                <input type="checkbox" name="can_assign" title="分配" lay-skin="primary">
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
        layui.use(['jquery', 'index', 'form', 'http', 'common'], function () {
            var form = layui.form, http = layui.http, $ = layui.$;

            form.on("submit(LAY-user-back-submit)", function (data) {
                http.post('{{route('sea-staff.store')}}', data.field).then(function (res) {
                    parent.table.reload('LAY-user-back-manage', {
                        where: {orderBy: 'id', sortedBy: 'desc', position_id: parent.position_id}
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