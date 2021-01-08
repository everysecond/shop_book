@extends('manage::layouts.master')

@section('style')
    <style>
        .layui-col-md12 {
            /*margin: 5px;*/
        }

        .layui-card {
            margin: 10px;
        }

        .layui-icon-file::before {
            content: "\e67e";
            border: 1px solid #c0c4cc;
            font-size: 12px;
            color: #666;
        }

        .layui-icon-file {
            line-height: 15px;
        }

        .inline-div, .layui-tree-entry {
            display: inline;
        }

        .title-font {
            font-family: "微软雅黑 Bold", "微软雅黑 Regular", 微软雅黑;
            font-weight: 700;
            font-style: normal;
            color: rgb(30, 30, 30);
            width: 160px;
            text-align: center;
            display: inline-block;
            cursor: pointer;
        }

        .title-font:hover {
            color: rgb(26, 188, 156);
        }

        .title-select {
            color: rgb(26, 188, 156);
            border-bottom: 3px solid rgb(26, 188, 156);
        }

        html, body, .layui-col-md3, .layui-col-md12, .layui-col-md9 {
            height: 100%;
        }

        html, body {
            height: 97%;
        }

        .layui-card-header {
            border-bottom: 1px solid #cccccc;
        }

        .layui-inline {
            margin-right: 15px !important;
        }

        .layui-table-cell {
            padding: 0 8px
        }

        .layui-layer-setwin .layui-layer-close2 {
            right: 0 !important;
            top: 0 !important;
        }
    </style>
@endsection

@section('content')
    <div class="layui-col-md12">
        <div class="layui-card" style="padding:10px 25px;height:110px;">
            <div class="layui-card-header" style="padding: 0;padding-bottom: 2px !important;">
                <div class="title-font title-select" data-type="myself" lay-filter="LAY-user-back-search" lay-submit>
                    我的
                </div>
                <div class="title-font" data-type="myteam" lay-filter="LAY-user-back-search" lay-submit>我协作的</div>
                <div class="title-font" data-type="under" lay-filter="LAY-user-back-search" lay-submit>下属的</div>
                <div class="title-font" data-type="underteam" lay-filter="LAY-user-back-search" lay-submit>下属协作的</div>
            </div>
            <div class="layui-form" style="padding:15px 0;">
                <div class="layui-inline" align="left">
                    <input type="text" id="searchStr" placeholder="客户名称、姓名" autocomplete="off"
                           class="layui-input">
                </div>
                <div class="layui-inline" align="left">
                    {{Form::select("follow_mode",dictArr('crm_follow_mode'),'',["lay-search","placeholder"=>"跟进方式"])}}
                </div>
                <div class="layui-inline" align="left">
                    <div class="layui-input-inline" style="width: 210px;">
                        <input type="text" class="layui-input" placeholder="跟进时间"
                               autocomplete="off" id="date1">
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn" lay-filter="LAY-user-back-search" lay-submit>
                        <i class="layui-icon"></i>搜索
                    </button>
                    <button class="layui-btn layui-btn-primary clearInput" style="width: 86px">清空
                    </button>
                </div>
            </div>
        </div>
        <div class="layui-card" style="padding: 25px;height:80%;">

            <div class="layui-card-body" style=" height: 100%;padding: 0;overflow: auto;zoom:1;">
                <table id="LAY-user-back-manage" lay-filter="LAY-user-back-manage"></table>
            </div>
        </div>
        <div style="clear: both"></div>
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
        var position_id = '', table, type = 'myself', layer;
        layui.use(['laydate', 'index', 'table', 'form', 'common', 'http', 'tree', 'util'], function () {
            layer = layui.layer;
            var laydate = layui.laydate;
            laydate.render({
                elem: '#date1'
                , type: 'date'
                , range: true
            });

            var http = layui.http, common = layui.common, $ = layui.$, form = layui.form;
            $('.title-font').click(function () {
                    $('.title-font').removeClass('title-select');
                    $(this).addClass('title-select');
                    type = $(this).data('type');
                    if (type != "myself") {
                        $(".mybtn").addClass("hidden");
                    } else {
                        $(".mybtn").removeClass("hidden");
                    }
                }
            );

            table = layui.table;
            table.render({
                elem: "#LAY-user-back-manage",
                url: '{{route('plan.paginate')}}',
                page: 1,
                limit: 15,
                limits: [10, 15, 20, 30, 40, 50, 60, 70, 100],
                cols: [
                    [
                        {
                            field: "name",
                            title: "跟进客户",
                            width: '11%',
                            templet: function (item) {
                                var name = item.cus.name ? item.cus.name : item.cus.mobile;
                                var deltail = '/manage/crm/cus/detail/' + item.cus.id;
                                return '<a class="open-frame-r color-green" data-method="get" href="' + deltail + '" ' +
                                    'title="客户详情" data-width="1050px" data-type="detail"' +
                                    ' data-height="100%">' + name + '</a>';
                            }
                        },
                        {
                            field: "mode",
                            title: "跟进方式",
                            width: '6%'
                        },
                        {
                            field: "contact_name",
                            title: "联系人",
                            width: '10%'
                        },
                        {
                            field: "follow_users",
                            title: "跟进人员",
                            width: '11%'
                        },
                        {
                            field: "content",
                            title: "跟进内容",
                            width: '20%'
                        },
                        {
                            title: "图片",
                            width: '15%',
                            templet: function (item) {
                                if(item.images){
                                    var images = '';
                                    for (i=0;i<item.images.length;i++){
                                        images += '<img src="'+ item.images[i].relative_path +'" class="sm-img mr10">';
                                    }
                                    return images;
                                }
                            }
                        },
                        {
                            title: "负责人",
                            field: "charger",
                            width: "8%"
                        },
                        {field: "follow_at", title: "跟进时间"}
                    ]
                ],
                where: {orderBy: 'follow_at', sortedBy: 'desc', record_type: 1}
            });

            form.on('submit(LAY-user-back-search)', function (data) {
                table.reload('LAY-user-back-manage', {
                    where: {
                        searchStr: $('#searchStr').val(),
                        date: $('#date1').val(),
                        follow_mode: $('select[name="follow_mode"]').val(),
                        type: type,
                        record_type: 1
                    }
                });
            });

            $(document).on("click", ".open-frame-r", function (e) {
                e.preventDefault();
                var href = $(this).attr("href");
                var full = $(this).data('full') || false;
                var width = $(this).data('width') || '420px';
                var height = $(this).data('height') || '420px';
                var btn = $(this).data('type') ? '' : ['确定', '取消'];
                full && (width = height = '100%');

                layer.open({
                    type: 2,
                    title: false,
                    area: [width, height],
                    content: href,
                    anim: 2,
                    closeBtn: 1,
                    btn: btn,
                    offset: 'r',
                    yes: function (index, layero) {
                        var frameWindow = window["layui-layer-iframe" + index];
                        if (common.isFunction(frameWindow.layerYesCallback)) {
                            frameWindow.layerYesCallback(index, layero);
                        }
                        setTimeout(function () {
                            table.reload('LAY-user-back-manage', {
                                where: {
                                    searchStr: $('#searchStr').val(),
                                    cus_type: $('select[name="cus_type"]').val(),
                                    cus_source: $('select[name="cus_source"]').val(),
                                    cus_level: $('select[name="cus_level"]').val(),
                                    province_id: $('select[name="province_id"]').val(),
                                    type: type,
                                }
                            });
                        }, 1000);
                    }
                });
            });
        });
    </script>
@endsection
