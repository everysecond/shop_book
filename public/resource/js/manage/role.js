layui.use(['element','jquery','layer','form'], function(){
    var element = layui.element;
    var form    = layui.form;
    $ = layui.jquery;
    var layer = layui.layer;
    var active = {
        addRole: function(){
            var that = this;
            $("#addRole")[0].reset();
            //多窗口模式，层叠置顶
            layer.open({
                type: 1 //此处以iframe举例
                ,title: '<i class="layui-icon">&#xe608;</i> 添加角色'
                ,area: ['390px', 'auto']
                ,shade: 0.8
                ,maxmin: false
                ,content: $("#addRole")
                ,btn: ['保存', '关闭'] //只是为了演示
                ,yes: function(){
                    if($("#rolename").val()==""){
                        $("#rolename").focus();
                        layer.msg("角色名称不能为空！");
                        return false;
                    }
                    $.ajax({
                        url: adminurl + "/roles/add",
                        data:$("#addRole").serialize(),
                        type:"POST",
                        dataType:"json",
                        beforeSend: function (request) {
                            index = layer.load(2, {
                                shade: [0.1,'#FFF'] //0.1透明度的白色背景
                            });
                        },
                        success:function(res){
                            layer.close(index);
                            if(res.code==1){
                                layer.msg(res.msg);
                                setTimeout(function(){
                                    location.reload();
                                },1000)
                            }else if(res.code==1002){
                                layer.msg(res.msg);
                                setTimeout(function(){
                                    location.href= adminurl + "/login";
                                },500)
                            }else{
                                layer.msg(res.msg);
                                return false;
                            }
                        },
                        error:function(){
                            layer.close(index);
                            layer.msg("网络请求错误，稍后重试！");
                            return false;
                        }
                    });
                }
                ,btn2: function(){
                    layer.closeAll();
                }
                ,zIndex: layer.zIndex //重点1
            });
        }
        ,editRole: function(){
            var that = this;
            $("#rolename").val($(this).data("title"));
            $("#remark").val($(this).data("remark"));
            $("#roleId").val($(this).data("id"));
            layer.open({
                type: 1 //此处以iframe举例
                ,title: '<i class="layui-icon">&#xe608;</i> 编辑角色'
                ,area: ['390px', 'auto']
                ,shade: 0.8
                ,maxmin: false
                ,content: $("#addRole")
                ,btn: ['保存', '关闭'] //只是为了演示
                ,yes: function(){
                    if($("#rolename").val()==""){
                        $("#rolename").focus();
                        return false;
                    }
                    if($("#remark").val().length > 90){
                        $("#remark").focus();
                        layer.msg("角色描述最多不能超过30个字符！");
                        return false;
                    }
                    $.ajax({
                        url: adminurl + "/roles/edit",
                        data:$("#addRole").serialize(),
                        type:"PUT",
                        dataType:"json",
                        beforeSend: function (request) {
                            index = layer.load(2, {
                                shade: [0.1,'#FFF'] //0.1透明度的白色背景
                            });
                        },
                        success:function(res){
                            layer.close(index);
                            if(res.code==1){
                                layer.closeAll();
                                layer.msg(res.msg);
                                setTimeout(function(){
                                    location.reload();
                                },1000)
                            }else if(res.code==1002){
                                layer.msg(res.msg);
                                setTimeout(function(){
                                    location.href= adminurl + "/login";
                                },500)
                            }else{
                                layer.msg(res.msg);
                                return false;
                            }
                        },
                        error:function(){
                            layer.close(index);
                            layer.msg("网络请求错误，稍后重试！");
                            return false;
                        }
                    });
                }
                ,btn2: function(){
                    layer.closeAll();
                }
                ,zIndex: layer.zIndex //重点1
            });
        }
        ,deleteRole:function(){
            var _id = $(this).data("id");
            layer.confirm('您确认删除此角色吗？', {
                btn: ['确认','取消'] //按钮
                ,title:'<i class="layui-icon">&#xe607;</i> 确认提示'
                ,icon:0
            }, function(){
                $.ajax({
                    url: adminurl + "/roles/delete",
                    data:{
                        id:_id,
                        _token:_token,
                    },
                    type:"DELETE",
                    dataType:"json",
                    beforeSend: function (request) {
                        index = layer.load(2, {
                            shade: [0.1,'#FFF'] //0.1透明度的白色背景
                        });
                    },
                    success:function(res){
                        layer.close(index);
                        if(res.code==1){
                            layer.closeAll();
                            layer.msg(res.msg);
                            setTimeout(function(){
                                location.reload();
                            },1000)
                        }else if(res.code==1002){
                            layer.msg(res.msg);
                            setTimeout(function(){
                                location.href= adminurl + "/login";
                            },500)
                        }else{
                            layer.msg(res.msg);
                            return false;
                        }
                    },
                    error:function(){
                        layer.close(index);
                        layer.msg("网络请求错误，稍后重试！");
                        return false;
                    }
                });
            });
        }
    };
    $('body .handle').on('click', function(){
        var othis = $(this), method = othis.data('method');
        active[method] ? active[method].call(this, othis) : '';
    });
    //数据搜索
    form.on('submit(saveAuth)', function(data){
        $.ajax({
            url: adminurl + "/roles/editauth",
            data:$("form").serialize(),
            type:"PUT",
            dataType:"json",
            beforeSend: function (request) {
                index = layer.load(2, {
                    shade: [0.1,'#FFF'] //0.1透明度的白色背景
                });
            },
            success:function(res){
                layer.close(index);
                if(res.code==1){
                    layer.closeAll();
                    layer.msg(res.msg);
                    /*
                    setTimeout(function(){
                        location.reload();
                    },1000)*/
                }else if(res.code==1002){
                    layer.msg(res.msg);
                    setTimeout(function(){
                        location.href= adminurl + "/login";
                    },500)
                }else{
                    layer.msg(res.msg);
                    return false;
                }
            },
            error:function(){
                layer.close(index);
                layer.msg("网络请求错误，稍后重试！");
                return false;
            }
        });
        return false;
    });

    //区域全选
    form.on('checkbox(region)', function(data){
        var child = $("#regionChild").find('input[type="checkbox"]');
        child.each(function(index, item){
            item.checked = data.elem.checked;
        });
        form.render('checkbox');
    });
    //菜单全选
    form.on('checkbox(menu)', function(data){
        var child = $("#menuChild").find('input[type="checkbox"]');
        child.each(function(index, item){
            item.checked = data.elem.checked;
        });
        form.render('checkbox');
    });
    //如果子类全部选中则选中全选操作
    form.on('checkbox(regionItem)',function(data){
        var sib = $("#regionChild").find('input[type="checkbox"]:checked').length;
        var total = $("#regionChild").find('input[type="checkbox"]').length;
        if(sib == total){
            $("#regionbox").find('input[type="checkbox"]').prop("checked",true);
            form.render();
        }else{
            $("#regionbox").find('input[type="checkbox"]').prop("checked",false);
            form.render();
        }
    });
    //如果子类全部选中则选中全选操作(通过最后一级菜单判断是否全选)
    form.on('checkbox(menuItem)',function(data){
        var menuChildKey = $(this).data("pkey");
        var menuChildChecked = $("#menuChildChild_"+menuChildKey).find('input[type="checkbox"]:checked').length;
        var menuChildTotal = $("#menuChildChild_"+menuChildKey).find('input[type="checkbox"]').length;
        if(menuChildChecked == menuChildTotal){
            $("#menuChildChildBox_"+menuChildKey).prop("checked",true);
            form.render();
        }else{
            $("#menuChildChildBox_"+menuChildKey).prop("checked",false);
            form.render();
        }
        var sib = $("#menuChild").find('input[type="checkbox"]:checked').length;
        var total = $("#menuChild").find('input[type="checkbox"]').length;
        if(sib == total){
            $("#menubox").find('input[type="checkbox"]').prop("checked",true);
            form.render();
        }else{
            $("#menubox").find('input[type="checkbox"]').prop("checked",false);
            form.render();
        }
    });
    //子菜单全选，并通过此操作判断是否全选
    form.on('checkbox(menuChild)', function(data){
        var _key = $(this).data("key");
        var child = $("#menuChildChild_"+_key).find('input[type="checkbox"]');
        child.each(function(index, item){
            item.checked = data.elem.checked;
        });
        var sib = $("#menuChild").find('input[type="checkbox"]:checked').length;
        var total = $("#menuChild").find('input[type="checkbox"]').length;
        if(sib == total){
            $("#menubox").find('input[type="checkbox"]').prop("checked",true);
            form.render();
        }else{
            $("#menubox").find('input[type="checkbox"]').prop("checked",false);
            form.render();
        }
        form.render('checkbox');
    });
    //选择操作
    //如果子类全部选中则选中全选操作
    form.on('checkbox(menuAction)',function(data){
        $(data.elem).parent().prev().find("input").prop("checked",true);
        form.render('checkbox');
    });
});