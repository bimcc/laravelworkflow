<?php
/*
 * 公司新闻模块
 * @2018年1月21日
 * @Gwb
 */

namespace App\Http\Controllers;

use App\Work\Model\NewsType;
use App\Work\Model\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class NewsController extends Controller
{
	/**
	 *前置方法
	 */
	protected $beforeActionList = [
        'newstype'  =>  ['only'=>'add,edit'],
    ];
	/**
	 *前置方法角色及类别部署
	 */
	protected function newstype(Request $request)
    {
        $type = NewsType::all();
		return view('news.type',['type'=>$type]);
    }
    /**
     * 新闻列表
     */
    public function index(Request $request)
    {
        if ($request->get("new_title")) {
            $map[] = ['new_title','like',"%" . $request->get("new_title") . "%"];
        }else{
            $map = [];
        }
        $list=$this->commonlist('news',$map);
        return view('news.index',['list'=>$list]);
    }

    /**
     * 新增新闻
     */
    public function add(Request $request)
    {
        if($request->method() == 'POST'){
            $data = $request->all();
            $ret=$this->commonadd('news',$data);
            if($ret['code']==0){
                return $this->msg_return('发布成功！');
            }else{
                return $this->msg_return($ret['data'],1);
            }
        }
        $type = [['id'=>1,'type'=>'类别1'],['id'=>2,'type'=>'类别2'],['id'=>3,'type'=>'类别3']];
        return view('news.add',['type'=>$type]);
    }

    /**
     * 修改新闻
     */
    public function edit(Request $request,$id)
    {
        if($request->method() == 'POST'){
            $data = $request->all();
            $ret=$this->commonedit('news',$data);
            if($ret['code']==0){
                return $this->msg_return('修改成功！');
            }else{
                return $this->msg_return($ret['data'],1);
            }
        }
        if($id){
            $info = News::find($id);
        }
        $type = [['id'=>1,'type'=>'类别1'],['id'=>2,'type'=>'类别2'],['id'=>3,'type'=>'类别3']];
        return view('news.add',['info'=>$info,'type'=>$type]);
    }

    /**
	 * 删除新闻
	 */
	public function del(Request $request,$id)
	{
	    $ret=$this->commondel('news',$id);
	    if($ret['code']==0){
			return $this->msg_return('删除成功！');
        }else{
			return $this->msg_return($ret['data'],1);
		}
	}
	/**
     * 查看新闻
     */
    public function view(Request $request,$id)
    {
        $info = News::find($id);
        return view('news.view',['info'=>$info]);
    }
	/**
     * 类别管理
     */
    public function type(Request $request)
    {
		$data = $request->all();
	    $ret=$this->commonadd('news_type',$data);
		if($ret['code']==0){
			$this->success('新增成功！');
			}else{
			$this->error('新增失败---Db0001');
		}
        if($request->get('tid')){
            $info = News::find($request->get('tid'));
        }
        $list=$this->commonlist('news_type');
	  
        return view('info_list',['list'=>$list,'ifno'=>$info]);
    }
	/**
     * 类别编辑
     */
    public function type_edit(Request $request)
    {
        $data = Input::all();
	    $ret=$this->commonedit('news_type',$data);
		if($ret['code']==0){
			return $this->success('修改成功！',url('type'));
        }else{
			return error('新增失败---Db0001');
		}
    }
    /**
     * 类别删除
     */
    public function type_del(Request $request)
    {
	   $not = News::where('new_type',$request->get('id'))->find();
	   if($not){
			return json(['code'=>1,'msg'=>'该类别已有通知文件，无法删除！']);
		}
	   $ret=$this->commondel('news_type',$request->get('id'));
	   if($ret['code']==0){
			return json(['code'=>0,'msg'=>'删除成功！']);
			}else{
			return json(['code'=>1,'msg'=>$ret['data']]);
		}
    }
}