@include('pub.base')
<article class="page-container">
        @if(isset($info->id))
			<form action="{{url('/wf/wfedit',['id'=>$id])}}" method="post" name="form" id="form">
			<input type="hidden" name="id" value="{{$info->id}}">
		@else
			<form action="{{url('/wf/wfadd')}}" method="post" name="form" id="form">
        @endif
		<table class="table table-border table-bordered table-bg">
			<tr>
			<td style='width:75px'>流程名称</td>
			<td style='width:330px'><input type="text" class="input-text" value="{{$info->flow_name ?? ''}}" name="flow_name"  datatype="*" ></td>
			
			</tr>
			<tr>
			<td>流程类型</td><td>
			<span class="select-box">
				<select name="type"  class="select"  datatype="*" >
                    @foreach ($type as $key=>$item)
    					<option value="{{$key}}">{{$item}}</option>
                    @endforeach
				</select>
				</span>
			</td>
			</tr>
			<tr>
			<td style='width:75px'>排序值</td>
			<td style='width:330px'><input type="text" class="input-text" value="{{$info->sort_order ?? ''}}" name="sort_order"  datatype="*" ></td>
			</tr>
			<tr>
			<tr>
			<td>流程描述</td><td>
			<textarea name='flow_desc'  datatype="*" style="width:100%;height:55px;">{{$info->flow_desc ?? ''}}</textarea></td>
			
			</tr>
			<tr class='text-c'>
			<td colspan=2>
			<button  class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 保存</button>
				<button  class="btn btn-default radius" type="button" onclick="layer_close()">&nbsp;&nbsp;取消&nbsp;&nbsp;</button></td>
			</tr>
			
		</table>
	</form>
</article>

<script type="text/javascript">
$(function(){
	$("[name='type']").find("[value='{{$info->type ?? '0'}}']").attr("selected",true);
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

</body>
</html>