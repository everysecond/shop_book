@extends('manage::layouts.frame')

@section('style')
    {{Html::style(asset('resource/lib/select2/css/select2.css'))}}
@endsection

@section('content')

    <div class="layui-form" lay-filter="form" id="form" style="padding: 20px 30px 0 0;">
        <div class="layui-form-item">
            <label class="layui-form-label">权限名称</label>
            <div class="layui-input-inline">
                <input type="text" name="name" lay-verify="required" placeholder="请输入角色名" autocomplete="off"
                       class="layui-input" value="">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">上级模块</label>
            <div class="layui-input-inline">
                @if($parent)
                    {{Form::select('pid',[$parent->id => $parent->name])}}
                @else
                    {{Form::select('pid',[0=>'根模块'])}}
                @endif
            </div>
        </div>

        @if($parent && $parent->level==1)
            <div class="layui-form-item">
                <label class="layui-form-label">操作</label>

                <div class="layui-input-inline">
                    <select class="select2" lay-ignore multiple="multiple">
                        @foreach($routes as $route)
                            <option name="{{$route}}">{{$route}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif

        <div class="layui-form-item layui-hide">
            <input type="button" lay-submit lay-filter="form-submit" id="form-submit" value="确认">
        </div>
    </div>
@endsection

@section('script')
    <script>
        var layerId = 0;
        layui.extend({
            'select2': '../../select2/js/select2'
        }).use(['index', 'form', 'http', 'common', 'select2'], function () {
            var $ = layui.jquery;
            var form = layui.form;
            var http = layui.http;
            var common = layui.common;

            form.on("submit(form-submit)", function (data) {
                data.field.action = $('.select2').val();

                http.post('{{route('manager-permission.store')}}', data.field).then(function (res) {
                    parent.location.reload();

                    common.success(res.msg || '操作成功');

                    layerId && parent.layer.close(layerId);
                });
            });


            $(".select2").select2({
                language: "zh-CN",
                multiple: true,
                width: '360px',
                minimumInputLength: 0,
                placeholder: "请选择操作", //默认值
                allowClear: true,
            });

            //清除默认选中项
            $(".select2").val([]).trigger('change');
        });

        function layerYesCallback(index) {
            layerId = index;

            layui.$('#form-submit').trigger('click');
        }
    </script>
@endsection