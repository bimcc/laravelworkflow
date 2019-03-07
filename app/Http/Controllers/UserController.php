<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Work\Model\User;



class UserController extends Controller
{
    public function index(Request $request)
    {
		if ($request->get("username")) $map[] = ['username','like',"%" . $request->get("username") . "%"];
        $list=$this->commonlist('User',$map);
        return view('user.list',['list'=>$list]);
    }

    public function add(Request $request)
    {
        $UserDB = model("User");
        if ($request->method() == 'POST') {
            $password = $_POST['password'];
            $repassword = $_POST['repassword'];
            if(empty($password) || empty($repassword)){
				return $this->msg_return('密码必须！',1);
            }
            if($password != $repassword){
				return $this->msg_return('两次输入密码不一致！',1);
            }
            //根据表单提交的POST数据创建数据对象
            $userInfo = User::create($_POST);
            if(isset($userInfo['id'])){
                $data['user_id'] = $userInfo->id;
                $data['role_id'] = $request->get('role');

                if(model('RoleUser')->addRoleUser($data)) {
					return msg_return('添加成功！');
                } else {
					return msg_return('用户添加成功,但角色对应关系添加失败',1);
                }
            } else {
                $this->error($userInfo->getError());
            }
        } else{
            $role = model('Role')->getAllRole(array('status'=>1),'sort DESC');
            $this->assign('role',$role);
            return $this->fetch();
        }
    }

    // 编辑用户
    public function edit()
	{
        $UserDB = model("User");
        if(isset($_POST['dosubmit'])) {;
            $password = $_POST['password'];
            $repassword = $_POST['repassword'];
            if(!empty($password) || !empty($repassword)){
                if($password != $repassword){
					return msg_return('两次输入密码不一致！',1);
                }
            }
            if(empty($password) && empty($repassword)) unset($_POST['password']);   //不填写密码不修改
            $userInfo = $UserDB->update($_POST);
            //根据表单提交的POST数据创建数据对象
            if(isset($userInfo[$userInfo->getPk()])){
                $where['user_id'] = $this->request->post('id');
                $data['role_id'] = $this->request->post('role');
                model('RoleUser')->upRoleUser($where,$data);
				return msg_return('编辑成功！');
            } else {
                $this->error($userInfo->getError());
            }
        }else{
            $id = input('param.id',0,'intval');
            if(!$id)$this->error('参数错误!');
            $role = model('Role')->getAllRole(array('status'=>1),'sort DESC');
            $info = $UserDB->getUser(array('id'=>$id));
            $this->assign('role',$role);
            $this->assign('info',$info);
            return $this->fetch('add');
        }
    }

    //ajax 验证用户名
    public function check_username()
	{
        $userid = $this->request->param('userid','0','intval');
        $username =  $this->request->param('username');
        if(model("User")->check_name($username,$userid)){
            echo 1;
        }else{
            echo 0;
        }
    }

    //删除用户
    public function del()
	{
        $id = input('param.id',0,'intval');
        if(!$id)$this->error('参数错误!');
        $UserDB = model('User');
        $info = $UserDB->getUser(array('id'=>$id));

        if($info['username'] == config('SPECIAL_USER')){     //无视系统权限的那个用户不能删除
            $this->error('禁止删除此用户!');
        }

        if($UserDB->delUser('id='.$id)){
            if(model("RoleUser")->where('user_id='.$id)->delete()){
                return json(['msg'=>'删除成功！']);
            }else{
				 return json(['msg'=>'用户成功,但角色对应关系删除失败!']);
            }
        } else{
            $this->error('删除失败!');
        }
    }

    /* ========角色部分======== */

    // 角色管理列表
    public function role()
	{
        $list=controller('Base', 'event')->commonlist('Role');
		$this->assign('list', $list);
        return $this->fetch();
    }

    // 添加角色
    public function addrole()
	{
        $RoleDB = model("Role");
       if ($this->request->isPost()) {
            //根据表单提交的POST数据创建数据对象
            $roleInfo = $RoleDB->create(input('post.'));
            if(isset($roleInfo[$roleInfo->getPk()])) {
                return msg_return('添加成功！');
            }else{
				return msg_return($roleInfo->getError(),1);
            }
        }else{
            return $this->fetch();
        }
    }

    // 编辑角色
    public function roleedit()
	{
        $RoleDB = model("Role");
          if ($this->request->isPost()) {
            //根据表单提交的POST数据创建数据对象
            $roleInfo = $RoleDB->update(input('post.'));

            if(isset($roleInfo[$roleInfo->getPk()])) {
                return msg_return('编辑成功！');
            } else {
                return msg_return($roleInfo->getError(),1);
            }
        }else{
            $id = input('id',0,'intval');
            if(!$id)$this->error('参数错误!');
            $info = $RoleDB->getRole(array('id'=>$id));
            $this->assign('info',$info);
            return $this->fetch('addrole');
        }
    }

    //删除角色
    public function role_del()
	{
        $id = input('param.id',0,'intval');
        if(!$id)$this->error('参数错误!');
        $RoleDB = model('Role');
        if($RoleDB->delRole('id='.$id)){
			 return json(['msg'=>'删除成功！']);
        }else{
			 return json(['msg'=>'删除失败']);
        }
    }

    // 排序权重更新
    public function role_sort()
	{
        $sorts = $_POST['sort'];
        if(!is_array($sorts))$this->error('参数错误!');
        foreach ($sorts as $id => $sort) {
            model('Role')->upRole( ['id'=>$id , 'sort'=>intval($sort)] );
        }
        $this->success('更新完成！',url('/Admin/User/role'));
    }
}
