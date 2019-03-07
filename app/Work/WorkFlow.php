<?php
/**
 *+------------------
 * Tpflow 工作流核心驱动类
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */

namespace App\Work;

use App\Work\Repositories\FlowRepo;
use App\Work\Repositories\InfoRepo;
use App\Work\Repositories\LogRepo;
use App\Work\Repositories\ProcessRepo;
use App\Work\Service\TaskService;
use App\Work\Repositories\UserRepo;


	
class workflow{
    /**
     * 根据业务类别获取工作流
     *
     * @param  $type 类别
     */
    public static function getWorkFlow($type)
    {
        return FlowRepo::getWorkflowByType($type);
    }
    /**
     *流程发起
        *
        * @param  $config 参数信息
        * @param  $uid    用户ID
        **/
    public static function startWorkflow($config,$uid)
    {
        $wf_id = $config['wf_id'];
        $wf_fid = $config['wf_fid'];
        $wf_type = $config['wf_type'];
        //判断流程是否存在
        $wf = FlowRepo::getWorkflow($wf_id);
        if(!$wf){
            return ['msg'=>'未找到工作流！','code'=>'-1'];
        }
        //判断单据是否存在
        $wf = InfoRepo::getBill($wf_fid,$wf_type);
        if(!$wf){
            return ['msg'=>'单据不存在！','code'=>'-1'];
        }
        
        //根据流程获取流程第一个步骤
        $wf_process = ProcessRepo::getWorkflowProcess($wf_id);
        if(!$wf_process){
            return ['msg'=>'流程设计出错，未找到第一步流程，请联系管理员！','code'=>'-1'];
        }
        //满足要求，发起流程
        $wf_run = InfoRepo::addWorkflowRun($wf_id,$wf_process['id'],$wf_fid,$wf_type,$uid);
        if(!$wf_run){
            return ['msg'=>'流程发起失败，数据库操作错误！！','code'=>'-1'];
        }
        //添加流程步骤日志
        $wf_process_log = InfoRepo::addWorkflowProcess($wf_id,$wf_process,$wf_run,$uid);
        if(!$wf_process_log){
            return ['msg'=>'流程步骤操作记录失败，数据库错误！！！','code'=>'-1'];
        }
        //添加流程日志
        $run_cache = InfoRepo::addWorkflowCache($wf_run,$wf,$wf_process,$wf_fid);
        if(!$run_cache){
            return ['msg'=>'流程步骤操作记录失败，数据库错误！！！','code'=>'-1'];
        }
        
        //更新单据状态
        $bill_update = InfoRepo::updateBill($wf_fid,$wf_type);
        if(!$bill_update){
            return ['msg'=>'流程步骤操作记录失败，数据库错误！！！','code'=>'-1'];
        }
        
        $run_log = LogRepo::addRunLog($uid,$wf_run,$config,'Send');
        
        return ['run_id'=>$wf_run,'msg'=>'success','code'=>'1'];
    }
    /**
         * 流程状态查询
        *
        * @$wf_fid 单据编号
        * @$wf_type 单据表 
        **/
    public static function workflowInfo($wf_fid,$wf_type,$userinfo=[])
    {
        if ($wf_fid == '' || $wf_type == '') {
            return ['msg'=>'单据编号，单据表不可为空！','code'=>'-1'];
        }
        return InfoRepo::workflowInfo($wf_fid,$wf_type,$userinfo);
    }
    /*
        * 获取下一步骤信息
        *
        * @param  $config 参数信息
        * @param  $uid 用户ID
        **/
    public static function workDoaction($config,$uid)
    {
        if( @$config['run_id']=='' || @$config['run_process']==''){
            throw new \Exception ( "config参数信息不全！" );
        }
        $wf_actionid = $config['submit_to_save'];
        $sing_st = $config['sing_st'];
        if($sing_st == 0){
            if ($wf_actionid == "ok") {//提交处理
                $ret = TaskService::doTask($config,$uid);
            } else if ($wf_actionid == "back") {//退回处理
                $ret = TaskService::doBack($config,$uid);
            } else if ($wf_actionid == "sing") {//会签
                $ret = TaskService::doSing($config,$uid);
            } else { //通过
                throw new \Exception ( "参数出错！" );
            }
        }else{
            $ret = TaskService::doSingEnt($config,$uid,$wf_actionid);
        }
        return $ret;
    }
    /*
        * 工作流监控
        *
        * @param  $status 流程状态
        **/
    public static function workList($status = 0)
    {
        return InfoRepo::workList();
    }
    
    /*
        * FlowDesc API
        *
        **/
    
    public static function flowApi($wf_type,$data='')
    {
        if ($wf_type == "List") {
                $info = FlowRepo::getFlow();		//获取工作流列表
            } else if ($wf_type == "AddFlow") {
                $info = FlowRepo::addFlow($data); //新增工作流
            } else if ($wf_type == "EditFlow") {
                $info = FlowRepo::editFlow($data);//更新工作流
            } else if ($wf_type == "GetFlowInfo")  { 
                $info = FlowRepo::getFlow($data); //获取工作流详情
            }else{
                throw new \Exception ( "参数出错！" );
            }
        return $info;
    }
    /*
        * FlowLog API
        *
        **/
    public static function flowLog($logtype,$wf_fid,$wf_type)
    {
        if ($logtype == "logs") {
                $info = ProcessRepo::runLog($wf_fid,$wf_type);//获取log
            }else{
                throw new \Exception ( "参数出错！" );
            }
        return $info;
    }
    /*
        * ProcessDesc API
        * 
        **/
    
    public static function processApi($ProcessType,$flow_id,$data='')
    {
        if ($ProcessType == "All") {
                $info = FlowRepo::processAll($flow_id); 
            } else if ($ProcessType == "ProcessDel") {       //删除步骤
                $info = FlowRepo::processDel($flow_id,$data);
            } else if ($ProcessType == "ProcessDelAll") {    //删除步骤
                $info = FlowRepo::processDelAll($flow_id);
            } else if ($ProcessType == "ProcessAdd")  {      //新增步骤
                $info = FlowRepo::processAdd($flow_id); 
            } else if ($ProcessType == "ProcessLink")  { 
                $info = FlowRepo::processLink($flow_id,$data); //保存设计样式
            } else if ($ProcessType == "ProcessAttSave")  { 
                $info = FlowRepo::processAttSave($flow_id,$data); //保存步骤属性
            } else if ($ProcessType == "ProcessAttView")  { 
                $info = FlowRepo::processAttView($flow_id,$data); //查看属性设计
            }else{
                throw new \Exception ( "参数出错！" );
            }
        return $info;
    }
    
    /*
        * SuperApi API
        * 
        **/
    public static function superApi($stype,$key,$data='')
    {
        if ($stype == "WfEnd") {
                $ret = TaskService::doSupEnd($key,$data); //终止工作流
            }else if ($stype == "Role") {    
                $ret = UserRepo::getRole();
            }else if ($stype == "CheckFlow") {    
                $ret = FlowRepo::checkFlow($key);
            }else{
                throw new \Exception ( "参数出错！" );
            }
        return $ret;
    }

    public static function getprocessinfo($pid,$run_id){
        if( @$pid=='' || @$run_id ==''){
            throw new \Exception ( "config参数信息不全！" );
        }
        $wf_process = ProcessRepo::getRunProcess($pid,$run_id);
        if($wf_process['auto_person']==3){
            $todo = $wf_process['sponsor_ids'].'*%*'.$wf_process['sponsor_text'];
            }else{
            $todo = '';
        }
        return $todo;
    }
    
    public function send_mail()
    {
        $mail = new SendMail();
        $mail->setServer("smtp.qq.com", "1838188896@qq.com", "pass");
        $mail->setFrom("1838188896@qq.com");
        $mail->setReceiver("632522043@qq.com");
        $mail->setReceiver("632522043@qq.com");
        $mail->setMailInfo("test", "<b>test</b>");
        $mail->sendMail();
    }
}