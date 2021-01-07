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

        .layui-table-cell {
            padding: 0 8px
        }
    </style>
@endsection

@section('content')
    <div class="layui-col-md12">
        <div class="layui-card" style="padding:10px 25px;height:65px;">
            <div class="layui-form" style="padding:15px 0;">
                <div class="layui-inline" align="left">
                    <input type="text" id="searchStr" placeholder="类型、名称、键、值" autocomplete="off"
                           class="layui-input">
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
        <div class="layui-card" style="padding: 25px;height:calc(97% - 124px);">
            <div style="margin-bottom: 10px;height: 35px;">
                <button class="layui-btn layui-btn-sm open-frame mybtn" title="新增字典"
                        style="float: right" data-width="450px"
                        href="{{route('dict.create',["position_id"=>''])}}" data-height="440px">
                    新增字典
                </button>
            </div>
            <div class="layui-card-body" style=" height: 100%;padding: 0;overflow: auto;zoom:1;">
                <table id="LAY-user-back-manage" lay-filter="LAY-user-back-manage"></table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var table, layer;
        layui.use(['laydate', 'index', 'table', 'form', 'common', 'http', 'tree', 'util'], function () {
            layer = layui.layer;
            table = layui.table;
            var common = layui.common,form = layui.form,$=layui.$;
            table.render({
                elem: "#LAY-user-back-manage",
                url: '{{route('dict.paginate')}}',
                page: 1,
                limit: 10,
                limits: [10, 15, 20, 30, 40, 50, 60, 70, 100],
                cols: [
                    [
                        {
                            field: "dict_type",
                            title: "字典类型",
                            templet: function (item) {
                                var deltail = common.route('{{route('dict.edit',[':id'])}}', {id: item.id});
                                return '<a class="open-frame color-green" data-method="get" href="' + deltail + '" ' +
                                    'title="编辑" data-width="450px" data-type="detail"' +
                                    ' data-height="440px">' + item.dict_type + '</a>';
                            }
                        },
                        {
                            field: "type_means",
                            title: "类型名称"
                        },
                        {
                            field: "code",
                            title: "字典键"
                        },
                        {
                            field: "means",
                            title: "字典值"
                        },
                        {
                            field: "sort",
                            title: "排序"
                        },
                        {
                            title: "创建人",
                            width: '6%',
                            templet: function (item) {
                                return item.createdUser.name;
                            }
                        },
                        {field: "created_at", title: "创建时间", width: '12%'},
                        {
                            title: "操作",
                            width: 200,
                            align: "center",
                            fixed: "right",
                            templet: function (item) {
                                var route = common.route('{{route('dict.destroy',':id')}}', {id: item.id});
                                return '<a class="layui-btn layui-btn-danger layui-btn-xs ajax-link" ' +
                                    'data-method="delete" href="' + route + '" ' +
                                    'title="删除" data-confirm="是否确认删除？">' +
                                    '<i class="layui-icon layui-icon-delete"></i>移除</a>';
                            }
                        }
                    ]
                ]
            });

            form.on('submit(LAY-user-back-search)', function (data) {
                table.reload('LAY-user-back-manage', {
                    where: {
                        searchStr: $('#searchStr').val(),
                    }
                });
            });
        });
    </script>
@endsection
