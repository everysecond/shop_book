@extends('manage::layouts.master')

@section('style')
    <style>
        .layui-col-md3, .layui-col-md9, .layui-col-md12 {
            padding: 5px;
        }

        .layui-card {
            min-height: 600px;
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
            font-family: 'Arial Negreta', 'Arial Normal', 'Arial';
            font-weight: 700;
            font-style: normal;
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

        .layui-btn + .layui-btn {
            margin-left: 3px;
        }

        .btn {
            display: block;
            height: 36px;
            line-height: 36px;
            width: 90%;
            background-color: #f2f2f2;
            color: #1abc9c;
            white-space: nowrap;
            text-align: center;
            font-size: 14px;
            border: none;
            border-radius: 2px;
            cursor: pointer;
            border: 1px solid #f2f2f2;
            margin-top: 18px;
        }

        .btn:hover {
            border: 1px solid #1abc9c;
        }

        .btn-select {
            color: white;
            background-color: #1abc9c;
            border: 1px solid #1abc9c;
        }
    </style>
@endsection

@section('content')
    <div class="layui-col-md12">
        <div class="layui-col-md3">
            <div class="layui-card" style="padding: 25px;height:100%;overflow: auto;">
                <div class="layui-card-header">
                    <span class="title-font">
                        公海
                        <i class="layui-icon layui-icon-tips"
                           style="position: inherit;top: 0;right: 0;left: 7px;color:darkorange;"
                           data-tips="若要删除公海请确保公海内所有客户已清空,且删除所有已使用该公海的公海规则">
                        </i>
                    </span>
                </div>
                <div style="padding: 10px 5px;">
                    <button class="layui-btn layui-btn-primary layui-btn-sm open-frame" title="新增"
                            href="{{route('sea.create')}}" data-height="430px">
                        &nbsp;&nbsp;新增&nbsp;&nbsp;
                    </button>
                    <button class="layui-btn layui-btn-primary layui-btn-sm open-frame-edit" title="编辑"
                            data-height="430px">
                        &nbsp;&nbsp;编辑&nbsp;&nbsp;
                    </button>
                    <button class="layui-btn layui-btn-primary layui-btn-sm ajax-link2" title="删除"
                            data-method="delete" data-confirm="是否确定删除该公海以及该公海的所有管理人员?">
                        &nbsp;&nbsp;删除&nbsp;&nbsp;
                    </button>
                </div>
                <div id="sea_tree" style="padding:0 10px; width: 100%">

                </div>
            </div>
        </div>
        <div class="layui-col-md9">
            <div class="layui-card" style="padding: 25px;height:100%;overflow: auto;">
                <div class="layui-card-header">
                    <span class="title-font"><span id="position_title">{{$defaultPosition->name}}</span>:人员</span>
                    <button class="layui-btn layui-btn-sm open-frame" title="添加人员"
                            id="add_staff" style="float: right"
                            href="{{route('sea-staff.create',["sea_id"=>$defaultPosition->id])}}" data-height="430px">
                        添加人员
                    </button>
                </div>
                <div class="layui-card-body" style=" height: 80%;">
                    <table id="LAY-user-back-manage" lay-filter="LAY-user-back-manage"></table>
                </div>
            </div>
        </div>
        <div style="clear: both"></div>
    </div>
@endsection

@section('script')
    <script>
        var tree = "", position_id = '{{$defaultPosition->id}}', table, treeLoad;
        layui.use(['index', 'table', 'form', 'common', 'http', 'tree', 'util'], function () {
            var form = layui.form, http = layui.http, common = layui.common,
                layer = layui.layer, util = layui.util, $ = layui.$;

            var tips = $(".layui-icon-tips");
            tips.mouseover(function () {
                layer.tips($(this).data("tips"), this, {tips: [2, "rgb(107, 106, 106)"], time: 0});
            });
            tips.mouseout(function () {
                layer.closeAll();
            });

            table = layui.table;
            table.render({
                elem: "#LAY-user-back-manage",
                url: '{{route('sea-staff.paginate')}}',
                page: 1,
                limit: 10,
                cols: [
                    [
                        // {type: "checkbox", fixed: "left"},
                        {field: "id", width: 80, title: "ID"},
                        {
                            field: "staff.name",
                            title: "姓名",
                            templet: function (item) {
                                return item.staff.name;
                            }
                        },
                        {
                            field: "staff.mobile",
                            title: "手机号/账号",
                            templet: function (item) {
                                return item.staff.mobile;
                            }
                        },
                        {
                            title: "权限",
                            templet: function (item) {
                                var a = item.can_assign == 2 ? "、分配" : "",
                                    b = item.can_get == 2 ? "、认领" : "";
                                return "查看" + a + b;
                            }
                        },
                        {field: "created_at", title: "加入时间"},
                        {
                            title: "操作",
                            width: 200,
                            align: "center",
                            fixed: "right",
                            templet: function (item) {
                                var route = common.route('{{route('sea-staff.destroy',':id')}}', {id: item.id}),
                                    edit = common.route('{{route('sea-staff.edit',':id')}}', {id: item.id});
                                return '<a class="layui-btn layui-btn-normal layui-btn-xs open-frame" ' +
                                    'data-method="get" href="' + edit + '" ' +
                                    'title="编辑权限">' +
                                    '<i class="layui-icon layui-icon-delete"></i>授权</a>' +
                                    '<a class="layui-btn layui-btn-danger layui-btn-xs ajax-link3" ' +
                                    'data-method="delete" href="' + route + '" ' +
                                    'title="移除人员" data-confirm="是否确认移除？">' +
                                    '<i class="layui-icon layui-icon-delete"></i>移除</a>';
                            }
                        }
                    ]
                ],
                where: {orderBy: 'id', sortedBy: 'desc', position_id: position_id}
            });

            $(document).on("click", ".btn", function () {
                $(".btn").removeClass("btn-select");
                $(this).addClass("btn-select");
                position_id = $(this).data("id");
                $("#position_title").html($(this).html());
                $("#add_staff").attr("href", "{{route('sea-staff.create')}}?sea_id=" + position_id);
                table.reload('LAY-user-back-manage', {
                    page: {curr: 1},
                    where: {orderBy: 'id', sortedBy: 'desc', position_id: position_id}
                });
            });


            treeLoad = function () {
                http.post(common.route('{{route('sea.show')}}')).then(function (res) {
                    if (res && res.data.length > 0) {
                        var seaTree = $("#sea_tree"), btn = '', selectBtn = "";
                        seaTree.html("");
                        for (var i = 0; i < res.data.length; i++) {
                            selectBtn = "";
                            if (res.data[i].id == position_id) {
                                selectBtn = "btn-select";
                            }
                            btn = '<button class="btn ' + selectBtn + '" data-id="'
                                + res.data[i].id + '">' + res.data[i].name + '</button>';
                            seaTree.append(btn);
                        }
                    }
                });
            }

            treeLoad();

            $("body").on('click', '.ajax-link2', function (e) {
                e.preventDefault();
                var href = common.route("{{route('sea.destroy',':id')}}", {id: position_id});
                var msg = $(this).data('confirm') || false;
                var method = $(this).data('method') || 'get';
                var title = this.title || '';

                var doSubmit = function () {
                    http.request(method, href).then(function (res) {
                        common.success(res.msg || '操作成功');
                        layer.closeAll();
                        location.reload();
                    });
                }
                if (msg) {
                    layer.confirm(msg, {
                        title: title,
                        icon: 3
                    }, function (index) {
                        doSubmit(index);
                    })
                } else {
                    doSubmit();
                }

            });

            $("body").on('click', '.ajax-link3', function (e) {
                e.preventDefault();
                var href = $(this).attr("href");
                var msg = $(this).data('confirm') || false;
                var method = $(this).data('method') || 'get';
                var title = this.title || '';

                var doSubmit = function () {
                    http.request(method, href).then(function (res) {
                        common.success(res.msg || '操作成功');
                        layer.closeAll();
                        table.reload('LAY-user-back-manage', {
                            page: {curr: 1},
                            where: {orderBy: 'id', sortedBy: 'desc', position_id: position_id}
                        });
                    });
                }
                if (msg) {
                    layer.confirm(msg, {
                        title: title,
                        icon: 3
                    }, function (index) {
                        doSubmit(index);
                    })
                } else {
                    doSubmit();
                }

            });

            $(document).on("click", ".open-frame-edit", function (e) {
                e.preventDefault();
                var href = common.route("{{route('sea.edit',':id')}}", {id: position_id});
                var full = $(this).data('full') || false;
                var width = $(this).data('width') || '420px';
                var height = $(this).data('height') || '420px';
                full && (width = height = '100%');

                layer.open({
                    type: 2,
                    title: '<i class="layui-icon layui-icon-app"></i> ' + this.title,
                    area: [width, height],
                    content: href,
                    shadeClose: true,
                    btn: ['确定', '取消'],
                    yes: function (index, layero) {
                        var frameWindow = window["layui-layer-iframe" + index];
                        if (common.isFunction(frameWindow.layerYesCallback)) {
                            frameWindow.layerYesCallback(index, layero);
                        }
                    }
                });
            });
        });
    </script>
@endsection
