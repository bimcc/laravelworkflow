@include('pub.base')
<body onload="prettyPrint()">
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 欢迎使用，Tpflow 工作流插件示例</nav>
<div class="page-container">
<span class="btn btn-success radius" data-title="官方博客" data-href="http://www.cojz8.com" onclick="Hui_admin_tab(this)">官方博客</span>
<span class="btn btn-success radius" data-title="开发文档" data-href="{{url('doc')}}" onclick="Hui_admin_tab(this)">在线开发文档（精简）</span>
<span class="btn btn-success radius" data-title="完整文档" data-href="https://www.kancloud.cn/guowenbin/tpflow/" onclick="Hui_admin_tab(this)">看云完整文档</span>
<span class="btn btn-success radius" data-title="源码下载" data-href="https://gitee.com/ntdgg/tpflow/" onclick="Hui_admin_tab(this)">源码下载</span>
<br/>


<h4>模拟登入(点击姓名，进行模拟登入)：
<mark>
@if(session('uid',''))
欢迎您：{{session('uname')}} 使用本插件！
@else
    请先模拟登入！
@endif
</mark></h4>
@foreach ($user as $k)
    <a href="/index/login?id={{$k->id}}&user={{$k->username}}&role={{$k->role}}" class="btn btn-primary radius">{{$k->username}}</a>
@endforeach

</body>
</html>
<script type="text/javascript" src="http://cdn.bootcss.com/prettify/r298/prettify.min.js"></script>