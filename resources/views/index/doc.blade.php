@include('pub.base')
<body onload="prettyPrint()">
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 欢迎使用，Tpflow 工作流插件示例</nav>


<div class="page-container">
<article class="f-14 l-30 mt-20">
			<p>致亲爱的Tpflow用户：</p>
			<p style="text-indent:2em">首先感谢你们一路的支持，Tpflow上线以来收货颇多，这也是开源平台第一个工作流开源项目，我们希望做得更好。我们深知这非常不容易，目前团队只有一个人开发，希望有志之士的加入！</p>
			<p style="text-indent:2em">希望各路大神，一起完善，改进这个开源的工作流。不管是公司，还是个人，UI前端，还是后端，若有建议，不妨提出。开源精神，与君共勉！</p>
			
			<p class="text-r">蝈蝈<br>2018.07.19</p>
		</article>
<h3>API 文档说明（此文档在看云的基础上进行精简，更适合快速开发）</h3>

<h3 style='color:red'>本插件基于Thinkphp 5.1开发，请关注官方手册！</h3>

<h4>数据库开发说明</h4>
<pre class="prettyprint linenums">
flow         流程主表                 //流程主表主要记录流程名称
flow_process 流程附表（详细步骤表）  //主要字段：process_to（下一步骤）  out_condition（转出条件，SQL） 
run          流程运行主表            //运行后主要记录这张表
run_process  流程运行步骤表          //运行步骤，关联运行主表
run_sign     流程运行会签步骤表
run_cache    流程运行缓存表
run_log      流程运行日志表
</pre>
<h4>文件说明</h4>
<pre class="prettyprint linenums">
Flow.php         \application\index\controller        //前端控制器，权限控制（验证按钮审核权限）
workflow.php     \extend\workflow\                    //工作流入口文件，核心驱动  
TaskService.php  \extend\workflow\class\command       //工作流服务文件，中间驱动（根据用户信息，选择对应的驱动服务）
</pre>
<h4>简单运行说明</h4>
<pre class="prettyprint linenums">
##第一步：工作流设计##
//详见 Flowdesign.php 

##第二步：表单填写##

##第三步：选择工作流——>发起流程##

$workflow = new workflow();
$flow = $workflow->getWorkFlow($wf_type); //获取本类工作流信息

$flow = $workflow->startworkflow($wf_id,$wf_fid,$wf_type); //直接发起工作流

##第四步：审核单据发起——>获取工作流信息，获取下一个工作流信息——>日志记录——>发起消息通知##
$workflow = new workflow();
$flowinfo = $workflow->workflowInfo($wf_fid,$wf_type); //工作流审核发起，获取当前及下一个审批流信息
$flowinfo = $workflow->workdoaction($config); //工作流审核发起保存
</pre>

<img src='/process.png'></img>

</div>

</body>
</html>
<script type="text/javascript" src="http://cdn.bootcss.com/prettify/r298/prettify.min.js"></script>