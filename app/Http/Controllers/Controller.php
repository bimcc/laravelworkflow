<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    	/**
	* table 表名，不含表前缀
	* map   查询条件
	* field 筛选字段
	* order_by 字段排序
	**/
    protected function commonlist($table,$map='',$field='',$limit='',$order_by='id')
    {
        if(!$field){
            return DB::table($table)
            ->where($map)
            ->orderBy($order_by,'desc')
            ->limit($limit)
            ->paginate('10');
        }
		return DB::table($table)
                ->select($field)
                ->where($map)
                ->orderBy($order_by,'desc')
				->limit($limit)
				->paginate('10');
	}
	/*
	 * table 表名，不含表前缀
	 * data 提交的数据 
	 */
	protected function commonadd($table,$data)
	{
		$data['uid']=session('uid');
		$data['add_time']=time();
		$id=DB::table($table)->insertGetId($data);
		if($id){
			return ['code'=>0,'data'=>$id];
		}else{
			return ['code'=>1,'data'=>'Db0001-写入数据库出错！'];
		}
	}
	/*
	 * table 表名，不含表前缀
	 * data 提交的数据 
	 */
	protected function commonedit($table,$data)
	{
        $d = $data;
        unset($d['id']);
		$ret = DB::table($table)->where('id', $data['id'])->update($d);
		if($ret){
			return ['code'=>0,'data'=>$ret];
		}else{
			return ['code'=>1,'data'=>'Db0002-更新数据库出错！'];
		}
	}
	/*
	 * table 表名，不含表前缀
	 * data 提交的数据 
	 */
	protected function commondel($table,$id)
	{
		$ret = DB::table($table)->delete($id);;
		if($ret){
			return ['code'=>0,'data'=>$ret];
		}else{
			return ['code'=>1,'data'=>'Db0003-数据库删除数据出错！'];
		}
    }

    // 数组保存到文件
    protected function arr2file($filename, $arr='')
    {
        if(is_array($arr)){
            $con = var_export($arr,true);
        } else{
            $con = $arr;
        }
        $con = "<?php\nreturn $con;\n?>";//\n!defined('IN_MP') && die();\nreturn $con;\n
        write_file($filename, $con);
    }

    protected function get_commonval($table,$id,$val)
    {
        return Db($table)->where('id',$id)->value($val);
    }
    //文件写入
    protected function write_file($l1, $l2='')
    {
        $dir = dirname($l1);
        if(!is_dir($dir)){
            mkdirss($dir);
        }
        return @file_put_contents($l1, $l2);
    }

    //对象转化数组
    protected function obj2arr($obj) 
    {
        return json_decode(json_encode($obj),true);
    }
    /**
     * ajax数据返回，规范格式
     */
    protected function msg_return($msg = "操作成功！", $code = 0,$data = [],$redirect = 'parent',$alert = '', $close = false, $url = '')
    {
        $ret = ["code" => $code, "msg" => $msg, "data" => $data];
        $extend['opt'] = [
            'alert'    => $alert,
            'close'    => $close,
            'redirect' => $redirect,
            'url'      => $url,
        ];
        $ret = array_merge($ret, $extend);
        return response()->json($ret);
    }
    /**
     * get_rolename 获取角色名
     */
    protected function get_rolename($roleid)
    {
        $role = DB::table('role')->where('id',$roleid)->first();
        return $role->name;
    }
}
