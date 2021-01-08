//Demo
layui.use(['form', 'jquery'], function () {
    var form = layui.form;
    var $ = layui.jquery;
    //监听提交
    form.on('submit(addForm)', function (data) {
        $.ajax({
            url: adminurl + "/admins",
            data: $("form").serialize(),
            type: "POST",
            dataType: "json",
            beforeSend: function (request) {
                index = layer.load(2, {
                    shade: [0.1, '#FFF'] //0.1透明度的白色背景
                });
            },
            success: function (res) {
                layer.close(index);
                if (res.code == 1) {
                    layer.msg(res.msg);
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
                layer.msg("网络请求错误，稍后重试！");
                return false;
            }
        });
        return false;
    });
    //编辑用户信息
    form.on('submit(editForm)', function (data) {
        $.ajax({
            url: adminurl + "/admins/edit",
            data: $("form").serialize(),
            type: "PUT",
            dataType: "json",
            beforeSend: function (request) {
                index = layer.load(2, {
                    shade: [0.1, '#FFF'] //0.1透明度的白色背景
                });
            },
            success: function (res) {
                layer.close(index);
                if (res.code == 1) {
                    layer.msg(res.msg);
                    setTimeout(function () {
                        location.href = adminurl + "/admins";
                    }, 500)
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
                layer.msg("网络请求错误，稍后重试！");
                return false;
            }
        });
        return false;
    });
    //密码修改
    form.on('submit(repassForm)', function (data) {
        $.ajax({
            url: adminurl + "/admins/repass",
            data: $("form").serialize(),
            type: "POST",
            dataType: "json",
            beforeSend: function (request) {
                index = layer.load(2, {
                    shade: [0.1, '#FFF'] //0.1透明度的白色背景
                });
            },
            success: function (res) {
                layer.close(index);
                if (res.code == 1) {
                    layer.msg(res.msg);
                    setTimeout(function () {
                        parent.location.href = adminurl + "/../login";
                    }, 500)
                } else if (res.code == 1002) {
                    layer.msg(res.msg);
                    setTimeout(function () {
                        parent.location.href = adminurl + "/../login";
                    }, 500)
                } else {
                    layer.msg(res.msg);
                    return false;
                }
            },
            error: function () {
                layer.close(index);
                layer.msg("网络请求错误，稍后重试！");
                return false;
            }
        });
        return false;
    });

    //系统基本信息设置
    form.on('submit(formBase)', function (data) {
        $.ajax({
            url: adminurl + "/bases",
            data: $("form").serialize(),
            type: "PUT",
            dataType: "json",
            beforeSend: function (request) {
                index = layer.load(2, {
                    shade: [0.1, '#FFF'] //0.1透明度的白色背景
                });
            },
            success: function (res) {
                layer.close(index);
                if (res.code == 1) {
                    layer.msg(res.msg);
                    setTimeout(function () {
                        location.reload();
                    }, 500)
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
                layer.msg("网络请求错误，稍后重试！");
                return false;
            }
        });
        return false;
    });

    //监听select选择
    form.on('select(region)', function (data) {
        $.ajax({
            url: adminurl + "/bases/setregion",
            data: {
                region_id: data.value
            },
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
                    layer.msg(res.msg);
                    setTimeout(function () {
                        location.reload();
                    }, 500)
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
                layer.msg("网络请求错误，稍后重试！");
                return false;
            }
        });
    });

    var active = {
        showAdmin: function () {
            var is_show = $(this).children("div").hasClass("layui-form-onswitch") ? 1 : 2;
            var adminId = $(this).children("input").data("id");
            $.ajax({
                url: adminurl + "/admins/edit/status",
                data: {
                    _token: _token,
                    id: adminId,
                    is_show: is_show
                },
                type: "PUT",
                dataType: "json",
                beforeSend: function (request) {
                    layer.closeAll();
                    index = layer.load(2, {
                        shade: [0.3, '#FFF'] //0.1透明度的白色背景
                    });
                },
                success: function (res) {
                    layer.close(index);
                    if (res.code == 1) {
                        layer.msg(res.msg);
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
                    layer.msg("网络请求错误，稍后重试！");
                    return false;
                }
            });
        }
        , deleteAdmin: function () {
            var _id = $(this).data("id");
            var obj = $(this);
            layer.confirm('您确认删除此用户吗？', {
                btn: ['确认', '取消'] //按钮
                , title: '<i class="layui-icon">&#xe607;</i> 确认提示'
                , icon: 0
            }, function () {
                $.ajax({
                    url: adminurl + "/admins/del",
                    data: {
                        id: _id,
                        _token: _token,
                    },
                    type: "DELETE",
                    dataType: "json",
                    beforeSend: function (request) {
                        index = layer.load(2, {
                            shade: [0.1, '#FFF'] //0.1透明度的白色背景
                        });
                    },
                    success: function (res) {
                        layer.close(index);
                        if (res.code == 1) {
                            layer.closeAll();
                            layer.msg(res.msg);
                            obj.parent().parent().remove();
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
                        layer.msg("网络请求错误，稍后重试！");
                        return false;
                    }
                });
            });
        }
    };
    $('body .handle').on('click', function () {
        var othis = $(this), method = othis.data('method');
        active[method] ? active[method].call(this, othis) : '';
    });
});

//头像上传操作~
function uploadImage(obj, id) {
    index = layer.load(2, {
        shade: [0.1, '#FFF'] //0.1透明度的白色背景
    });
    if (!file) {
        layer.closeAll();
    }
    var file = obj.files[0];
    if (!file) {
        layer.closeAll();
        return false;
    }
    //判断类型是不是图片
    if (!/image\/\w+/.test(file.type)) {
        layer.closeAll();
        layer.msg("请确保文件为图像类型");
        return false;
    }
    var reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = function (e) {
        imgRes = this.result;
        $.ajax({
            type: "PUT",
            url: adminurl + "/admins/upload",
            data: {
                image: this.result,
                _token: _token
            },
            dataType: 'json',
            success: function (res) {
                if (res.code == 1) {
                    layer.closeAll();
                    layer.msg(res.msg);
                    $("#" + id).attr("src", imgRes);
                    $("#putavator").val(res.data.source);
                } else if (res.code == 1002) {
                    layer.msg(res.msg);
                    setTimeout(function () {
                        location.href = adminurl + "/login";
                    }, 500)
                } else {
                    layer.msg(res.msg);
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                layer.close(index);
                alert("上传失败，请检查网络后重试");
            }
        });
    }
}

//头像异步上传操作~
function uploadLogo(obj, id) {
    index = layer.load(2, {
        shade: [0.1, '#FFF'] //0.1透明度的白色背景
    });
    var file = obj.files[0];
    if (!file) {
        layer.closeAll();
        return false;
    }
    //判断类型是不是图片
    if (!/image\/\w+/.test(file.type)) {
        layer.closeAll();
        layer.msg("请确保文件为图像类型");
        return false;
    }
    var reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = function (e) {
        imgRes = this.result;
        $.ajax({
            type: "PUT",
            url: adminurl + "/uploadImg",
            data: {
                image: this.result,
                type: 2,
                cut_size: [{width: 30, height: 30}, {width: 90, height: 90}],
                is_cat: 1,
                _token: _token
            },
            dataType: 'json',
            success: function (res) {
                layer.closeAll();
                if (res.code == 1) {
                    layer.msg("logo修改成功！");
                    $("#" + id).attr("src", imgRes);
                    $("#putavator").val(res.data.avator);
                } else if (res.code == 1002) {
                    layer.msg(res.msg);
                    setTimeout(function () {
                        location.href = adminurl + "/login";
                    }, 500)
                } else {
                    layer.msg(res.msg);
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                layer.close(index);
                layer.msg("网络异常稍后重试！");
            }
        });
    }
}

//二码异步上传操作~
function uploadQrcode(obj, id) {
    index = layer.load(2, {
        shade: [0.1, '#FFF'] //0.1透明度的白色背景
    });
    var file = obj.files[0];
    if (!file) {
        layer.closeAll();
        return false;
    }
    //判断类型是不是图片
    if (!/image\/\w+/.test(file.type)) {
        layer.closeAll();
        layer.msg("请确保文件为图像类型");
        return false;
    }
    var reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = function (e) {
        imgRes = this.result;
        $.ajax({
            type: "PUT",
            url: adminurl + "/bases/upload",
            data: {
                imgpath: this.result,
                type: 2,
                _token: _token
            },
            dataType: 'json',
            success: function (res) {
                if (res.code == 1) {
                    layer.closeAll();
                    layer.msg("二维码保存成功！");
                    $("#" + id).attr("src", imgRes);
                    $("#putavator").val(res.data.avator);
                } else if (res.code == 1002) {
                    layer.msg(res.msg);
                    setTimeout(function () {
                        location.href = adminurl + "/login";
                    }, 500)
                } else {
                    layer.msg(res.msg);
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                layer.close(index);
                layer.msg("网络异常稍后重试！");
            }
        });
    }
}