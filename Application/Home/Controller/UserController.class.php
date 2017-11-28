<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends Controller{
	//注册
	public function register(){
		//两个逻辑
		if(IS_POST){
			//表单提交
			$data = I('post.');
			// dump($data);die;
			$model = D('User');
			if( !$model -> create($data) ){
				//发生错误
				$error = $model -> getError();
				$this -> error($error);
			}
			//添加数据到数据表
			$res = $model -> add();
			if($res){
				//注册成功
				$this -> success('注册成功', U('Home/User/login'));
			}else{
				// 注册失败
				$this -> error('注册失败');
			}
		}else{
			//临时关闭模板布局
			layout(false);
			$this -> display();
		}
		
	}

	//登录
	public function login(){
		//两个逻辑
		if(IS_POST){
			//表单提交
			$data = I('post.');
			// dump($data);
			//根据用户名查询用户表
			$user = D('User') -> where("email = '{$data['username']}' or phone = '{$data['username']}'") -> find();
			if($user && $user['password'] == encrypt_password( $data['password']) ){
				//登录成功
				//设置登录标识
				session('user_info', $user);
				//登录成功，跳转到首页
				$this -> success('登录成功', U('Home/Index/index'));
			}else{
				//登录失败
				$this -> error('用户名或密码错误');
			}
		}else{
			//关闭模板布局
			layout(false);
			$this -> display();
		}
		
	}

	//退出
	public function logout(){
		//清空session中的登录标识
		session(null);
		//跳转到首页
		$this -> redirect('Home/Index/index');
	}
}