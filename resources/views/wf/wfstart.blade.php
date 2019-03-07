@include('pub.base')
<link rel="stylesheet" href="/static/lib/multiple-select/multiple-select.css" />
<article class="page-container">
		<form action="{{url('/wf/start_save')}}" method="post" name="form" id="form">
		<input type='hidden' value='{{$info['wf_type']}}' name='wf_type'>
		<input type='hidden' value='{{$info['wf_fid']}}' name='wf_fid'>
		<table class="table table-border table-bordered table-bg">
			<tr>
			<td style='width:75px'>项目名称：</td>
			<td style='width:330px'>{{$info['wf_title']}}</td>
			</tr>
			<tr>
			<td>选择工作流：</td><td>
			<span class="select-box">
				<select name="wf_id"  class="select"  datatype="*" >
					<option value="0">请选择工作流</option>
                    @foreach ($flow as $k)
                        <option value="{{$k->id}}">{{$k->flow_name}}</option>
                    @endforeach
				</select>
				</span>
			</td>
			</tr>
			
			<tr>
			<td>紧急程度：</td><td>
			<span class="select-box">
				<select name="new_type"  class="select"  datatype="*" >
					<option value="0">普通</option>
					<option value="1">加急</option>
					<option value="2">紧急</option>
					<option value="3">特急</option>
				</select>
				</span>
			</td>
			</tr>
			<tr>
			<td>审核意见：</td><td>
				<input type="text" class="input-text" value="{{$info->new_title ?? ''}}" name="check_con"  datatype="*" >
			</td>
			</tr>
			<tr>
			<td colspan='2' class='text-c'>
			
			<button  class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 保存</button>
				<button  class="btn btn-default radius" type="button" onclick="layer_close()">&nbsp;&nbsp;取消&nbsp;&nbsp;</button></td>
			</tr>
		</table>
		
		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
				
			</div>
		</div>
	</form>
</article>


<script type="text/javascript">
$(function(){
	
	$("#form").Validform({
            tiptype:2,
            ajaxPost:true,
            showAllError:true,
            callback:function(ret){
                ajax_progress(ret);
            }
        });

});
</script>
<!--/请在上方写此页面业务相关的脚本-->
</body>
</html>