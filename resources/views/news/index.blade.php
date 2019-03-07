@include('pub.base')
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i>  Tpflow 工作流插件示例 
<a href="{{url('index/welcome')}}"  class="btn btn-primary radius"> 返回</a>

<a href="javascript:;" onclick="layer_show('新增新闻','{{url('/news/add')}}','850','500')" class="btn btn-primary radius">
	<i class="Hui-iconfont">&#xe600;</i> 新增新闻</a></nav>
<div class="page-container">
	<table class="table table-border table-bordered table-bg">
		<thead>
			<tr class="text-c">
				<th width="25">ID</th>
				<th width="50">发布人</th>
				<th width="80">新闻类型</th>
				<th width="150">新闻标题</th>
				<th width="150">发布时间</th>
				<th width="150">状态</th>
				<th width="100">操作</th>
			</tr>
		</thead>
		<tbody>
            @foreach ($list as $k)
			<tr class="text-c">
				<td>{{$k->id}}</td>
				<td>{{$k->uid}}</td>
				<td>{{$k->new_type}}</td>
				<td>@if($k->new_top == 1)
				<i class="Hui-iconfont" style='color:red'>&#xe684;</i>
				@endif{{$k->new_title}}</td>
				<td>{{$k->created_at}}</td>
				<td>
				<!--获取流状态-->
				{!! \App\Helper::status($k->status) !!}
				</td>
				<td class="td-manage">
				<div class="btn-group">
					<span class="btn  radius size-S" data-title="查看" data-href="{{url('/news/view',['id'=>$k->id])}}" onclick="Hui_admin_tab(this)"><i class="Hui-iconfont">查看</span>
						<!--按钮权限验证，审批权限验证，发起验证-->
						{!! \App\Helper::btn($k->id,'news',$k->status) !!}
					<span class="btn  radius size-S" onclick="layer_show('修改','{{url('/news/edit',['id'=>$k->id])}}','850','500')">修改</span>
				</div>
				</td>
			</tr>
            @endforeach
		</tbody>
	</table>
</div>
<div class="page-bootstrap">{{$list}}</div>
</body>
</html>