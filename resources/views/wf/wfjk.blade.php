@include('pub.base')
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i>  Tpflow 工作流插件示例 
<a href="{{url('index/welcome')}}"  class="btn btn-primary radius"> 返回</a>
</nav>
<div class="page-container">
	<table class="table table-border table-bordered table-bg">
		<thead>
			<tr class="text-c">
				<th>工作流编号</th>
				<th >工作流类型</th>
				<th >工作流名称</th>
				<th >当前状态</th>
				<th >业务办理人</th>
				<th >业务描述</th>
				<th >操作</th>
			</tr>
		</thead>
		<tbody>
            @foreach ($list as $k)
			<tr class="text-c">
				<td>{{$k->id}}</td>
				<td>{{$k->from_table}}</td>
				<td>{{$k->flow_name}}</td>
				<td>
				@if($k->status == 0)
                    未审核
                @else
                    已审核
                @endif
				</td>
				<td>{{$k->user}}</td>
				<td>{{$k->created_at}}</td>
				<td>
					<a onclick='end({{$k->id}})'>终止</a>  | {!! \App\Helper::btn($k->from_id,$k->from_table,100) !!}
				</td>
				
			</tr>
            @endforeach
		</tbody>
	</table>
</div>
<script>
function end(id){
		layer.confirm('你确定终止此流程？[此操作无法恢复]',function(index){
			$.ajax({
				type: 'POST',
				url: '{{url("/wf/wfend")}}?id='+id,
				dataType: 'json',
				success: function(data){
					layer.msg('操作成功!',{icon:1,time:1000});
					//setTimeout("location.reload()",1000);
				},
				error:function(data) {
					console.log(data.msg);
				},
			});		
		});
	}
</script>
</body>
</html>