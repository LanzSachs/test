<?php
// ①声明命名空间 namespace关键字  分组目录名\控制器目录名
namespace Home\Controller;
// ②引入父类控制器 use关键字 命名空间\类名称
use Think\Controller;
// ③定义当前控制器类 继承父类控制器
class TestController extends Controller{
	//定义一些方法
	public function index(){
		// echo 'This is Test index';
		//使用U函数
		// echo U('Home/Test/index');
		// echo '<br>';
		// echo U('Home/Test/index', 'id=100&page=10', true, false);
		// echo '<br>';
		// echo U('Home/Test/index', 'id=100&page=10', true, true);
		//调用模板文件
		// $this -> display();
		// $this -> display('index');
		// $this -> display('/Test/index');

		//dump函数
		$person = array(
			'name' => 'kongkong',
			'age' => 500
		);
		// echo '<pre>';
		// var_dump($person);
		dump($person);
	}
}