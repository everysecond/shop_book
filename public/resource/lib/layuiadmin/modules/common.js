/** layuiAdmin.std-v1.0.0 LPPL License By http://www.layui.com/admin/ */
;layui.define(['table', 'http'], function (e) {
    var $ = layui.$, table = layui.table, http = layui.http;
    // var i = (layui.$, layui.layer, layui.laytpl, layui.setter, layui.view, layui.admin);
    //
    // i.events.logout = function () {
    //     i.req({
    //         url: layui.setter.base + "json/user/logout.js", type: "get", data: {}, done: function (e) {
    //             i.exit(function () {
    //                 location.href = "user/login.html"
    //             })
    //         }
    //     })
    // };

    $(document).on('click', ".sm-img", function () {
        $(".large-img").attr("src", $(this).attr("src"));
        if ($(this).attr("data-wRatio")) {
            $(".large-img").css("height", $(this).attr("data-hRatio"));
            $(".large-img").css("width", $(this).attr("data-wRatio"));
        }
        $("#enlargeImage").removeClass("hidden");
    });

    $(document).on('click', "#closeLargeImg", function () {
        $("#enlargeImage").addClass("hidden");
    });


    $(document).on('click', '.clearInput', function (e) {
        $("input").val("");
        $("select").val("");
    });
    //注册数组方法
    Array.prototype.pluck = function (filed, key) {
        var items = [];

        this.forEach(function (val, index) {
            index = key ? val[key] : index;
            items[index] = val[filed];
        })

        return items;
    }

    //监听下拉按钮
    $(document).on('click', '.dropdown-toggle', function (e) {
        e.preventDefault();
        e.stopPropagation();

        $('.layui-btn-group .layui-show').removeClass('layui-show');

        $(this).next('.dropdown-menu').addClass('layui-show');
    });

    $(document).on('click', function () {
        $('.layui-btn-group .layui-show').removeClass('layui-show');
    });

    //监听.open-frame
    $(document).on("click", ".open-frame", function (e) {
        e.preventDefault();
        var href = $(this).attr("href");
        var full = $(this).data('full') || false;
        var width = $(this).data('width') || '420px';
        var height = $(this).data('height') || '420px';
        full && (width = height = '100%');

        layer.open({
            type: 2,
            title: '<i class="layui-icon layui-icon-app"></i> ' + this.title,
            area: [width, height],
            content: href,
            btn: ['确定', '取消'],
            yes: function (index, layero) {
                var frameWindow = window["layui-layer-iframe" + index];
                if (common.isFunction(frameWindow.layerYesCallback)) {
                    frameWindow.layerYesCallback(index, layero);
                }
            }
        });
    });

    $(document).on('click', '.ajax-link', function (e) {
        e.preventDefault();

        var href = $(this).attr("href");
        var msg = $(this).data('confirm') || false;
        var method = $(this).data('method') || 'get';
        var title = this.title || '';

        var doSubmit = function () {
            http.request(method, href).then(function (res) {
                common.success(res.msg || '操作成功');

                location.reload();
            });
        }
        if (msg) {
            layer.confirm(msg, {
                title: title,
                icon: 3
            }, function (index) {
                doSubmit(index);
            })
        } else {
            doSubmit();
        }

    });

    //监听批量操作按钮 .btn-batch
    $(document).on('click', '.btn-batch', function (e) {
        e.preventDefault();

        var href = $(this).attr("href");
        var tableFilter = $(this).attr('for');
        var msg = $(this).data('confirm') || false;
        var method = $(this).data('method') || 'get';
        var title = this.title || '';

        var checkStatus = table.checkStatus(tableFilter)
            , checkData = checkStatus.data; //得到选中的数据

        if (!checkData.length) {
            return layer.msg('请选择数据');
        }

        var doSubmit = function () {
            var ids = checkData.pluck('id');

            http.request(method, href, {id: ids}).then(function (res) {
                common.msg(res.msg || '操作成功');

                location.reload();
            });
        }

        if (msg) {
            layer.confirm(msg, {
                title: title,
                icon: 3
            }, function (index) {
                doSubmit(index);
            })
        } else {
            doSubmit();
        }
    });

    //监听批量操作按钮 .btn-batch
    $(document).on('click', '.batch-operate', function (e) {
        e.preventDefault();

        var href = $(this).attr("href");
        var tableFilter = $(this).attr('for') || false;
        var msg = $(this).data('confirm') || false;
        var method = $(this).data('method') || 'get';
        var title = this.title || '';
        if (tableFilter) {
            var checkStatus = table.checkStatus(tableFilter)
                , checkData = checkStatus.data; //得到选中的数据

            if (!checkData.length) {
                return layer.msg('请选择数据');
            }

            var ids = checkData.pluck('id');
            ids = ids.join(',');
            href = href + "?ids=" + ids;
        }

        layer.open({
            type: 2,
            title: title,
            content: href,
            area: ['500px', '400px'],
            btn: ['确定', '取消'],
            yes: function (index, layero) {
                var iframeWindow = window["layui-layer-iframe" + index];
                if (common.isFunction(iframeWindow.layerYesCallback)) {
                    iframeWindow.layerYesCallback(index, layero);
                }
            }
        });
    });

    $(document).on("focus", ".ajax-change", function () {
        $(this).attr('data-val', $(this).val());
    });
    $(document).on("change", ".ajax-change", function () {
        var id = $(this).data("id");
        var href = $(this).attr('href');
        var name = $(this).attr("name");
        var value = $(this).val();

        http.put(href, {id: id, name: name, value: value}).then(function (res) {
            common.msg(res.msg || '操作成功');
        });
    });

    var common = {};

    common.success = function (msg, title, callback) {
        if (this.isFunction(title)) {
            callback = title;
            title = null;
        }

        top.layer.open({
            title: title || '成功',
            content: msg,
            icon: 6,
        });
    }

    common.error = function (msg, title, callback) {
        if (this.isFunction(title)) {
            callback = title;
            title = null;
        }
        top.layer.open({
            title: title || '错误',
            content: msg,
            icon: 5,
            anim: 6
        });
    }

    common.msg = function (msg) {
        return top.layer.msg(msg);
    }


    common.route = function (url, params) {
        var params = params || {};
        for (var x in params) {
            url = url.replace(':' + x, params[x]);
        }
        return url;
    }

    common.searchPack = function (params) {
        var field = [];
        for (var x in params) {
            if (params[x] !== '') {
                field.push(x + ':' + params[x]);
            }
        }
        return field.join(';');
    }

    common.isFunction = function (fn) {
        return 'function' === typeof fn;
    }

    e("common", common);
});