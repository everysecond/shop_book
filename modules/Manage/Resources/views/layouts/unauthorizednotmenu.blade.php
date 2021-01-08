<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="{{url("layui/css/layui.css")}}">
    <link rel="stylesheet" type="text/css" href="{{url("static/themev1.0/css/style.css")}}">
</head>
    <div class="layui-body">
        <!-- 内容主体区域 -->
        <style>
            .aubox i{display: block;
                font-size:48px;color:red;}
            .aubox p{margin-top:10px;}
        </style>
        <div id="role-action" class="layui-row" style="padding:10px;">
            <div class="aubox" style="width: 260px;height:140px;position: absolute;left:50%;top:50%;margin-top:-100px;margin-left:-130px;border:1px solid #eeeeee;text-align: center;padding-top:15px;">
                <i class="layui-icon">&#xe69c;</i>
                <span>未授权</span>
                <p><a href="javascript:history.go(-1)" class="layui-btn layui-btn-primary">返回</a></p>
            </div>
        </div>
    </div>
    <script src="{{url("layui/layui.js")}}"></script>
    <script src="{{asseturl("js/Merchant/merchant-view-commentlists.js")}}"></script>
