/*
项目：workflow.3.0
*/
(function($) {
   var defaults = {
      processData:{},
      fnRepeat:function(){
        alert("步骤连接重复");
      },
      fnClick:function(){
        alert("单击");
      },
      fnDbClick:function(){
        alert("双击");
      },
      itemStyle: {
        fontFamily : 'verdana',
        color: '#333',
        border: '0',
        padding:'5px 40px 5px 20px'
      },
      itemHoverStyle: {
        border: '0',
        color: '#fff',
        backgroundColor: '#5a6377'
      },
      mtAfterDrop:function(params){
          //alert('连接成功后调用');
          //alert("连接："+params.sourceId +" -> "+ params.targetId);
      },
      //这是连接线路的绘画样式
      connectorPaintStyle : {
          lineWidth:3,
          strokeStyle:"#49afcd",
          joinstyle:"round"
      },
      //鼠标经过样式
      connectorHoverStyle : {
          lineWidth:3,
          strokeStyle:"#da4f49"
      }

   };/*defaults end*/
   var initEndPoints = function(){
      $(".process-flag").each(function(i,e) {
          var p = $(e).parent();
          jsPlumb.makeSource($(e), {
              parent:p,
              anchor:"Continuous",
              endpoint:[ "Dot", { radius:1 } ],
              connector:[ "Flowchart", { stub:[5, 5] } ],
              connectorStyle:defaults.connectorPaintStyle,
              hoverPaintStyle:defaults.connectorHoverStyle,
              dragOptions:{},
              maxConnections:-1
          });
      });
  }
  /*设置隐藏域保存关系信息*/
  var aConnections = [];
  var setConnections = function(conn, remove) {
      if (!remove) aConnections.push(conn);
      else {
          var idx = -1;
          for (var i = 0; i < aConnections.length; i++) {
              if (aConnections[i] == conn) {
                  idx = i; break;
              }
          }
          if (idx != -1) aConnections.splice(idx, 1);
      }
      if (aConnections.length > 0) {
          var s = "";
          for ( var j = 0; j < aConnections.length; j++ ) {
              var from = $('#'+aConnections[j].sourceId).attr('process_id');
              var target = $('#'+aConnections[j].targetId).attr('process_id');
              s = s + "<input type='hidden' value=\"" + from + "," + target + "\">";
          }
          $('#wf_process_info').html(s);
      } else {
          $('#wf_process_info').html('');
      }
      jsPlumb.repaintEverything();//重画
  };

   /*Flowdesign 命名纯粹为了美观，而不是 formDesign */
   $.fn.Flowdesign = function(options)
    {
        var _canvas = $(this);
        //右键步骤的步骤号
        _canvas.append('<input type="hidden" id="wf_active_id" value="0"/><input type="hidden" id="wf_copy_id" value="0"/>');
        _canvas.append('<div id="wf_process_info"></div>');

        /*配置*/
        $.each(options, function(i, val) {
          if (typeof val == 'object' && defaults[i])
            $.extend(defaults[i], val);
          else 
            defaults[i] = val;
        });
        /*画布右键绑定*/
        jsPlumb.importDefaults({
            DragOptions : { cursor: 'pointer'},
            EndpointStyle : { fillStyle:'#225588' },
            Endpoint : [ "Dot", {radius:1} ],
            ConnectionOverlays : [
                [ "Arrow", { location:1 } ],
                [ "Label", {
                        location:0.1,
                        id:"label",
                        cssClass:"aLabel"
                    }]
            ],
            Anchor : 'Continuous',
            ConnectorZIndex:5,
            HoverPaintStyle:defaults.connectorHoverStyle
        });
        if( $.browser.msie && $.browser.version < '9.0' ){ //ie9以下，用VML画图
            jsPlumb.setRenderMode(jsPlumb.VML);
        } else { //其他浏览器用SVG
            jsPlumb.setRenderMode(jsPlumb.SVG);
        }
    //初始化原步骤
    var lastProcessId=0;
    var processData = defaults.processData;
    if(processData.list){
        $.each(processData.list, function(i,row) {
            var nodeDiv = document.createElement('div');
			 var nodeId = "window" + row.id;
            $(nodeDiv).attr("id",nodeId)
            .attr("style",row.style)
            .attr("process_to",row.process_to)
            .attr("process_id",row.id)
            .addClass("process-step wf_btn")
            .html('<span class="process-flag"><img src="/static/work/process.png"></span>&nbsp;' +row.process_name )
            .mousedown(function(e){
              if( e.which == 3 ) { //右键绑定
                  _canvas.find('#wf_active_id').val(row.id);
              }
            });
            _canvas.append(nodeDiv);
            lastProcessId = row.id;
        });
    }
    var timeout = null;
    //点击或双击事件,这里进行了一个单击事件延迟，因为同时绑定了双击事件
    $(".process-step").live('click',function(){
        _canvas.find('#wf_active_id').val($(this).attr("process_id")),
        clearTimeout(timeout);
        var obj = this;
        timeout = setTimeout(defaults.fnClick,300);
    }).live('dblclick',function(){
        clearTimeout(timeout);
        defaults.fnDbClick();
    });
    jsPlumb.draggable(jsPlumb.getSelector(".process-step"),{containment: 'parent'});//绑定元素可以拖动，但是只能在容器内拖动
    initEndPoints();
    //绑定添加连接操作。画线-input text值  拒绝重复连接
    jsPlumb.bind("jsPlumbConnection", function(info) {
        setConnections(info.connection)
    });
    //绑定删除connection事件
    jsPlumb.bind("jsPlumbConnectionDetached", function(info) {
        setConnections(info.connection, true);
    });
    //绑定删除确认操作
    jsPlumb.bind("click", function(c) {
      if(confirm("你确定取消连接吗?"))
        jsPlumb.detach(c);
    });
    //连接成功回调函数
    function mtAfterDrop(params){
        defaults.mtAfterDrop({sourceId:$("#"+params.sourceId).attr('process_id'),targetId:$("#"+params.targetId).attr('process_id')});
    }
    jsPlumb.makeTarget(jsPlumb.getSelector(".process-step"), {
        dropOptions:{ hoverClass:"hover", activeClass:"active" },
        anchor:"Continuous",
        maxConnections:-1,
        endpoint:[ "Dot", { radius:1 } ],
        paintStyle:{ fillStyle:"#ec912a",radius:1 },
        hoverPaintStyle:this.connectorHoverStyle,
        beforeDrop:function(params){
            if(params.sourceId == params.targetId) return false;/*不能链接自己*/
            var j = 0;
            $('#wf_process_info').find('input').each(function(i){
                var str = $('#' + params.sourceId).attr('process_id') + ',' + $('#' + params.targetId).attr('process_id');
                if(str == $(this).val()){
                    j++;
                    return;
                }
            })
            if( j > 0 ){
                defaults.fnRepeat();
                return false;
            } else {
                mtAfterDrop(params);
                return true;
            }
        }
    });
    //reset  start
    var _canvas_design = function(){
        $('.process-step').each(function(i){
            var sourceId = $(this).attr('process_id');
            var prcsto = $(this).attr('process_to');
            var toArr = prcsto.split(",");
            var processData = defaults.processData;
            $.each(toArr,function(j,targetId){
                if(targetId!='' && targetId!=0){
                    //检查 source 和 target是否存在
                    var is_source = false,is_target = false;
                    $.each(processData.list, function(i,row){
                        if(row.id == sourceId){
                            is_source = true;
                        }else if(row.id == targetId){
                            is_target = true;
                        }
                        if(is_source && is_target)
                            return true;
                    });
                    if(is_source && is_target){
                        jsPlumb.connect({
                            source:"window"+sourceId, 
                            target:"window"+targetId,
							overlays: [["Label", {cssClass: "component label",label: sourceId+" - "+targetId,}],"Arrow"]
                        });
                        return ;
                    }
                }
            })
        });
    }
    _canvas_design();
//-----外部调用----------------------
    var Flowdesign = {
        delProcess:function(activeId){ //删除步骤
            if(activeId<=0) return false;
            $("#window"+activeId).remove();
            return true;
        },
        getActiveId:function(){ //获取当前激活的编号
          return _canvas.find("#wf_active_id").val();
        },
        getProcessInfo:function(){ //获取步骤信息
            try{
              var aProcessData = {};
              $("#wf_process_info input[type=hidden]").each(function(i){
                  var processVal = $(this).val().split(",");
                  if(processVal.length==2)
                  {
                    if(!aProcessData[processVal[0]])
                    {
                        aProcessData[processVal[0]] = {"top":0,"left":0,"process_to":[]};
                    }
                    aProcessData[processVal[0]]["process_to"].push(processVal[1]);
                  }
              })
              _canvas.find("div.process-step").each(function(i){ //生成Json字符串，发送到服务器解析
                      if($(this).attr('id')){
                          var pId = $(this).attr('process_id');
                          var pLeft = parseInt($(this).css('left'));
                          var pTop = parseInt($(this).css('top'));
                         if(!aProcessData[pId])
                          {
                              aProcessData[pId] = {"top":0,"left":0,"process_to":[]};
                          }
                          aProcessData[pId]["top"] =pTop;
                          aProcessData[pId]["left"] =pLeft;
                      }
                  })
             return JSON.stringify(aProcessData);
          }catch(e){
              return '';
          }
        }
    };
    return Flowdesign;
  }
})(jQuery);