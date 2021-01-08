<!DOCTYPE html>
<html class="full-height">
<head>
    <meta charset="utf-8">
    <title>Crm-规则设置</title>
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

    .layui-col-md12, .layui-card {
        padding: 10px
    }


    .layui-card .layui-tab-brief .layui-tab-title li {
        padding: 0 15px;
        margin: 0;
        font-weight: bold;
    }

    .search-div select {
        padding: 3px 5px 5px 10px;
        font-size: 13px;
    }

    input[type=number] {
        -moz-appearance: textfield;
    }

    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        Height: 37px;
        margin: 0;
    }

    input[type="number"] {
        -moz-appearance: textfield;
    }

</style>

<div class="layui-col-md12" align="center" style="height:100%;">
    <div class="layui-card">
        <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
            <ul class="layui-tab-title">
                <li class="layui-this">租点-用户</li>
                <li>租点-网点</li>

            </ul>
            <div class="layui-tab-content">
            @if(!empty($data_1))

                <div class="layui-tab-item layui-show">

                    <form class="layui-form" action="">

                        <div class="layui-form-item" style="float:right">
                            <div class="layui-input-block">
                                <button type="button" class="layui-btn " style="width: 100px;float:right" id="search2">保存</button>
                            </div>
                        </div>
                        <hr>
                        <div class="layui-form-item">
                            <label class="layui-form-label" style="width:95px;font-weight: bold;font-size: 15px">客户流入规则</label>
                            <div class="layui-input-block">
                                <input type="radio" name="inflow_rules" value="1" title="启用"
                                       @if($data_1['inflow_rules'] == 1)  checked=""  @endif  >&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="inflow_rules" value="2" title="禁用"
                                       @if($data_1['inflow_rules'] == 2)  checked=""  @endif >

                            </div>
                        </div>

                        <div class="layui-form-item" style="margin-left: 80px;margin-top: 30px">
                            <div class="layui-inline">
                                <label class="layui-form-label" style="font-weight: bold;">流入规则</label>
                                <div class="layui-input-inline">
                                    <input type="number" name="lease_expires" autocomplete="off" class="layui-input" value="{{$data_1['lease_expires']}}">

                                </div>
                                <div class="layui-input-inline" style="margin:6px">
                                    天后租赁到期和

                                </div>

                                <div class="layui-input-inline"
                                     style="margin:-3px;margin-left: -80px;font-weight: bold;">
                                    <input type="checkbox" name="unleased" lay-skin="primary" title="未租赁用户"
                                           @if($data_1['unleased'] == 1 )  checked=""  @endif     value="1">
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item" style="margin-left: 80px">
                            <div class="layui-inline">
                                <label class="layui-form-label" style="font-weight: bold;">选择公海</label>
                                <div class="layui-input-inline">
                                    <select name="international_waters_1" id="international_waters_1" lay-verify="required" lay-search="">
                                        <option value="0" @if($data_1['international_waters_1'] == 0 ) selected = "selected"  @endif>请选择公海</option>
                                        @foreach($water as $id=>$province)
                                            <option value="{{$id}}" @if($data_1['international_waters_1'] == $id )  selected = "selected"  @endif>{{$province}}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item" style="margin-top: 50px">
                            <label class="layui-form-label"
                                   style="width:95px;font-weight: bold;font-size: 15px">客户返还规则</label>
                            <div class="layui-input-block">
                                <input type="radio" name="return_rules" value="1" title="启用"
                                       @if($data_1['return_rules'] == 1 )  checked=""  @endif >&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="return_rules" value="2" title="禁用"
                                       @if($data_1['return_rules'] == 2 )  checked=""  @endif >

                            </div>
                        </div>

                        <div class="layui-form-item" style="margin-left: 80px;margin-top: 30px">
                            <div class="layui-inline">
                                <label class="layui-form-label" style="font-weight: bold;">返还规则</label>
                                <div class="layui-input-inline">
                                    <input type="number" name="no_track" autocomplete="off" value="{{$data_1['no_track']}}" class="layui-input">

                                </div>
                                <div class="layui-input-inline" style="margin:6px">
                                    天未跟进或

                                </div>

                                <div class="layui-input-inline" style="margin:-3px;margin-left: -110px;">
                                    <input type="number" name="no_rent" autocomplete="off" class="layui-input" value="{{$data_1['no_rent']}}">

                                </div>
                                <div class="layui-input-inline" style="margin:6px">
                                    天未成单

                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item" style="margin-left: 80px">
                            <div class="layui-inline">
                                <label class="layui-form-label" style="font-weight: bold;">选择公海</label>
                                <div class="layui-input-inline">
                                    <select name="international_waters_2"  id="international_waters_2" lay-verify="required" lay-search="">
                                        <option value="0" @if($data_1['international_waters_2'] == 0 ) selected = "selected"  @endif>请选择公海</option>
                                        @foreach($water as $id=>$province)
                                            <option value="{{$id}}" @if($data_1['international_waters_2'] == $id )  selected = "selected"  @endif>{{$province}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

            @else
                    <div class="layui-tab-item layui-show">

                        <form class="layui-form" action="">

                            <div class="layui-form-item" style="float:right">
                                <div class="layui-input-block">
                                    <button type="button" class="layui-btn " style="width: 100px;float:right" id="search2">保存</button>
                                </div>
                            </div>
                            <hr>
                            <div class="layui-form-item">
                                <label class="layui-form-label" style="width:95px;font-weight: bold;font-size: 15px">客户流入规则</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="inflow_rules" value="1" title="启用"
                                          checked >&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type="radio" name="inflow_rules" value="2" title="禁用"
                                          >

                                </div>
                            </div>

                            <div class="layui-form-item" style="margin-left: 80px;margin-top: 30px">
                                <div class="layui-inline">
                                    <label class="layui-form-label" style="font-weight: bold;">流入规则</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="lease_expires" autocomplete="off" class="layui-input" value="">

                                    </div>
                                    <div class="layui-input-inline" style="margin:6px">
                                        天后租赁到期和

                                    </div>

                                    <div class="layui-input-inline"
                                         style="margin:-3px;margin-left: -80px;font-weight: bold;">
                                        <input type="checkbox" name="unleased" lay-skin="primary" title="未租赁用户"
                                               checked     value="1">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item" style="margin-left: 80px">
                                <div class="layui-inline">
                                    <label class="layui-form-label" style="font-weight: bold;">选择公海</label>
                                    <div class="layui-input-inline">
                                        <select name="international_waters_1" id="international_waters_1" lay-verify="required" lay-search="">
                                            <option value="0" >请选择公海</option>
                                            @foreach($water as $id=>$province)
                                                <option value="{{$id}}">{{$province}}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="layui-form-item" style="margin-top: 50px">
                                <label class="layui-form-label"
                                       style="width:95px;font-weight: bold;font-size: 15px">客户返还规则</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="return_rules" value="1" title="启用" checked
                                          >&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type="radio" name="return_rules" value="2" title="禁用"
                                           >

                                </div>
                            </div>

                            <div class="layui-form-item" style="margin-left: 80px;margin-top: 30px">
                                <div class="layui-inline">
                                    <label class="layui-form-label" style="font-weight: bold;">返还规则</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="no_track" autocomplete="off" value="" class="layui-input">

                                    </div>
                                    <div class="layui-input-inline" style="margin:6px">
                                        天未跟进或

                                    </div>

                                    <div class="layui-input-inline" style="margin:-3px;margin-left: -110px;">
                                        <input type="number" name="no_rent" autocomplete="off" class="layui-input" value="">

                                    </div>
                                    <div class="layui-input-inline" style="margin:6px">
                                        天未成单

                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item" style="margin-left: 80px">
                                <div class="layui-inline">
                                    <label class="layui-form-label" style="font-weight: bold;">选择公海</label>
                                    <div class="layui-input-inline">
                                        <select name="international_waters_2"  id="international_waters_2" lay-verify="required" lay-search="">
                                            <option value="0" >请选择公海</option>
                                            @foreach($water as $id=>$province)
                                                <option value="{{$id}}" >{{$province}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
            @endif

            @if(!empty($data_2))
                <div class="layui-tab-item">
                    <form class="layui-form" action="">

                        <div class="layui-form-item" style="float:right">
                            <div class="layui-input-block">

                                <button type="button" class="layui-btn " style="width: 100px;float:right" id = "search1">保存</button>
                            </div>
                        </div>
                        <hr>
                        <div class="layui-form-item">
                            <label class="layui-form-label"
                                   style="width:95px;font-weight: bold;font-size: 15px">客户流入规则</label>
                            <div class="layui-input-block">
                                <input type="radio" name="inflow_rules_2" value="1" title="启用"
                                       @if($data_2['inflow_rules'] == 1)  checked=""  @endif >&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="inflow_rules_2" value="2" title="禁用"
                                       @if($data_2['inflow_rules'] == 2)  checked=""  @endif >

                            </div>
                        </div>

                        <div class="layui-form-item" style="margin-left: 80px;margin-top: 30px">
{{--                            <div class="layui-inline">--}}
{{--                                <label class="layui-form-label" style="font-weight: bold;">流入规则</label>--}}
{{--                                <div class="layui-input-inline" style="font-weight: bold;">--}}
{{--                                    <input type="checkbox" name="crm_input" lay-skin="primary" value="1" title="CRM录入"  @if($data_2['crm_input'] == 1 )  checked=""  @endif>--}}


{{--                                </div>--}}
{{--                                <div class="layui-input-inline" style="font-weight: bold;margin-left: -50px">--}}
{{--                                    <input type="checkbox" name="branch_register" lay-skin="primary"  value="1" title="网点注册" @if($data_2['branch_register'] == 1 )  checked=""  @endif>--}}

{{--                                </div>--}}


{{--                            </div>--}}
                            <div class="layui-inline">
                                <label class="layui-form-label" style="font-weight: bold;">流入规则</label>
                                <div class="layui-input-inline">
                                    <input type="number" name="crm_input" autocomplete="off" class="layui-input" value="{{$data_2['crm_input']}}">

                                </div>
                                <div class="layui-input-inline" style="margin:6px">
                                    天后合同到期和

                                </div>

                                <div class="layui-input-inline"
                                     style="margin:-3px;margin-left: -80px;font-weight: bold;">
                                    <input type="checkbox" name="branch_register" lay-skin="primary" title="未成单用户"
                                           @if($data_2['branch_register'] == 1 )  checked=""  @endif     value="1">
                                </div>
                            </div>


                        </div>
                        <div class="layui-form-item" style="margin-left: 80px">
                            <div class="layui-inline">
                                <label class="layui-form-label" style="font-weight: bold;">选择公海</label>
                                <div class="layui-input-inline">
                                    <select name="international_waters_3" id="international_waters_3" lay-verify="required" lay-search="">
                                        <option value="0" @if($data_2['international_waters_1'] == 0 ) selected = "selected"  @endif>请选择公海</option>
                                        @foreach($water as $id=>$province)
                                            <option value="{{$id}}" @if($data_2['international_waters_1'] == $id )  selected = "selected"  @endif>{{$province}}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item" style="margin-top: 50px">
                            <label class="layui-form-label"
                                   style="width:95px;font-weight: bold;font-size: 15px">客户返还规则</label>
                            <div class="layui-input-block">
                                <input type="radio" name="return_rules_2" value="1" title="启用"
                                       @if($data_2['return_rules'] == 1 )  checked=""  @endif>&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="return_rules_2" value="2" title="禁用"
                                       @if($data_2['return_rules'] == 2 )  checked=""  @endif>

                            </div>
                        </div>

                        <div class="layui-form-item" style="margin-left: 80px;margin-top: 30px">
                            <div class="layui-inline">
                                <label class="layui-form-label" style="font-weight: bold;">返还规则</label>
                                <div class="layui-input-inline">
                                    <input type="number" name="no_track_2" autocomplete="off" class="layui-input" value="{{$data_2['no_track']}}">

                                </div>
                                <div class="layui-input-inline" style="margin:6px">
                                    天未跟进或

                                </div>

                                <div class="layui-input-inline" style="margin:-3px;margin-left: -110px;">
                                    <input type="number" name="no_rent_2" autocomplete="off" class="layui-input" value="{{$data_2['no_rent']}}">

                                </div>
                                <div class="layui-input-inline" style="margin:6px">
                                    天未成单

                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item" style="margin-left: 80px">
                            <div class="layui-inline">
                                <label class="layui-form-label" style="font-weight: bold;">选择公海</label>
                                <div class="layui-input-inline">
                                    <select name="international_waters_4" id="international_waters_4" lay-verify="required" lay-search="">
                                        <option value="0" @if($data_2['international_waters_2'] == 0 ) selected = "selected"  @endif>请选择公海</option>
                                        @foreach($water as $id=>$province)
                                            <option value="{{$id}}" @if($data_2['international_waters_2'] == $id )  selected = "selected"  @endif>{{$province}}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            @else
                    <div class="layui-tab-item">
                        <form class="layui-form" action="">

                            <div class="layui-form-item" style="float:right">
                                <div class="layui-input-block">

                                    <button type="button" class="layui-btn " style="width: 100px;float:right" id = "search1">保存</button>
                                </div>
                            </div>
                            <hr>
                            <div class="layui-form-item">
                                <label class="layui-form-label"
                                       style="width:95px;font-weight: bold;font-size: 15px">客户流入规则</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="inflow_rules_2" value="1" title="启用"
                                        checked  >&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type="radio" name="inflow_rules_2" value="2" title="禁用"
                                            >

                                </div>
                            </div>

                            <div class="layui-form-item" style="margin-left: 80px;margin-top: 30px">
                                <div class="layui-inline">
                                    <label class="layui-form-label" style="font-weight: bold;">流入规则</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="crm_input" autocomplete="off" class="layui-input" value="1">

                                    </div>
                                    <div class="layui-input-inline" style="margin:6px">
                                        天后合同到期和

                                    </div>

                                    <div class="layui-input-inline"
                                         style="margin:-3px;margin-left: -80px;font-weight: bold;">
                                        <input type="checkbox" name="branch_register" lay-skin="primary" title="未成单用户"
                                                  value="1">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item" style="margin-left: 80px">
                                <div class="layui-inline">
                                    <label class="layui-form-label" style="font-weight: bold;">选择公海</label>
                                    <div class="layui-input-inline">
                                        <select name="international_waters_3" id="international_waters_3" lay-verify="required" lay-search="">
                                            <option value="0" >请选择公海</option>
                                            @foreach($water as $id=>$province)
                                                <option value="{{$id}}" >{{$province}}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="layui-form-item" style="margin-top: 50px">
                                <label class="layui-form-label"
                                       style="width:95px;font-weight: bold;font-size: 15px">客户返还规则</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="return_rules_2" value="1" title="启用"
                                           checked  >&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type="radio" name="return_rules_2" value="2" title="禁用"
                                           >

                                </div>
                            </div>

                            <div class="layui-form-item" style="margin-left: 80px;margin-top: 30px">
                                <div class="layui-inline">
                                    <label class="layui-form-label" style="font-weight: bold;">返还规则</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="no_track_2" autocomplete="off" class="layui-input" value="">

                                    </div>
                                    <div class="layui-input-inline" style="margin:6px">
                                        天未跟进或

                                    </div>

                                    <div class="layui-input-inline" style="margin:-3px;margin-left: -110px;">
                                        <input type="number" name="no_rent_2" autocomplete="off" class="layui-input" value="">

                                    </div>
                                    <div class="layui-input-inline" style="margin:6px">
                                        天未成单

                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item" style="margin-left: 80px">
                                <div class="layui-inline">
                                    <label class="layui-form-label" style="font-weight: bold;">选择公海</label>
                                    <div class="layui-input-inline">
                                        <select name="international_waters_4" id="international_waters_4" lay-verify="required" lay-search="">
                                            <option value="0" >请选择公海</option>
                                            @foreach($water as $id=>$province)
                                                <option value="{{$id}}" >{{$province}}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
            @endif
            </div>
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

<script src="{{asseturl("js/lease/report/renewal_customer.js?").time()}}"></script>
<script>

    $("#search2").click(function(){
        load_broken();

    });

    function load_broken(){
        var inflow_rules = $("input[name='inflow_rules']:checked").val();
        var unleased = $("input[name='unleased']:checked").val();
        var lease_expires = $(" input[ name='lease_expires' ] ").val()
        var international_waters_1 = $('#international_waters_1 option:selected').val();

        var no_track = $(" input[ name='no_track' ] ").val()
        var return_rules = $("input[name='return_rules']:checked").val();
        var no_rent = $("input[name='no_rent']").val();
        var international_waters_2 = $('#international_waters_2 option:selected').val();


        $.ajax({
            type: 'POST',
            data:{'type':1,'no_track':no_track,'return_rules':return_rules,'no_rent':no_rent,'international_waters_2':international_waters_2,
                'international_waters_1':international_waters_1,'inflow_rules':inflow_rules,'unleased':unleased,'lease_expires':lease_expires,'_token':_token},
            dataType: 'json',
            url: adminurl + "/crm/rule_create",
            success:function(res){
                if (res.code == 1){

                    layer.msg('保存成功');
                }else{
                    layer.msg('网络请求错误，稍后重试！');
                }

            },
        });
    }


    $("#search1").click(function(){
        load_broken_2();

    });

    function load_broken_2(){
        var inflow_rules = $("input[name='inflow_rules_2']:checked").val();
        // var crm_input = $("input[name='crm_input']:checked").val();
        var branch_register = $(" input[ name='branch_register' ]:checked ").val()
        var international_waters_1 = $('#international_waters_3 option:selected').val();

        var no_track = $(" input[ name='no_track_2' ] ").val()
        var return_rules = $("input[name='return_rules_2']:checked").val();
        var no_rent = $("input[name='no_rent_2']").val();
        var international_waters_2 = $('#international_waters_4 option:selected').val();
        var crm_input = $("input[name='crm_input']").val();

        $.ajax({
            type: 'POST',
            data:{'type':2,'no_track':no_track,'return_rules':return_rules,'no_rent':no_rent,'international_waters_2':international_waters_2,
                'international_waters_1':international_waters_1,'inflow_rules':inflow_rules,'crm_input':crm_input,'branch_register':branch_register,'_token':_token},
            dataType: 'json',
            url: adminurl + "/crm/rule_create",
            success:function(res){
                if (res.code == 1){

                    layer.msg('保存成功');
                }else{
                    layer.msg('网络请求错误，稍后重试！');
                }

            },
        });
    }

</script>
</body>
</html>