<?php
namespace App\Work\Repositories;

use App\Work\Model\Flow;
use App\Work\Model\Run;
use App\Work\Model\RunCache;
use App\Work\Model\RunProcess;
use Illuminate\Support\Facades\DB;
use App\Work\Model\FlowProcess;

class InfoRepo{
    /**
	 * 判断业务是否存在，避免已经删除导致错误
	 *
	 * @param $wf_fid  业务id
	 * @param $wf_type 业务表名
	 */
	public static function getBill($wf_fid,$wf_type) 
	{
		if ($wf_fid == '' || $wf_type == '' ) {
			return false;
		}
		$info = DB::table($wf_type)->find($wf_fid);
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
	/**
	 * 添加工作流
	 *
	 * @param $wf_id  流程主ID
	 * @param $wf_process 流程信息
	 * @param $wf_fid  业务id
	 * @param $wf_type 业务表名
	 */
	public static function addWorkflowRun($wf_id,$wf_process,$wf_fid,$wf_type,$uid)
	{
		$data = array(
            'pid'=>0,
            'uid'=>$uid,
            'flow_id'=>$wf_id,
			'from_table'=>$wf_type,
            'from_id'=>$wf_fid,
            'run_name'=>$wf_fid,
            'run_flow_id'=>$wf_id,
            'run_flow_process'=>$wf_process,
            'dateline'=>time(),
        );
        $run_id = Run::create($data);
		if(!$run_id){
            return  false;
        }else{
			 return $run_id->id;
		}
	}
	/**
	 * 添加运行步骤信息
	 *
	 * @param $wf_id  流程主ID
	 * @param $wf_process 流程信息
	 * @param $run_id  运行的id
	 * @param $wf_type 业务表名
	 */
	public static function addWorkflowProcess($wf_id,$wf_process,$run_id,$uid,$todo = '')
	{
		//非自由
		if($todo == ''){
			if($wf_process->auto_person==3){ //办理人员
				$sponsor_ids = $wf_process->range_user_ids;
				$sponsor_text = $wf_process->range_user_text;
			}
			if($wf_process->auto_person==4){ //办理人员
				$sponsor_ids = $wf_process->auto_sponsor_ids;
				$sponsor_text = $wf_process->auto_sponsor_text;
			}
			if($wf_process->auto_person==5){ //办理角色
				$sponsor_text = $wf_process->auto_role_text;
				$sponsor_ids = $wf_process->auto_role_ids;
			}
		}else{
			$todo = explode("*%*",$todo);
			$sponsor_text = $todo[1];
			$sponsor_ids = $todo[0];
		}
		$data = array(
            'uid'=>$uid,
            'run_id'=>$run_id,
            'run_flow'=>$wf_id,
            'run_flow_process'=>$wf_process->id,
            'parent_flow'=>0,
            'parent_flow_process'=>0,
            'run_child'=>0,//未处理，第一步不能进入子流程
            'remark'=>'',
            'is_sponsor'=>0,
            'status'=>0,
			'sponsor_ids'=>$sponsor_ids,//办理人id
			'sponsor_text'=>$sponsor_text,//办理人信息
			'auto_person'=>$wf_process->auto_person,//办理类别
            'js_time'=>time(),
            'dateline'=>time(),
			'wf_mode'=>$wf_process->wf_mode,
			'wf_action'=>$wf_process->wf_action,
        );
        $process_id = RunProcess::create($data);
		if(!$process_id)
        {
            return  false;
        }
        return $process_id;
	}
	/**
	 * 缓存信息
	 *
	 * @param $wf_fid  单据编号
	 * @param $flow_process 流程信息
	 * @param $run_id  运行的id
	 * @param $wf 流程信息
	 */
	public static function addWorkflowCache($run_id,$wf,$flow_process,$wf_fid)
	{
	$run_cache = array(
                'run_id'=>$run_id,
                'form_id'=>$wf_fid,
                'flow_id'=>$wf->id,
                'run_form'=>'',//从 serialize 改用  json_encode 兼容其它语言
                'run_flow'=>json_encode($wf),
                'run_flow_process'=>json_encode($flow_process), //这里未缓存 子流程 数据是不完善的， 后期会完善
                'dateline'=>time()
            );
     $run_cache = RunCache::create($run_cache);
	 if(!$run_cache)
        {
            return  false;
        }
        return $run_cache;
	}
	/**
	 * 根据单据ID，单据表 获取流程信息
	 *
	 * @param $run_id  运行的id
	 * @param $wf_type 业务表名
	 */
	public static function workflowInfo($wf_fid,$wf_type,$userinfo) {
		$workflow = [];
		//根据表信息，判断当前流程是否还在运行  
		$count = Run::where('from_id',$wf_fid)->where('from_table',$wf_type)->count();
		
		if($count > 0){
			//获取当前运行的信息
			$result = Run::where('from_id',$wf_fid)->where('from_table',$wf_type)->where('status',0)->orderBy('id','desc')->first();
			$info_list = RunProcess::where('run_id',$result['id'])
					->where('run_flow',$result['flow_id'])
					->where('run_flow_process',$result['run_flow_process'])
					->where('status',0)
					->get();
			
			if(count($info_list)==0){
				 $info_list[0]=RunProcess::where('run_id',$result['id'])->where('run_flow',$result['flow_id'])->where('run_flow_process',$result['run_flow_process'])->where('status',0)->get();
			}
			
			/*
			 * 2019年1月27日
			 *1、先计算当前流程下有几个步骤
			 *2、如果有多个步骤，判定为同步模式，（特别注意，同步模式下最后一个步骤，也会认定会是单一步骤）
			 *3、根据多个步骤进行循环，找出当前登入用户对应的步骤
			 *4、将对应的步骤设置为当前审批步骤
			 *5、修改下一步骤处理模式
			 *6、修改提醒模式
			 */
			//如果有两个以上的运行步骤，则认定为师同步模式
			if(count($info_list)<2){
				$info = $info_list[0];
				$workflow ['wf_mode'] = 0;//wf_mode
			}else{
				$workflow ['wf_mode'] = 2;//同步模式
				foreach($info_list as $k=>$v){
					if($v['auto_person']==4||$v['auto_person']==3){
							$uids = explode(",", $v['sponsor_ids']);
							if (in_array($userinfo['uid'], $uids)) {
								$info = $v;
								 break;
							}
						}else{
						$uids = explode(",", $v['sponsor_ids']);
						if (in_array($userinfo['role'], $uids)) {
							$info = $v;
							 break;
						}
					}
				}
				if(!isset($info)){
					return -1;
				}
			}
			if ($result) {
					if(!$info){
						throw new \Exception('no info error',500);
					}
					$workflow ['sing_st'] = 0;
					$workflow ['flow_id'] = $result['flow_id'];
					$workflow ['run_id'] = $result['id'];
					$workflow ['status'] = $info;
					$workflow ['flow_process'] = $info['run_flow_process'];
					$workflow ['run_process'] = $info['id'];
					$workflow ['flow_name'] = FlowRepo::getFlowInfo($result['flow_id']);
					$workflow ['process'] = ProcessRepo::getProcessInfo($info['run_flow_process']);
					$workflow ['nexprocess'] = ProcessRepo::getNexProcessInfo($wf_type,$wf_fid,$info['run_flow_process']);
					$workflow ['preprocess'] = ProcessRepo::getPreProcessInfo($info['id']);
					$workflow ['singuser'] = UserRepo::getUser();
					$workflow ['log'] = ProcessRepo::runLog($wf_fid,$wf_type);
					if($result['is_sing']==1){
						$info = RunProcess::where('run_id',$result['id'])->where('run_flow',$result['flow_id'])->where('run_flow_process',$result['run_flow_process'])->get();
					   	$workflow ['sing_st'] = 1;
						$workflow ['flow_process'] = $result['run_flow_process'];
						$process = ProcessRepo::getProcessInfo($result['run_flow_process']);
						$workflow ['status']['wf_mode'] = $process['wf_mode'];
						$workflow ['status']['wf_action'] = $process['wf_action'];
						$workflow ['nexprocess'] = ProcessRepo::getNexProcessInfo($wf_type,$wf_fid,$result['run_flow_process']);
						$workflow ['process'] = $process;
						$workflow ['run_process'] = $info['id'];
						$workflow ['sing_info'] = RunSign::find($result['sing_id']);
					}
			} else {
				$workflow ['bill_check'] = '';
				$workflow ['bill_time'] = '';
			}
		}else{
			$workflow ['bill_check'] = '';
			$workflow ['bill_time'] = '';
		}
		return $workflow;
	}
	
	/**
	 * 根据单据ID，单据表 获取流程信息
	 *
	 * @param $run_id  运行的id
	 * @param $wf_type 业务表名
	 */
	public static function workRunInfo($run_id) {
		$result = Run::find($run_id);
		return $result;
	}
	/**
	 * 更新单据信息
	 *
	 * @param $wf_fid  运行的id
	 * @param $wf_type 业务表名
	 * @param $status  单据状态
	 */
	public static function updateBill($wf_fid,$wf_type,$status = 1)
	{
		$result = DB::table($wf_type)->where('id',$wf_fid)->update(['status'=>$status,'uptime'=>time()]);
		 if(!$result){
            return  false;
        }
        return $result;
	} 
	/**
	 * 工作流列表
	 *
	 */
	public static function workList()
	{
		$result = Run::where('status',0)->get();
		foreach($result as $k=>$v)
		{
			$result[$k]['flow_name'] = Flow::where('id',$v['flow_id'])->value('flow_name');
			$process = FlowProcess::find($v['run_flow_process']);
			if($process->auto_person == 4){
				$result[$k]['user'] =$process->auto_sponsor_text;
				}else{
				$result[$k]['user'] =$process->auto_role_text;
			}
		}
        return $result;
	}
}