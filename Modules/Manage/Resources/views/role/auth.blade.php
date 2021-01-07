<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>layuiAdmin 主页示例模板一</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="{{asseturl("lib/layuiadmin/layui/css/layui.css")}}" media="all">
    <link rel="stylesheet" href="{{asseturl("lib/layuiadmin/style/admin.css")}}" media="all">
</head>
<body>
<div class="layui-body" style="left:0px">
    <div class=" layui-tab-brief">
        <div class="layui-breadcrumb-box">
            {{--            {{adminNav($thisAction)}}--}}
            <a class="go-back" href="javascript:history.go(-1)"><i class="layui-icon">&#xe65c;</i> 返回</a>
        </div>
    </div>
    <!-- 内容主体区域 -->
    <div id="mainbox" class="layui-row layui-form" style="margin: 15px;">
        <form class="layui-form">
            {{csrf_field()}}
            <input type="hidden" name="roles_id" value="{{$roles_id}}"/>
            <blockquote class="site-text layui-elem-quote" id="menubox">
                <input type="checkbox" name="" title="授权菜单" lay-filter="menu" lay-skin="primary"
                       @if($roles_id==1) disabled @endif>
            </blockquote>
            <div class="menu-auth-box" id="menuChild">
                @foreach($menuLists as $key=>$data)
                    <div class="colla-item">
                        <h2 class="colla-title">
                            <input id="menuChildChildBox_{{$key}}" type="checkbox" data-key="{{$key}}"
                                   title="{{$data->title}}" lay-skin="primary" lay-filter="menuChild"
                                   @if($roles_id==1) disabled @endif
                                   @if($data->thisIsChecked==1) checked @endif>
                        </h2>
                        <div class="layui-colla-content layui-show" id="menuChildChild_{{$key}}">
                            <table>
                                <colgroup>
                                    <col width="150">
                                    <col>
                                </colgroup>
                                <tbody>
                                @if(count($data->twoMenu))
                                    @foreach($data->twoMenu as $val)
                                        <tr>
                                            <td>
                                                <input class="menu_id" type="checkbox" name="menu_id[]"
                                                       data-pkey="{{$key}}" title="{{$val->title}}"
                                                       lay-skin="primary" lay-filter="menuItem" value="{{$val->id}}"
                                                       @if(in_array($val->id,$roleMenuIdArr)) checked
                                                       @endif @if($roles_id==1) disabled @endif>
                                            </td>
                                            <td>
                                                @if(count($val->menu_action))
                                                    @foreach($val->menu_action as $v)
                                                        <input type="checkbox" name="menu_action_id[{{$val->id}}][]"
                                                               data-pkey="{{$key}}" title="{{$v->name}}"
                                                               lay-skin="primary" lay-filter="menuAction"
                                                               value="{{$v->id}}"
                                                               @if($v->menu_action_checked==1) checked
                                                               @endif @if($roles_id==1) disabled @endif>
                                                    @endforeach
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td><input class="menu_id" type="checkbox" name="menu_id[]"
                                                   data-pkey="{{$key}}" title="{{$data->title}}" lay-skin="primary"
                                                   lay-filter="menuItem" value="{{$data->id}}"
                                                   @if(in_array($data->id,$roleMenuIdArr)) checked
                                                   @endif @if($roles_id==1) disabled @endif></td>
                                        <td>
                                            @if(count($data->menu_action))
                                                @foreach($data->menu_action as $n)
                                                    <input type="checkbox" name="menu_action_id[{{$data->id}}][]"
                                                           data-pkey="{{$key}}" title="{{$n->name}}"
                                                           lay-skin="primary" lay-filter="menuAction"
                                                           value="{{$n->id}}"
                                                           @if($n->menu_action_checked==1) checked
                                                           @endif @if($roles_id==1) disabled @endif>
                                                @endforeach
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="layui-form-btn">
                @if($roles_id!=1)
                    <button type="submit" class="layui-btn layui-btn-big layui-btn" lay-submit
                            lay-filter="saveAuth"><i class="layui-icon"></i>保存
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>

<script>
    var _token = '<?php echo e(csrf_token()); ?>';
    var adminurl = "<?php echo e(adminurl()); ?>";
</script>
<script src="{{asseturl("lib/layuiadmin/layui/layui.js")}}"></script>
<script src="{{asseturl("js/manage/role.js")}}"></script>

