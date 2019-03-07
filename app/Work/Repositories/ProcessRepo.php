<?php
namespace App\Work\Repositories;

use App\Work\Model\Flow;
use App\Work\Model\Run;
use App\Work\Model\User;
use App\Work\Model\RunProcess;
use App\Work\Model\FlowProcess;
use Illuminate\Support\Facades\DB;
use App\Work\Model\RunLog;

class ProcessRepo{
    /**
	 * 根据ID获取流程信息
	 *
	 * @param $pid 步骤编号
	 */
	public static function getProcessInfo($pid)
	{
		$info = DB::table('flow_process')
				->select('id','process_name','process_type','process_to','auto_person','auto_sponsor_ids','auto_role_ids','auto_sponsor_text','auto_role_text','range_user_ids','range_user_text','is_sing','sign_look','is_back','wf_mode','wf_action')
				->find($pid);
		if($info->auto_person==3){ //办理人员
			$ids = explode(",",$info->range_user_text);
			$info->todo = ['ids'=>explode(",",$info->range_user_ids),'text'=>explode(",",$info->range_user_text)];
		}
		if($info->auto_person==4){ //办理人员
			$info->todo = $info->auto_sponsor_text;
		}
		if($info->auto_person==5){ //办理角色
			$info->todo = $info->auto_role_text;
		}
		return $info;
	}
	/**
	 * 同步步骤信息
	 *
	 * @param $pid 步骤编号
	 */
	public static function GetProcessInfos($ids)
	{
		$info = DB::table('flow_process')
				->select('id','process_name','process_type','process_to','auto_person','auto_sponsor_ids','auto_role_ids','auto_sponsor_text','auto_role_text','range_user_ids','range_user_text','is_sing','sign_look','is_back','wf_mode','wf_action')
				->where('id','in',$ids)
				->get();
		foreach($info as $k=>$v){
			if($v['auto_person']==3){ //办理人员
				$ids = explode(",",$info['range_user_text']);
				$info[$k]['todo'] = ['ids'=>explode(",",$v['range_user_ids']),'text'=>explode(",",$v['range_user_text'])];
			}
			if($v['auto_person']==4){ //办理人员
				$info[$k]['todo'] = $v['auto_sponsor_text'];
			}
			if($v['auto_person']==5){ //办理角色
				$info[$k]['todo'] = $v['auto_role_text'];
			}
		}
		
		return $info;
	}
	/**
	 * 获取下个审批流信息
	 *
	 * @param $wf_type 单据表
	 * @param $wf_fid  单据id
	 * @param $pid   流程id
	 **/
	public static function GetNexProcessInfo($wf_type,$wf_fid,$pid)
	{
		$nex = DB::table('flow_process')->find($pid);
		//先判断下上一个流程是什么模式
		if($nex->process_to !=''){
		$nex_pid = explode(",",$nex->process_to);
		$out_condition = json_decode($nex->out_condition,true);
			//加入同步模式 2为同步模式
			/*
			 * 2019年1月28日14:30:52
			 *1、加入同步模式
			 *2、先获取本步骤信息
			 *3、获取本步骤的模式
			 *4、根据模式进行读取
			 *5、获取下一步骤需要的信息
			 **/
			switch ($nex->wf_mode){
			case 0:
			  $process = self::GetProcessInfo($nex_pid);
			  break;
			case 1:
				//多个审批流
				foreach($out_condition as $key=>$val){
					$where =implode(",",$val['condition']);
					//根据条件寻找匹配符合的工作流id
					$info = DB::table($wf_type)->where($where)->find('id',$wf_fid);
					if($info){
						$nexprocessid = $key; //获得下一个流程的id
						break;	
					}
				}
				$process = self::GetProcessInfo($nexprocessid);
			   break;
			case 2:
				$process = self::GetProcessInfos($nex_pid);
			  break;
			}
		}else{
			$process = ['auto_person'=>'','id'=>'','process_name'=>'END','todo'=>'结束'];
		}
		return $process;
	}
	/**
	 * 获取前步骤的流程信息
	 *
	 * @param $runid
	 */
	public static function getPreProcessInfo($runid)
	{
		$pre = [];
		$pre_n = DB::table('run_process')->find($runid);
		//获取本流程中小于本次ID的步骤信息
		$pre_p = DB::table('run_process')
			 ->where('run_flow',$pre_n->run_flow)
			 ->where('run_id',$pre_n->run_id)
			 ->where('id','lt',$pre_n->id)
			 ->select('run_flow_process')->get();
		//遍历获取小于本次ID中的相关步骤
		foreach($pre_p as $k=>$v){
			$pre[] = Db::table('flow_process')->where('id',$v['run_flow_process'])->get();
		}
		$prearray = [];
		if(count($pre)>=1){
			$prearray[0] = '退回制单人修改';
			foreach($pre as $k => $v){
				if($v['auto_person']==4){ //办理人员
					$todo = $v['auto_sponsor_text'];
				}
				if($v['auto_person']==5){ //办理角色
					$todo = $v['auto_role_text'];
				}
				$prearray[$v['id']] = $v['process_name'].'('.$todo.')';
			}
			}else{
			$prearray[0] = '退回制单人修改';	
		}
		return $prearray;
	}
	/**
	 * 获取前步骤的流程信息
	 *
	 * @param $runid
	 */
	public static function getRunProcess($pid,$run_id)
	{
		$pre_n = RunProcess::where('run_id',$run_id)->where('run_flow_process',$pid)->get();
		return $pre_n;
	}
	
	/**
	 * 获取第一个流程
	 *
	 * @param $wf_id
	 */
	public static function getWorkflowProcess($wf_id) 
	{
		$flow_process = FlowProcess::where('flow_id',$wf_id)->get();
		//找到 流程第一步
        $flow_process_first = array();
        foreach($flow_process as $value)
        {
            if($value['process_type'] == 'is_one')
            {
                $flow_process_first = $value;
                break;
            }
        }
		if(!$flow_process_first)
        {
            return  false;
        }
		return $flow_process_first;
	}
	/**
	 * 流程日志
	 *
	 * @param $wf_fid
	 * @param $wf_type
	 */
	public static function runLog($wf_fid,$wf_type) 
	{
		$run_log = RunLog::where('from_id',$wf_fid)->where('from_table',$wf_type)->get();
		foreach($run_log as $k=>$v)
        {
			$user = User::find($v['uid']);
			$run_log[$k]['user'] = $user->username;
        }
		return $run_log;
	}
	
}