@extends('manage::layouts.frame')

@section('style')
    {{Html::style(asset('resource/lib/select2/css/select2.css'))}}
    <style>
        .layui-form-select dl {
            max-height: 350px;
        }

        .layui-col-md6 {
            width: 48%;
            float: left;
        }

        .layui-input-block {
            width: 290px;
        }

        .layui-form-item {
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .select2 {
            width: 289px !important;
            line-height: 28px;
        }

        .closeIcon {
            position: relative;
            left: -15px;
            top: -16px;
        }

        .large-img {
            max-height: 400px !important;
            max-width: 800px !important;
        }
    </style>
@endsection

@section('content')
    <div class="layui-form" lay-filter="layuiadmin-form-admin" id="layuiadmin-form-admin"
         style="padding-top: 20px;">
        <div class="layui-col-md12">
            <div class="layui-col-md6">
                <div class="layui-form-item">
                    <label class="layui-form-label">客户名称</label>
                    <div class="layui-input-block">
                        <input type="hidden" name="type" value="1">
                        <input type="hidden" name="cus_id" value="{{$model->cus_id}}">
                        <input type="text" placeholder="" autocomplete="off" lay-verify="required"
                               class="layui-input layui-disabled" readonly
                               value="{{$model->cus->name?$model->cus->name:$model->cus->mobile}}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><span class="red">*</span>&nbsp;&nbsp;&nbsp;跟进方式</label>
                    <div class="layui-input-block">
                        {{Form::select("follow_mode",dictArr('crm_follow_mode'),$model->follow_mode,
                        ["lay-filter"=>"follow_mode","lay-search","placeholder"=>"跟进方式"])}}
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">联系人</label>
                    <div class="layui-input-block">
                        {{Form::select("contact_id",$contacts,$model->contact_id,["lay-filter"=>"follow_mode","lay-search"])}}
                    </div>
                </div>
            </div>
            <div class="layui-col-md6">
                <div class="layui-form-item">
                    <label class="layui-form-label">负责人</label>
                    <div class="layui-input-block">
                        {{Form::select("charger_id",$managers,$model->cus->charger_id,
                        ["lay-filter"=>"charger_id","lay-verify"=>'required',"lay-search","placeholder"=>"负责人","disabled"])}}
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><span class="red">*</span>&nbsp;&nbsp;&nbsp;跟进时间</label>
                    <div class="layui-input-block">
                        <input type="text" class="layui-input" placeholder="跟进时间" name="follow_at"
                               autocomplete="off" id="date" value="{{date('Y-m-d H:i:s',$model->follow_at)}}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><span class="red">*</span>&nbsp;&nbsp;&nbsp;跟进人员</label>
                    <div class="layui-input-block">
                        {{Form::select('',$teams,[],['class'=>'select2', 'lay-ignore', 'multiple'])}}
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span class="red">*</span>&nbsp;&nbsp;&nbsp;跟进内容</label>
                <div class="layui-input-block">
                    <textarea placeholder="" class="layui-textarea" lay-verify='required' style="width:721px"
                              name="content">{{$model->content}}</textarea>
                    <div id="imageDiv"
                         style="background-color: #f1f1f1;width:721px;border: 1px solid #e6e6e6;padding:10px;">
                        <button type="button" class="layui-btn" id="uploadImage">
                            <i class="layui-icon">&#xe67c;</i>上传图片
                        </button>
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
        <div class="layui-form-item layui-hide">
            <input type="button" lay-submit lay-filter="LAY-user-back-submit" id="LAY-user-back-submit"
                   value="确认">
        </div>
    </div>
@endsection

@section('script')
    <script>
        var layerId = 0, imgIds = [];
        layui.extend({
            'select2': '../../select2/js/select2'
        }).use(['upload', 'laydate', 'jquery', 'index', 'form', 'http', 'common', 'select2'], function () {
            var form = layui.form, http = layui.http, $ = layui.$, laydate = layui.laydate, common = layui.common;

            var upload = layui.upload;


            //执行实例
            var uploadInst = upload.render({
                elem: '#uploadImage' //绑定元素
                , url: '{{route("crm.upload")}}' //上传接口
                , data: {'_token': "{{ csrf_token() }}"}
                , done: function (res) {
                    if (res.code == 200) {
                        var image = '<span id="' + res.upload_id + '"><img src="' + res.relative_path + '" class="sm-img mr10" />' +
                            '<i class="layui-icon layui-icon-close-fill closeIcon" data-id="' + res.upload_id + '"></i></span>';
                        imgIds.push(res.upload_id);
                        $('#imageDiv').append(image);
                    } else {
                        layui.layer.msg(res.msg, {icon: 2});
                    }
                }
            });
            $(document).on('click', '.layui-icon-close-fill', function () {
                var id = $(this).data('id');
                var index = imgIds.indexOf(id);
                imgIds.splice(index, 1);
                $('#' + id).remove();
            });

            laydate.render({
                elem: '#date', type: 'datetime'
            });

            $(".select2").select2({
                language: "zh-CN",
                multiple: true,
                width: '260px',
                minimumInputLength: 0,
                placeholder: "请选择", //默认值
                allowClear: true,
            });

            //清除默认选中项
            var roles = eval('({!! collect($model->follow_users) !!})');
            $(".select2").val(roles || []).trigger('change');

            form.on("submit(LAY-user-back-submit)", function (data) {
                data.field.follow_user_ids = $('.select2').val() ? $('.select2').val().join(',') : '';
                data.field.img_ids = imgIds;
                http.put('{{route('plan.update',$model->id)}}', data.field).then(function (res) {
                    common.msg(res.msg);
                    parent.location.reload();
                    layerId && parent.layer.close(layerId);
                });
            });
        });


        function layerYesCallback(index) {
            layerId = index;

            layui.$('#LAY-user-back-submit').trigger('click');
        }

        function layerNoCallback(index) {

        }
    </script>
@endsection