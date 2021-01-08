@extends('manage::layouts.master')
@section('style')

@endsection
@section('content')
    <div class="layui-fluid" style="padding: 10px;">
        <div class="layui-card">
            <div class="layui-card-body">
                <div style="padding:15px 0;">
                    <button class="layui-btn layui-btn-primary layui-btn-sm open-frame" title="添加菜单"
                            href="{{route('manage-menu.create',['pid' => request('pid')])}}" data-full="1">添加菜单
                    </button>
                    <button type="button" class="layui-btn layui-btn-primary layui-btn-sm open-all">全部展开</button>
                    <button type="button" class="layui-btn layui-btn-primary layui-btn-sm close-all">全部关闭</button>
                    <button type="button" class="layui-btn layui-btn-primary layui-btn-sm refresh">刷新</button>
                </div>
                <table class="layui-table layui-form" id="tree-table" lay-size="sm"></table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['treeTable', 'index', 'common', 'layer', 'code', 'form'], function () {
            var o = layui.$,
                http = layui.http,
                common = layui.common,
                form = layui.form,
                layer = layui.layer,
                treeTable = layui.treeTable;
            // 直接下载后url: './data/table-tree.json',这个配置可能看不到数据，改为data:[],获取自己的实际链接返回json数组
            var re = treeTable.render({
                elem: '#tree-table',
                url: '/manage-menu/menus',
                icon_key: 'name',
                end: function (e) {
                    form.render();
                },
                cols: [
                    {
                        key: 'id',
                        title: 'ID',
                        width: '110px',
                    },
                    {
                        key: 'sort',
                        title: '排序',
                        width: '110px',
                        template: function (item) {
                            return '<input type="text" lay-skin="sort" class="layui-input ajax-change" name="sort" ' +
                                'value="' + item.sort + '" ' +
                                'href="{{route('manage-menu.change')}}" data-id="' + item.id + '"/>';
                        }
                    },
                    {
                        key: 'terminal',
                        title: '归属终端',
                        template: function (item) {
                            if (item.terminal == 'web') {
                                return 'WEB端';
                            } else {
                                return '移动端';
                            }
                        }
                    },
                    {
                        key: 'name',
                        title: '名称'
                    },
                    {
                        key: 'route',
                        title: '路由',
                    },
                    {
                        key: 'route',
                        title: '图标',
                        template: function (item) {
                            return '<span><i class="layui-icon ' + item.icon + '"></i></span>';
                        }
                    },
                    {
                        key: 'status',
                        title: '开关(显示/隐藏)',
                        template: function (item) {
                            var id = item.id;
                            if (item.status == 0) {
                                return '<input type="checkbox" name="' + id + '" lay-skin="switch" lay-text="ON|OFF">';
                            } else {
                                return '<input type="checkbox" name="' + id + '" lay-skin="switch" checked="checked" lay-text="ON|OFF">';
                            }
                        }
                    },
                    {
                        title: '操作',
                        template: function (item) {
                            var id = item.id;
                            return '<a class="layui-btn layui-btn-normal layui-btn-xs open-frame" data-full="1" ' +
                                'title="编辑菜单" href="/manage-menu/' + id + '/edit"> ' +
                                '<i class="layui-icon layui-icon-edit"></i> 编辑 </a> ' +
                                '<a class="layui-btn layui-btn-danger layui-btn-xs ajax-link" data-method="delete" ' +
                                'href="/manage-menu/' + id + '" title="删除菜单" ' +
                                'data-confirm="删除后不可恢复，请谨慎操作，是否确认删除此菜单？"> ' +
                                '<i class="layui-icon layui-icon-delete"></i> 删除 </a>';
                        }
                    }
                ]
            });
            form.on('switch', function (data) {
                var id = data.elem.name;
                var href = "{{route("manage-menu.change.status")}}";
                var name = "status";
                var value = data.elem.checked;
                http.put(href, {id: id, name: name, value: value}).then(function (res) {
                    common.msg(res.msg || '操作成功');
                });
            });
            // 监听展开关闭
            treeTable.on('tree(flex)', function (data) {

            });
            // 监听checkbox选择
            treeTable.on('tree(box)', function (data) {
                if (o(data.elem).parents('#tree-table1').length) {
                    var text = [];
                    o(data.elem).parents('#tree-table1').find('.cbx.layui-form-checked').each(function () {
                        o(this).parents('[data-pid]').length && text.push(o(this).parents('td').next().find('span').text());
                    })
                    o(data.elem).parents('#tree-table1').prev().find('input').val(text.join(','));
                }
                layer.msg(JSON.stringify(data));
            })
            // 监听自定义
            treeTable.on('tree(add)', function (data) {
                layer.msg(JSON.stringify(data));
            })
            // 获取选中值，返回值是一个数组（定义的primary_key参数集合）
            o('.get-checked').click(function () {
                layer.msg('选中参数' + treeTable.checked(re).join(','))
            })
            // 刷新重载树表（一般在异步处理数据后刷新显示）
            o('.refresh').click(function () {
                re.data.push({"id": 50, "pid": 0, "title": "1-4"}, {"id": 51, "pid": 50, "title": "1-4-1"});
                treeTable.render(re);
            })
            // 全部展开
            o('.open-all').click(function () {
                treeTable.openAll(re);
            })
            // 全部关闭
            o('.close-all').click(function () {
                treeTable.closeAll(re);
            })
        });
    </script>
@endsection