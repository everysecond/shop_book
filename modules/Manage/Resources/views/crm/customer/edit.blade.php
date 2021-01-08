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
                    <label class="layui-form-label">客户名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="name" placeholder="" autocomplete="off" lay-verify="required"
                               class="layui-input"  value="{{$model->name}}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">客户类型</label>
                    <div class="layui-input-block">
                        {{Form::select("cus_type",$cusTypes,$model->cus_type,
                        ["lay-filter"=>"cus_type","placeholder"=>"客户类型","disabled",'readonly'])}}
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><span class="red">*</span>&nbsp;&nbsp;&nbsp;客户号码</label>
                    <div class="layui-input-block">
                        <input type="text" name="mobile" placeholder="" autocomplete="off" lay-verify="required"
                               class="layui-input" value="{{$model->mobile}}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">所在地区</label>
                    <div class="layui-input-inline" style="width: 20%;">
                        {{Form::select("province_id",$provinces,$model->province_id,
                        ["lay-filter"=>"province","lay-search","placeholder"=>"省"])}}
                    </div>
                    <div class="layui-input-inline" style="width: 20%;">
                        <select name="city_id" id="city" lay-filter="city" lay-search="">
                            <option value="">市</option>
                            @if($model->city_id)
                                <option value="{{$model->city_id}}" selected>{{$model->city_name}}</option>
                            @endif
                        </select>
                    </div>
                    <div class="layui-input-inline" style="width: 22.5%;">
                        <select name="county_id" id="area" lay-filter="area" lay-search="">
                            <option value="">县/区</option>
                            @if($model->county_id)
                                <option value="{{$model->county_id}}" selected>{{$model->county_name}}</option>
                            @endif
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-col-md6">
                <div class="layui-form-item">
                    <label class="layui-form-label">助记名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="short_name" placeholder="" autocomplete="off"
                               class="layui-input" value="{{$model->short_name}}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><span class="red">*</span>&nbsp;&nbsp;&nbsp;客户等级</label>
                    <div class="layui-input-block">
                        {{Form::select("cus_level",$cusLevels,$model->cus_level,
                        ["lay-filter"=>"cus_level","lay-search","placeholder"=>"客户类型",'lay-verify'=>'required'])}}
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">负责人</label>
                    <div class="layui-input-block">
                        {{Form::select("charger_id",$managers,$model->charger_id,
                        ["lay-filter"=>"charger_id","disabled","placeholder"=>"负责人"])}}
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">具体地址</label>
                    <div class="layui-input-block">
                        <input type="text" name="address" placeholder="" autocomplete="off"
                               class="layui-input" value="{{$model->address}}">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">备注</label>
                <div class="layui-input-block">
                    <textarea placeholder="" class="layui-textarea" style="width:721px" name="memo">{{$model->memo}}</textarea>
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
                http.put('{{route('cus.update',$model->id)}}', data.field).then(function (res) {
                    // parent.parent.table.reload('LAY-user-back-manage', {
                    //     where: {
                    //         searchStr: parent.parent.$('#searchStr').val(),
                    //         cus_type: parent.parent.$('select[name="cus_type"]').val(),
                    //         cus_source: parent.parent.$('select[name="cus_source"]').val(),
                    //         cus_level: parent.parent.$('select[name="cus_level"]').val(),
                    //         province_id: parent.parent.$('select[name="province_id"]').val(),
                    //         type: parent.parent.type
                    //     }
                    // });
                    parent.location.reload();
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