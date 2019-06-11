<?php
namespace App\Http\Controllers;

use App\Work\Workflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class WorkflowController extends Controller {
    /**
	 	* 流程设计首页
	 	* @param $map 查询参数
		*/
    public function wfindex(Request $request){
        $list = Workflow::FlowApi('List');
				$type = ['news'=>'新闻信息','cnt'=>'合同信息','paper'=>'证件信息','null'=>'尚未选择',''=>'尚未选择'];
        return  view('wf.index',['list'=>$list,'type'=>$type]);
    }
	/**
	 * 工作流设计界面
	 */
    public function wfdesc(Request $request,$flow_id){
        if($flow_id<=0){
            return response()->json('参数有误，请返回重试!');
				}
        $one = Workflow::FlowApi('GetFlowInfo',$flow_id);
        if(!$one){
            return response()->json('未找到数据，请返回重试!');
        }
				return view('wf.desc',['one'=>$one,'process_data'=>Workflow::ProcessApi('All',$flow_id)]);
    }
    /**
	 * 流程添加
	 */
    public function wfadd(Request $request)
    {
				$data = $request->all();
				if(count($data) == 0){
					return  view('wf.add',['type'=>['news'=>'新闻信息','cnt'=>'合同信息','paper'=>'证件信息']]);
				}
				$data['uid']=session('uid');
				$data['add_time']=time();
				$ret= Workflow::FlowApi('AddFlow',$data);
				if($ret['code']==0){
						return $this->msg_return('发布成功！');
				}else{
						return $this->msg_return($ret['data'],1);
				}
    }
	 /**
	 * 流程修改
	 */
	public function wfedit(Request $request,$id)
	{
			if ($request->method() == 'POST') {
				$data = $request->all();
				$ret= Workflow::FlowApi('EditFlow',$data);
				if($ret['code']==0){
						return $this->msg_return('修改成功！');
				}else{
						return $this->msg_return($ret['data'],1);
				}
			}
			$data = [];
			$data['id'] = $id;
			if($id){
					$data['info'] = Workflow::FlowApi('GetFlowInfo',$id);
			}
			$data['type'] = ['news'=>'新闻信息','cnt'=>'合同信息','paper'=>'证件信息'];
			return view('wf.add',$data);
	}
	/**
	 * 状态改变
	 */
	public function wfchange(Request $request,$id=0,$status=0)
	{
			$data = ['id'=>$id,'status'=>$status];
			$ret= Workflow::FlowApi('EditFlow',$data);
			if($ret['code']==0){
					return response()->json('操作成功');
			}else{
					return response()->json('操作失败！');
			}
	}
	
    /**
	 * 删除流程
	 **/
   public function delete_process(Request $request)
    {
		return response()->json(Workflow::ProcessApi('ProcessDel',$request->get('flow_id'),$request->get('process_id')));
    }
	public function del_allprocess(Request $request)
	{
		return response()->json(Workflow::ProcessApi('ProcessDelAll',$request->get('flow_id')));
	}
	/**
	 * 添加流程
	 **/
    public function add_process(Request $request)
    {
        $flow_id = $request->get('flow_id',0);
        $one = Workflow::FlowApi('GetFlowInfo',$flow_id);
        if(!$one){
          return response()->json(['status'=>0,'msg'=>'添加失败,未找到流程','info'=>'']);
        }
				return response()->json(Workflow::ProcessApi('ProcessAdd',$flow_id));
    }
    /**
	 * 保存布局
	 **/
    public function save_canvas(Request $request)
    {
				return response()->json(Workflow::ProcessApi('ProcessLink',$request->get('flow_id'),$request->get('process_info')));
    }
    //右键属性
    public function wfatt(Request $request)
    {
	    	$info = Workflow::ProcessApi('ProcessAttView',$request->get('id',0));
	    	$data['op'] = $info['show'];
        $data['one'] = $info['info'];
				$data['from'] = $info['from'];
        $data['process_to_list'] = $info['process_to_list'];
				$data['child_flow_list'] = $info['child_flow_list'];
				return view('wf.att',$data);
		}
		
    public function save_attribute(Request $request)
    {
	    $data = $request->all();
			return response()->json(Workflow::ProcessApi('ProcessAttSave',$data['process_id'],$data));
    }
   
	//用户选择控件
    public function super_user(Request $request,$type='')
    {
				$data['user'] = DB::table('user')->select('id','username')->get();
				$data['kid'] = $type;
        return view('wf.super_user',$data);
    }
		//用户选择控件
    public function super_role(Request $request)
    {
				$data['role'] = DB::table('role')->select('id','name as username')->get();
        return view('wf.super_role',$data);
		}
		
		public function super_get(Request $request)
		{
				$type = trim($request->get('type'));
				if($type=='user'){
						$info = DB::table('user')->select('id as vlaue','username as text')->where('username','like','%'.$request->get('key').'%')->get();
				}else{
						$info = DB::table('role')->select('id as vlaue','name as text')->where('name','like','%'.input('key').'%')->get();
				}
				return response()->json(['data'=>$info,'code'=>1,'msg'=>'查询成功！']);
		}
		/*流程监控*/
		public function wfjk($map = [])
		{
				$data['list'] = Workflow::worklist();
				return view('wf.wfjk',$data);
		}
	
		public function btn($wf_fid,$wf_type,$status)
		{
				$url = url("/wf/wfcheck/",["wf_type"=>$wf_type,"wf_title"=>'2','wf_fid'=>$wf_fid]);
				$url_star = url("/wf/wfstart/",["wf_type"=>$wf_type,"wf_title"=>'2','wf_fid'=>$wf_fid]);
				switch ($status)
				{
						case 0:
							return '<span class="btn  radius size-S" onclick=layer_show(\'发起工作流\',"'.$url_star.'","450","350")>发起工作流</span>';
							break;
						case 1:
								$st = 0;
								$flowinfo =  Workflow::workflowInfo($wf_fid,$wf_type,['uid'=>session('uid'),'role'=>session('role')]);
			
						if($flowinfo!=-1){
								if($flowinfo['sing_st']==0){
									$user = explode(",", $flowinfo['status']['sponsor_ids']);

										if($flowinfo['status']['auto_person']==3||$flowinfo['status']['auto_person']==4){
												if (in_array(session('uid'), $user)) {
														$st = 1;
												}
										}
										if($flowinfo['status']['auto_person']==5){
												if (in_array(session('role'), $user)) {
														$st = 1;
												}
										}
								}else{
									if($flowinfo['sing_info']['uid']==session('uid')){
											$st = 1;
									}
								}
							}else{
									return '<span class="btn  radius size-S">无权限</span>';
							}	
							if($st == 1){
									return '<span class="btn  radius size-S" onclick=layer_show(\'审核\',"'.$url.'","850","650")>审核</span>';
							}else{
									return '<span class="btn  radius size-S">无权限</span>';
							}
			
							case 100:
									echo '<span class="btn btn-primary" onclick=layer_show(\'代审\',"'.$url.'?sup=1","850","650")>代审</span>';
							break;
		 
							break;
							default:
							return '';
					}
		}
		protected static function status($status)
		{
				switch ($status)
				{
					case 0:
						return '<span class="label radius">保存中</span>';
						break;
					case 1:
						return '<span class="label radius" >流程中</span>';
						break;
					case 2:
						return '<span class="label label-success radius" >审核通过</span>';
						break;
					default: //-1
						return '<span class="label label-danger radius" >退回修改</span>';
				}
		}
	
    /*发起流程，选择工作流*/
	public function wfstart(Request $request)
	{
		$info = ['wf_type'=>$request->get('wf_type'),'wf_title'=>$request->get('wf_title'),'wf_fid'=>$request->get('wf_fid')];
		$flow =  Workflow::getWorkFlow($request->get('wf_type'));
		$data['flow'] = $flow;
		$data['info'] = $info;
		return view('wf.wfstart',$data);
	}
	/*正式发起工作流*/
	public function start_save(Request $request)
	{
		$data = $request->all();
		$flow = Workflow::startworkflow($data,session('uid'));
		if($flow['code']==1){
			return $this->msg_return('Success!');
		}
	}
	
	public function wfcheck(Request $request)
	{
		$info = ['wf_title'=>$request->get('wf_title'),'wf_fid'=>$request->get('wf_fid'),'wf_type'=>$request->get('wf_type')];
		$info = json_decode(json_encode($info,true));
		$data['info'] = $info;
		$data['flowinfo'] = Workflow::workflowInfo($request->get('wf_fid'),$request->get('wf_type'),['uid'=>session('uid'),'role'=>session('role')]);
		$data['flowinfo'] = json_decode(json_encode($data['flowinfo'],true));
		return view('wf.check',$data);
	}
	public function do_check_save(Request $request)
	{
		$data = $request->all();
		Workflow::workdoaction($data,session('uid'));
		return $this->msg_return('Success!');
	}
	public function ajax_back(Request $request){
		$flowinfo =  Workflow::getprocessinfo($request->get('back_id'),$request->get('run_id'));
		return response()->json($flowinfo);
	}
	public function Checkflow(Request $request)
	{
		$fid = $request->get('fid',0);
		return Workflow::SuperApi('CheckFlow',$fid);
	}
	
	 public function wfup(Request $request)
    {
			return view('wf.wfup');
    }
	
	public function wfend(Request $request)
	{
		Workflow::SuperApi('WfEnd',$request->get('id'),session('uid'));
		return $this->msg_return('Success!');
	}
	public function wfupsave(Request $request)
    {
        $files = $request->file('file');
        $insert = [];
        foreach ($files as $file) {
            $path = public_path() . '/uploads/';
            $info = $file->move($path);
            if ($info) {
                $data[] = $info->getSaveName();
            } else {
                $error[] = $file->getError();
            }
        }
        return $this->msg_return($data,0,$info->getInfo('name'));
    }
	
}