@extends('manage::layouts.frame')

@section('style')
    <style>
        .layui-form-select dl {
            max-height: none;
        }

        .layui-col-md6 {
            width: 48%;
            float: left;
        }

        .layui-input-block {
            width: 290px;
        }

        .layui-form-item {
            margin-top: 10px;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="layui-form" lay-filter="layuiadmin-form-admin" id="layuiadmin-form-admin"
         style="padding: 20px 30px 0 0;">
        <div class="layui-col-md12">
            <div class="layui-col-md6">
                <div class="layui-form-item">
                    <label class="layui-form-label">客户名称</label>
                    <div class="layui-input-block">
                        <input type="text" placeholder="" autocomplete="off" lay-verify="required"
                               class="layui-input layui-disabled" readonly
                               value="{{$model->cus->name."(".$model->cus->mobile.")"}}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><span class="red">*</span>&nbsp;&nbsp;&nbsp;姓名</label>
                    <div class="layui-input-block">
                        <input type="text" name="name" placeholder="" autocomplete="off" lay-verify="required"
                               class="layui-input {{$request->type == "detail"?'layui-disabled':''}}"
                               value="{{$model->name}}"
                                {{$request->type == "detail"?'readonly':''}}>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><span class="red">*</span>&nbsp;&nbsp;&nbsp;手机</label>
                    <div class="layui-input-block">
                        <input type="text" name="mobile" placeholder="" autocomplete="off" lay-verify="required"
                               class="layui-input {{$request->type == "detail"?'layui-disabled':''}}"
                               value="{{$model->mobile}}" {{$request->type == "detail"?'readonly':''}}>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">职务</label>
                    <div class="layui-input-block">
                        <input type="text" name="position" placeholder="" autocomplete="off"
                               class="layui-input {{$request->type == "detail"?'layui-disabled':''}}"
                               {{$request->type == "detail"?'readonly':''}} value="{{$model->position}}">
                    </div>
                </div>
            </div>
            <div class="layui-col-md6">
                <div class="layui-form-item">
                    <label class="layui-form-label">称呼</label>
                    <div class="layui-input-block">
                        <input type="radio" name="sex" value="未知" title="未知" {{$model->sex == "未知"?"checked":''}}>
                        <input type="radio" name="sex" value="先生" title="先生" {{$model->sex == "先生"?"checked":''}}>
                        <input type="radio" name="sex" value="女士" title="女士" {{$model->sex == "女士"?"checked":''}}>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">微信号</label>
                    <div class="layui-input-block">
                        <input type="text" name="wechat" placeholder="" autocomplete="off"
                               class="layui-input {{$request->type == "detail"?'layui-disabled':''}}"
                               {{$request->type == "detail"?'readonly':''}} value="{{$model->wechat}}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">是否决策人</label>
                    <div class="layui-input-block">
                        <input type="radio" name="is_key" value="1" title="是" {{$model->is_key == 1?"checked":''}}>
                        <input type="radio" name="is_key" value="2" title="否" {{$model->is_key == 2?"checked":''}}>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">具体地址</label>
                    <div class="layui-input-block">
                        <input type="text" name="address" placeholder="" autocomplete="off"
                               class="layui-input {{$request->type == "detail"?'layui-disabled':''}}"
                               {{$request->type == "detail"?'readonly':''}} value="{{$model->address}}">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">备注</label>
                <div class="layui-input-block">
                    <textarea placeholder="" class="layui-textarea {{$request->type == "detail"?'layui-disabled':''}}"
                              style="width:683px"
                              name="memo" {{$request->type == "detail"?'readonly':''}}>{{$model->memo}}</textarea>
                </div>
            </div>
        </div>
        <div class="layui-form-item layui-hide">
            <input type="button" lay-submit lay-filter="LAY-user-back-submit" id="LAY-user-back-submit"
                   value="确认">
        </div>
    </div>
    @if($request->type == "detail")
        <div class="layui-layer-btn layui-layer-btn-"><a class="layui-layer-btn1"
                                                         onclick="javascript:parent.layer.closeAll()">取消</a></div>
    @endif
@endsection

@section('script')
    <script>
        var layerId = 0;
        layui.use(['jquery', 'index', 'form', 'http', 'common'], function () {
            var form = layui.form, http = layui.http, $ = layui.$, common = layui.common;

            form.on("submit(LAY-user-back-submit)", function (data) {
                http.put('{{route('contact.update',$model->id)}}', data.field).then(function (res) {
                    common.msg(res.msg);
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