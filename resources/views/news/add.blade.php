@include('pub.base')
  <link rel="stylesheet" href="/static/lib/multiple-select/multiple-select.css" />
<article class="page-container">
		@if(isset($info->id))
			<form action="{{url('/news/edit',['id'=>$info->id])}}" method="post" name="form" id="form">
			<input type="hidden" name="id" value="{{$info->id}}">
			<input type="hidden" name="status" value="0">
        @else
			<form action="{{url('/news/add')}}" method="post" name="form" id="form">
        @endif
		<table class="table table-border table-bordered table-bg">
			<tr>
			<td style='width:75px'>新闻标题：</td><td style='width:330px'><input type="text" class="input-text" value="{{$info->new_title ?? ''}}" name="new_title"  datatype="*" ></td>
			<td style='width:75px'>是否置顶：</td><td>
					<div class="skin-minimal">
						<div class="radio-box" >
						<input type="radio" checked id="radio-0" name="new_top"  value='1'>
						<label for="radio-0">是</label>
					  </div>
					<div class="radio-box" >
						<input type="radio"  id="radio-1" name="new_top"  value='0'>
						<label for="radio-1">否</label>
					  </div>
					</div>
			</td>
			</tr>
			<tr>
			<td>新闻类别：</td><td>
			<span class="select-box">
				<select name="new_type"  class="select"  datatype="*" >
						@foreach ($type as $k)                        
								<option value="{{$k['id']}}">{{$k['type']}}</option>
						@endforeach
				</select>
				</span>
			</td>
			<td></td><td>
			
			</td>
			</tr><tr><td>新闻内容</td><td colspan="3" ><textarea name='new_con'  datatype="*"  id="editor" type="text/plain" style="width:100%;height:300px;">
			{{$info->new_con ?? ''}}
			</textarea> </td></tr>
		</table>
		
		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
				<button  class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 保存</button>
				<button  class="btn btn-default radius" type="button" onclick="layer_close()">&nbsp;&nbsp;取消&nbsp;&nbsp;</button>
			</div>
		</div>
	</form>
</article>


<script type="text/javascript" src="/static/lib/ueditor/1.4.3/ueditor.config.js"></script> 
<script type="text/javascript" src="/static/lib/ueditor/1.4.3/ueditor.all.min.js"> </script> 
<script type="text/javascript" src="/static/lib/ueditor/1.4.3/lang/zh-cn/zh-cn.js"></script>
<script type="text/javascript">
$(function(){
	$("[name='new_top'][value='{{$info->new_top ?? ''}}']").attr("checked",true);
	$("[name='new_type']").find("[value='{{$info->new_type ?? '0'}}']").attr("selected",true);
	$('.skin-minimal input').iCheck({
		checkboxClass: 'icheckbox-blue',
		radioClass: 'iradio-blue',
		increaseArea: '20%'
	});
	
	$("#form").Validform({
            tiptype:2,
            ajaxPost:true,
            showAllError:true,
            callback:function(ret){
                ajax_progress(ret);
            }
        });
	var ue = UE.getEditor('editor');
});
</script>
<!--/请在上方写此页面业务相关的脚本-->
</body>
</html>