@extends('manage::layouts.frame')

@section('style')
    <link href="/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
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

        .layui-form-label{
            padding: 9px 0;
            margin-left: 15px;
        }
    </style>
@endsection

@section('content')
    <div class="layui-form" lay-filter="layuiadmin-form-admin" id="layuiadmin-form-admin"
         style="padding: 20px 30px 0 0;">
        <div class="layui-col-md12">
            <div class="layui-col-md6">
                <div class="layui-form-item">
                    <label class="layui-form-label" title="客户名称必须为搜索后的选择结果"><span class="red">*</span>&nbsp;&nbsp;&nbsp;客户名称</label>
                    <div class="layui-input-block">
                        <div class="input-group" style="width: 101%;">
                            <input type="text" class="layui-input" id="testNoBtn" autocomplete="off">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-white dropdown-toggle" data-toggle="" style="display: none;">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu" style="padding-top: 0px; max-height: 375px; max-width: 800px; overflow: auto; width: auto; transition: all 0.3s ease 0s;">
                                </ul>
                            </div>
                            <!-- /btn-group -->
                        </div>
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
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js?v=3.3.6"></script>
    <script src="/js/suggest/bootstrap-suggest.min.js"></script>
    <script type="text/javascript">
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
        });

        var layerId = 0;
        layui.use(['jquery', 'index', 'form', 'http', 'common'], function () {
            var form = layui.form, http = layui.http, $ = layui.$, common = layui.common;

            form.on("submit(LAY-user-back-submit)", function (data) {
                data.field.cus_id = $('#testNoBtn').data('id');
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