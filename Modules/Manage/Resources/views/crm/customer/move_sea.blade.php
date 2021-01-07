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
            <label class="layui-form-label">请选择公海</label>
            <div class="layui-input-block" style="width: 276px;z-index: 55">
                {{Form::select("sea_id",$seas,"",["lay-verify","lay-search","placeholder"=>"请选择或输入搜索"])}}
                <input type="hidden" lay-submit lay-filter="LAY-user-back-submit" id="customer_id"
                       value="{{request('ids')}}">
            </div>
        </div>


        <div class="layui-form-item layui-hide">
            <input type="button" lay-submit lay-filter="LAY-user-back-submit" id="LAY-user-back-submit" value="确认">
        </div>
    </div>
@endsection

@section('script')
    <script>
        var adminurl = "{{adminurl()}}";
        var _token = '{{ csrf_token() }}';
        var layerId = 0;
        var from = '{{request('from')}}';
        layui.use(['index', 'form', 'http', 'common'], function () {
            var form = layui.form, http = layui.http, $ = layui.$;
            var customer_id = $("#customer_id").attr("value");
            form.on("submit(LAY-user-back-submit)", function (data) {
                $.ajax({
                    type: 'POST',
                    data: {'sea_id': data.field.sea_id, 'ids': customer_id, '_token': _token},
                    url: '{{route("cus.move.sea")}}',
                    success: function (res) {
                        if (res.code == 0) {
                            layer.msg(res.msg, {icon: 1});
                            if(from == 'detail'){
                                // parent.parent.table.reload('LAY-user-back-manage', {
                                //     where: {
                                //         searchStr: parent.parent.$('#searchStr').val(),
                                //         cus_type: parent.parent.$('select[name="cus_type"]').val(),
                                //         cus_source: parent.parent.$('select[name="cus_source"]').val(),
                                //         cus_level: parent.parent.$('select[name="cus_level"]').val(),
                                //         province_id: parent.parent.$('select[name="province_id"]').val(),
                                //         type: parent.parent.type,
                                //     }
                                // });
                                parent.location.reload();
                            }else{
                                parent.table.reload('LAY-user-back-manage', {
                                    where: {
                                        searchStr: parent.$('#searchStr').val(),
                                        cus_type: parent.$('select[name="cus_type"]').val(),
                                        cus_source: parent.$('select[name="cus_source"]').val(),
                                        cus_level: parent.$('select[name="cus_level"]').val(),
                                        province_id: parent.$('select[name="province_id"]').val(),
                                        type: parent.type
                                    }
                                });
                            }
                            setTimeout(function () {
                                parent.layer.closeAll();
                            },1000)
                        } else {
                            layer.msg(res.msg);
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