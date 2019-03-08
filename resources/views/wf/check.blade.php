@include('pub.base')
<link rel="stylesheet" href="/static/lib/multiple-select/multiple-select.css" />
<div class="page-container" style='width:98%'>
<form action="{{url('/wf/do_check_save')}}" method="post" name="form" id="forms">
<input type="hidden" value="{{$info->wf_title}}" name="wf_title">
<input type="hidden" value="{{$info->wf_fid}}" name="wf_fid">
<input type="hidden" value="{{$info->wf_type}}" name="wf_type">
<input type="hidden" value="{{$flowinfo->flow_id}}" name="flow_id">
<input type="hidden" value="{{$flowinfo->flow_process}}" name="flow_process">
<input type="hidden" value="{{$flowinfo->run_id}}" name="run_id" id='run_id'>
<input type="hidden" value="{{$flowinfo->run_process}}" name="run_process">
@if($flowinfo->status->wf_mode != 2)
<input type="hidden" value="{{$flowinfo->nexprocess->id}}" name="npid">
@else
<input type="hidden" value="{{$flowinfo->process->process_to}}" name="npid">
@endif
<input type="hidden" value="{{$flowinfo->wf_mode}}" name="wf_mode">

<input type="hidden" value="{{$_GET['sup'] ?? ''}}" name="sup">
		<table class="table table-border table-bordered table-bg" style='width:98%'>
			<thead>
			<tr>
			<th style='width:38%' class='text-c'>单据审批</th>
			<th style='width:59%' class='text-c'>审批记录</th>
			</tr>
			<tr>
			</thead>
			<td style='height:80px'>
				<table class="table table-border table-bordered table-bg">
				<tr>
				<th style='width:30px'>
                @if($flowinfo->sing_st == 0) 审批 @else 会签 @endif 意见 </th>
				<th><textarea name='check_con'  datatype="*" style="width:100%;height:55px;"></textarea> </th>
				</tr>
				<tr id='nex_process'>
				<th style='width:30px' >下一步骤</th>
				<th>
				@if($flowinfo->wf_mode == 2)[同步]@endif
				
				@if($flowinfo->status->wf_mode != 2)
					@if($flowinfo->nexprocess->auto_person != 3)
						{{$flowinfo->nexprocess->process_name}}({{$flowinfo->nexprocess->todo}})
					@else
						<span class="select-box">
						<select name="todo" id='todo'  class="select"  datatype="*" >
						<option value="">请指定办理人员</option>
                        {volist name='$flowinfo.nexprocess.todo.ids' id='todo'}
                        @foreach ($flowinfo->nexprocess->todo->ids as $key=>$todo)                            
                            <option value="{{$todo}}*%*{{$flowinfo->nexprocess->todo->text[$key]}}">{{$flowinfo->nexprocess->todo->text[$key]}}</option>
                        @endforeach
						</select>
                    @endif
                @else
					<!--同步模式-->
                    {volist name='$flowinfo.nexprocess' id='v'}
                    @foreach ($flowinfo->nexprocess as $key=>$v)
						{{$v->process_name}}({{$v->todo}})<br/>
                    @endforeach
                @endif
				
				</th>
				</tr>
				<tr style='display:none' id='back_process'>
				<th style='width:30px'>回退步骤</th>
				<th>
					<span class="select-box">
					<select name="wf_backflow" id='backflow'  class="select"  datatype="*" onchange='find()'>
					<option value="">请选择回退步骤</option>
                    @foreach ($flowinfo->preprocess as $key=>$back)
    					<option value="{{$key}}">{{$back}}</option>
                    @endforeach
					</select>
					<input type="hidden" value="" name="btodo" id='btodo'>
				</span>
				</th>
				</tr>
				<tr style='display:none' id='sing_process'>
				<th style='width:30px'>会签步骤</th>
				<th>
					<span class="select-box">
					<select name="wf_singflow" id='singflow'  class="select"  datatype="*" >
					<option value="">请选择会签人</option>
                    @foreach ($flowinfo->singuser as $sing)
    					<option value="{{$sing->id}}">{{$sing->username}}</option>
                    @endforeach
					</select>
				
				</th>
				</tr>
				<tr>
				<td colspan=2 class='text-c'>
				<input id='submit_to_save' name='submit_to_save' value='' type='hidden'>
				<input id='upload' name='art' value='' type='hidden'>
				<input  name='sing_st' value='{{$flowinfo->sing_st}}' type='hidden'>
				@if($flowinfo->sing_st == 0)
					<a class="btn btn-primary radius" id='nexbton' onclick='tj("ok")' >提交</a> 
					@if($flowinfo->process['is_back'] == 2)
					<a class="btn btn-primary radius" id='backbton' onclick='tj("back")'value='back' >回退</a> 
					@endif
					@if($flowinfo->process['is_sing'] == 1)
					<a class="btn btn-primary radius" id='singbton' onclick='tj("sing")' value='sing' >会签</a>
					@endif
                    <a class="btn btn-primary radius" id='bupload'onclick="layer_show('上传','{{url('wfup', ['id' => 'upload'])}}','140','160')">附件</a> 
                @else
					<a class="btn btn-primary radius" id='nexbton' onclick='sing("sok")' >会签提交</a> 
					<a class="btn btn-primary radius" id='backbton' onclick='sing("sback")'value='back' >会签回退</a> 
                    <a class="btn btn-primary radius" id='singbton' onclick='sing("ssing")' value='sing' >再会签</a>
                @endif
				
				</td>
				</tr>
				</table>
			</td>
			<td valign="top" >
				<div style='width:98%;overflow-y:scroll; height:200px'>
				<table class="table table-border table-bordered " style='width:98%;'>
					<tr><td>审批人</td><td>审批意见</td><td>审批操作</td><td>审批时间</td></tr>
                    @foreach ($flowinfo->log as $k)
						<tr><td>{{$k->user}}</td><td>{{$k->content}}
						@if($k->art != "")<a class="btn btn-success" href="/uploads/{{$k->art}}" target="download">下载</a>@endif
						</td><td>{{$k->btn}}</td><td>{{$k->created_at}}</td></tr>
                    @endforeach
				</table>
				</div>
			</td>
			</tr>
		</table>
</form>		
		<table class="table table-border table-bordered mt-20" style='width:98%'>
		<tr><td>
		
		<iframe src="{{url($info->wf_type.'/'.$flowinfo->status->wf_action,['id'=>$info->wf_fid])}}" id="iframepage" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" onLoad="iFrameHeight()"></iframe>
		
		</td></tr>
		</table>
	
</div>

<script type="text/javascript">
$(function(){
	$("#forms").Validform({
            tiptype:2,
            ajaxPost:true,
            showAllError:true,
            callback:function(ret){
                ajax_progress(ret);
            }
        });
});
function tj(value){
	if(value =='back'){
		$('#nex_process').hide();//
		$('#nexbton').hide();
		$('#singbton').hide();
		$('#backbton').html('确认回退');
		$('#back_process').show();
		var select = $('#backflow option:selected').val();
		$("#singflow").removeAttr("datatype");
		if(select==''){
			layer.msg('请选择回退步骤');
			return false;
		}
	}
	if(value =='sing'){
		$('#nex_process').hide();//
		$('#nexbton').hide();
		$('#backbton').hide();
		$('#backbton').html('确认会签');
		$('#sing_process').show();
		$("#backflow").removeAttr("datatype");
		var select = $('#singflow option:selected').val();
		if(select==''){
			layer.msg('请选择会签人');
			return false;
		}
	}
	if(value =='ok'){
		$("#backflow").removeAttr("datatype");
		$("#singflow").removeAttr("datatype");
	}
	$('#submit_to_save').val(value);
	$('#forms').submit();
}
function sing(value){
	if(value =='sback'){
		$('#nex_process').hide();//
		$('#nexbton').hide();
		$('#singbton').hide();
		$('#backbton').html('确认回退');
		$('#back_process').show();
		var select = $('#backflow option:selected').val();
		$("#singflow").removeAttr("datatype");
		if(select==''){
			layer.msg('请选择回退步骤');
			return false;
		}
	}
	if(value =='ssing'){
		$('#nex_process').hide();//
		$('#nexbton').hide();
		$('#backbton').hide();
		$('#backbton').html('确认会签');
		$('#sing_process').show();
		$("#backflow").removeAttr("datatype");
		var select = $('#singflow option:selected').val();
		if(select==''){
			layer.msg('请选择会签人');
			return false;
		}
	}
	if(value =='sok'){
		$("#backflow").removeAttr("datatype");
		$("#singflow").removeAttr("datatype");
	}
	$('#submit_to_save').val(value);
	$('#forms').submit();
} 
function iFrameHeight() {   
		var ifm= document.getElementById("iframepage");   
		var subWeb = document.frames ? document.frames["iframepage"].document : ifm.contentDocument;   
		if(ifm != null && subWeb != null) {
		   ifm.height = subWeb.body.scrollHeight;
		   ifm.width = '100%';
		}   
} 
function find(){
	$.post("{{url('ajax_back')}}",{"back_id":$('#backflow').val(),"run_id":$('#run_id').val()},function(data){
				if(data != ''){
					$('#btodo').val(data);
				}
				
			},'json');
}
</script>
<!--/请在上方写此页面业务相关的脚本-->
</body>
</html>