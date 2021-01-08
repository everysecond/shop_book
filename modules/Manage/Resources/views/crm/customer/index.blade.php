@extends('manage::layouts.master')

@section('style')
    <style>
        .layui-col-md12 {
            /*margin: 5px;*/
        }

        .layui-card {
            margin: 10px;
        }

        .layui-icon-file::before {
            content: "\e67e";
            border: 1px solid #c0c4cc;
            font-size: 12px;
            color: #666;
        }

        .layui-icon-file {
            line-height: 15px;
        }

        .inline-div, .layui-tree-entry {
            display: inline;
        }

        .title-font {
            font-family: "微软雅黑 Bold", "微软雅黑 Regular", 微软雅黑;
            font-weight: 700;
            font-style: normal;
            color: rgb(30, 30, 30);
            width: 160px;
            text-align: center;
            display: inline-block;
            cursor: pointer;
        }

        .title-font:hover {
            color: rgb(26, 188, 156);
        }

        .title-select {
            color: rgb(26, 188, 156);
            border-bottom: 3px solid rgb(26, 188, 156);
        }

        html, body, .layui-col-md3, .layui-col-md12, .layui-col-md9 {
            height: 100%;
        }

        html, body {
            height: 97%;
        }

        .layui-card-header {
            border-bottom: 1px solid #cccccc;
        }

        .layui-inline {
            margin-right: 5px !important;
            margin-top: 10px;
        }

        .layui-table-cell {
            padding: 0 2px
        }

        .layui-layer-setwin .layui-layer-close2 {
            right: 0 !important;
            top: 0 !important;
        }
    </style>
@endsection

@section('content')
    <div class="layui-col-md12">
        <div class="layui-card" style="padding:10px 25px;min-height:110px;">
            <div class="layui-card-header" style="padding: 0;padding-bottom: 2px !important;">
                <div class="title-font title-select" data-type="myself" lay-filter="LAY-user-back-search" lay-submit>
                    我的客户
                </div>
                <div class="title-font" data-type="myteam" lay-filter="LAY-user-back-search" lay-submit>我协作的</div>
                <div class="title-font" data-type="under" lay-filter="LAY-user-back-search" lay-submit>下属客户</div>
                <div class="title-font" data-type="underteam" lay-filter="LAY-user-back-search" lay-submit>下属协作的</div>
            </div>
            <div class="layui-form" style="padding:15px 0;">
                <div class="layui-inline" align="left" style="width:125px;">
                    <input type="text" id="searchStr" placeholder="名称/号码/负责人" autocomplete="off"
                           class="layui-input">
                </div>
                <div class="layui-inline" align="left" style="width:98px;">
                    <select name="cus_type">
                        <option value="">客户类型</option>
                        <option value="1">租点用户</option>
                        <option value="2">租点网点</option>
                        {{--                        <option value="3">快点用户</option>--}}
                        {{--                        <option value="4">快点网点</option>--}}
                    </select>
                </div>
                <div class="layui-inline" align="left" style="width:98px;">
                    <select name="cus_level">
                        <option value="">客户等级</option>
                        <option value="1">重点客户</option>
                        <option value="2">普通客户</option>
                        <option value="3">非优先客户</option>
                    </select>
                </div>
                <div class="layui-inline" align="left" style="width:98px;">
                    <select name="cus_source">
                        <option value="">客户来源</option>
                        <option value="1">APP录入</option>
                        <option value="2">租点系统</option>
                        <option value="3">中台录入</option>
                    </select>
                </div>
                <div class="layui-inline" align="left" style="width:98px;">
                    <select name="history_deal">
                        <option value="">成交状态</option>
                        <option value="2">成交</option>
                        <option value="1">未成交</option>
                    </select>
                </div>
                <div class="layui-inline" align="left" style="width:90px;">
                    <select name="province_id">
                        <option value="">区域</option>
                        @foreach(allLeaseProvinces() as $id=>$name)
                            <option value="{{$id}}">{{$name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="layui-inline" align="left" style="width: 175px;">
                    <input type="text" class="layui-input" placeholder="创建时间"
                           autocomplete="off" id="date1">
                </div>
                <div class="layui-inline" align="left" style="width: 175px;">
                    <input type="text" class="layui-input" placeholder="租赁合约到期时间"
                           autocomplete="off" id="date2">
                </div>
                <div class="layui-inline" align="left" style="width: 175px;">
                    <input type="text" class="layui-input" placeholder="网点合同到期时间"
                           autocomplete="off" id="date3">
                </div>


                <div class="layui-inline">
                    <button class="layui-btn" lay-filter="LAY-user-back-search" lay-submit>
                        <i class="layui-icon"></i>搜索
                    </button>
                    <button class="layui-btn layui-btn-primary clearInput" style="width: 86px">清空
                    </button>
                </div>
            </div>
        </div>
        <div class="layui-card" style="padding: 25px;height:80%;">
            <div style="margin-bottom: 10px;height:30px">
                <button class="cps-btn mybtn batch-operate" for="LAY-user-back-manage"
                        title="转移给他人" href="{{route('cus.move')}}">转移给他人
                </button>
                <button class="cps-btn mybtn batch-operate" for="LAY-user-back-manage"
                        title="移入公海" href="{{route('cus.move.sea')}}">移入公海
                </button>
                <button class="cps-btn mybtn btn-batch" for="LAY-user-back-manage"
                        data-confirm="是否确认标记/取消选中的数据？"
                        href="{{route('cus.mark','batch')}}" data-method="put">标记/取消
                </button>
                <button class="cps-btn mybtn btn-batch" for="LAY-user-back-manage"
                        data-confirm="是否确认置顶/取消选中的数据？"
                        href="{{route('cus.top','batch')}}" data-method="put">置顶/取消
                </button>
                <button class="cps-btn ml10" title="导出excel,请控制筛选范围在1W以下，否则数据过大下载较慢" onclick="doExport()"
                        style="float: right;width: 80px;">
                    导出excel
                </button>
                <button class="layui-btn layui-btn-sm open-frame2 mybtn" title="新增客户"
                        style="float: right;width: 80px;" data-width="900px"
                        href="{{route('cus.create')}}" data-height="520px">
                    新增客户
                </button>
            </div>

            <div class="layui-card-body" style=" height: 100%;padding: 0;overflow: auto;zoom:1;">
                <table id="LAY-user-back-manage" lay-filter="LAY-user-back-manage"></table>
            </div>
        </div>
        <div style="clear: both"></div>
    </div>
@endsection

@section('script')
    <script>
        var position_id = '', table, type = 'myself', layer, $, doExport;
        layui.use(['laydate', 'index', 'table', 'form', 'common', 'http', 'tree', 'util'], function () {
            layer = layui.layer;
            var laydate = layui.laydate;
            laydate.render({
                elem: '#date1'
                , type: 'date'
                , range: true
            });
            laydate.render({
                elem: '#date2'
                , type: 'date'
                , range: true
            });
            laydate.render({
                elem: '#date3'
                , type: 'date'
                , range: true
            });

            var http = layui.http, common = layui.common, form = layui.form;
            $ = layui.$;
            $('.title-font').click(function () {
                    $('.title-font').removeClass('title-select');
                    $(this).addClass('title-select');
                    type = $(this).data('type');
                    if (type != "myself") {
                        $(".mybtn").addClass("hidden");
                    } else {
                        $(".mybtn").removeClass("hidden");
                    }
                }
            );

            table = layui.table;
            table.render({
                elem: "#LAY-user-back-manage",
                url: '{{route('cus.paginate')}}',
                page: 1,
                limit: 10,
                limits: [10, 15, 20, 30, 40, 50, 60, 70, 100],
                cols: [
                    [
                        {type: "checkbox", fixed: "left"},
                        {
                            field: "is_mark",
                            title: "标记/置顶",
                            align: "center",
                            templet: function (item) {
                                var icon, title, top = '';
                                if (item.is_mark == 1) {
                                    icon = 'layui-icon-star-fill';
                                    title = '已标记';
                                } else {
                                    icon = 'layui-icon-star';
                                    title = '未标记';
                                }
                                if (item.is_top == 1) {
                                    top = '<i class="font18 layui-icon color-green layui-icon-upload-circle ml5" title = "已置顶"></i>'
                                }
                                return '<a href="javascript:;" title="' + title + '">' +
                                    '<i class="font20 layui-icon color-green ' + icon + '"></i>' + top + '</a>';
                            }
                        },
                        {
                            field: "name",
                            title: "客户名称(手机号)",
                            templet: function (item) {
                                var name = item.name ? item.name : item.mobile;
                                var deltail = '/manage/crm/cus/detail/' + item.id;
                                return '<a class="open-frame-r color-green" data-method="get" href="' + deltail + '" ' +
                                    'title="客户详情" data-width="1050px" data-type="detail"' +
                                    ' data-height="100%">' + name + '</a>';
                            }
                        },
                        {
                            field: "cus_type",
                            title: "客户类型",
                        },
                        {
                            field: "cus_level",
                            title: "客户等级",
                        },
                        {
                            field: "cus_source",
                            title: "客户来源",
                        },
                        {
                            title: "客户号码",
                            field: 'mobile',
                        },
                        {
                            field: "first_contact",
                            title: "首联系人",
                        },
                        {
                            field: "first_contact_mobile",
                            title: "首联系人号码",
                        },
                        {
                            field: "area",
                            title: "所在地区",
                        },
                        {
                            field: "charger_name",
                            title: "负责人",
                        },
                        {
                            title: "历史成交",
                            templet: function (item) {
                                if (item.history_deal == 2) {
                                    return '成交';
                                } else {
                                    return '未成交';
                                }
                            }
                        },
                        {
                            field: "constract_end_at",
                            title: "租赁合约到期",
                            templet: function (item) {
                                if(item.cus_type == '租点用户'){
                                    return item.contract?item.contract.lease_expired_at:'';
                                }else{
                                    return '';
                                }
                            }
                        },
                        {
                            field: "constract_end_at",
                            title: "网点合同到期"
                        },
                        {
                            field: "preFollowAt",
                            title: "最后跟进"
                        },
                        {field: "created_at", title: "创建时间"}
                    ]
                ],
                where: {orderBy: 'created_at', sortedBy: 'desc'}
            });

            form.on('submit(LAY-user-back-search)', function (data) {
                table.reload('LAY-user-back-manage', {
                    page: {curr: 1},
                    where: {
                        searchStr: $('#searchStr').val(),
                        cus_type: $('select[name="cus_type"]').val(),
                        cus_source: $('select[name="cus_source"]').val(),
                        cus_level: $('select[name="cus_level"]').val(),
                        history_deal: $('select[name="history_deal"]').val(),
                        province_id: $('select[name="province_id"]').val(),
                        date1: $('#date1').val(),
                        date2: $('#date2').val(),
                        date3: $('#date3').val(),
                        type: type,
                    }
                });
            });
            //监听.open-frame2
            $(document).on("click", ".open-frame2", function (e) {
                e.preventDefault();
                var href = $(this).attr("href");
                var full = $(this).data('full') || false;
                var width = $(this).data('width') || '420px';
                var height = $(this).data('height') || '420px';
                var btn = $(this).data('type') ? '' : ['确定', '取消'];
                full && (width = height = '100%');

                layer.open({
                    type: 2,
                    title: '<i class="layui-icon layui-icon-app"></i> ' + this.title,
                    area: [width, height],
                    content: href,
                    shadeClose: true,
                    btn: btn,
                    yes: function (index, layero) {
                        var frameWindow = window["layui-layer-iframe" + index];
                        if (common.isFunction(frameWindow.layerYesCallback)) {
                            frameWindow.layerYesCallback(index, layero);
                        }
                        setTimeout(function () {
                            table.reload('LAY-user-back-manage', {
                                where: {
                                    searchStr: $('#searchStr').val(),
                                    cus_type: $('select[name="cus_type"]').val(),
                                    cus_source: $('select[name="cus_source"]').val(),
                                    cus_level: $('select[name="cus_level"]').val(),
                                    history_deal: $('select[name="history_deal"]').val(),
                                    province_id: $('select[name="province_id"]').val(),
                                    date1: $('#date1').val(),
                                    type: type,
                                }
                            });
                        }, 1000);
                    }
                });
            });

            $(document).on("click", ".open-frame-r", function (e) {
                e.preventDefault();
                var href = $(this).attr("href");
                var full = $(this).data('full') || false;
                var width = $(this).data('width') || '420px';
                var height = $(this).data('height') || '420px';
                var btn = $(this).data('type') ? '' : ['确定', '取消'];
                full && (width = height = '100%');

                layer.open({
                    type: 2,
                    title: false,
                    area: [width, height],
                    content: href,
                    anim: 2,
                    closeBtn: 1,
                    btn: btn,
                    offset: 'r',
                    yes: function (index, layero) {
                        var frameWindow = window["layui-layer-iframe" + index];
                        if (common.isFunction(frameWindow.layerYesCallback)) {
                            frameWindow.layerYesCallback(index, layero);
                        }
                        setTimeout(function () {
                            table.reload('LAY-user-back-manage', {
                                where: {
                                    searchStr: $('#searchStr').val(),
                                    cus_type: $('select[name="cus_type"]').val(),
                                    cus_source: $('select[name="cus_source"]').val(),
                                    cus_level: $('select[name="cus_level"]').val(),
                                    history_deal: $('select[name="history_deal"]').val(),
                                    province_id: $('select[name="province_id"]').val(),
                                    date1: $('#date1').val(),
                                    type: type,
                                }
                            });
                        }, 1000);
                    }, end: function () {
                        table.reload('LAY-user-back-manage', {
                            where: {
                                searchStr: $('#searchStr').val(),
                                cus_type: $('select[name="cus_type"]').val(),
                                cus_source: $('select[name="cus_source"]').val(),
                                cus_level: $('select[name="cus_level"]').val(),
                                history_deal: $('select[name="history_deal"]').val(),
                                province_id: $('select[name="province_id"]').val(),
                                date1: $('#date1').val(),
                                type: type,
                            }
                        });
                    }
                });
            });

            doExport = function () {//检索筛选
                layer.confirm('速率约为500条/秒，确定导出?',function () {
                    var params = {
                        searchStr: $('#searchStr').val(),
                        cus_type: $('select[name="cus_type"]').val(),
                        cus_source: $('select[name="cus_source"]').val(),
                        cus_level: $('select[name="cus_level"]').val(),
                        history_deal: $('select[name="history_deal"]').val(),
                        province_id: $('select[name="province_id"]').val(),
                        date1: $('#date1').val(),
                        type: type,
                        orderBy: 'created_at',
                        sortedBy: 'desc'
                    };
                    queryString = $.param(params);
                    window.location.href = "{{route('cus.down')}}?" + queryString;
                    layer.closeAll();
                })
            }
        });
    </script>
@endsection
