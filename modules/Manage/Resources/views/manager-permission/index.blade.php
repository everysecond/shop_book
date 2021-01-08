@extends('manage::layouts.master')
@section('style')

@endsection
@section('content')
    <div class="layui-fluid">
        <div class="layui-card">
            <div class="layui-card-body">
                <div style="padding-bottom: 10px;">
                    <button class="layui-btn open-frame" data-height="240px"
                            title="添加模块" href="{{route('manager-permission.create')}}">
                        添加模块
                    </button>
                </div>

                <table class="layui-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>名称</th>
                        <th>操作列表</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($tree as $node)
                        <tr>
                            <td>{{$node['id']}}</td>
                            <td>{{$node['name']}}</td>
                            <td>-</td>
                            <td>
                                <a class="layui-btn layui-btn-xs open-frame" data-height="240px"
                                   title="添加权限组" href="{{route('manager-permission.create',['id'=>$node['id']])}}">
                                    添加权限组
                                </a>

                                <a class="layui-btn layui-btn-normal layui-btn-xs open-frame" data-height="240px"
                                   title="编辑模块" href="{{route('manager-permission.edit',$node['id'])}}">
                                    <i class="layui-icon layui-icon-edit"></i> 编辑模块
                                </a>

                                <a class="layui-btn layui-btn-danger layui-btn-xs ajax-link" data-method="delete"
                                   href="{{route('manager-permission.destroy',$node['id'])}}" title="删除模块"
                                   data-confirm="删除后不可恢复，请谨慎操作，是否确认删除此模块？">
                                    <i class="layui-icon layui-icon-delete"></i> 删除
                                </a>
                            </td>
                        </tr>

                        @if(isset($node['_child']))
                            <?php $len = count($node['_child']);?>

                            @foreach($node['_child'] as $i=>$v)
                                <tr>
                                    <td>{{$v['id']}}</td>
                                    <td>&nbsp;&nbsp;{{$i+1<$len?'├─':'└─'}}&nbsp;{{$v['name']}}</td>
                                    <td>
                                        @if(isset($v['_child']))
                                            @foreach($v['_child'] as $vv)
                                                <div class="layui-btn-group">
                                                    <a class="layui-btn layui-btn-sm">{{$vv['name']}}</a>
                                                    <a class="layui-btn layui-btn-sm dropdown-toggle">
                                                        <i class="layui-edge"></i>
                                                    </a>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="{{route("manager-permission.edit",$vv['id'])}}"
                                                               data-height="410px" data-width="520px"
                                                               class="open-frame">修改</a>
                                                        </li>
                                                        <li>
                                                            <a class="font-bold ajax-link" data-method="delete"
                                                               href="{{route("manager-permission.destroy",$vv['id'])}}"
                                                               data-confirm="是否确认删除此权限，此操作无法恢复？">删除</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        <a class="layui-btn layui-btn-xs open-frame"
                                           data-height="410px" data-width="520px"
                                           title="添加操作"
                                           href="{{route('manager-permission.create',['id'=>$v['id']])}}">
                                            添加操作
                                        </a>

                                        <a class="layui-btn layui-btn-normal layui-btn-xs open-frame"
                                           data-height="240px"
                                           title="编辑权限组" href="{{route('manager-permission.edit',$v['id'])}}">
                                            <i class="layui-icon layui-icon-edit"></i> 编辑权限组
                                        </a>

                                        <a class="layui-btn layui-btn-danger layui-btn-xs ajax-link"
                                           data-method="delete"
                                           href="{{route('manager-permission.destroy',$v['id'])}}" title="删除权限组"
                                           data-confirm="删除后不可恢复，请谨慎操作，是否确认删除此权限组？">
                                            <i class="layui-icon layui-icon-delete"></i> 删除
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
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

            form.on('select(manager-role-type)', function (data) {
                location.href = '{{route('manager-permission.index')}}?search=id:' + data.value;
            });

        });
    </script>
@endsection
