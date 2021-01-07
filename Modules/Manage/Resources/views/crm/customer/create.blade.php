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
         style="padding-top: 20px;">
        <div class="layui-col-md12">
            <div class="layui-col-md6">
                <div class="layui-form-item">
                    <label class="layui-form-label"><span class="red">*</span>&nbsp;&nbsp;&nbsp;客户名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="name" placeholder="" autocomplete="off" lay-verify="required"
                               class="layui-input" value="">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><span class="red">*</span>&nbsp;&nbsp;&nbsp;客户类型</label>
                    <div class="layui-input-block">
                        <select name="cus_type" lay-verify="required">
                            <option value="">客户类型</option>
                            <option value="1">租点用户</option>
                            <option value="2">租点网点</option>
{{--                            <option value="3">快点用户</option>--}}
{{--                            <option value="4">快点网点</option>--}}
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><span class="red">*</span>&nbsp;&nbsp;&nbsp;客户号码</label>
                    <div class="layui-input-block">
                        <input type="text" name="mobile" placeholder="" autocomplete="off" lay-verify="required"
                               class="layui-input" value="">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">所在地区</label>
                    <div class="layui-input-inline" style="width: 20%;">
                        {{Form::select("province_id",$provinces,'',
                        ["lay-filter"=>"province","lay-search","placeholder"=>"省"])}}
                    </div>
                    <div class="layui-input-inline" style="width: 20%;">
                        <select name="city_id" id="city" lay-filter="city" lay-search="">
                            <option value="">市</option>
                        </select>
                    </div>
                    <div class="layui-input-inline" style="width: 22.5%;">
                        <select name="county_id" id="area" lay-filter="area" lay-search="">
                            <option value="">县/区</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-col-md6">
                <div class="layui-form-item">
                    <label class="layui-form-label">助记名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="short_name" placeholder="" autocomplete="off"
                               class="layui-input" value="">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><span class="red">*</span>&nbsp;&nbsp;&nbsp;客户等级</label>
                    <div class="layui-input-block">
                        <select name="cus_level" lay-verify="required">
                            <option value="">客户等级</option>
                            <option value="1">重点客户</option>
                            <option value="2">普通客户</option>
                            <option value="3">非优先客户</option>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><span class="red">*</span>&nbsp;&nbsp;&nbsp;负责人</label>
                    <div class="layui-input-block">
                        {{Form::select("charger_id",$managers,getUserId(),
                        ["lay-filter"=>"charger_id","lay-verify"=>'required',"lay-search","placeholder"=>"负责人"])}}
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
                    <textarea placeholder="" class="layui-textarea" style="width:721px" name="memo"></textarea>
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
            var form = layui.form, http = layui.http, $ = layui.$,
                common = layui.common, provinceText = "", cityText = "", areaText = "";
            form.on("submit(LAY-user-back-submit)", function (data) {
                http.post('{{route('cus.store')}}', data.field).then(function (res) {
                    common.msg(res.msg);
                    layerId && parent.layer.close(layerId);
                });
            });

            //监听省下拉框
            form.on('select(province)', function (dataObj) {
                //移除城市下拉框所有选项
                $("#city").empty();
                var cityHtml = '<option value="">市</option>';
                $("#city").html(cityHtml);
                var areaHtml = '<option value="">县/区</option>';
                $("#area").html(areaHtml);
                var value = $("select[name='province_id']").val();
                if(!value){
                    form.render('select');
                    return;
                }
                //异步加载下拉框数据
                $.ajax({
                    type: 'POST',
                    data: {'_token': "{{ csrf_token() }}"},
                    dataType: 'json',
                    url: common.route('{{route('agent.search',':id')}}', {id: value}),
                    success: function (data) {
                        if (data.code) {
                            layer.msg(data.msg)
                        } else {
                            var $html = "";
                            if (data.data != null) {
                                $.each(data.data, function (index, item) {
                                    $html += "<option value='" + index + "'>" + item + "</option>";
                                });
                                $("#city").append($html);
                                //append后必须从新渲染
                                form.render('select');
                            }
                        }
                    }
                })
            });
            //监听市下拉框
            form.on('select(city)', function (dataObj) {
                //移除县区下拉框所有选项
                $("#area").empty();
                var html = '<option value="">县/区</option>';
                $("#area").html(html);
                var value = $("#city").val();
                if(!value){
                    form.render('select');
                    return;
                }
                //异步加载下拉框数据
                $.ajax({
                    type: 'POST',
                    data: {'_token': "{{ csrf_token() }}"},
                    dataType: 'json',
                    url: common.route('{{route('agent.search',':id')}}', {id: value}),
                    success: function (data) {
                        if (data.code) {
                            layer.msg(data.msg)
                        } else {
                            var $html = "";
                            if (data.data != null) {
                                $.each(data.data, function (index, item) {
                                    $html += "<option value='" + index + "'>" + item + "</option>";
                                });
                                $("#area").append($html);
                                //append后必须从新渲染
                                form.render('select');
                            }
                        }
                    }
                })
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