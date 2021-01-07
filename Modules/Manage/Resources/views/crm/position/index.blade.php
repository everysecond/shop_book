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

        .layui-card-header{
            border-bottom: 1px solid #cccccc;
        }
    </style>
@endsection

@section('content')
    <div class="layui-col-md12">
        <div class="layui-col-md3">
            <div class="layui-card" style="padding: 25px;height:100%;overflow: auto;">
                <div class="layui-card-header">
                    <span class="title-font">职位</span>
                    <button class="layui-btn layui-btn-sm open-frame" title="添加职位"
                            style="float: right"
                            href="{{route('position.create')}}" data-height="430px">添加职位
                    </button>
                </div>
                <div id="test9" class="demo-tree demo-tree-box"
                     style="width: 250px; height: 80%;margin-top: 15px;"></div>
            </div>
        </div>
        <div class="layui-col-md9">
            <div class="layui-card" style="padding: 25px;height:100%;overflow: auto;">
                <div class="layui-card-header">
                    <span class="title-font">(职位:<span id="position_title">{{$defaultPosition->title}}</span>)人员</span>
                    <button class="layui-btn layui-btn-sm open-frame" title="添加人员"
                            id="add_staff" style="float: right"
                            href="{{route('staff.create',["position_id"=>$defaultPosition->id])}}" data-height="430px">
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
        var tree = "";
        var position_id = '{{$defaultPosition->id}}';
        var isLoad = false;
        var table;
        var treeLoad;
        layui.use(['index', 'table', 'form', 'common', 'http', 'tree', 'util'], function () {
            var form = layui.form, http = layui.http, common = layui.common,
                layer = layui.layer, util = layui.util, $ = layui.$;
            tree = layui.tree;
            table = layui.table;

            table.render({
                elem: "#LAY-user-back-manage",
                url: '{{route('staff.paginate')}}',
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
                        {field: "created_at", title: "加入时间"},
                        {
                            title: "操作",
                            width: 200,
                            align: "center",
                            fixed: "right",
                            templet: function (item) {
                                var route = common.route('{{route('staff.destroy',':id')}}', {id: item.id});
                                return '<a class="layui-btn layui-btn-danger layui-btn-xs ajax-link2" ' +
                                    'data-method="delete" href="' + route + '" ' +
                                    'title="移除人员" data-confirm="是否确认移除？">' +
                                    '<i class="layui-icon layui-icon-delete"></i>移除</a>';
                            }
                        }
                    ]
                ],
                where: {orderBy: 'id', sortedBy: 'desc', position_id: position_id}
            });

            treeLoad = function () {
                http.post(common.route('{{route('position.show')}}')).then(function (res) {
                    tree.render({
                        elem: '#test9'
                        , id: 'position'
                        , data: res.data
                        , onlyIconControl: true
                        // , showCheckbox: true
                        , click: function (obj) {
                            var id = obj.data.id, elem = obj.elem;
                            if (obj.data.children.length > 0) {
                                $(elem).find(".layui-tree-entry:eq(0)").find(".inline-div").remove();
                                var operate = '<div class="layui-btn-group layui-tree-btnGroup inline-div">';
                                operate += '<i class="layui-icon layui-icon-add-1" data-id="' + id + '"></i>';
                                operate += '<i class="layui-icon layui-icon-edit" data-id="' + id + '"></i></div>';
                                $(elem).find(".layui-tree-entry:eq(0)").append(operate);
                            } else {
                                $(elem).find(".layui-tree-entry:eq(0)").find(".inline-div").remove();
                                var operate = '<div class="layui-btn-group layui-tree-btnGroup inline-div">';
                                operate += '<i class="layui-icon layui-icon-add-1" data-id="' + id + '"></i>';
                                operate += '<i class="layui-icon layui-icon-edit" data-id="' + id + '"></i>';
                                operate += '<i class="layui-icon layui-icon-delete" data-id="' + id + '"></i></div>';
                                $(elem).find(".layui-tree-entry:eq(0)").append(operate);
                            }

                            if (null === $(this).click.caller) {
                                position_id = id;
                                $("#position_title").html(obj.data.title);
                                $("#add_staff").attr("href", "{{route('staff.create')}}?position_id=" + id);
                                table.reload('LAY-user-back-manage', {
                                    page: {curr: 1},
                                    where: {orderBy: 'id', sortedBy: 'desc', position_id: id}
                                });
                            }
                        }
                    });
                    $(".layui-tree-txt").each(function () {
                        $(this).click();
                    });
                });
            }

            treeLoad();

            $("body").on('click', '.layui-icon-add-1', function () {
                layer.open({
                    type: 2,
                    title: "添加职位",
                    content: common.route("{{route('position.create')}}?id=" + $(this).data("id")),
                    area: ["420px", "430px"],
                    btn: ["确定", "取消"],
                    yes: function (index, layero) {
                        var iframeWindow = window["layui-layer-iframe" + index];
                        if (common.isFunction(iframeWindow.layerYesCallback)) {
                            iframeWindow.layerYesCallback(index, layero);
                        }
                    }
                })
            });

            $("body").on('click', '.layui-icon-edit', function () {
                layer.open({
                    type: 2,
                    title: "编辑职位",
                    content: common.route("{{route('position.edit',':id')}}", {id: $(this).data("id")}),
                    area: ["420px", "430px"],
                    btn: ["确定", "取消"],
                    yes: function (index, layero) {
                        setTimeout(function () {
                            treeLoad();
                        },300);
                        var iframeWindow = window["layui-layer-iframe" + index];
                        if (common.isFunction(iframeWindow.layerYesCallback)) {
                            iframeWindow.layerYesCallback(index, layero);
                        }
                    }
                })
            });

            $("body").on('click', '.layui-icon-delete', function () {
                var id = $(this).data("id");
                layer.confirm("是否确定删除该职位以及拥有该职位的所有人员？", {icon: 3, title: '删除'}, function (index) {
                    http.delete(common.route('{{route('position.destroy',':id')}}', {id: id})).then(function (res) {
                        location.reload();
                        layer.close(index);
                        layer.msg(res.msg);
                    });
                });
            });

            $("body").on('click', '.ajax-link2', function (e) {
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
        });
    </script>
@endsection
