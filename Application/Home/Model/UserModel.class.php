<?php
namespace Home\Model;
use Think\Model;
class UserModel extends Model{
	//自动验证
	protected $_validate = array(
		//邮箱注册
		array('email', 'require', '邮箱不能为空'),
		array('email', 'email', '邮箱格式不正确'),
		array('email', '', '邮箱已被使用', 0, 'unique'),
		//手机号注册
		array('phone', 'require', '手机号码不能为空'),
		array('phone', '/^\d{11}$/', '手机号码格式不正确'),
		array('phone', '', '手机号码已被使用', 0, 'unique'),

		array('password', 'require', '密码不能为空'),
		// array('repassword', 'require', '确认密码不能为空'),
		array('password', 'repassword', '两次密码输出必须一致', 0, 'confirm'),

	);

	//自动完成
	protected $_auto = array(
		//密码加密
		array('password', 'encrypt_password', 3, 'function'),
		//添加时间
		array('create_time', 'time', 1, 'function')
	);
}