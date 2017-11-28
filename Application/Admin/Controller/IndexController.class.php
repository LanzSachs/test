<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController{
	// //构造方法
	// public function __construct(){
	// 	//先实现父类的构造方法
	// 	parent::__construct();
	// 	//当前构造方法的代码
	// 	if(!session('?manager_info')){
	// 		//没有登录标识，跳转到登录页
	// 		$this -> redirect('Admin/Login/login');
	// 	}
	// }
	//后台首页
	public function index(){
		$this -> display();
	}
}