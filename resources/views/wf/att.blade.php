@include('pub.base')
<link rel="stylesheet" type="text/css" href="/static/work/workflow.3.0.css"/>
<link rel="stylesheet" type="text/css" href="/static/work/multiselect2side.css"/>
<form  class="form-horizontal" action="{{url('/wf/save_attribute')}}" method="post" name="form" id="form">
<input type="hidden" name="flow_id" value="{{$one->flow_id}}"/>
<input type="hidden" name="process_id" value="{{$one->id}}"/>
<table class="tables">
<tr><td>步骤名称</td><td><input type="text" class="smalls" name="process_name" value="{{$one->process_name}}"></td><td>步骤尺寸</td><td>
<input type="text" class="smalls" name="style_width" value="{{$one->style['width']}}" style='width:60px'> X 
			<input type="text" class="smalls" name="style_height" value="{{$one->style['height']}}" style='width:60px'></td></tr>
<tr></tr>
<tr><td>字体颜色</td><td  colspan='3'><input type="text" class="smalls" name="style_color" id="style_color" placeholder="#000000" value="{{$one->style['color']}}">
            <div class="colors" org-bind="style_color">
                <ul>
                  <li class="Black active" org-data="#000" title="Black">1</li>
                  <li class="red" org-data="#d54e21" title="Red">2</li>
                  <li class="green" org-data="#78a300" title="Green">3</li>
                  <li class="blue" org-data="#0e76a8" title="Blue">4</li>
                  <li class="aero" org-data="#9cc2cb" title="Aero">5</li>
                  <li class="grey" org-data="#73716e" title="Grey">6</li>
                  <li class="orange" org-data="#f70" title="Orange">7</li>
                  <li class="yellow" org-data="#fc0" title="Yellow">8</li>
                  <li class="pink" org-data="#ff66b5" title="Pink">9</li>
                  <li class="purple" org-data="#6a5a8c" title="Purple">10</li>
                </ul>
            </div></td></tr>
			<tr><td>步骤类型</td><td>
				<input type="radio" name="process_type" value="is_step" @if($one->process_type == 'is_step') checked="checked" @endif>正常步骤
                <input type="radio" name="process_type" value="is_one" @if($one->process_type == 'is_one') checked="checked" @endif>第一步</td>
				<td>调用方法</td>
				<td>
					<input type="text" class="smalls" name="wf_action"  value="{{$one->wf_action ?? 'view'}}">
				</td>
				
				</tr>
				<tr><td>步骤模式</td><td  colspan='3'>
					<select name="wf_mode" id="wf_mode_id" datatype="*" nullmsg="请选择步骤模式">
					<option value="">请选择步骤模式</option>
					@if (count($one->process_to)>1)
					 <option value="1" @if($one->wf_mode == 1) selected="selected" @endif>转出模式（符合执行）</option>
                     <option value="2" @if($one->wf_mode == 2) selected="selected" @endif>同步模式（均需办理）</option>
                    @else
					 <option value="0" @if($one->wf_mode == 0) selected="selected" @endif>单线模式（流程为直线型单一办理模式）</option>
					@endif
				  </select>
				</td></tr>
			<tr><td>会签方式</td><td><select name="is_sing" >
              <option value="1" @if($one->is_sing == 1) selected="selected" @endif>允许会签</option>
              <option value="2" @if($one->is_sing == 2) selected="selected" @endif>禁止会签</option>
            </select></td>
			
			<td>回退方式</td><td><select name="is_back" >
              <option value="1" @if($one->is_back == 1) selected="selected" @endif>不允许</option>
              <option value="2" @if($one->is_back == 2) selected="selected" @endif>允许回退</option>
            </select></td>
			</tr>
			<tr><td>办理人员</td><td colspan='3'><select name="auto_person" id="auto_person_id" datatype="*" nullmsg="请选择办理人员或者角色！">
                <option value="">请选择办理人员或者角色</option>
				 @if($one->process_type != 'is_one')<option value="3" @if($one->auto_person == 3) selected="selected" @endif>自由选择</option>@endif
				 <option value="4" @if($one->auto_person == 4) selected="selected" @endif>指定人员</option>
                <option value="5" @if($one->auto_person == 5) selected="selected" @endif>指定角色</option>
              </select>
              <span class="help-inline">*选择人员或者办理的角色！</span>
			<div id="auto_person_3" @if($one->auto_person != 3) class="hide" @endif>
              办理人
                    <input type="hidden" name="range_user_ids" id="range_user_ids" value="{{$one->range_user_ids}}">
                    <input class="input-xlarge" readonly="readonly" type="text" placeholder="选择办理人范围" name="range_user_text" id="range_user_text" value="{{$one->range_user_text ?? ''}}"> 
					<a class="btn" onclick="layer_show('办理人','{{url('/wf/super_user',['kid'=>'range_user'])}}','350','500')">选择</a>
				
            </div>
            <div id="auto_person_4" @if($one->auto_person != 4) class="hide"@endif>
              办理人<input type="hidden" name="auto_sponsor_ids" id="auto_sponsor_ids" value="{{$one->auto_sponsor_ids}}">
                    <input class="input-xlarge" readonly="readonly" type="text" placeholder="指定办理人" name="auto_sponsor_text" id="auto_sponsor_text" value="{{$one->auto_sponsor_text ?? ''}}"> 
					<a class="btn" onclick="layer_show('办理人','{{url('/wf/super_user',['kid'=>'auto_sponsor'])}}','350','500')">选择</a>
            </div>
            <div id="auto_person_5" @if($one->auto_person != 5) class="hide" @endif>
              指定角色
                    <input type="hidden" name="auto_role_ids" id="auto_role_value" value="{{$one->auto_role_ids}}" >
                    <input class="input-xlarge" readonly="readonly" type="text" placeholder="指定角色" name="auto_role_text" id="auto_role_text" value="{{$one->auto_role_text ?? ''}}">
					<a class="btn" onclick="layer_show('办理人','{{url('/wf/super_role')}}','350','500')">选择</a>
               
            </div></td></tr>
<!--重新设计，带转出模式-->


<tr id='wf_mode_2' @if($one->wf_mode != 1) class="hide" @endif>
<td colspan=4>
<table class="table" >
      <thead>
        <tr>
          <th style="width:30px;">步骤</th>
          <th>转出条件设置</th>
        </tr>
      </thead>
      <tbody>

<!--模板-->
@foreach ($process_to_list as $item)
    @if(in_array($item->id,$one->process_to))
    <tr>    
        <td style="width: 30px;">{{$item->process_name}}{{$item->id}}</td>
        <td>
            <table class="table table-condensed">
            <tbody>
                <tr>
                <td>
                    <select id="field_{{$item->id}}" class="smalls">
                        <option value="">选择字段</option>
                        @foreach($from as $key=>$v)
                        <option value="{{$key}}">{{$v}}</option>
                        @endforeach
                    </select>
                    <select id="condition_{{$item->id}}" class="smalls" style="width: 60px;">
                        <option value="=">=</option>
                        <option value="&lt;&gt;"><></option>
                        <option value="&gt;">></option>
                        <option value="&lt;"><</option>
                        <option value="&gt;=">>=</option>
                        <option value="&lt;="><=</option>
                        <option value="include">含</option>
                        <option value="exclude">不含</option>
                    </select>
                    <input type="text" id="item_value_{{$item->id}}" class="smalls" style="width: 40px;">
                    <select id="relation_{{$item->id}}" class="smalls" style="width: 40px;"><option value="AND">AND</option><option value="OR">OR</option>
                    </select>
                </td>
                <td>
                    <button type="button" class="wf_btn" onclick="fnAddLeftParenthesis('{{$item->id}}')">（</button>
                    <button type="button" class="wf_btn" onclick="fnAddRightParenthesis('{{$item->id}}')">）</button>
                    <button type="button" onclick="fnAddConditions('{{$item->id}}')" class="wf_btn">新增</button>
                </td>
                </tr>
                <tr>
                <td>
                    <select id="conList_{{$k->id}}" multiple="" style="width: 100%;height: 80px;">
                    {{-- {$k.condition|raw} --}}
                    {{$item->contition}}
                    </select>
                </td>
                <td>
                    
                <button type="button" onclick="fnDelCon('{{$item->id}}')" class="wf_btn">删行</button>
                <button type="button" onclick="fnClearCon('{{$item->id}}')" class="wf_btn">清空</button>
                    <input name="process_in_set_{{$item->id}}" id="process_in_set_{{$item->id}}" type="hidden">
                </td>
                </tr>
                
            </tbody>
            </table>
        </td>
        </tr>
    @endif
@endforeach

  </tbody>
  
</table>
</td></tr>
</table>

    
<input type="hidden" name="process_condition" id="process_condition" value='{{$one->process_tos}}'>

<div>
  <hr/>
  <span class="pull-right">
      <a onclick="layer_close()" class="btn" >取消</a>
      <button  class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 保存</button>
  </span>
</div>
</form>
<script type="text/javascript" src="/static/work/jquery-1.7.2.min.js?"></script>

<script type="text/javascript" src="/static/work/jquery-ui-1.9.2-min.js?" ></script>
<!--select 2-->
<script type="text/javascript" src="/static/work/multiselect2side.js?" ></script>
<!--flowdesign-->
<script type="text/javascript" src="/static/work/workflow-att.3.0.js"></script>


<script type="text/javascript" src="/static/lib/Validform/5.3.2/Validform.min.js"></script>
<script type="text/javascript">
$(function(){
	$("#form").Validform({
            tiptype:1,
            ajaxPost:true,
            showAllError:true,
            callback:function(ret){
                ajax_progress(ret);
            }
        });
});
var wf_mode = "@isset($one->wf_mode){{$one->wf_mode}}@endisset";
if(wf_mode){
	check_from();
}
</script>
