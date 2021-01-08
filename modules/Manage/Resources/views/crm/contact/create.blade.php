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
                    <label class="layui-form-label"><span class="red">*</span>&nbsp;&nbsp;&nbsp;客户名称</label>
                    <div class="layui-input-block">
                        @if(request('cus_id'))
                            <input name="cus_id" type="hidden" value="{{request('cus_id')}}">
                            <input type="text" autocomplete="off" class="layui-input layui-disabled" readonly
                                   value="{{request('cus_name')}}">
                        @else
                            {{Form::select("cus_id",$customers,'',["lay-verify"=>'required',"lay-search","placeholder"=>"请选择或输入搜索"])}}
                        @endif
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><span class="red">*</span>&nbsp;&nbsp;&nbsp;姓名</label>
                    <div class="layui-input-block">
                        <input type="text" name="name" placeholder="" autocomplete="off" lay-verify="required"
                               class="layui-input" value="">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><span class="red">*</span>&nbsp;&nbsp;&nbsp;手机</label>
                    <div class="layui-input-block">
                        <input type="text" name="mobile" placeholder="" autocomplete="off" lay-verify="required"
                               class="layui-input" value="">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">职务</label>
                    <div class="layui-input-block">
                        <input type="text" name="position" placeholder="" autocomplete="off"
                               class="layui-input" value="">
                    </div>
                </div>
            </div>
            <div class="layui-col-md6">
                <div class="layui-form-item">
                    <label class="layui-form-label">称呼</label>
                    <div class="layui-input-block">
                        <input type="radio" name="sex" value="未知" title="未知" checked>
                        <input type="radio" name="sex" value="先生" title="先生">
                        <input type="radio" name="sex" value="女士" title="女士">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">微信号</label>
                    <div class="layui-input-block">
                        <input type="text" name="wechat" placeholder="" autocomplete="off"
                               class="layui-input" value="">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">是否决策人</label>
                    <div class="layui-input-block">
                        <input type="radio" name="is_key" value="1" title="是" checked>
                        <input type="radio" name="is_key" value="2" title="否">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">具体地址</label>
                    <div class="layui-input-block">
                        <input type="text" name="address" placeholder="" autocomplete="off"
                               class="layui-input" value="">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">备注</label>
                <div class="layui-input-block">
                    <textarea placeholder="" class="layui-textarea" style="width:683px" name="memo"></textarea>
                </div>
            </div>
        </div>
        <div class="layui-form-item layui-hide">
            <input type="button" lay-submit lay-filter="LAY-user-back-submit" id="LAY-user-back-submit"
                   value="确认">
        </div>
    </div>
@endsection

@section('script')
    <script>
        var layerId = 0;
        layui.use(['jquery', 'index', 'form', 'http', 'common'], function () {
            var form = layui.form, http = layui.http, $ = layui.$, common = layui.common;
            ;

            form.on("submit(LAY-user-back-submit)", function (data) {
                http.post('{{route('contact.store')}}', data.field).then(function (res) {
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