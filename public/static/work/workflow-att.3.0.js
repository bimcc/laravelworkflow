
//-----条件设置--strat----------------
    function _id(id) {
        return !id ? null : document.getElementById(id);
    }
    function trim(str) {
        return (str + '').replace(/(\s+)$/g, '').replace(/^\s+/g, '');
    }

    function fnCheckExp(text){
        //检查公式
        if( text.indexOf("(")>=0 ){
            var num1 = text.split("(").length;
            var num2 = text.split(")").length;
            if( num1!=num2 ) {
                return false;
            }
        }
        return true;
    }
    /**
     * 增加左括号表达式，会断行
     */
    function fnAddLeftParenthesis(id){
        var oObj = _id('conList_' + id);
        var current = 0;
        if(oObj.options.length>0){ //检查是否有条件
            for ( var i = 0;i < oObj.options.length;i++ ){
                if( oObj.options[i].selected ) {
                    current = oObj.selectedIndex;
                    break;
                }
            }
            if(current==0){
                current = oObj.options.length-1;
            }
        } else { //有条件才能添加左括号表达式
            alert("请先添加条件，再选择括号");
            return;
        }
        var sText = oObj.options[current].text,sValue = oObj.options[current].value;
        //已经有条件的话
        if( (trim(sValue).substr(-3,3) == 'AND') || (trim(sValue).substr(-2,2) == 'OR') ){
            alert("无法编辑已经存在关系的条件");
            return;
        }
        var sRelation = _id('relation_'+id).value;
        if( sValue.indexOf('(')>=0 ){
            if( !fnCheckExp(sValue) ){
                alert("条件表达式书写错误,请检查括号匹配");
                return;
            } else {
                sValue = sValue + " " + sRelation;
                sText = sText + " " + sRelation;
            }
        } else {
            sValue = sValue + " " + sRelation;
            sText = sText + " " + sRelation;
        }
        oObj.options[current].value = sValue;
        oObj.options[current].text = sText;
       // $('#conList_'+id+' option').eq(current).text(sText)
       $('#conList_'+id).append('<option value="( ">( </option>');

    }
    /**
     * 增加右括号表达式
     */
    function fnAddRightParenthesis(id){
        var oObj = _id('conList_' + id);
        var current = 0;
        if( oObj.options.length>0 ){
            for ( var i = 0;i < oObj.options.length;i++ ){
                if( oObj.options[i].selected ) {
                    current = oObj.selectedIndex;
                    break;
                }
            }
            if( current == 0 ){
                current = oObj.options.length-1;
            }
        } else {
            alert("请先添加条件，再选择括号");
            return;
        }
        var sText = oObj.options[current].text,sValue = oObj.options[current].value;
        if( (trim(sValue).substr(-3,3)=='AND') || (trim(sValue).substr(-2,2)=='OR') ){
            alert("无法编辑已经存在关系的条件");
            return;
        }
        if( (trim(sValue).length==1) ){
            alert("请添加条件");
            return;
        }
        if( !fnCheckExp(sValue) ){
            sValue = sValue + ")";
            sText = sText + ")";
        }
        oObj.options[current].value = sValue;
        oObj.options[current].text = sText;

    }
    function fnAddConditions(id){
        var sField = $('#field_'+id).val(),sField_text = $('#field_'+id).find('option:selected').text(),sCon = $('#condition_'+id).val(),sValue = $('#item_value_'+id).val();

        var bAdd = true;
        if( sField!=='' && sCon!=='' && sValue!=='' ){
            var oObj = _id('conList_'+id);

            if( oObj.length>0 ){
                var sLength = oObj.options.length;
                var sText = oObj.options[sLength-1].text;
                if(!fnCheckExp(sText)){
                    bAdd = false;
                }
            }
            if( sValue.indexOf("'")>=0 ){
                alert("值中不能含有'号");
                return;
            }
            var sNewText = "" + sField + "" + sCon + " '" + sValue + "'";
            var sNewText_text = "" + sField + "" + sCon + " '" + sValue + "'";
            for( var i=0;i<oObj.options.length;i++ ){
                if( oObj.options[i].value.indexOf(sNewText)>=0 ){
                    alert("条件重复");
                    return;
                }
            }
            var sRelation = $('#relation_'+id).val();
            if( bAdd ){
                var nPos = oObj.options.length;
                $('#conList_'+id).append('<option value="'+sNewText+'">'+sNewText_text+'</option>');
                if( nPos>0 ){
                    oObj.options[nPos-1].text += "  " + sRelation;
                    oObj.options[nPos-1].value += "  " + sRelation;
                }
            } else {

                if( trim(oObj.options[sLength-1].text).length==1 ){
                    oObj.options[sLength-1].text += sNewText_text;
                    oObj.options[sLength-1].value += sNewText;
                } else {
                    oObj.options[sLength-1].text += " " + sRelation + " " + sNewText_text;
                    oObj.options[sLength-1].value += " " + sRelation + " " + sNewText;
                }
            }
			check_from();
        } else {
            alert("请补充完整条件");
            return;
        }
    }
	 
	function check_from(){
      //条件检测
      var cond_data  = $("#process_condition").val();
      if( cond_data !== ''){
          var pcarr = cond_data.split(',');
          for( var i = 0;i < pcarr.length;i++ ){
              if( pcarr[i]!=='' ){
                  var obj = _id('conList_'+pcarr[i]);
                  if(obj.length>0){
                      var constr = '';
                      for( var j=0;j<obj.options.length;j++){
                          constr += obj.options[j].value+'@wf@';
                          if(!fnCheckExp(constr)){
                              alert("条件表达式书写错误,请检查括号匹配");
                              $('#condition').click();
                              return false;
                          }
                      }
                      _id('process_in_set_'+pcarr[i]).value = constr;
                  } else {
                      _id('process_in_set_'+pcarr[i]).value = '';
                  }
              }
          }
      }

  };
    function fnDelCon(id){
        var oObj = _id('conList_'+id);
        var maxOpt = oObj.options.length;
        if(maxOpt<0) maxOpt = 0;

        for (var i = 0;i < oObj.options.length;i++ ){
            if( oObj.options[i].selected ) {
                if((i+1)==maxOpt){
                    if(typeof oObj.options[i-1] !== 'undefined'){
                        oObj.options[i-1].text = oObj.options[i-1].text.replace(/(AND|OR)$/,'');
                        oObj.options[i-1].value = oObj.options[i-1].value.replace(/(AND|OR)$/,'');
                    }
                }
                oObj.removeChild(oObj.options[i]);
                i--;
            }
        }
    }
    function fnClearCon(id){
        $('#conList_' + id).html('');
    }

$(function(){
  //选人方式
  $("#auto_person_id").on('change',function(){
      var apid = $(this).val();
	  if(apid==3)//指定用户
      {
          $("#auto_person_3").show();
      }else{
          $("#auto_person_3").hide();
      }
      if(apid==4)//指定用户
      {
          $("#auto_person_4").show();
      }else{
          $("#auto_person_4").hide();
      }
      if(apid==5)//指定角色
      {
          $("#auto_person_5").show();
      }else{
          $("#auto_person_5").hide();
      }
  });
    $("#wf_mode_id").on('change',function(){
      var apid = $(this).val();
	  if(apid==0)//单一转出模式
      {
          $("#wf_mode_2").hide();
      }
      if(apid==2)//转出模式
      {
         $("#wf_mode_2").hide();
      }
      if(apid==1)//同步模式
      {
          $("#wf_mode_2").show();
      }else{
          $("#wf_mode_2").hide();
      }
  });
  

  /*样式*/
  $('.colors li').click(function() {
      var self = $(this);
      if (!self.hasClass('active')) {
        self.siblings().removeClass('active');
      }
      var color = self.attr('org-data') ? self.attr('org-data') : '';
      var parentDiv = self.parents(".colors");
      var orgBind = parentDiv.attr("org-bind");
      $("#"+orgBind).css({ color:'#fff',background: color });
      $("#"+orgBind).val(color);
      self.addClass('active');
  });
});