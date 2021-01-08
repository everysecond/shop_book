;layui.define(['view', 'setter'], function (e) {
    var $ = layui.$, config = layui.setter;

    var http = function () {

    }

    http.get = function (url, data, option) {
        return this.request('get', url, data, option);
    }

    http.post = function (url, data, option) {
        return this.request('post', url, data, option);
    }

    http.put = function (url, data, option) {
        return this.request('put', url, data, option);
    }

    http.delete = function (url, data, option) {
        return this.request('delete', url, data, option);
    }

    http.request = function (method, url, data, option) {
        var option = $.extend({
            showError: true,
        }, option);
        return new Promise(function (resolve, reject) {
            $.ajax({
                url: url,
                type: method,
                dataType: "json",
                data: data || {},
                headers: {
                    'X-CSRF-TOKEN': $("meta[name='x-csrf-token']").attr('content')
                },
                success: function (res) {
                    var r = config.response;
                    if (res[r.statusName] == r.statusCode.ok) {
                        resolve(res);
                    } else {
                        if (option.showError) {
                            var msg = "<cite>错误：</cite> " + (res[r.msgName] || "返回状态码异常");
                            top.layer.open({title: '错误', content: msg, icon: 5, anim: 6});
                            if(vercode = $("#LAY-user-get-vercode")){//如果有验证码，自动刷新一次验证码
                                vercode.click();
                            }
                        }

                        reject(res);
                    }
                },
                error: function (e, type) {
                    var msg = '请求异常，请重试<br><cite>错误信息：</cite>';
                    if (type == 'error') {
                        msg += '[' + e.status + ']' + e.statusText;
                    } else if (type == 'timeout') {
                        msg += '请求超时';
                    } else if (type == 'parsererror') {
                        msg += '服务器响应内容无法解析';
                    } else if (type == 'notmodified') {
                        msg += 'notmodified';
                    }

                    top.layer.open({title: '错误', content: msg, icon: 5, anim: 6});
                }
            });
        });
    }

    e("http", http);
});