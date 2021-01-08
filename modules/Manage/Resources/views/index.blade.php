@extends('manage::layouts.master')

@section('title','后台主页')

@section('bodyClass','layui-layout-body')

@section('style')
    {{Html::style(asset('resource/css/style.css'))}}
    <script>
        if (window.top !== window.self) {
            window.top.location = window.location;
        }
    </script>
@endsection

@section('content')
    <div id="LAY_app">
        <div class="layui-layout layui-layout-admin">
            <div class="layui-header">
                <!-- 头部区域 -->
                <div class="layui-layout-left">
                    <ul class="layui-nav">
                    </ul>
                    <div class="layui-tab layui-tab-brief" lay-filter="top-nav">
                        <ul class="layui-tab-title ">
                            @foreach($menuTree as $i=>$v)
                                <li class="{{!$i?'layui-this':''}}" lay-id="{{$v['id']}}">
                                    @if($v['icon'])
                                        <i class="layui-icon {{$v['icon']}}"></i>
                                    @endif{{$v['name']}}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <ul class="layui-nav layui-layout-right" lay-filter="layadmin-layout-right">

                    <li class="layui-nav-item layadmin-flexible" lay-unselect>
                        <a href="javascript:;" layadmin-event="flexible" title="侧边伸缩">
                            <i class="layui-icon layui-icon-shrink-right" id="LAY_app_flexible"></i>
                        </a>
                    </li>
                    <li class="layui-nav-item" lay-unselect>
                        <a href="javascript:;" layadmin-event="refresh" title="刷新">
                            <i class="layui-icon layui-icon-refresh"></i>
                        </a>
                    </li>
                    <li class="layui-nav-item" lay-unselect>
                        <a lay-href="app/message/index.html" layadmin-event="message" lay-text="消息中心">
                            <i class="layui-icon layui-icon-notice"></i>

                            <!-- 如果有新消息，则显示小圆点 -->
                            <span class="layui-badge-dot"></span>
                        </a>
                    </li>
                    <li class="layui-nav-item layui-hide-xs" lay-unselect>
                        <a href="javascript:;" layadmin-event="theme">
                            <i class="layui-icon layui-icon-theme"></i>
                        </a>
                    </li>
                    <li class="layui-nav-item layui-hide-xs" lay-unselect>
                        <a href="javascript:;" layadmin-event="note">
                            <i class="layui-icon layui-icon-note"></i>
                        </a>
                    </li>
                    <li class="layui-nav-item layui-hide-xs" lay-unselect>
                        <a href="javascript:;" layadmin-event="fullscreen">
                            <i class="layui-icon layui-icon-screen-full"></i>
                        </a>
                    </li>
                    <li class="layui-nav-item" lay-unselect>
                        <a href="javascript:;">
                            <cite>{{$manager->name}}</cite>
                        </a>
                        <dl class="layui-nav-child">
                            <dd><a lay-href="{{route('repasss')}}">修改密码</a></dd>
                            <hr>
                            <dd layadmin-event="logout" style="text-align: center;"><a class="loginout">退出</a></dd>
                        </dl>
                    </li>

                    <li class="layui-nav-item layui-hide-xs" lay-unselect>
                        <a href="javascript:;" layadmin-event="about"><i
                                    class="layui-icon layui-icon-more-vertical"></i></a>
                    </li>
                    <li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-unselect>
                        <a href="javascript:;" layadmin-event="more"><i class="layui-icon layui-icon-more-vertical"></i></a>
                    </li>
                </ul>
            </div>

            <!-- 侧边菜单 -->
            <div class="layui-side layui-side-menu">
                <div class="layui-side-scroll">
                    <div class="layui-logo" lay-href="manage/console">
                        <span style="font-size: 20px;font-weight: bold;">中台系统</span>
                    </div>


                    @foreach($menuTree as $i=>$v)
                        <ul class="layui-nav layui-nav-tree {{$i?'layui-hide':''}} side-nav-{{$v['id']}} side-nav">
                            @if(!empty($v['children']))
                                @foreach($v['children'] as $k=>$vv)
                                    <li class="layui-nav-item {{!$k?'layui-nav-itemed':''}}">
                                        @if(!empty($vv['children']))
                                            <a href="javascript:;">
                                                @if($vv['icon'])
                                                    <i class="layui-icon {{$vv['icon']}}"></i>
                                                @endif<cite>{{$vv['name']}}</cite></a>
                                            <dl class="layui-nav-child">
                                                @foreach($vv['children'] as $vvv)
                                                    <dd>
                                                        @if($vvv['target'] == 'in')
                                                            <a lay-href="{{routeToUrl($vvv['route'])}}">
                                                                @if($vvv['icon'])
                                                                    <i class="layui-icon {{$vvv['icon']}}"></i>
                                                                @endif
                                                                <cite>{{$vvv['name']}}</cite>
                                                            </a>
                                                        @else
                                                            <a href="{{routeToUrl($vvv['route']) != "javascript:;"?routeToUrl($vvv['route']):$vvv['route']}}"
                                                               target="{{$vvv['target']}}">
                                                                @if($vvv['icon'])
                                                                    <i class="layui-icon {{$vvv['icon']}}"></i>
                                                                @endif
                                                                <cite>{{$vvv['name']}}</cite>
                                                            </a>
                                                        @endif
                                                    </dd>
                                                @endforeach
                                            </dl>

                                        @else
                                            @if($vv['target'] == 'in')
                                                <a lay-href="{{routeToUrl($vv['route'])}}">
                                                    @if($vv['icon'])
                                                        <i class="layui-icon {{$vv['icon']}}"></i>
                                                    @endif
                                                    <cite>{{$vv['name']}}</cite></a>
                                            @else
                                                <a href="{{routeToUrl($vv['route']) != "javascript:;"?routeToUrl($vv['route']):$vv['route']}}"
                                                   target="{{$vv['target']}}">
                                                    @if($vv['icon'])
                                                        <i class="layui-icon {{$vv['icon']}}"></i>
                                                    @endif
                                                    <cite>{{$vv['name']}}</cite></a>
                                            @endif
                                        @endif

                                    </li>
                                @endforeach
                            @else
                                <li class="layui-nav-item">
                                    @if($v['target'] == 'in')
                                        <a lay-href="{{routeToUrl($v['route'])}}">
                                            @if($v['icon'])
                                                <i class="layui-icon {{$v['icon']}}"></i>
                                            @endif
                                            <cite>{{$v['name']}}</cite>
                                        </a>
                                    @else
                                        <a href="{{routeToUrl($v['route']) != "javascript:;"?routeToUrl($v['route']):$v['route']}}"
                                           target="{{$v['target']}}">
                                            @if($v['icon'])
                                                <i class="layui-icon {{$v['icon']}}"></i>
                                            @endif
                                            <cite>{{$v['name']}}</cite>
                                        </a>
                                    @endif
                                </li>
                            @endif
                        </ul>
                    @endforeach
                </div>
            </div>

            <!-- 页面标签 -->
            <div class="layadmin-pagetabs" id="LAY_app_tabs">
                <div class="layui-icon layadmin-tabs-control layui-icon-prev" layadmin-event="leftPage"></div>
                <div class="layui-icon layadmin-tabs-control layui-icon-next" layadmin-event="rightPage"></div>
                <div class="layui-icon layadmin-tabs-control layui-icon-down">
                    <ul class="layui-nav layadmin-tabs-select" lay-filter="layadmin-pagetabs-nav">
                        <li class="layui-nav-item" lay-unselect>
                            <a href="javascript:;"></a>
                            <dl class="layui-nav-child layui-anim-fadein">
                                <dd layadmin-event="closeThisTabs"><a href="javascript:;">关闭当前标签页</a></dd>
                                <dd layadmin-event="closeOtherTabs"><a href="javascript:;">关闭其它标签页</a></dd>
                                <dd layadmin-event="closeAllTabs"><a href="javascript:;">关闭全部标签页</a></dd>
                            </dl>
                        </li>
                    </ul>
                </div>
                <div class="layui-tab" lay-unauto lay-allowClose="true" lay-filter="layadmin-layout-tabs">
                    <ul class="layui-tab-title" id="LAY_app_tabsheader">
                        <li lay-id="{{route($home)}}" lay-attr="{{route($home)}}"
                            class="layui-this">
                            <i class="layui-icon layui-icon-home"></i>
                        </li>
                    </ul>
                </div>
            </div>


            <!-- 主体内容 -->
            <div class="layui-body" id="LAY_app_body">
                <div class="layadmin-tabsbody-item layui-show">
                    <iframe src="{{route($home)}}" frameborder="0" class="layadmin-iframe"></iframe>
                </div>
            </div>

            <!-- 辅助元素，一般用于移动设备下遮罩 -->
            <div class="layadmin-body-shade" layadmin-event="shade"></div>
        </div>
    </div>

@endsection

@section('script')
    <script src="{{asseturl("js/public/main.js")}}"></script>
    <script>
        var _token = '{{ csrf_token() }}';
        var adminurl = "{{adminurl()}}";
    </script>
    <script>
        layui.use(['index', 'jquery'], function () {
            var element = layui.element;
            $ = layui.jquery;

            element.on('tab(top-nav)', function () {
                var id = this.getAttribute('lay-id');
                $('.side-nav').addClass('layui-hide');
                $('.side-nav-' + id).removeClass('layui-hide');
            });
        });
    </script>
@endsection
