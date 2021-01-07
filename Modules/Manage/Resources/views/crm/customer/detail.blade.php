@extends('manage::layouts.master')

@section('style')
    <style>
        html, body, .layui-col-md3, .layui-col-md12, .layui-col-md9 {
            height: 100%;
        }

        html, body {
            height: 97%;
        }

        .layui-card-header {
            border-bottom: 1px solid #cccccc;
        }

        .layui-card .layui-tab-brief .layui-tab-title li {
            padding: 0 33px;
            margin: 0;
        }

        .layui-tab-brief > .layui-tab-more li.layui-this:after, .layui-tab-brief > .layui-tab-title .layui-this:after {
            border-bottom: 3px solid rgb(26, 188, 156);
        }

        .layui-this {
            color: rgb(26, 188, 156) !important;
        }

        .tb-cus {
            margin-top: 10px;
            border-top: 1px solid #e4e4e4;
            border-left: 1px solid #e4e4e4;
        }

        .tb-cus td {
            padding: 4px 10px;
            border-bottom: 1px solid #e4e4e4;
            border-right: 1px solid #e4e4e4;
        }

        .tb-cus td:nth-child(2n+1) {
            width: 120px;
            background-color: #f7f8fa;
        }

        .tb-cus td:nth-child(2n) {
            width: 350px;
        }

        .plan_div {
            min-height: 80px;
            background-color: #f7f8fa;
            padding: 10px 15px;
            margin: 10px 0;
        }

        .large-img {
            max-height: 550px !important;
            max-width: 800px !important;
        }
    </style>
@endsection

@section('content')
    <div class="layui-col-md12" style="height: 100%">
        <div class="layui-card" style="padding: 15px;margin-bottom: 0;">
            @if($model->is_mark)
                <a href="javascript:;" title="已标记">
                    <i class="font20 layui-icon layui-icon-star-fill color-green"></i>
                </a>
            @endif
            <span class="header16">
                {{$model->name?$model->name:$model->mobile}}
            </span>
            @if($model->short_name)
                <span class="short-name" style="margin-left: 10px">
                    {{$model->short_name}}
                </span>
            @endif
            <div style="width:100%;padding: 12px 0;">
                <span class="label-l">客户类型：</span>
                <span class="label-r wid130">{{$model->cus_type}}</span>
                <span class="label-l">客户等级：</span>
                <span class="label-r wid130">{{$model->cus_level}}</span>
                <span class="label-l">联系方式：</span>
                <span class="label-r wid130">{{$model->mobile}}</span>
                <span class="label-l">负责人：</span>
                <span class="label-r wid130">{{$model->charger_name}}</span>
            </div>
            @if($model->charger_id == getUserId())
                <div style="margin-top: 12px">
                    <button class="cps-btn mybtn batch-operate" title="转移给他人"
                            href="{{route('cus.move',['ids'=>$model->id,'from'=>'detail'])}}">转移给他人
                    </button>
                    <button class="cps-btn mybtn batch-operate" title="移入公海"
                            href="{{route('cus.move.sea',['ids'=>$model->id,'from'=>'detail'])}}">移入公海
                    </button>
                    <button class="cps-btn mybtn" onclick="operateCus(this)"
                            data-confirm="是否确认标记/取消？"
                            href="{{route('cus.mark',$model->id)}}" data-method="put">标记/取消
                    </button>
                    <button class="cps-btn mybtn" onclick="operateCus(this)"
                            data-confirm="是否确认置顶/取消？"
                            href="{{route('cus.top',$model->id)}}" data-method="put">置顶/取消
                    </button>
                </div>
            @endif
        </div>
        <div class="layui-card" style="padding: 0;margin-top: 10px;height: calc(100% - 105px)">
            <div class="layui-card-body" style="padding: 0;">
                <div class="layui-tab layui-tab-brief" lay-filter="component-tabs-brief">
                    <ul class="layui-tab-title" style="border-bottom-color: #cccccc;margin: 0 15px;">
                        <li class="tab-title layui-this">跟进记录</li>
                        <li class="tab-title">客户资料</li>
                        <li class="tab-title" onclick="contactLoad()">联系人</li>
                        <li class="tab-title" onclick="teamLoad()">归属团队</li>
                        @if($model->cus_type == "租点用户")
                            <li class="tab-title" onclick="constrctLoad(1)">租赁合同</li>
                        @endif
                        @if($model->cus_type == "租点网点")
                            <li class="tab-title" onclick="constrctLoad(2)">网点合同</li>
                        @endif
                        @if($model->cus_type == "快点用户")
                            <li class="tab-title" onclick="constrctLoad(3)">租赁合同</li>
                        @endif
                        @if($model->cus_type == "快点网点")
                            <li class="tab-title" onclick="constrctLoad(4)">网点合同</li>
                        @endif
                        <li class="tab-title">操作记录</li>
                    </ul>
                    <div class="layui-tab-content" style="padding: 0;background-color: white">
                        <div class="layui-tab-item layui-show" style="height: 100%;">
                            @if($model->charger_id == getUserId())
                                <div style="padding: 15px">
                                    <a class="layui-btn layui-btn-sm open-frame" style="float: right;width: 80px"
                                       data-method="get"
                                       href="{{route('plan.create',['cusId'=>$model->id,'cusName'=>$model->name?$model->name:$model->mobile,'type'=>2])}}"
                                       title="跟进计划" data-width="600px" data-height="600px">跟进计划</a>
                                    <a class="layui-btn layui-btn-sm open-frame mr10" style="float: right;width: 80px"
                                       data-method="get"
                                       href="{{route('plan.create',['cusId'=>$model->id,'cusName'=>$model->name?$model->name:$model->mobile,'type'=>1])}}"
                                       title="跟进记录" data-width="900px" data-height="550px">跟进记录</a>
                                </div>
                            @endif
                            <div style="padding: 15px">
                                @if($mark=0)@endif
                                @foreach($follows as $k=>$follow)
                                    @if($follow->type == 2)
                                        @if($mark++)@endif
                                        <div class="plan_div @if($mark>2){{'hidden hiddenplan'}}@endif">
                                            <span class="g-title">跟进计划</span>
                                            <div class="float-r font12">
                                                <span class="">下次跟进时间:</span>
                                                <span class="red">{{date('Y-m-d H:i:s',$follow->follow_at)}}</span>
                                            </div>
                                            <div>
                                                <span class="min-w-180">
                                                    <span class="label-l-12">跟进客户：</span>
                                                    <span class="label-r-12">{{$model->name?$model->name:$model->mobile}}</span>
                                                </span>
                                                <span class="min-w-120">
                                                    <span class="label-l-12">联系人：</span>
                                                    <span class="label-r-12">{{$follow->contact?$follow->contact->name:''}}</span>
                                                </span>
                                                <span class="min-w-120">
                                                    <span class="label-l-12">跟进方式：</span>
                                                    <span class="label-r-12">{{$follow->mode}}</span>
                                                </span>
                                                <span class="min-w-180">
                                                    <span class="label-l-12">跟进人员：</span>
                                                    <span class="label-r-12">{{$follow->follow_users}}</span>
                                                </span>
                                            </div>
                                            <div>
                                                <span class="label-l-12">跟进内容：</span>
                                                <span class="label-r-12">{!! $follow->content !!}</span>
                                            </div>
                                            @if($model->charger_id == getUserId())
                                                <div style="height:40px">
                                                    <div class="float-r" style="padding-top: 10px">
                                                        <button class="cps-btn cps-btn-sm font12 ajax-link"
                                                                data-method="delete" title="取消计划"
                                                                href="{{route('plan.destroy',$follow->id)}}">
                                                            取消计划
                                                        </button>
                                                        <button class="layui-btn layui-btn-sm open-frame cps-btn-sm  font12"
                                                                title="转为记录" data-width="900px" data-height="550px"
                                                                style="margin: -3px 0 0 5px;width: 70px;padding: 0 10px;"
                                                                href="{{route('plan.edit',$follow->id)}}">
                                                            转为记录
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                                @if($mark>2)
                                    <div style="text-align: center">
                                        <a id="animate_plan" class="font13" href="javascript:;">更多计划>></a>
                                    </div>
                                @endif
                            </div>
                            <div style="padding: 15px">
                                <ul class="layui-timeline" style="height: 100%">
                                    @foreach($follows as $k => $follow)
                                        @if($follow->type == 1)
                                            <li class="layui-timeline-item">
                                                <i class="layui-icon layui-timeline-axis"></i>
                                                <div class="layui-timeline-content layui-text">
                                                    <div class="layui-timeline-title">
                                                        {{$follow->created_at}}(跟进时间)
                                                        <div class="plan_div">
                                                            <span class="label-r">{{$follow->follow_users}}</span>
                                                            <div class="float-r font12">
                                                                <span class="label-l-12">跟进客户:</span>
                                                                <span class="label-r-12">{{$model->name?$model->name:$model->mobile}}</span>
                                                            </div>
                                                            <div style="margin-top: 5px">
                                                                <span class="label-l-12">跟进内容：</span>
                                                                <span class="label-r-12">{!! $follow->content !!}</span>
                                                                <div>
                                                                    @foreach($follow->images as $img)
                                                                        <img src="{{$img->relative_path}}"
                                                                             class="sm-img mr10"/>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                            <div style="margin-top: 10px">
                                                                <span class="min-w-120">
                                                                    <span class="label-l-12">跟进方式：</span>
                                                                    <span class="label-r-12">{{$follow->mode}}</span>
                                                                </span>
                                                                <span class="min-w-120">
                                                                    <span class="label-l-12">联系人：</span>
                                                                    <span class="label-r-12">{{$follow->contact?$follow->contact->name:''}}</span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="layui-tab-item" style="width:100%;height: 100%;">
                            <div style="padding: 15px">
                                <div class="title-square"></div>
                                <span style="clear: both">基本信息</span>
                                @if($model->charger_id == getUserId())
                                    <a class="layui-btn layui-btn-sm open-frame" style="float: right;width: 80px"
                                       data-method="get" href="{{route('cus.edit',$model->id)}}" title="编辑客户信息"
                                       data-width="850px"
                                       data-height="520px">编辑</a>
                                @endif
                                <table class="tb-cus">
                                    <tr>
                                        <td>客户名称</td>
                                        <td>{{$model->name?$model->name:$model->mobile}}</td>
                                        <td>助记名称</td>
                                        <td>{{$model->short_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>客户类型</td>
                                        <td>{{$model->cus_type}}</td>
                                        <td>客户等级</td>
                                        <td>{{$model->cus_level}}</td>
                                    </tr>
                                    <tr>
                                        <td>客户电话</td>
                                        <td>{{$model->mobile}}</td>
                                        <td>所在地区</td>
                                        <td>{{$model->area}}</td>
                                    </tr>
                                    <tr>
                                        <td>详细地址</td>
                                        <td>{{$model->address}}</td>
                                        <td>负责人</td>
                                        <td>{{$model->charger_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>备注</td>
                                        <td colspan="3">{{$model->memo}}</td>
                                    </tr>
                                </table>

                                <div class="title-square mt20"></div>
                                <span style="clear: both">系统信息</span>
                                <table class="tb-cus">
                                    <tr>
                                        <td>系统编号</td>
                                        <td>{{$model->id}}</td>
                                        <td>前负责人</td>
                                        <td>{{$model->pre_charger_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>创建人员</td>
                                        <td>{{$model->createUser?$model->createUser->name:''}}</td>
                                        <td>创建时间</td>
                                        <td>{{$model->created_at}}</td>
                                    </tr>
                                    <tr>
                                        <td>更新时间</td>
                                        <td>{{$model->updated_at}}</td>
                                        <td>最后跟进时间</td>
                                        <td>{{$model->preFollow?date('Y-m-d H:i:s',$model->preFollow->follow_at):''}}</td>
                                    </tr>
                                    <tr>
                                        <td>下次跟进时间</td>
                                        <td>{{$model->nextFollow?date('Y-m-d H:i:s',$model->nextFollow->follow_at):''}}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="layui-tab-item" style="width:96%;height: 100%;padding: 20px">
                            @if(getUserId() == $model->charger_id)
                                <div style="height: 40px;">
                                    <button class="layui-btn layui-btn-sm open-frame2 mybtn" title="新增联系人"
                                            id="add_staff" style="float: right" data-width="850px"
                                            href="{{route('contact.create',["cus_id"=>$model->id,'cus_name'=>$model->name.'('.$model->mobile.')'])}}"
                                            data-height="520px">
                                        新增联系人
                                    </button>
                                </div>
                            @endif
                            <table id="contact-table" lay-filter="contact-table"></table>
                        </div>
                        <div class="layui-tab-item" style="width:96%;height: 100%;padding: 20px">
                            @if(getUserId() == $model->charger_id)
                                <div style="height: 40px;">
                                    <button class="layui-btn layui-btn-sm" title="新增团队成员"
                                            id="add_team" style="float: right" data-width="850px"
                                            data-height="520px">
                                        新增团队成员
                                    </button>
                                </div>
                            @endif
                            <table id="team-table" lay-filter="team-table"></table>
                        </div>
                        <div class="layui-tab-item" style="width:96%;height: 100%;padding: 20px">
                            <table id="constract-table" lay-filter="constract-table"></table>
                        </div>
                        <div class="layui-tab-item" style="width:96%;height: 100%;padding: 20px">
                            <ul class="layui-timeline" style="height: 100%">
                                @foreach($logs as $k => $log)
                                    <li class="layui-timeline-item">
                                        @if($k == 0)
                                            <i class="layui-icon layui-timeline-axis"></i>
                                        @else
                                            <i class="layui-icon layui-timeline-axis"></i>
                                        @endif
                                        <div class="layui-timeline-content layui-text">
                                            <div class="layui-timeline-title">
                                                {{$log->created_at}}，{{$log->createdUser->name}}
                                                &nbsp;&nbsp;{{$log->type}}&nbsp;&nbsp;
                                                @if($log->targetUser)
                                                    {{$log->targetUser->name}}&nbsp;&nbsp;
                                                @endif
                                                @if(strlen($log->content) > 40)
                                                    <br>
                                                @endif
                                                <span>
                                                    {{$log->content}}
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="enlargeImage" class="hidden">
        <div class="img-wrap">
            <i id="closeLargeImg" class="img-close"></i>
            <img class="large-img" src=""/>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var operateCus, position_id = '', table, layer, type = '{{getUserId()==$model->charger_id?'myself':''}}',
            contactLoad, teamLoad, constrctLoad;
        layui.use(['laydate', 'index', 'table', 'form', 'common', 'http', 'tree', 'util'], function () {
            layer = layui.layer;
            var http = layui.http, common = layui.common, $ = layui.$, form = layui.form;

            var hiddenplan = $('.hiddenplan');
            $("#animate_plan").click(function () {
                if (hiddenplan.hasClass('hidden')) {
                    hiddenplan.removeClass('hidden');
                    $('#animate_plan').html('收起计划<<');
                } else {
                    hiddenplan.addClass('hidden');
                    $('#animate_plan').html('更多计划>>');
                }
            });

            table = layui.table;
            contactLoad = function () {
                table.render({
                    elem: "#contact-table",
                    url: '{{route('contact.paginate.cus',$model->id)}}',
                    cols: [
                        [
                            {
                                field: "name",
                                title: "姓名",
                                width: "8%",
                                templet: function (item) {
                                    var deltail = common.route('{{route('contact.edit',[':id','type'=>'detail'])}}', {id: item.id});
                                    return '<a class="open-frame2 color-green" data-method="get" href="' + deltail + '" ' +
                                        'title="详情" data-width="850px" data-type="detail"' +
                                        ' data-height="520px">' + item.name + '</a>';
                                }
                            },
                            {
                                field: "sex",
                                title: "称呼",
                                width: "6%"
                            },
                            {
                                field: "mobile",
                                title: "手机",
                                width: "12%"
                            },
                            {
                                field: "wechat",
                                title: "微信"
                            },
                            {
                                field: "position",
                                title: "职务",
                                width: "6%"
                            },
                            {
                                field: "is_key",
                                title: "决策人",
                                width: "8%"
                            },
                            {
                                title: "创建人",
                                templet: function (item) {
                                    return item.createdUser.name;
                                }
                            },
                            {
                                field: "created_at",
                                title: "创建时间",
                                width: "15%"
                            },
                            {
                                title: "操作",
                                align: "center",
                                fixed: "right",
                                width: "15%",
                                templet: function (item) {
                                    if (type == "myself") {
                                        var edit = common.route('{{route('contact.edit',':id')}}', {id: item.id});
                                        return '<a class="layui-btn layui-btn-normal layui-btn-xs open-frame2" ' +
                                            'data-method="get" href="' + edit + '" ' +
                                            'title="编辑联系人" data-width="850px" data-height="520px">' +
                                            '<i class="layui-icon layui-icon-edit"></i>编辑</a>' +
                                            '<a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">' +
                                            '<i class="layui-icon layui-icon-delete"></i>移除</a>';
                                    } else {
                                        return "";
                                    }
                                }
                            }
                        ]
                    ],
                    where: {orderBy: 'created_at', sortedBy: 'desc'}
                });
            };


            var active = {};
            active.del = function (e) {
                layer.confirm("确定移除该联系人吗？", {icon: 3, title: '移除'}, function (index) {
                    http.delete(common.route('{{route('contact.destroy',':id')}}', {id: e.data.id})).then(function (res) {
                        e.del();
                        layer.close(index);
                        layer.msg(res.msg);
                    });
                });
            }

            operateCus = function (cus) {
                layer.confirm($(cus).data("confirm"), {icon: 3, title: '操作'}, function (index) {
                    http.put($(cus).attr("href")).then(function (res) {
                        // parent.table.reload('LAY-user-back-manage', {
                        //     where: {
                        //         searchStr: parent.$('#searchStr').val(),
                        //         cus_type: parent.$('select[name="cus_type"]').val(),
                        //         cus_source: parent.$('select[name="cus_source"]').val(),
                        //         cus_level: parent.$('select[name="cus_level"]').val(),
                        //         province_id: parent.$('select[name="province_id"]').val(),
                        //         type: parent.type
                        //     }
                        // });
                        location.reload();
                        layer.msg(res.msg);
                    });
                });
            };

            table.on("tool(contact-table)", function (e) {
                active[e.event] && common.isFunction(active[e.event]) && active[e.event].call(this, e);
            });

            //监听.open-frame2
            $(document).on("click", ".open-frame2", function (e) {
                e.preventDefault();
                var href = $(this).attr("href");
                var full = $(this).data('full') || false;
                var width = $(this).data('width') || '420px';
                var height = $(this).data('height') || '420px';
                var btn = $(this).data('type') ? '' : ['确定', '取消'];
                full && (width = height = '100%');

                layer.open({
                    type: 2,
                    title: '<i class="layui-icon layui-icon-app"></i> ' + this.title,
                    area: [width, height],
                    content: href,
                    btn: btn,
                    yes: function (index, layero) {
                        var frameWindow = window["layui-layer-iframe" + index];
                        if (common.isFunction(frameWindow.layerYesCallback)) {
                            frameWindow.layerYesCallback(index, layero);
                        }
                        setTimeout(function () {
                            table.reload('contact-table');
                        }, 1000);
                    }
                });
            });


            teamLoad = function () {
                table.render({
                    elem: "#team-table",
                    url: '{{route('crm.reports.sea_customer_team',$model->id)}}',
                    cols: [
                        [
                            {
                                field: "name",
                                title: "姓名",
                            },
                            {
                                field: "team_role",
                                title: "团队角色",
                                templet: function (item) {

                                    if (item.team_role == 1) {
                                        return "负责人";
                                    } else if (item.team_role == 2) {
                                        return "协作人员";
                                    }


                                }
                            },
                            {
                                field: "position",
                                title: "职位",

                            },
                            {
                                field: "mobile",
                                title: "联系方式",

                            },

                            {
                                title: "操作",
                                align: "center",
                                fixed: "right",
                                event: 'setSign',
                                templet: function (item) {
                                    if (type == "myself") {
                                        return '<a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">' +
                                            '<i class="layui-icon layui-icon-delete"></i>移除</a>';


                                    } else {
                                        return "";
                                    }
                                }
                            }
                        ]
                    ],
                    where: {orderBy: 'created_at', sortedBy: 'desc'}
                });

            };

            constrctLoad = function (id) {
                if (id == 1) {
                    table.render({
                        elem: "#constract-table",

                        url: '{{route('crm.reports.sea_customer_contract',$model->user_id)}}',
                        cols: [[

                            {field: 'contract_no', title: '合同编号', sort: true, width: "15%"}
                            , {
                                field: 'status', title: '合约状态', width: "10%",
                                templet: function (item) {
                                    if (item.status == 3) {
                                        return "生效中";
                                    } else {
                                        return "未生效";
                                    }
                                }

                            }
                            , {field: 'model_name', title: '电池型号', width: "10%"}
                            , {
                                field: 'audited_at', title: '租期', width: "10%",
                                templet: function (item) {

                                    if (item.lease_unit == "year") {
                                        var year = '年';
                                    } else if (item.lease_unit == "month") {
                                        var year = '个月';
                                    }

                                    return item.lease_term + year;

                                }
                            }
                            , {
                                field: 'rentals', title: '租金', width: "10%",
                                templet: function (item) {

                                    return JSON.parse(item.rentals);

                                }


                            }
                            , {field: 'prepayment', title: '预付款', width: "10%"}
                            , {field: 'payment_payed_at', title: '支付时间', width: "15%"}
                            , {field: 'effected_at', title: '生效时间', width: "15%"}
                            , {field: 'contract_expired_at', title: '到期时间', width: "15%"}
                            , {
                                field: 'user_nickname', title: '用户', width: "15%",
                                templet: function (item) {
                                    if (!item.user_nickname) {
                                        return item.user_mobile;
                                    } else {
                                        return item.user_nickname;
                                    }
                                }

                            }
                            , {
                                field: 'service_name', title: '网点', event: 'setSign', width: "15%"

                            }
                            , {
                                field: 'area', title: '区域', width: "15%",
                                templet: function (item) {

                                    return item.service_province_name + item.service_city_name;

                                }

                            }
                            , {field: 'charger_name', title: '负责人', width: "15%"}
                        ]],
                        where: {orderBy: 'created_at', sortedBy: 'desc', id: id}
                    });

                } else if (id == 2) {
                    table.render({
                        elem: "#constract-table",
                        url: '{{route('crm.reports.sea_customer_contract',$model->user_id)}}',
                        cols: [[
                            // {type: 'checkbox', value: "id"}
                            {field: 'id', title: '合同编号', sort: true, width: "10%"}
                            , {
                                field: 'status', title: '合同状态', width: "10%",
                                templet: function (item) {
                                    if (item.status == 1) {
                                        return "生效中";
                                    } else {
                                        return "未生效";
                                    }
                                }

                            }
                            , {field: 'constract_expires', title: '合同期限', width: "10%"}
                            , {field: 'audited_at', title: '审核时间', width: "15%"}
                            , {field: 'constract_begin_at', title: '生效时间', width: "15%"}
                            , {field: 'constract_end_at', title: '到期时间', width: "15%"}
                            , {field: 'league', title: '加盟费', width: "10%"}
                            , {field: 'bail', title: '保证金', width: "10%"}
                            , {
                                field: 'service_name', title: '网点', event: 'setSign', width: "15%"

                            }
                            , {
                                field: 'area', title: '区域', width: "15%",
                                templet: function (item) {

                                    return item.province_name + item.city_name;

                                }

                            }
                            , {field: 'charger_name', title: '负责人', width: "15%"}
                        ]],
                        where: {orderBy: 'created_at', sortedBy: 'desc', id: id}
                    });


                }


            };
            //监听单元格事件
            var _token = '{{ csrf_token() }}';
            table.on('tool(team-table)', function (obj) {
                var data = obj.data;
                if (obj.event === 'setSign') {
                    var id = data.user_id;

                    layer.confirm(' 你确定移除团队人员吗？', {}, function () {

                        $.ajax({
                            type: 'GET',
                            data: {'user_id': id, '_token': _token},
                            dataType: 'json',
                            url: '{{route('crm.reports.sea_customer_team_del',$model->id)}}',
                            success: function (res) {
                                if (res.code == 1) {
                                    table.reload('team-table');
                                    layer.msg('移除成功', {icon: 1});
                                } else {
                                    layer.msg('您不能移除该成员！');
                                }
                            },
                        });

                    });


                }
            });


            $("#add_team").click(function () {

                // var checkStatus = layui.table.checkStatus('demo').data;
                // var arr = new Array();
                // var table = layui.table;
                // $.each(checkStatus, function (i, n) {
                //     arr[i] = n.id;
                // });
                // var customer_id = arr.join(",");

                layer.open({
                    type: 2,
                    title: "新增",
                    content: '{{route('crm.reports.sea_customer_team_add',$model->id)}}',
                    {{--content: layui.common.route("{{route('crm.reports.sea_customer_distribute_view')}}"),--}}
                    area: ['500px', '400px'],
                    shadeClose: true,
                    btn: ['确定', '取消'],
                    yes: function (index, layero) {
                        var iframeWindow = window["layui-layer-iframe" + index];
                        if (common.isFunction(iframeWindow.layerYesCallback)) {
                            iframeWindow.layerYesCallback(index, layero);
                        }
                    }
                });

            });
        });
    </script>
@endsection
