@extends('manage::layouts.frame')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header">角色权限：{{$model->name}}</div>
        <div class="layui-card-body">
            <div class="layui-form">
                <div class="layui-form-item">
                    <label class="layui-form-label">权限</label>
                    <div class="layui-input-inline">
                        <div id="permission-tree"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('script')
    <script>
        var tree, http, common;
        layui.use(['index', 'http', 'common', 'tree'], function () {
            tree = layui.tree, http = layui.http, common = layui.common;

            var data = eval('({!! json_encode($permissions) !!})');

            //权限树
            tree.render({
                elem: '#permission-tree',
                id: 'permission-tree',
                data: data,
                showCheckbox: true
            });
            var a = eval('{{json_encode($rolePermissions)}}');
            a.forEach(function (item) {
                setTimeout(function () {
                    tree.setChecked('permission-tree', item)
                },0);
            });
        });

        function layerYesCallback(index) {
            var checkedData = tree.getChecked('permission-tree');
            var perIds = getTreeIds(checkedData);

            http.put('{{route('manager-role.permission')}}', {
                id: '{{request('id')}}',
                perIds: perIds
            }).then(function (res) {
                parent.layer.close(index);

                common.success(res.msg);
            });
        }

        function getTreeIds(tree) {
            var arr = [];
            for (var i = 0, len = tree.length; i < len; i++) {
                arr.push(tree[i].id);
                if (tree[i].children && tree[i].children.length > 0) {
                    arr = arr.concat(getTreeIds(tree[i].children));
                }
            }
            return arr;
        }
    </script>
@endsection