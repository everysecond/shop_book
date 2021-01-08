@extends('manage::layouts.frame')

@section('style')
    {{Html::style(asset('resource/lib/select2/css/select2.css'))}}
@endsection

@section('content')

    <div class="layui-form" lay-filter="layuiadmin-form-admin" id="layuiadmin-form-admin"
         style="padding: 20px 30px 0 0;">
        <div class="layui-form-item">
            <label class="layui-form-label">登录帐号</label>
            <div class="layui-input-inline">
                <input type="text" name="mobile" lay-verify="required" placeholder="请输入用户名(只能手机号)" autocomplete="off"
                       class="layui-input" value="">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">密码</label>
            <div class="layui-input-inline">
                <input name="password" lay-verify="required" placeholder="请输入密码" class="layui-input"
                       autocomplete="new-password" onfocus="this.type='password'">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">姓名</label>
            <div class="layui-input-inline">
                <input type="text" name="name" placeholder="请输入姓名" autocomplete="off"
                       class="layui-input" value="">
            </div>
        </div>



        <div class="layui-form-item">
            <label class="layui-form-label">审核状态</label>
            <div class="layui-input-inline">
                <input type="checkbox" name="status" lay-text="正常|冻结" lay-skin="switch" value="1" checked>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">角色</label>
            <div class="layui-input-inline">
                {{Form::select('',$roles,[],['class'=>'select2', 'lay-ignore', 'multiple'])}}
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">租点区域</label>
            <div class="layui-input-inline">
                {{Form::select('agent_id',$agents)}}
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">快点区域</label>
            <div class="layui-input-inline">
                {{Form::select('site_id',$sites)}}
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
        layui.extend({
            'select2': '../../select2/js/select2'
        }).use(['index', 'form', 'http', 'select2'], function () {
            var form = layui.form, http = layui.http, $ = layui.$;

            form.on("submit(LAY-user-back-submit)", function (data) {
                data.field.roles = $('.select2').val();

                http.post('{{route('manager.store')}}', data.field).then(function (res) {
                    parent.location.reload();

                    layerId && parent.layer.close(layerId);
                });
            });

            $(".select2").select2({
                language: "zh-CN",
                multiple: true,
                width: '260px',
                minimumInputLength: 0,
                placeholder: "请选择", //默认值
                allowClear: true,
            });

            //清除默认选中项
            $(".select2").val([]).trigger('change');

        });


        function layerYesCallback(index) {
            layerId = index;

            layui.$('#LAY-user-back-submit').trigger('click');
        }

        function layerNoCallback(index) {

        }
    </script>
@endsection