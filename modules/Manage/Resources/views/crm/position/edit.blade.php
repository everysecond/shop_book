@extends('manage::layouts.frame')
@section('style')
    {{Html::style(asset('resource/lib/select2/css/select2.css'))}}
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
            <label class="layui-form-label">职位名称</label>
            <div class="layui-input-inline">
                <input type="text" name="title" lay-verify="required" placeholder="请输入职位名称" autocomplete="off"
                       class="layui-input" value="{{$model->title}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">排序</label>
            <div class="layui-input-inline">
                <input type="text" name="sort" placeholder="" autocomplete="off"
                       class="layui-input" value="{{$model->sort}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">上级</label>
            <div class="layui-input-block" style="width: 276px;">
                {{Form::select('pid',$options,$model->pid,["lay-verify","lay-search"])}}
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
        layui.use(['index', 'form', 'http', 'common'], function () {
            var form = layui.form, http = layui.http, $ = layui.$;

            form.on("submit(LAY-user-back-submit)", function (data) {
                http.put('{{route('position.update',$model->id)}}', data.field).then(function (res) {
                    layer.confirm(res.msg, {
                        btn: ['确定'] //按钮
                        ,title:'<i class="layui-icon">&#xe607;</i> 确认提示'
                        ,icon:0
                    }, function(){
                        layerId && parent.layer.close(layerId);
                    });
                });
            });
        });

        function layerYesCallback(index) {
            layerId = index;
            layui.$('#LAY-user-back-submit').trigger('click');
        }
    </script>
@endsection