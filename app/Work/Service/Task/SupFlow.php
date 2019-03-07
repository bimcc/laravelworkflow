<?php
/**
*+------------------
* Tpflow 普通提交工作流
*+------------------
* Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
*+------------------
* Author: guoguo(1838188896@qq.com)
*+------------------ 
*/
namespace App\Work\Service\Task;

use App\Work\Repositories\InfoRepo;
use App\Work\Repositories\FlowRepo;
use App\Work\Repositories\LogRepo;


class SupFlow{
	/**
	 * 任务执行
	 * 
	 * @param  $config 参数信息
	 * @param  $uid  用户ID
	 */
	public static function doSupEnd($wfid,$uid) {
		//读取工作流信息
		$wfinfo = InfoRepo::workRunInfo($wfid);
		if(!$wfinfo){
				return ['msg'=>'流程信息有误！','code'=>'-1'];
			} 
		$config = [
				'wf_fid'=>$wfinfo['from_id'],
				'wf_type'=>$wfinfo['from_table'],
                'check_con'=>'编号：'.$uid.'的超级管理员终止了本流程！',
            ];
		//结束当前run 工作流
		$end = FlowRepo::end_flow($wfid);
		$end = FlowRepo::end_process($wfinfo['run_flow_process'],$config['check_con']);
		$run_log = LogRepo::addRunLog($uid,$wfid,$config,'SupEnd');
		
		if(!$end){
				return ['msg'=>'结束流程错误！！！','code'=>'-1'];
			} 
		//更新单据状态
		$bill_update = InfoRepo::updateBill($config['wf_fid'],$config['wf_type'],2);
		if(!$bill_update){
			return ['msg'=>'流程步骤操作记录失败，数据库错误！！！','code'=>'-1'];
		}
	}
}