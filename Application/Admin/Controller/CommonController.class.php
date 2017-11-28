<?php
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller{
	//构造方法
	public function __construct(){
		//先实现父类的构造方法
		parent::__construct();
		//当前构造方法的代码
		//登录判断
		if(!session('?manager_info')){
			//没有登录标识，跳转到登录页
			$this -> redirect('Admin/Login/login');
		}

		//查询左侧菜单权限数据
		$this -> getnav();

		//权限检测
		$this -> checkauth();
	}

	//查询左侧菜单权限方法
	public function getnav(){
		//判断，如果session中有菜单权限数据，直接从session取 减少对数据库的查询
		if(session('?top') && session('?second')){
			return ;
		}
		//从session获取当前登录管理员的role_id
		$role_id = session('manager_info.role_id');
		if($role_id == 1){
			//超级管理员，直接查询权限表
			//查询顶级权限
			$top = D('Auth') -> where(" pid = 0 and is_nav = 1 ") -> select();
			//查询二级权限
			$second = D('Auth') -> where(" pid > 0  and is_nav = 1 ") -> select();
		}else{
			//普通管理员 先查询角色表
			$role = D('Role') -> where( "role_id = $role_id" ) -> find();
			$role_auth_ids = $role['role_auth_ids']; //多个数字以逗号拼接的字符串
			//查询权限表 顶级权限
			$top = D('Auth') -> where(" id in ($role_auth_ids) and pid = 0 and is_nav = 1 ") -> select();
			//查询二级权限
			$second = D('Auth') -> where(" id in ($role_auth_ids) and pid > 0 and is_nav = 1 ") -> select();
		}
		//可以将菜单权限保存到session中
		session('top', $top);
		session('second', $second);
	}

	//权限检测
	public function checkauth(){
		//获取当前管理员的角色id
		$role_id = session('manager_info.role_id');
		if($role_id == 1){
			//超级管理员，不需要进行权限检测
			return;
		}else{
			//其他管理员，根据role_id查询角色表
			$role = D('Role') -> where(['role_id' => $role_id]) -> find();
			// $role['role_auth_ids']  $role['role_auth_ac']
			//当前控制器名称 CONTROLLER_NAME和方法名称 ACTION_NAME
			$c = CONTROLLER_NAME;
			$a = ACTION_NAME;
			//如果是首页 不需要检测权限
			if($c == 'Index' && $a == 'index'){
				return;
			}
			//将控制器名称和方法名称 以-拼接，代表一个权限
			//将拼接的权限字符串，和$role['role_auth_ac'] 进行范围判断
			$ac = $c . '-' . $a;
			$role_auth_ac = explode(',', $role['role_auth_ac']);
			if(!in_array($ac, $role_auth_ac)){
				//没有权限
				$this -> redirect('Admin/Index/index');
			}

		}
	}
}