@extends('manage::layouts.master')

@section('content')
    <div class="layui-fluid">
        <div class="layui-card">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">登录名(手机)</label>
                        <div class="layui-input-block">
                            <input type="text" name="mobile" placeholder="请输入登录手机号" autocomplete="off"
                                   class="layui-input">
                        </div>
                    </div>
                    {{--                    <div class="layui-inline">--}}
                    {{--                        <label class="layui-form-label">手机</label>--}}
                    {{--                        <div class="layui-input-block">--}}
                    {{--                            <input type="text" name="mobile" placeholder="请输入" autocomplete="off" class="layui-input">--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}
                    <div class="layui-inline">
                        <label class="layui-form-label">姓名</label>
                        <div class="layui-input-block">
                            <input type="text" name="name" placeholder="请输入姓名" autocomplete="off" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-inline">
                        <button class="layui-btn layuiadmin-btn-admin" lay-submit lay-filter="LAY-user-back-search">
                            <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="layui-card-body">
                <div style="padding-bottom: 10px;">
                    <button class="layui-btn btn-batch" for="LAY-user-back-manage"
                            href="{{route('manager.destroy','batch')}}" data-method="delete"
                            data-confirm="是否确认删除选中的数据？">删除
                    </button>
                    <button class="layui-btn open-frame" href="{{route('manager.create')}}" title="添加管理员"
                            data-height="550px">添加
                    </button>
                </div>

                <table id="LAY-user-back-manage" lay-filter="LAY-user-back-manage"></table>
                <script type="text/html" id="status-tpl">
                    @{{#  if(d.status == 1){ }}
                    <button class="layui-btn layui-btn-xs">正常</button>
                    @{{#  } else if (d.status == 2){ }}
                    <button class="layui-btn layui-btn-xs layui-btn-disabled">已冻结</button>
                    @{{#  } else { }}
                    <button class="layui-btn layui-btn-xs layui-btn-primary">已冻结</button>
                    @{{# } }}
                </script>

                <script type="text/html" id="toolbar">
                    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit">
                        <i class="layui-icon layui-icon-edit"></i>编辑</a>
                    @{{# if(!d.is_super){ }}
                    <a class="layui-btn layui-btn-warm layui-btn-xs" lay-event="permission">
                        <i class="layui-icon layui-icon-auz"></i> 授权
                    </a>
                    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i
                                class="layui-icon layui-icon-delete"></i>删除</a>
                    @{{# } }}
                </script>

                <script type="text/html" id="role-tpl">
                    @{{# for(var i=0; i < d.roles.length; i++) { }}
                    <span class="layui-badge layui-bg-blue">@{{ d.roles[i] }}</span>
                    @{{# } }}
                </script>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index', 'table', 'form', 'common', 'http'], function () {
            var table = layui.table, form = layui.form, http = layui.http, common = layui.common;

            table.render({
                elem: "#LAY-user-back-manage",
                url: '{{route('manager.paginate')}}',
                page: 1,
                cols: [
                    [
                        {type: "checkbox", fixed: "left"},
                        {field: "id", width: 80, title: "ID", sort: true},
                        {field: "name", title: "姓名", sort: true},
                        {field: "mobile", title: "登录名(手机)", sort: true},
                        // {field: "mobile", title: "手机", sort: true},
                        {field: 'role', title: '角色', templet: '#role-tpl'},
                        {field: "created_at", title: "加入时间", sort: true},
                        {field: "access_at", title: "(移动端)最后登录时间"},
                        {field: "last_login_at", title: "(web端)最后登录时间"},
                        {
                            field: "status",
                            title: "审核状态",
                            templet: "#status-tpl",
                            minWidth: 80,
                            align: "center",
                            sort: true
                        },
                        {title: "操作", width: 200, align: "center", fixed: "right", toolbar: "#toolbar"}
                    ]
                ],
                autoSort: false,
                where: {orderBy: 'id', sortedBy: 'desc'}
            });

            var active = {};

            active.del = function (e) {
                layer.confirm("确定删除此管理员？", {icon: 3, title: '删除'}, function (index) {
                    http.delete(common.route('{{route('manager.destroy',':id')}}', {id: e.data.id})).then(function (res) {
                        e.del();
                        layer.close(index);
                        layer.msg(res.msg);
                    });
                });
            }

            active.edit = function (e) {
                layer.open({
                    type: 2,
                    title: "编辑管理员",
                    content: common.route("{{route('manager.edit',':id')}}", {id: e.data.id}),
                    area: ["420px", "550px"],
                    btn: ["确定", "取消"],
                    yes: function (index, layero) {
                        var iframeWindow = window["layui-layer-iframe" + index];

                        if (common.isFunction(iframeWindow.layerYesCallback)) {
                            iframeWindow.layerYesCallback(index, layero);
                        }
                    }
                })
            }
            active.permission = function (e) {
                layer.open({
                    type: 2,
                    title: "管理员授权",
                    content: '{{route('manager.permission')}}?id=' + e.data.id,
                    area: ["420px", "430px"],
                    btn: ["确定", "取消"],
                    yes: function (index, layero) {
                        var iframeWindow = window["layui-layer-iframe" + index];

                        if (common.isFunction(iframeWindow.layerYesCallback)) {
                            iframeWindow.layerYesCallback(index, layero);
                        }
                    }
                })
            }

            table.on("tool(LAY-user-back-manage)", function (e) {
                active[e.event] && common.isFunction(active[e.event]) && active[e.event].call(this, e);
            });

            //监听搜索
            form.on('submit(LAY-user-back-search)', function (data) {
                table.reload('LAY-user-back-manage', {
                    where: {
                        searchJoin: 'and',
                        search: common.searchPack(data.field)
                    }
                });
            });

            table.on('sort(LAY-user-back-manage)', function (res) {
                table.reload('LAY-user-back-manage', {
                    where: {
                        orderBy: res.field,
                        sortedBy: res.type
                    }
                });
            });
        });
    </script>
@endsection
