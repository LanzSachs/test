<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller{
	public function login(){
		//一个方法处理两个业务逻辑
		if(IS_POST){
			//表单提交
			$data = I('post.');
			// dump($data);
			//验证码校验 使用验证码类的check方法
			$verify = new \Think\Verify();
			$check = $verify -> check($data['code']);
			if(!$check){
				//验证码错误
				$this -> error('验证码不正确');
			}
			//查询tpshop_manager 管理员用户表
			$user = D('Manager') -> where(['username' => $data['username']]) -> find();
			if( $user && $user['password'] == encrypt_password($data['password']) ){
				//如果用户存在且密码一致，登录成功
				//设置登录标识
				session('manager_info', $user);
				$this -> success('登录成功', U('Admin/Index/index'));
			}else{
				//登录失败
				$this -> error('用户名或者密码错误');
			}
		}else{
			//展示页面
			$this -> display();
		}
	}
	//退出登录
	public function logout(){
		//清空session
		session(null);
		//跳转到登录页面
		$this -> redirect('Admin/Login/login');
	}

	//生成验证码方法
	public function captcha(){
		//实例化Verify类
		//自定义配置数组
		$config = array(
			'useCurve'  =>  false,            // 是否画混淆曲线
        	'useNoise'  =>  false,            // 是否添加杂点
        	'length'    =>  4,               // 验证码位数
		);
		$verify = new \Think\Verify($config);
		//调用entry方法生成并输出验证码图片
		$verify -> entry();
	}

	//ajax登录
	public function ajaxlogin(){
		//接收请求参数
		$data = I('post.');
		// dump($data);
		//验证码校验 使用验证码类的check方法
		$verify = new \Think\Verify();
		$check = $verify -> check($data['code']);
		if(!$check){
			//验证码错误
			// $this -> error('验证码不正确');
			$return = array(
				'code' => 10001,
				'msg' => '验证码不正确'
			);
			//返回json格式字符串
			// echo json_encode($return);
			$this -> ajaxReturn($return);
		}
		//查询tpshop_manager 管理员用户表
		$user = D('Manager') -> where(['username' => $data['username']]) -> find();
		if( $user && $user['password'] == encrypt_password($data['password']) ){
			//如果用户存在且密码一致，登录成功
			//设置登录标识
			session('manager_info', $user);
			//返回数据 10000表示登录成功
			$return = array(
				'code' => 10000,
				'msg' => 'success'
			);
			$this -> ajaxReturn($return);
		}else{
			//登录失败
			$return = array(
				'code' => 10002,
				'msg' => '用户名或者密码错误'
			);
			$this -> ajaxReturn($return);
		}
	}
}