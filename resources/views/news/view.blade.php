@include('pub.base')
<article class="page-container">
		<table class="table table-border table-bordered table-bg">
			<tr>
			<td style='width:70px'>新闻标题：</td><td style='width:330px' colspan="3">
			{{$info->new_title}}</td>
			</tr>
			<tr>
			<td>新闻类别：</td><td>{{$info->new_type}}</td>
			<td></td><td></td>
			</tr><td>新闻内容:</td><td colspan="3" >{{$info->new_con}}
			</td></tr>
		</table>
</article>
</body>
</html>