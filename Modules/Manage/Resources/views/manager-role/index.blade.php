@extends('manage::layouts.master')
@section('style')

@endsection
@section('content')
    <div class="layui-fluid">
        <div class="layui-card">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        角色筛选
                    </div>
                    <div class="layui-inline">
                        {{Form::select('id',[''=>'全部']+$options->toArray(),i5Search('id'),['lay-filter'=>'manager-role-type'])}}
                    </div>
                </div>
            </div>

            <div class="layui-card-body">
                <div style="padding-bottom: 10px;">
                    <button class="layui-btn layuiadmin-btn-role open-frame" data-height="240px" title="添加角色"
                            href="{{route('manager-role.create')}}">
                        添加
                    </button>
                </div>

                <table class="layui-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>角色名</th>
                        <th>显示名称</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($result as $item)
                        <tr>
                            <td>{{$item->id}}</td>
                            <td>{{$item->name}}</td>
                            <td>{{$item->guard_name}}</td>
                            <td>
                                <a class="layui-btn layui-btn-normal layui-btn-xs open-frame" data-height="240px"
                                   title="编辑角色" href="{{route('manager-role.edit',$item->id)}}">
                                    <i class="layui-icon layui-icon-edit"></i> 编辑
                                </a>
                                @if(!$item->isSuper())
                                    <a class="layui-btn layui-btn-warm layui-btn-xs open-frame" data-height="640px"
                                       title="角色授权" href="{{route('manager-role.permission',['id'=>$item->id])}}">
                                        <i class="layui-icon layui-icon-auz"></i> 授权
                                    </a>
                                    <a class="layui-btn layui-btn-danger layui-btn-xs ajax-link" data-method="delete"
                                       href="{{route('manager-role.destroy',$item->id)}}" title="删除角色"
                                       data-confirm="删除后不可恢复，请谨慎操作，是否确认删除此角色？">
                                        <i class="layui-icon layui-icon-delete"></i> 删除
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index', 'common', 'form'], function () {
            var form = layui.form;
            var common = layui.common;

            form.on('select(manager-role-type)', function (data) {
                var search = common.searchPack({
                    id: data.value
                });

                location.href = '{{route('manager-role.index')}}?search=' + search;
            });

        });
    </script>
@endsection
