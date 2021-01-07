@extends('manage::layouts.frame')

@section('content')

    <div class="layui-form" lay-filter="form" id="form" style="padding: 20px 30px 0 0;">
        <div class="layui-form-item">
            <label class="layui-form-label">名称</label>
            <div class="layui-input-inline">
                <input type="text" name="name" lay-verify="required" placeholder="请输入菜单名称" autocomplete="off"
                       class="layui-input" value="{{$model->name}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">父级</label>
            <div class="layui-input-inline">
                {{Form::select('pid',$options,$model->pid)}}
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">归属终端</label>
            <div class="layui-input-inline">
                {{Form::select('terminal',['web'=>'WEB端','app'=>'移动端'],$model->terminal)}}
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">窗口位置</label>
            <div class="layui-input-inline">
                {{Form::select('target',['in'=>'Iframe内','_blank'=>'新窗口'],$model->target)}}
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">动作</label>
            <div class="layui-input-inline">
                <input type="text" name="route" placeholder="请输入菜单路由" autocomplete="off"
                       class="layui-input" value="{{$model->route}}">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">图标</label>
            <div class="layui-input-inline">
                <input type="text" name="icon" id="icon" placeholder="请选择菜单图标" autocomplete="off"
                       class="layui-input" value="{{$model->icon}}">
            </div>
            <a class="layui-btn open-frame" data-width="835px" data-height="525px" href="{{route('manage-menu.icon')}}">选择图标</a>
        </div>

        <div class="layui-form-item layui-hide">
            <input type="button" lay-submit lay-filter="form-submit" id="form-submit" value="确认">
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

            form.on("submit(form-submit)", function (data) {
                http.put('{{route('manage-menu.update',$model->id)}}', data.field).then(function (res) {
                    parent.location.reload();

                    common.success(res.msg);

                    layerId && parent.layer.close(layerId);
                });
            });
        });


        function layerYesCallback(index) {
            layerId = index;

            layui.$('#form-submit').trigger('click');
        }
    </script>
@endsection