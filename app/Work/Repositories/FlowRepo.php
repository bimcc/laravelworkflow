<?php
namespace App\Work\Repositories;

use App\Work\Model\Flow;
use App\Work\Model\Run;
use App\Work\Model\RunProcess;
use Illuminate\Support\Facades\DB;
use App\Work\Model\FlowProcess;


class FlowRepo{
    // 获取类别工作流
    public static function getWorkflowByType($wf_type){
        if(!$wf_type) return [];
        return Flow::where('status',0)->where('type',$wf_type)->get();
    }

    // 获取流程信息
    public static function GetFlowInfo($fid){
        if(!$fid) return false;
        $info = Flow::findOrFail($fid);
        return $info['flow_name'];
    }

    // 判断工作流是否存在
    public static function getWorkFlow($wf_id){
        if(!$wf_id) return false;
        $info = Flow::findOrFail($wf_id);
        return $info;
    }

    // 获取步骤信息
    public static function getFlowProcess($id){
        if(!$id) return false;
        return FlowProcess::findOrFail($id);
    }

    // API 获取工作流列表
    public static function getFlow($info = ''){
        if(!$info){
            $list = Flow::orderBy('id','desc')->paginate(10);
            foreach($list as $key=>$value){
                $value['edit'] = Run::where('flow_id',$value['id'])->where('status',0)->get(['id'])->toarray();
            }
        }else{
            $list = Flow::find($info);
        }
        return $list;
    }

    // API 新增工作流
    public static function addFlow($data){
        if(!$data){
            return ['code'=>1,'data'=>''];
        }
        $data = Flow::create($data);        
        return ['code' => 0,'data'=>$data->id];
    }

    // API 编辑工作流
    public static function editFlow($data){
        if(!$data){
            return ['code'=>1,'data'=>''];
        }
        $flow = Flow::find($data['id']);
        foreach($data as $key=>$value){
            if($key != 'id'){
                $flow->{$key} = $value;
            }
        }
        $flow->save();
                
        return ['code' => 0,'data'=>$data['id']];
    }

    // 获取所有步骤信息
    public static function processAll($flow_id){
        $list = FlowProcess::where('flow_id',$flow_id)->orderBy('id','desc')->get();
        $processData = [];
        $processTotal = 0;
        foreach($list as $value){
            $processTotal +=1;
            $style = json_decode($value['style'],true);
            $processData[] = [
                'id' => $value['id'],
                'flow_id' => $value['flow_id'],
                'process_name' => $value['process_name'],
                'process_to' => $value['process_to'],
                'style' => 'width:' . $style['width'] . 'px;height:' . $style['height'] . 'px;line-height:30px;color:' . $style['color'] . ';left:' . $value['setleft'] . 'px;top:' . $value['settop'] . 'px;',   
            ];
        }
        return json_encode(['total' => $processTotal, 'list' => $processData]);
    }

    // 删除步骤信息
    public static function processDel($flow_id, $process_id)
    {
        if ($process_id <= 0 or $flow_id <= 0) {
            return ['status' => 0, 'msg' => '操作不正确'];
        }
        $map = ['id' => $process_id, 'flow_id' => $flow_id];
        DB::beginTransaction();
        $trans = FlowProcess::where($map)->delete();
        if (!$trans) {
            DB::rollback();
            return ['status' => 0, 'msg' => '删除失败', 'info' => ''];
        }
        $list = FlowProcess::select('id','process_to')->where('flow_id', $flow_id)->where('process_to', "FIND_IN_SET(" . $process_id . ",process_to)")->get();
        if (is_array($list)) {
            foreach ($list as $value) {
                $arr = explode(',', $value['process_to']);
                $k = array_search($process_id, $arr);
                unset($arr[$k]);
                $process_to = '';
                if (!empty($arr)) {
                    $process_to = implode(',', $arr);
                }
                $data = ['process_to' => $process_to, 'updatetime' => time()];
                $trans = FlowProcess::where('id', $value['id'])->update($data);
                if (!$trans) {//有错误，跳出
                    break;
                }
            }
        }
        if (!$trans) {
            DB::rollBack();
            return ['status' => 0, 'msg' => '删除失败，请重试', 'info' => ''];
        }
        
        DB::commit();
        return ['status' => 1, 'msg' => '删除成功', 'info' => ''];
    }
    /**
     * 删除步骤信息
     * @param $flow_id 
     */
    public static function processDelAll($flow_id)
    {
        $res = FlowProcess::where('flow_id', $flow_id)->delete();
        if ($res) {
            return ['status' => 1, 'data' => $res, 'msg' => '操作成功！'];
        } else {
            return ['status' => 0, 'msg' => '操作错误！'];
        }
    }

    /**
     * 新增步骤信息
     * @param $flow_id 
     */
    public static function processAdd($flow_id)
    {
        $process_count = FlowProcess::where('flow_id', $flow_id)->count();
        $process_type = 'is_step';
        if ($process_count <= 0){
            $process_type = 'is_one';
			$process_setleft = '100';
			$process_settop = '100';			
		}else{
			//新建步骤显示在上一个步骤下方 2019年1月28日14:32:45
            $style = FlowProcess::where('flow_id',$flow_id)->orderBy('id','desc')->first();
			$process_type = 'is_step';
			$process_setleft = $style['setleft']+30;
			$process_settop = $style['settop']+30;
		}
        $data = [
            'flow_id' => $flow_id,'setleft' => $process_setleft,'settop' => $process_settop,
            'process_type' => $process_type, 'style' => json_encode(['width' => '120', 'height' => '38', 'color' => '#0e76a8'])
        ];
        $processid = FlowProcess::create($data);
        if (!$processid) {
            return ['status' => 0, 'msg' => '添加失败！', 'info' => ''];
        } else {
            return ['status' => 1, 'msg' => '添加成功！', 'info' => ''];
        }
    }

    /**
     * 步骤连接
     * @param $flow_id 
	 * @param $process_info 
     */
    public static function processLink($flow_id, $process_info)
    {
        $one = self::getFlow($flow_id);;
        if (!$one) {
            return ['status' => 0, 'msg' => '未找到流程数据', 'info' => ''];
        }
        $process_info = json_decode(htmlspecialchars_decode(trim($process_info)), true);
        if ($flow_id <= 0 or !$process_info) {
            return ['status' => 0, 'msg' => '参数有误，请重试', 'info' => ''];
        }
        foreach ($process_info as $process_id => $value) {
            $datas = [
                'setleft' => (int)$value['left'],
                'settop' => (int)$value['top'],
                'process_to' => self::ids_parse($value['process_to']),
                'updatetime' => time()
            ];
            FlowProcess::where('id', $process_id)->where('flow_id',$flow_id)->update($datas);
        }
        return ['status' => 1, 'msg' => '添加成功！', 'info' => ''];
    }
	/**
     * 属性保存
     * @param $process_id 
	 * @param $datas 
     */
    public static function processAttSave($process_id, $datas)
    {
        $process_condition = trim($datas['process_condition'], ',');//process_to
        $process_condition = explode(',', $process_condition);
        $out_condition = array();
		if(count($process_condition)>1 and  $datas['wf_mode']==1){
			foreach ($process_condition as $value) {
				$value = intval($value);
				if ($value > 0) {
					$condition = trim($datas['process_in_set_' . $value], "@wf@");
					 if ($condition=='') {
						return ['code' => 1, 'msg' => '转出条件必须设置！!', 'info' => ''];
					}
					$condition = $condition ? explode("@wf@", $condition) : array();
					$out_condition[$value] = ['condition' => $condition];
				}
			}
		}

        $data = [
            'process_name' => $datas['process_name'],
            'process_type' => $datas['process_type'],
            'auto_person' => $datas['auto_person'],
			'wf_mode' => $datas['wf_mode'],
			'wf_action' => $datas['wf_action'],
            'auto_sponsor_ids' => $datas['auto_sponsor_ids'],
            'auto_sponsor_text' => $datas['auto_sponsor_text'],
            'auto_role_ids' => $datas['auto_role_ids'] ?? '',
            'auto_role_text' => $datas['auto_role_text'] ?? '',
            'range_user_ids' => $datas['range_user_ids'] ?? '',
            'range_user_text' => $datas['range_user_text'] ?? '',
            'is_sing' => $datas['is_sing'],
            'is_back' => $datas['is_back'],
            'out_condition' => json_encode($out_condition),
            'style' => json_encode(['width' => $datas['style_width'], 'height' => $datas['style_height'], 'color' => $datas['style_color']])
        ];
        //在没有下一步骤的时候保存属性
        if (isset($datas["process_to"])) {
            $data['process_to'] = self::ids_parse($datas['process_to']);
        }
        $ret = FlowProcess::where('id', $process_id)->update($data);
        if ($ret!==false) {
            return ['code' => 0, 'msg' => '保存成功！', 'info' => ''];
        } else {
            return ['code' => 1, 'msg' => '保存失败！', 'info' => ''];
        }

    }
	/**
     * 属性查看
	 * @param $process_id
     */
    public static function processAttView($process_id)
    {
        //连接数据表用的。表 model 
        $one = self::getFlowProcess($process_id);
        if (!$one) {
            return ['status' => 0, 'msg' => '未找到步骤信息!', 'info' => ''];
        }
        $flow_one = self::getFlow($one['flow_id']);
        if (!$flow_one) {
            return ['status' => 0, 'msg' => '未找到流程信息!', 'info' => ''];
        }
		$one['process_tos'] = $one['process_to'];
        $one['process_to'] = $one['process_to'] == '' ? array() : explode(',', $one['process_to']);
        $one['style'] = json_decode($one['style'], true);
        $one['out_condition'] = self::parse_out_condition($one['out_condition'], '');//json
        $process_to_list =  FlowProcess::select('id','process_name','process_type')->where('id','in',$one['process_tos'])->select();
		foreach($process_to_list as $k=>$v){
			if((count($one['out_condition'])>1)){
				$process_to_list[$k]['condition'] = $one['out_condition'][$v['id']]['condition'];
			}else{
				$process_to_list[$k]['condition'] = '';
			}
		}
        $child_flow_list =  Flow::select('id','flow_name')->select();
        return ['show' => 'basic', 'info' => $one, 'process_to_list' => $process_to_list, 'child_flow_list' => $child_flow_list, 'from' => self::get_db_column_comment($flow_one['type'])];
    }
	/**
     * 步骤逻辑检查
     * @param $wfid 
     */
	public  static function checkFlow($wfid)
	{
		$flow = Flow::find($wfid);
		if (!$wfid) {
            return ['status' => 0, 'msg' => '参数出错!', 'info' => ''];
        }
        $pinfo =  FlowProcess::where('flow_id',$wfid)->get();
		if (count($pinfo)<1) {
            return ['status' => 0, 'msg' => '没有找到步骤信息!', 'info' => ''];
        }
		$one_pinfo =FlowProcess::where('flow_id',$wfid)->where('process_type','is_one')->count();
		if ($one_pinfo<1) {
            return ['status' => 0, 'msg' => '没有设置第一步骤,请修改!', 'info' => ''];
        }
		if ($one_pinfo>1) {
            return ['status' => 0, 'msg' => '有两个起始步骤，请注意哦！', 'info' => ''];
        }
		return ['status' => 1, 'msg' => '简单逻辑检查通过，请自行检查转出条件！', 'info' => ''];
	}
	
	/**
	 *结束工作流主状态
	 *
	 *@param $run_flow_process 工作流ID
	 **/
	public static function end_flow($run_id)
	{
		return Run::where('id',$run_id)->update(['status'=>1,'endtime'=>time()]);
	}
	/**
	 *结束工作流步骤信息
	 *
	 *@param $run_flow_process 工作流ID
	 **/
	public static function end_process($run_process,$check_con)
	{
		return RunProcess::where('id',$run_process)->update(['status'=>2,'remark'=>$check_con,'bl_time'=>time()]);
	}
	/**
	 *更新流程主信息
	 *
	 *@param $run_flow_process 工作流ID
	 **/
	public static function up($run_id,$flow_process)
	{
		return Run::where('id',$run_id)->update(['run_flow_process'=>$flow_process]);	
	}
    /**
     * JSON 转换处理
     * @param $flow_id 
	 * @param $process_info 
     */
    public static function parse_out_condition($json_data, $field_data)
    {
        $array = json_decode($json_data, true);
        if (!$array) {
            return '[]';
        }
        $json_data = array();//重置
        foreach ($array as $key => $value) {
            $condition = '';
            foreach ($value['condition'] as $val) {
                $preg = "/'(data_[0-9]*|checkboxs_[0-9]*)'/s";
                preg_match_all($preg, $val, $temparr);
                $val_text = '';
                foreach ($temparr[0] as $k => $v) {
                    $field_name = self::get_field_name($temparr[1][$k], $field_data);
                    if ($field_name)
                        $val_text = str_replace($v, "'" . $field_name . "'", $val);
                    else
                        $val_text = $val;
                }
                $condition .= '<option value="' . $val . '">' . $val . '</option>';
            }
            $value['condition'] = $condition;
            $json_data[$key] = $value;
        }
        return $json_data;
    }

    /**
     * 获取字段名称
     */
    public static function get_field_name($field, $field_data)
    {
        $field = trim($field);
        if (!$field) return '';
        $title = '';
        foreach ($field_data as $value) {
            if ($value['leipiplugins'] == 'checkboxs' && $value['parse_name'] == $field) {
                $title = $value['title'];
                break;
            } else if ($value['name'] == $field) {
                $title = $value['title'];
                break;
            }
        }
        return $title;
    }
	/**
     * IDS数组转换
     */
    public static function ids_parse($str, $dot_tmp = ',')
    {
        if (!$str) return '';
        if (is_array($str)) {
            $idarr = $str;
        } else {
            $idarr = explode(',', $str);
        }
        $idarr = array_unique($idarr);
        $dot = '';
        $idstr = '';
        foreach ($idarr as $id) {
            $id = intval($id);
            if ($id > 0) {
                $idstr .= $dot . $id;
                $dot = $dot_tmp;
            }
        }
        if (!$idstr) $idstr = 0;
        return $idstr;
    }


    /**
     * 获取表字段信息
     *
     */
    public static function get_db_column_comment($table_name = '', $field = true, $table_schema = '')
    {
        $database = env('DB_DATABASE');
        $table_schema = empty($table_schema) ? $database : $table_schema;
        $table_name = $table_name;
        $fieldName = $field === true ? 'allField' : $field;
        $cacheKeyName = 'db_' . $table_schema . '_' . $table_name . '_' . $fieldName;
        $param = [
            $table_name,
            $table_schema
        ];
        $columeName = '';
        if ($field !== true) {
            $param[] = $field;
            $columeName = "AND COLUMN_NAME = ?";
        }
        $res = DB::select("SELECT COLUMN_NAME as field,column_comment as comment FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = ? AND table_schema = ? $columeName", $param);
        $result = [];
        foreach($res as $k=>$value) {
            foreach ($value as $key => $v) {
                if ($value['comment'] != '') {
                    $result[$value['field']] = $value['comment'];
                }
            }
        }
        return count($result) == 1 ? reset($result) : $result;
    }
}

