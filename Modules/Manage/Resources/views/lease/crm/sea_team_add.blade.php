@extends('manage::layouts.frame')

@section('style')
    <style>
        .layui-form-select dl {
            max-height: none;
        }

        input[readonly], input[readonly]:hover {
            color: #666 !important;
            opacity: 0.6
        }
    </style>
@endsection

@section('content')

    <div class="layui-form" lay-filter="layuiadmin-form-admin" id="layuiadmin-form-admin"
         style="padding: 20px 30px 0 0;">

        <div class="layui-form-item">
            <label class="layui-form-label">请选择人员</label>
            <div class="layui-input-block" style="width: 276px;z-index: 55">
                {{Form::select("staff_id",$options,"",["lay-verify","lay-search","placeholder"=>"请选择或输入搜索"])}}
                <input type="hidden" lay-submit lay-filter="LAY-user-back-submit" id="customer_id"
                       value="{{$customer_id}}">
            </div>
        </div>


        <div class="layui-form-item layui-hide">
            <input type="button" lay-submit lay-filter="LAY-user-back-submit" id="LAY-user-back-submit" value="确认">
        </div>
    </div>
@endsection

@section('script')
    {{--    <script src="{{asseturl("lib/echarts/jquery-3.2.1.min.js")}}"></script>--}}
    <script>
        var adminurl = "{{adminurl()}}";
        var _token = '{{ csrf_token() }}';
        var layerId = 0;
        layui.use(['index', 'form', 'http', 'common'], function () {
            var form = layui.form, http = layui.http, $ = layui.$;
            var customer_id = $("#customer_id").attr("value");
            form.on("submit(LAY-user-back-submit)", function (data) {

                $.ajax({
                    type: 'POST',
                    data: {'staff_id': data.field.staff_id, 'customer_id': customer_id, '_token': _token},
                    // dataType: 'json',
                    url: adminurl + "/crm/sea_customer_team_create",
                    success: function (res) {
                        if (res.code == 1) {
                            layer.msg('创建成功！', {icon: 1});
                            parent.table.reload('team-table');
                            parent.layer.closeAll();

                        } else {
                            layer.msg('网络请求错误，稍后重试！');
                            parent.table.reload('team-table');
                            parent.layer.closeAll();
                        }
                    },
                });
            });
        });

        function layerYesCallback(index) {
            layerId = index;
            layui.$('#LAY-user-back-submit').trigger('click');
        }

    </script>
@endsection