@include('pub.base')
<div class="page-container">
    <input type="hidden" id="callbackId" value="{{$_GET['id']}}">
    <div>
                <div id="drag" style='width:80px;margin-left:30px'>
                    <label for="file-input">
                        <i class="Hui-iconfont Hui-iconfont-upload" style="font-size: 50px;color:blue" id="upload-btn"></i>
                    </label>
                </div>
                <input type="file" accept="*/*" name="file[]" id="file-input" multiple class="input-file" style="display: none">
     </div>
</div>

<script src="/static/lib/H5upload.js"></script>

<script>
    $(function () {
        var callbackId = $("#callbackId").val();
       // 文件上传
        $("#file-input").tpUpload({
            url: '{{url("wfupsave")}}',
            data: {a: 'a'},
            drag: '',
            start: function () {
                layer_msg = layer.msg('正在上传中…', {time: 100000000});
            },
            progress: function (loaded, total, file) {
                $('.layui-layer-msg .layui-layer-content').html('已上传' + (loaded / total * 100).toFixed(2) + '%');
            },
            success: function (ret) {
                callback(callbackId,ret.msg[0],ret.data);
            },
            error: function (ret) {
                layer.alert(ret);
            },
            end: function () {
                layer.close(layer_msg);
            }
        });

    });

    /**
     * 数据回调
     * @param id
     * @param value
     */
    function callback(id,value,name) {
        if (window.parent.frames.length == 0){
            layer.alert('请在弹层中打开此页');
        } else {
            parent.document.getElementById(id).value = value;
			parent.$("#s"+id).remove();
			var data = '<br/><b id="s'+id+'">'+name+'</b>';
			parent.$('#b'+id).after(data);
			parent.$('#b'+id).html('上传成功！');
			parent.$('#b'+id).removeAttr('onclick');
            layer_close();
        }
    }

</script>