@extends('manage::layouts.frame')

@section('style')
    {{Html::style(asset('resource/lib/select2/css/select2.css'))}}
    <link href="/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <style>
        .layui-form-select dl {
            max-height: 350px;
        }

        .layui-col-md6 {
            width: 48%;
            float: left;
        }

        .layui-input-block {
            width: 390px;
        }

        .layui-form-item {
            margin-top: 10px;
            margin-bottom: 15px;
        }

        .select2 {
            width: 389px !important;
            line-height: 28px;
        }

        .layui-form-label {
            padding: 9px 0;
            margin-left: 15px;
        }
    </style>
@endsection

@section('content')
    <div class="layui-form" lay-filter="layuiadmin-form-admin" id="layuiadmin-form-admin"
         style="padding-top: 20px;">
        <div class="layui-form-item">
            <label class="layui-form-label" title="客户名称必须为搜索后的选择结果"><span
                        class="red">*</span>&nbsp;&nbsp;&nbsp;客户名称</label>
            <div class="layui-input-block">
                <input type="hidden" name="type" value="{{request('type')}}">
                @if(request('cusId'))
                    <input type="hidden" name="cus_id" value="{{request('cusId')}}">
                    <input type="text" placeholder="" autocomplete="off" lay-verify="required"
                           class="layui-input layui-disabled" readonly value="{{request('cusName')}}">
                @else
                    <div class="input-group" style="width: 101%;">
                        <input type="text" class="layui-input" id="testNoBtn" autocomplete="off">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-white dropdown-toggle" data-toggle=""
                                    style="display: none;">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu"
                                style="padding-top: 0px; max-height: 375px; max-width: 800px; overflow: auto; width: auto; transition: all 0.3s ease 0s;">
                            </ul>
                        </div>
                        <!-- /btn-group -->
                    </div>
                @endif
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">负责人</label>
            <div class="layui-input-block">
                {{Form::select("charger_id",$managers,$user->id,
                ["lay-filter"=>"charger_id","lay-verify"=>'required',"lay-search","placeholder"=>"负责人","disabled"])}}
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><span class="red">*</span>&nbsp;&nbsp;&nbsp;跟进时间</label>
            <div class="layui-input-block">
                <input type="text" class="layui-input" placeholder="跟进时间" name="follow_at"
                       autocomplete="off" id="date">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><span class="red">*</span>&nbsp;&nbsp;&nbsp;跟进方式</label>
            <div class="layui-input-block">
                {{Form::select("follow_mode",dictArr('crm_follow_mode'),'',
                ["lay-filter"=>"follow_mode","lay-search","placeholder"=>"跟进方式"])}}
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">联系人</label>
            <div class="layui-input-block">
                {{Form::select("contact_id",$contacts,'',["lay-filter"=>"follow_mode","lay-search"])}}
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><span class="red">*</span>&nbsp;&nbsp;&nbsp;跟进人员</label>
            <div class="layui-input-block">
                {{Form::select('',$teams,[],['class'=>'select2', 'lay-ignore', 'multiple'])}}
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><span class="red">*</span>&nbsp;&nbsp;&nbsp;跟进内容</label>
            <div class="layui-input-block">
                <textarea placeholder="" class="layui-textarea" lay-verify='required' style="width:390px"
                          name="content"></textarea>
            </div>
        </div>
        <div class="layui-form-item layui-hide">
            <input type="button" lay-submit lay-filter="LAY-user-back-submit" id="LAY-user-back-submit"
                   value="确认">
        </div>
    </div>
@endsection

@section('script')
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js?v=3.3.6"></script>
    <script src="/js/suggest/bootstrap-suggest.min.js"></script>
    <script>
        var layerId = 0;
        layui.extend({
            'select2': '../../select2/js/select2'
        }).use(['laydate', 'jquery', 'index', 'form', 'http', 'common', 'select2'], function () {
            var form = layui.form, http = layui.http, $ = layui.$, laydate = layui.laydate,
                common = layui.common, provinceText = "", cityText = "", areaText = "";
            laydate.render({
                elem: '#date',
                type: 'datetime',
                min: '{{now()}}'
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
            var roles = eval('({{getUserId()}})');
            $(".select2").val(roles || []).trigger('change');

            form.on("submit(LAY-user-back-submit)", function (data) {
                data.field.follow_user_ids = $('.select2').val() ? $('.select2').val().join(',') : '';
                data.field.cus_id = data.field.cus_id ? data.field.cus_id : $('#testNoBtn').data('id');
                http.post('{{route('plan.store')}}', data.field).then(function (res) {
                    common.msg(res.msg);
                    parent.location.reload();
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
                if (!value) {
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
                if (!value) {
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

            /**
             * 不显示下拉按钮
             */
            var testBsSuggest = $("#testNoBtn").bsSuggest({
                indexId: 0, //data.value 的第几个数据，作为input输入框的 data-id，设为 -1 且 idField 为空则不设置此值
                indexKey: 1, //data.value 的第几个数据，作为input输入框的内容
                idField: 'ID',//每组数据的哪个字段作为 data-id，优先级高于 indexId 设置（推荐）
                keyField: 'Keyword',//每组数据的哪个字段作为输入框内容，优先级高于 indexKey 设置（推荐）
                // allowNoKeyword: false, //是否允许无关键字时请求数据
                showBtn: true,
                multiWord: false, //以分隔符号分割的多关键字支持
                getDataMethod: "url", //获取数据的方式，总是从 URL 获取
                effectiveFields: ["Keyword"],
                effectiveFieldsAlias: {
                    Keyword: "员工"
                },
                showHeader: false,
                url: '{{route('cus.search')}}?code=utf-8&extras=1&Name=',
                processData: function (json) { // url 获取数据时，对数据的处理，作为 getData 的回调函数;
                    var i, len, data = {
                        value: []
                    };

                    if (!json || json.length == 0) {
                        return false;
                    }

                    len = json.length;

                    for (var j = 0; j < len; j++) {
                        data.value.push({
                            "Id": json[j].id,
                            "Keyword": json[j].name
                        });
                    }
                    return data;
                }
            }).on('onSetSelectValue', function (e, keyword) {
                //移除县区下拉框所有选项
                $("select[name='contact_id']").empty();
                $("select.select2").empty();
                var value = keyword.id;
                if (!value) {
                    form.render('select');
                    return;
                }
                //异步加载下拉框数据
                $.ajax({
                    type: 'get',
                    data: {'_token': "{{ csrf_token() }}"},
                    dataType: 'json',
                    url: '{{route('cus.change.search')}}?cusId=' + value,
                    success: function (data) {
                        if (data) {
                            if (data.contacts) {
                                var html = "";
                                $.each(data.contacts, function (index, item) {
                                    html += "<option value='" + index + "'>" + item + "</option>";
                                });
                                $("select[name='contact_id']").append(html);
                                //append后必须从新渲染
                                form.render('select');
                            }
                            if (data.teams) {
                                var html = "";
                                $.each(data.teams, function (index, item) {
                                    html += "<option value='" + index + "'>" + item + "</option>";
                                });
                                $("select.select2").append(html);
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