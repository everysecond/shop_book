@extends('manage::layouts.master')
@section('style')

@endsection
@section('content')
    <div class="layui-fluid">
        <div class="layui-card">
            <div class="layui-card-header">
                <span class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
                    @foreach($parents as $i=>$parent)
                        @if($i + 1 < $parents->count())
                            <a href="{{route('manage-menu.index',['pid'=>$parent->id])}}">{{$parent->name}}</a>
                            <span lay-separator="">/</span>
                        @else
                            <a><cite>{{$parent->name}}</cite></a>
                        @endif
                    @endforeach
                </span>
            </div>
            <div class="layui-card-body">

                <div style="padding-bottom: 10px;">
                    <button class="layui-btn open-frame" title="添加菜单"
                            href="{{route('manage-menu.create',['pid' => request('pid')])}}" data-full="1">添加
                    </button>
                </div>

                <table class="layui-table">
                    <thead>
                    <tr>
                        <th style="width: 100px">ID</th>
                        <th style="width: 100px">排序</th>
                        <th>名称</th>
                        <th>路由</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($result as $item)
                        <tr>
                            <td>{{$item->id}}</td>
                            <td>
                                <input type="text" class="layui-input ajax-change" name="sort" value="{{$item->sort}}"
                                       href="{{route('manage-menu.change')}}" data-id="{{$item->id}}"/>
                            </td>
                            <td>

                                @if($item->icon)
                                    <i class="layui-icon {{$item->icon}}"></i>
                                @endif
                                <a href="{{route('manage-menu.index',['pid'=>$item->id])}}">
                                    {{$item->name}}
                                </a>
                            </td>
                            <td>{{$item->route}}</td>
                            <td>
                                <a class="layui-btn layui-btn-normal layui-btn-xs open-frame" data-full="1"
                                   title="编辑菜单" href="{{route('manage-menu.edit',$item->id)}}">
                                    <i class="layui-icon layui-icon-edit"></i> 编辑
                                </a>

                                <a class="layui-btn layui-btn-danger layui-btn-xs ajax-link" data-method="delete"
                                   href="{{route('manage-menu.destroy',$item->id)}}" title="删除菜单"
                                   data-confirm="删除后不可恢复，请谨慎操作，是否确认删除此菜单？">
                                    <i class="layui-icon layui-icon-delete"></i> 删除
                                </a>
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
