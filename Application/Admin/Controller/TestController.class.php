<?php
namespace Admin\Controller;
use Think\Controller;
class TestController extends Controller{
	public function index(){
		//模型实例化
		// $model = new \Admin\Model\GoodsModel();
		//快速实例化 D函数
		// $model = D();
		// $model = D('Goods');
		// $model = D('Managers');

		//快速实例化 M函数
		// $model = M();
		// $model = M('Goods');
		// $model = M('Managers');

		//特殊表 advice表
		// $model = M('Advice', null);
		$model = D('advice');
		//获取最后一条sql语句
		dump( $model -> getLastSql() );
		dump( $model -> _sql() );
		//返回当前的sql语句，并不执行
		$res = $model -> fetchSql(true) -> select();
		dump($res);
		dump( $model -> getLastSql() );
		// dump($model);
	}

	public function chaxun(){
		//查询数据
		$model = D('Goods');
		//select方法
		// $data = $model -> select();
		// $data = $model -> select('2');
		// $data = $model -> select('2,3,4');
		//find方法
		// $data = $model -> find();
		// $data = $model -> find(2);

		//辅助查询方法where
		// $data = $model -> where("goods_number = '300'") -> select();
		// $data = $model -> where( ['goods_number' => '300'] ) -> select();
		// $data = $model -> where( ['goods_number' => ['GT', '300'] ] ) -> field('id, goods_name,goods_number') -> select();
		// dump($data);

		//连表查询
		$adviceModel = D('Advice');
		$data = $adviceModel -> alias('t1') -> field('t1.*, t2.username') -> join("left join tpshop_user as t2 on t1.user_id=t2.id") -> select();
		dump($data);
	}

	public function zhanshi(){
		//普通字符串变量
		$username = 'kongkong';
		$this -> assign('username', $username);
		//数组变量
		$person = ['name' => 'zhenzhen', 'age' => 18];
		$this -> assign('person', $person);
		//二维数组
		$team = [
			['name' => 'zhenzhen', 'age' => 18, 'sex' => 1],
			['name' => 'kongkong', 'age' => 500, 'sex' => 2],
		];
		$this -> assign('team', $team);
		// foreach ($team as $key => $value) {
		// 	# code...
		// }

		$time = time();
		$this -> assign('time', $time);
		$this -> display();
	}

	public function tianjia(){
		//向goods表添加数据
		//准备要添加的数据
		// $goods = [
		// 	'goods_name' => 'test add goods 1',
		// 	'goods_price' => 100,
		// 	'goods_number' => 10000
		// ];
		//实例化模型
		$model = D('Goods');
		//使用add方法添加一条数据
		// $res = $model -> add($goods);

		//AR方式添加
		$model -> goods_name = 'test add goods 2';
		$model -> goods_price = 200;
		$model -> goods_number = 10000;
		$res = $model -> add();
		dump($res);
	}

	public function xiugai(){
		//修改goods表中的记录
		//准备要修改的数据
		// $goods = [
		// 	'id' => 13,
		// 	'goods_name' => 'test xiugai goods',
		// ];
		//实例化模型
		$model = D('Goods');
		// $res = $model -> save($goods);

		//AR方式修改
		$model -> id = 13;
		$model -> goods_name = 'test xiugai ar';
		$res = $model -> save();
		dump($res);
	}
	//文件载入
	public function wenjian(){
		//使用自定义的密码加密函数
		$password = '123456';
		echo encrypt_password($password);

		//使用自定义的手机号加密函数
		$phone = '15312345678';
		echo '<br>';
		load('Common/str');
		echo encrypt_phone($phone);

		//类文件载入
		$page = new \Tools\Page();
		echo '<br>';
		echo $page -> getPage();
	}

	//cookie函数使用
	public function test_cookie(){
		//设置cookie
		// cookie('username', 'kongkong');
		// //读取cookie
		// $username = cookie('username');
		// dump($username);
		// //删除cookie
		// cookie('username', null);
		// //读取cookie
		// $username = cookie('username');
		// dump($username);

		//第三个参数option
		//给指定cookie设置有效期
		// cookie('username', 'zhenzhen', 3);
		cookie('username', 'zhenzhen', 'expire=3&prefix=thinkphp_');
		dump(cookie('thinkphp_username'));
		// cookie('username', 'zhenzhen', ['expire' => 3, 'prefix' => 'thinkphp_']);
	}

	//session函数
	public function test_session(){
		//设置session
		// session('username', 'konkong');
		// //读取session
		// dump( session('username') );
		// //删除session
		// // session('username', null);
		// // dump( session('username') );
		// //读取所有session
		// dump( session() );
		// //删除所有session
		// session(null);
		// dump( session() );

		//数组的存放
		session('user', ['name' => 'kongkong', 'age' => 500]);
		dump( session() );
		//使用.语法
		session('person.name', 'zhenzhen');
		dump( session() );
		dump( session('person.name') );

		session('user.sex', '男');
		dump( session() );
		//判断
		dump( session('?user') );
		dump( session('?username') );

	}
}