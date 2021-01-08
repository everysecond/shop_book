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
            margin-right: 15px !important;
        }
        .layui-table-cell{
            padding:0 8px
        }
        .layui-layer-setwin .layui-layer-close2 {
            right: 0 !important;
            top: 0 !important;
        }
    </style>
@endsection

@section('content')
    <div class="layui-col-md12">
        <div class="layui-card" style="padding:10px 25px;height:110px;">
            <div class="layui-card-header" style="padding: 0;padding-bottom: 2px !important;">
                <div class="title-font title-select" data-type="myself" lay-filter="LAY-user-back-search" lay-submit>
                    我的
                </div>
                <div class="title-font" data-type="myteam" lay-filter="LAY-user-back-search" lay-submit>我协作的</div>
                <div class="title-font" data-type="under" lay-filter="LAY-user-back-search" lay-submit>下属的</div>
                <div class="title-font" data-type="underteam" lay-filter="LAY-user-back-search" lay-submit>下属协作的</div>
            </div>
            <div class="layui-form" style="padding:15px 0;">
                <div class="layui-inline" align="left">
                    <input type="text" id="searchStr" placeholder="姓名、客户名称、号码" autocomplete="off"
                           class="layui-input">
                </div>
                <div class="layui-inline" align="left">
                    <select name="is_key">
                        <option value="">关键决策人</option>
                        <option value="1">是</option>
                        <option value="2">不是</option>
                    </select>
                </div>
                <div class="layui-inline" align="left">
                    <div class="layui-input-inline" style="width: 210px;">
                        <input type="text" class="layui-input" placeholder="创建时间"
                               autocomplete="off" id="date1">
                    </div>
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
            <div style="margin-bottom: 10px">
                <button class="cps-btn letter2 mybtn btn-batch" for="LAY-user-back-manage"
                        href="{{route('contact.destroy','batch')}}" data-method="delete"
                        data-confirm="是否确认删除选中的数据？">删除
                </button>
{{--                <button class="cps-btn mybtn ml10" title="导出excel,请控制筛选范围在1W以下，否则数据过大下载较慢" onclick="doExport()"--}}
{{--                        style="float: right;width: 80px;">--}}
{{--                    导出excel--}}
{{--                </button>--}}
                <button class="layui-btn layui-btn-sm open-frame2 mybtn" title="新增联系人"
                        id="add_staff" style="float: right" data-width="850px"
                        href="{{route('contact.create.by.console')}}" data-height="520px">
                    新增联系人
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
        var position_id = '', table, type = 'myself',layer,doExport;
        layui.use(['laydate', 'index', 'table', 'form', 'common', 'http', 'tree', 'util'], function () {
            layer = layui.layer;
            var laydate = layui.laydate;
            laydate.render({
                elem: '#date1'
                , type: 'date'
                , range: true
            });

            var http = layui.http, common = layui.common, $ = layui.$, form = layui.form;
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
                url: '{{route('contact.paginate')}}',
                page: 1,
                limit: 15,
                limits: [10, 15, 20, 30, 40, 50, 60, 70, 100],
                cols: [
                    [
                        {type: "checkbox", fixed: "left",width:'6%'},
                        {
                            field: "name",
                            title: "姓名",
                            width:'8%',
                            templet: function (item) {
                                var deltail = common.route('{{route('contact.edit',[':id','type'=>'detail'])}}', {id: item.id});
                                return '<a class="open-frame2 color-green" data-method="get" href="' + deltail + '" ' +
                                    'title="详情" data-width="850px" data-type="detail"' +
                                    ' data-height="520px">' + item.name + '</a>';
                            }
                        },
                        {
                            title: "关联客户(手机号)",
                            width:'20%',
                            templet: function (item) {
                                var name = item.cus.name ? item.cus.name : item.cus.mobile;
                                var deltail = '/manage/crm/cus/detail/'+item.cus.id;
                                return '<a class="open-frame-r color-green" data-method="get" href="' + deltail + '" ' +
                                    'title="客户详情" data-width="1050px" data-type="detail"' +
                                    ' data-height="100%">' + name + '</a>';
                            }
                        },
                        {
                            field: "sex",
                            title: "称呼",
                            width:'5%'
                        },
                        {
                            field: "mobile",
                            title: "手机",
                            width:'10%'
                        },
                        {
                            field: "wechat",
                            title: "微信",
                            width:'11%'
                        },
                        {
                            field: "position",
                            title: "职务",
                            width:'6%'
                        },
                        {
                            field: "is_key",
                            title: "决策人",
                            width:'6%'
                        },
                        {
                            title: "创建人",
                            width:'6%',
                            templet: function (item) {
                                return item.createdUser.name;
                            }
                        },
                        {field: "created_at", title: "创建时间",width:'12%'},
                        {
                            title: "操作",
                            align: "center",
                            fixed: "right",
                            templet: function (item) {
                                if (type == "myself") {
                                    var edit = common.route('{{route('contact.edit',':id')}}', {id: item.id});
                                    return '<a class="layui-btn layui-btn-normal layui-btn-xs open-frame2" ' +
                                        'data-method="get" href="' + edit + '" ' +
                                        'title="编辑联系人" data-width="850px" data-height="520px">' +
                                        '<i class="layui-icon layui-icon-edit"></i>编辑</a>';
                                } else {
                                    return "";
                                }
                            }
                        }
                    ]
                ],
                where: {orderBy: 'created_at', sortedBy: 'desc'}
            });

            form.on('submit(LAY-user-back-search)', function (data) {
                table.reload('LAY-user-back-manage', {
                    where: {
                        searchJoin: 'and',
                        searchStr: $('#searchStr').val(),
                        date: $('#date1').val(),
                        is_key: $('select[name="is_key"]').val(),
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
                var btn = $(this).data('type')?'':['确定', '取消'];
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
                                    searchJoin: 'and',
                                    searchStr: $('#searchStr').val(),
                                    date: $('#date1').val(),
                                    is_key: $('select[name="is_key"]').val(),
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
                var btn = $(this).data('type')?'':['确定', '取消'];
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
                                    province_id: $('select[name="province_id"]').val(),
                                    type: type,
                                }
                            });
                        }, 1000);
                    },end:function () {
                        table.reload('LAY-user-back-manage', {
                            where: {
                                searchStr: $('#searchStr').val(),
                                cus_type: $('select[name="cus_type"]').val(),
                                cus_source: $('select[name="cus_source"]').val(),
                                cus_level: $('select[name="cus_level"]').val(),
                                province_id: $('select[name="province_id"]').val(),
                                type: type,
                            }
                        });
                    }
                });
            });

            {{--doExport = function () {//检索筛选--}}
            {{--    var params = {--}}
            {{--        searchStr: $('#searchStr').val(),--}}
            {{--        cus_type: $('select[name="cus_type"]').val(),--}}
            {{--        cus_source: $('select[name="cus_source"]').val(),--}}
            {{--        cus_level: $('select[name="cus_level"]').val(),--}}
            {{--        history_deal: $('select[name="history_deal"]').val(),--}}
            {{--        province_id: $('select[name="province_id"]').val(),--}}
            {{--        date1: $('#date1').val(),--}}
            {{--        type: type,--}}
            {{--        orderBy: 'created_at',--}}
            {{--        sortedBy: 'desc'--}}
            {{--    };--}}
            {{--    queryString = $.param(params);--}}
            {{--    window.location.href = "{{route('contact.down')}}?" + queryString;--}}
            {{--}--}}
        });
    </script>
@endsection
