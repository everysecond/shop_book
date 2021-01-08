layui.use(['element', 'jquery', 'layer', 'form', 'laydate', 'laypage'], function () {
    var element = layui.element;
    var form = layui.form;
    $ = layui.jquery;
    var layer = layui.layer;
    var laydate = layui.laydate;
    var laypage = layui.laypage;
    laydate.render({
        elem: '#searchSelect' //指定元素
        , range: true
    });
    var active = {
        reFormSearch: function () {
            getRegionLists(1, "", "");
        }
    };
    $('#log-action .log-action').on('click', function () {
        var othis = $(this), method = othis.data('method');
        active[method] ? active[method].call(this, othis) : '';
    });
    //数据搜索
    form.on('submit(formSearch)', function (data) {
        if (data.field.keyword == "" && data.field.datetime == "") {
            layer.msg("搜索内容不能为空！");
        } else {
            getRegionLists(1, data.field.keyword, data.field.datetime);
        }
        return false;
    });

    getRegionLists(1, "", ""); //初始化表格数据
    function getRegionLists(page, keyword, datetime) {
        $.ajax({
            url: adminurl + "/logs/lists?page=" + page + "&keyword=" + keyword + "&datetime=" + datetime,
            data: '',
            type: "get",
            dataType: "json",
            beforeSend: function (request) {
                index = layer.load(2, {
                    shade: [0.1, '#FFF'] //0.1透明度的白色背景
                });
            },
            success: function (res) {
                layer.close(index);
                if (res.code == 1) {
                    innerData = '';
                    if (res.data.count == 0) {
                        innerData += "<tr>";
                        innerData += '<td colspan="12" class="align-center">暂无数据</td>';
                        innerData += '</tr>';
                        laypage.render({elem: 'pages', count: res.data.count});
                    } else {
                        laypage.render({
                            elem: 'pages'
                            , count: res.data.count
                            , limit: 15
                            , jump: function (obj, first) {
                                //getRegionLists(obj.curr,obj);
                                //首次不执行
                                if (!first) {
                                    $.ajax({
                                        url: adminurl + "/logs/lists?page=" + obj.curr + "&keyword=" + keyword + "&datetime=" + datetime,
                                        data: '',
                                        type: "get",
                                        dataType: "json",
                                        beforeSend: function (request) {
                                            index = layer.load(2, {
                                                shade: [0.1, '#FFF'] //0.1透明度的白色背景
                                            });
                                        },
                                        success: function (res) {
                                            layer.close(index);
                                            if (res.code == 1) {
                                                innerData = '';
                                                if (res.count == 0) {
                                                    innerData += "<tr>";
                                                    innerData += '<td colspan="12">' + item.name + '</td>';
                                                    innerData += '</tr>';
                                                } else {
                                                    floatDataTpl(res.data.lists, obj.curr);
                                                }
                                                $('#logbox').html(innerData);
                                                form.render();
                                            } else if (res.code == 1002) {
                                                layer.msg(res.msg);
                                                setTimeout(function () {
                                                    location.href = adminurl + "/login";
                                                }, 500)
                                            } else {
                                                layer.msg(res.msg);
                                                return false;
                                            }
                                        },
                                        error: function () {
                                            layer.close(index);
                                            layer.msg("网络异常，请检查网络连接！");
                                            return false;
                                        }
                                    });
                                } else {
                                    floatDataTpl(res.data.lists, obj.curr);
                                }
                            }
                        });
                    }
                    $('#logbox').html(innerData);
                    form.render();
                } else if (res.code == 1002) {
                    layer.msg(res.msg);
                    setTimeout(function () {
                        location.href = adminurl + "/login";
                    }, 500)
                } else {
                    layer.msg(res.msg);
                    return false;
                }
            },
            error: function () {
                layer.close(index);
                layer.msg("网络异常，请检查网络连接！");
                return false;
            }
        });
    }

    //数据格式化模板
    function floatDataTpl(data, page) {
        layui.each(data, function (index, item) {
            innerData += "<tr>";
            innerData += '<td>' + ((index + 1) + (page - 1) * 15) + '</td>';
            innerData += '<td>' + item.name + '</td>';
            innerData += '<td>' + item.roles + '</td>';
            // innerData +='<td>'+item.country+'</td>';
            innerData += '<td>中国</td>';
            // innerData += '<td>' + item.city + '</td>';
            innerData += '<td>合肥</td>';
            // innerData += '<td>' + item.ip + '</td>';
            innerData += '<td>保密</td>';
            innerData += '<td>' + item.create_date + '</td>';
            innerData += '<td>' + item.content + '</td>';
            innerData += '</tr>';
        });
    }
})
