<!DOCTYPE html>
<html class="full-height">
<head>
    <meta charset="utf-8">
    <title>租点—续租统计</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="{{asseturl("lib/layuiadmin/layui/css/layui.css")}}" media="all">
    <link rel="stylesheet" href="{{asseturl("lib/layuiadmin/style/admin.css")}}" media="all">
    <script src="{{asseturl("lib/echarts/echarts.min.js")}}"></script>
    <script src="{{asseturl("lib/echarts/jquery.js")}}"></script>
    <script src="{{asseturl("lib/layui/layui.js")}}"></script>
</head>
<body>
<style>
    html, body {
        width: 100%;
        height: 100%;
        margin: 0;
    }

    .layui-col-md12, .layui-col-md6 {
        padding: 5px
    }

    .layui-card-header {
        font-family: 'Arial Negreta', 'Arial Normal', 'Arial';
        font-weight: 700;
        font-style: normal;
    }

    .layui-form-item {
        padding-top: 10px;
    }
</style>
<div class="layui-col-md12" style="height: 100%">
    <div class="layui-col-md6">
        <div class="layui-card">
            <div class="layui-form" align="right" style="width:800px;">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <input type="text" class="layui-input" placeholder="开始时间 - 结束时间" id="test12"
                                   style="display: none" autocomplete="off"
                            >
                        </div>
                    </div>
                    <div class="layui-inline" align="left">
                        <select name="modules" lay-verify="required" lay-search="" id='tes2' lay-filter="demo12">
                            @foreach($timeType as $item)
                                <option value="{{$item['id']}}">{{$item['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="layui-inline" align="left">
                        <select name="modules" lay-verify="required" lay-search="" id='test13'>
                            {{--                            <option value="0">全部区域</option>--}}
                            @foreach($provinces as $id=>$province)
                                <option value="{{$id}}">{{$province}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="layui-inline">
                        <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch" id='search2'><i
                                    class="layui-icon"></i>搜索
                        </button>

                    </div>
                </div>
            </div>
            <div id="box2" style="width:800px;height:450px;"></div>
        </div>
    </div>
    <div class="layui-col-md6">
        <div class="layui-card">
            <div class="layui-form" align="right" style="width:800px;">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <input type="text" class="layui-input" placeholder="开始时间 - 结束时间" id="test22"
                                   style="display: none" autocomplete="off"
                            >
                        </div>
                    </div>
                    <div class="layui-inline" align="left">
                        <select name="modules" lay-verify="required" lay-search="" id='tes22' lay-filter="demo22">
                            @foreach($timeType as $item)
                                <option value="{{$item['id']}}">{{$item['name']}}</option>
                            @endforeach
                        </select>
                    </div>
{{--                    <div class="layui-inline" align="left">--}}
{{--                        <select name="modules" lay-verify="required" lay-search="" id='test13'>--}}
{{--                            --}}{{--                            <option value="0">全部区域</option>--}}
{{--                            @foreach($provinces as $id=>$province)--}}
{{--                                <option value="{{$id}}">{{$province}}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}
{{--                    </div>--}}
                    <div class="layui-inline">
                        <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch" id='search3'><i
                                    class="layui-icon"></i>搜索
                        </button>

                    </div>
                </div>
            </div>
            <div id="box3" style="width:800px;height:450px;"></div>
        </div>
    </div>
    <div class="layui-col-md6">
        <div class="layui-card">
            <div class="layui-form" align="right" style="width:800px;">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <input type="text" class="layui-input" placeholder="开始时间 - 结束时间" id="test9"
                                   style="display: none" autocomplete="off"
                            >
                        </div>
                    </div>
                    <div class="layui-inline" align="left">
                        <select name="modules" lay-verify="required" lay-search="" id='tes1' lay-filter="demo">
                            @foreach($timeType as $item)
                                <option value="{{$item['id']}}">{{$item['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="layui-inline" align="left">
                        <select name="modules" lay-verify="required" lay-search="" id='test11'>
                            {{--                            <option value="0">全部区域</option>--}}
                            @foreach($provinces as $id=>$province)
                                <option value="{{$id}}">{{$province}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="layui-inline">
                        <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch" id='search1'><i
                                    class="layui-icon"></i>搜索
                        </button>

                    </div>
                </div>
            </div>
            <div id="box1" style="width:800px;height:450px;"></div>
        </div>
    </div>
    <div class="layui-col-md6">
        <div class="layui-card">
            <div class="layui-form" align="right" style="width:800px;">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <input type="text" class="layui-input" placeholder="开始时间 - 结束时间" id="test19"
                                   style="display: none" autocomplete="off"
                            >
                        </div>
                    </div>
                    <div class="layui-inline" align="left">
                        <select name="modules" lay-verify="required" lay-search="" id='tes11' lay-filter="demo19">
                            @foreach($timeType as $item)
                                <option value="{{$item['id']}}">{{$item['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="layui-inline" align="left">
                        <select name="modules" lay-verify="required" lay-search="" id='test111'>
                            {{--                            <option value="0">全部区域</option>--}}
                            @foreach($provinces as $id=>$province)
                                <option value="{{$id}}">{{$province}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="layui-inline">
                        <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch" id='search11'><i
                                    class="layui-icon"></i>搜索
                        </button>

                    </div>
                </div>
            </div>
            <div id="box11" style="width:800px;height:450px;"></div>
        </div>
    </div>

    <div class="layui-col-md12" align="center" style="">
        <div class="layui-card" style="padding: 20px">
            <div class="layui-form" align="right">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <input type="text" class="layui-input" id="test10" placeholder="开始时间 - 结束时间"
                                   autocomplete="off">
                        </div>
                    </div>
                    <div class="layui-inline" align="left">
                        <select name="modules" lay-verify="required" lay-search="" id='test'>
                            {{--                            <option value="0">全部区域</option>--}}
                            @foreach($provinces as $id=>$province)
                                <option value="{{$id}}">{{$province}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="layui-inline">
                        <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch" id='search'><i
                                    class="layui-icon"></i>搜索
                        </button>

                    </div>
                </div>
            </div>


            <table id="demo" lay-filter="test"></table>
        </div>
    </div>

</div>

<script>
    var adminurl = "{{adminurl()}}";
    var _token = '{{ csrf_token() }}';
    layui.config({
        base: "{{asseturl("lib")}}" + '/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'console']);

</script>

<script src="{{asseturl("js/lease/report/renewal.js?").time()}}"></script>
</body>
</html>