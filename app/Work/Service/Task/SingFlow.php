<?php
/**
*+------------------
* Tpflow 会签模块
*+------------------ 
* Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
*+------------------
* Author: guoguo(1838188896@qq.com)
*+------------------
*/
namespace App\Work\Service\Task;

use think\Db;
use App\Work\Repositories\FlowRepo;
use App\Work\Repositories\LogRepo;
use App\Work\Repositories\ProcessRepo;
use App\Work\Repositories\InfoRepo;
use App\Work\Model\RunSign;
use App\Work\Model\Run;

class SingFlow{
	/**
	 * 回退工作流
	 * 
	 * @param  $config 参数信息
	 * @param  $uid  用户ID
	 */
	public static function doTask($config,$uid) {
		//任务全局类
		$wf_title = $config['wf_title'];
		$wf_fid = $config['wf_fid'];
		$wf_type = $config['wf_type'];
		$flow_id = $config['flow_id'];
		$run_process = $config['run_process'];
		$npid = $config['npid'];//下一步骤流程id
		$run_id = $config['run_id'];
		if($config['sup']=='1'){
			$check_con = '[管理员代办]'.$config['check_con'];
			$config['check_con'] = '[管理员代办]'.$config['check_con'];
		}else{
			$check_con = $config['check_con'];
		}
		$submit_to_save = $config['submit_to_save'];
		$sid = self::addSing($config);
		//结束当前流程，给个会签标志
		$end = self::up_flow($run_id,$sid);
		//结束process
		$end = FlowRepo::end_process($run_process,$check_con);
		//加入会签
		$run_log = LogRepo::addRunLog($uid,$run_id,$config,'Sing');
		//日志记录
	}
	/**
	 *会签确认
	 *
	 * @param $config 参数信息
	 * @param $uid  用户ID
	 * @param $wf_actionid 操作按钮值
	 **/
	public static function doSingEnt($config,$uid,$wf_actionid)
	{
        $sing_id = Run::find($config['run_id']);
        $sing_id = $sing_id->sing_id;
		self::endSing($sing_id,$config['check_con']);//结束当前会签
		if ($wf_actionid == "sok") {//提交处理
			if($config['npid'] !=''){
				/*
				 * 2019年1月27日21:20:13
				 ***/
				$nex_pid = explode(",",$config['npid']);
				foreach($nex_pid as $v){
					$wf_process = ProcessRepo::getProcessInfo($v);
					$add_process = InfoRepo::addWorkflowProcess($config['flow_id'],$wf_process,$config['run_id'],$uid);	
				}
				self::up_flow_press($config['run_id'],$config['npid']);
			}
			
			self::up_run($config['run_id']);
			$run_log = LogRepo::addRunLog($uid,$config['run_id'],$config,'sok');
			if(!$run_log){
                return ['msg'=>'消息记录失败，数据库错误！！！','code'=>'-1'];
            }
			
			//日志记录
		}else if($wf_actionid == "sback") {//退回处理
			//判断是否是第一步，第一步：更新单据，发起修改，不是第一步，写入新的工作流
			$wf_backflow = $config['wf_backflow'];//退回的步骤ID，如果等于0则默认是第一步
			
			if($wf_backflow==0){
				$back = true;
				}else{
				$back =false;
			}
			if($back){//第一步
				//更新单据状态
				$bill_update = InfoRepo::updateBill($config['wf_fid'],$config['wf_type'],'-1');
				if(!$bill_update){
					return ['msg'=>'流程步骤操作记录失败，数据库错误！！！','code'=>'-1'];
				}
				$run_log = LogRepo::addRunLog($uid,$config['run_id'],$config,'SingBack');
				self::up_run($config['run_id']);
				//日志记录
			}else{ //结束流程
				$wf_process = ProcessRepo::getProcessInfo($wf_backflow);
				$wf_run_process = InfoRepo::addWorkflowProcess($config['flow_id'],$wf_process,$config['run_id'],$uid);
				self::up_run($config['run_id']);
				//消息通知发起人
				$run_log = LogRepo::addRunLog($uid,$config['run_id'],$config,'SingBack');
				if(!$run_log){
                    return ['msg'=>'消息记录失败，数据库错误！！！','code'=>'-1'];
                }
			}
			//日志记录
		} else if ($wf_actionid == "ssing") {//会签
			//日志记录
			$run_log = LogRepo::addRunLog($uid,$config['run_id'],$config,'SingSing');
			$sid = self::AddSing($config);
			$end = self::up_flow($config['run_id'],$sid);
			//发起新的会签
		} else { //通过
			throw new \Exception ("参数出错！");
		}
		
	}
	/**
	 *会签执行
	 *
	 * @param $sing_sign 会签ID
	 * @param $check_con  审核内容
	 **/
	public static function endSing($sing_sign,$check_con)
	{
		return RunSign::where('id',$sing_sign)->update(['is_agree'=>1,'content'=>$check_con,'dateline'=>time()]);
	}
	/**
	 *更新单据信息
	 *
	 *@param $run_id 工作流run id
	 **/
	public static function up_run($run_id)
	{
		return Run::where('id',$run_id)->update(['is_sing'=>0]);
	}
	/**
	 *更新流程信息
	 *
	 *@param $run_id 工作流ID
	 *@param $run_process 运行步骤
	 **/
	public static function up_flow_press($run_id,$run_process)
	{
		return Run::where('id',$run_id)->update(['run_flow_process'=>$run_process]);
	}
	/**
	 *新增会签
	 *
	 *@param $config 参数信息
	 **/
	public static function addSing($config)
	{
		$data = [
			'run_id'=>$config['run_id'],
			'run_flow'=>$config['flow_id'],
			'run_flow_process'=>$config['run_process'],
			'content' => '',
			'uid'=>$config['wf_singflow'],
			'dateline'=>time()
		];
		$run_sign = RunSign::create($data);
		if(!$run_sign){
            return  false;
        }
        return $run_sign->id;	
	}
	/**
	 *更新流程
	 *
	 *@param $run_id 工作流ID
	 *@param $sid 会签ID
	 **/
	public static function up_flow($run_id,$sid)
	{
		return Run::where('id',$run_id)->update(['is_sing'=>1,'sing_id'=>$sid,'endtime'=>time()]);
	}
}