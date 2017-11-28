<?php
namespace Admin\Controller;
use Think\Controller;
class GoodsController extends CommonController{
	public function index(){
		//查询数据
		//实例化Goods模型
		$model = D('Goods');
		//分页展示
		//查询总记录数
		$total = $model -> count();
		//每页显示条数
		$pagesize = 2;
		//实例化Page类
		$page = new \Think\Page($total, $pagesize);
		//修改分页栏显示定制数组
		$page -> setConfig('prev', '上一页');
		$page -> setConfig('next', '下一页');
		$page -> setConfig('first', '首页');
		$page -> setConfig('last', '尾页');
		$page -> setConfig('theme', '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
		//设置分页类的属性
		$page -> rollPage = 4;
		$page -> lastSuffix = false;
		//获取分页栏的html代码 使用show方法
		$page_html = $page -> show();
		$this -> assign('page_html', $page_html);
		//查询当前页数据
		$data = $model -> limit($page -> firstRow, $page -> listRows) -> select();
		//变量赋值
		$this -> assign('data', $data);
		$this -> display();
	}

	public function add(){
		//处理两个业务逻辑 一个是页面展示 另一个是表单提交
		//根据请求方式判断
		if(IS_POST){
			//表单提交
			//接收数据
			//以下两行只是用于测试xss攻击
			// $data = $_POST;
			// $data['name'] = htmlspecialchars( $_POST['name'] );
			// echo $data['name'];die;
			$data = I('post.');
			// dump($data);die;
			// dump($_POST);
			// dump($data);die;
			//对商品简介字段 要做特殊处理 不使用I函数自带的htmlspecialchars函数做转化
			// $data['introduce'] = $_POST['introduce'];
			// $data['introduce'] = I('post.introduce', '', 'trim');
			//对富文本年编辑器字段，防范xss攻击
			// dump($data);
			$data['introduce'] = I('post.introduce', '', 'remove_xss');
			// dump($data);die;
			// dump($_FILES);die;
			//添加到数据库
			$model = D('Goods');
			//在添加商品基本数据之前，先实现文件上传，得到图片地址信息
			// if($_FILES['logo'] && $_FILES['logo']['error'] == 0){
			// 	//实例化文件上传类
			// 	//自定义配置数据
			// 	$config = array(
			// 		'maxSize'       =>  5 * 1024 * 1024, //上传的文件大小限制 (0-不做限制) byte
	  //       		'exts'          =>  array('jpg', 'png', 'gif', 'jpeg'), //允许上传的文件后缀
	  //       		'rootPath'      =>  ROOT_PATH . UPLOAD_PATH, //保存根路径
			// 	);
			// 	$upload = new \Think\Upload($config);
			// 	$upload_res = $upload -> uploadOne($_FILES['logo']);
			// 	// dump($upload_res);die;
			// 	if(!$upload_res){
			// 		//上传失败，获取错误信息
			// 		$error = $upload -> getError();
			// 		$this -> error($error);
			// 	}
			// 	//上传成功，需要拼接文件保存路径，用于添加到数据表
			// 	$data['goods_big_img'] = UPLOAD_PATH . $upload_res['savepath'] . $upload_res['savename'];

			// 	//商品logo图片上传成功，生成缩略图
			// 	// 实例化Image类
			// 	$image = new \Think\Image();
			// 	// 使用open方法打开原始图片（需要真实的文件路径）
			// 	$image -> open( ROOT_PATH . $data['goods_big_img']);
			// 	// 使用thumb方法生成缩略图（需要传递最大宽度和最大高度限制）
			// 	$image -> thumb(188, 188);
			// 	// 使用save方法保存缩略图图片（需要真实的文件路径）
			// 	$thumb_img = UPLOAD_PATH . $upload_res['savepath'] . 'thumb_' . $upload_res['savename'];
			// 	$image -> save( ROOT_PATH . $thumb_img);
			// 	//将缩略图的路径保存到$data中，最终保存到数据表
			// 	$data['goods_small_img'] = $thumb_img;
			// }
			// dump($data);
			// $create = $model -> create($data);
			// dump($create);die;
			//调用模型中我们自己封装的upload_logo
			$data = $model -> upload_logo($_FILES['logo'], $data);
			if(!$data){
				//上传过程中出错
				$error = $model -> getError();
				$this -> error($error);
			}
			if(!$model -> create($data)){
				//create方法执行失败
				//通过getError方法获取错误信息
				$error = $model -> getError();
				//直接报错到页面
				$this -> error($error);
			}
			$res = $model -> add();//使用create方法之后，这里不需要传递参数了
			if($res){
				//添加成功 跳转到列表页
				//商品基本信息添加成功，上传商品相册图片  上传时只处理goods_pics
				// $model -> upload_pics( array($_FILES['goods_pics']) );
				$files = $_FILES;
				unset($files['logo']);
				$model -> upload_pics( $files, $res ); //$res 是商品主键id

				//将商品属性对应的属性值，保存到商品属性关联表
				//商品属性 属性值 信息，在$data['attr_value']上
				foreach( $data['attr_value'] as $k => $v ){
					//$k 是attr_id   $v 是一个数组，包含一个或多个属性值
					foreach($v as $value){
						//$value 是一个属性值
						$row['goods_id'] = $res;
						$row['attr_id'] = $k;
						$row['attr_value'] = $value;
						// dump($row);
						D('GoodsAttr') -> add($row);
					}
				}
				// die;
				// $this -> redirect('Admin/Goods/index');//直接跳转，不显示提示信息
				$this -> success('添加成功', U('Admin/Goods/index'));
			}else{
				//添加失败 返回添加页面
				$this -> error('添加失败');
			}
		}else{
			//页面展示
			//查询所有商品类型。用于下拉列表展示
			$type = D('Type') -> select();
			$this -> assign('type', $type);

			//查询所有的商品分类，用于下拉列表展示
			$cate = D('Category') -> select();
			$this -> assign('cate', $cate);
			$this -> display();
		}
	}

	public function edit(){
		//一个方法处理两个逻辑
		if(IS_POST){
			//表单提交
			$data = I('post.');
			// dump($data);
			//对商品描述 富文本编辑器的字段，特殊处理，防范xss攻击
			$data['introduce'] = I('post.introduce', '', 'remove_xss');
			//实例化模型，保存数据
			//使用create方法自动创建数据集，使用字段映射、自动验证等功能
			$model = D('Goods');

			//调用模型中我们自己封装的upload_logo
			$data = $model -> upload_logo($_FILES['logo'], $data);
			if(!$data){
				//上传过程中出错
				$error = $model -> getError();
				$this -> error($error);
			}
			//如果上传了新图片，$data中会有goods_big_img字段
			if($data['goods_big_img']){
				//在保存新图片路径之前，先将旧图片路径查询到，用于后续删除
				$goods = $model -> where(['id' => $data['id']]) -> find();
			}
			if(!$model -> create($data)){
				//有错误
				$error = $model -> getError();
				$this -> error($error);
			}
			$res = $model -> save(); //使用create方法后，这里不需要传递参数
			// $res = D('Goods') -> save($data);
			if($res !== false){
				//修改成功
				//如果上传了新图片，删除旧logo图片
				if($data['goods_big_img']){
					//删除旧图片 使用PHP自带的unlink函数
					unlink( ROOT_PATH . $goods['goods_big_img']);
					unlink( ROOT_PATH . $goods['goods_small_img']);
				}

				//继续上传新的相册图片
				$files = $_FILES;
				unset($files['logo']);
				//调用Goods模型的upload_pics方法上传相册图片
				$model -> upload_pics($files, $data['id']);

				//商品属性修改
				foreach($data['attr_value'] as $k => $v){
					//$k 就是attr_id 值
					foreach($v as $attr){
						// $attr 就是 attr_value 值
						$attr_data[] = array(
							//上面添加商品时返回值就是添加成功的主键id
							'goods_id' => $data['id'],
							'attr_id' => $k,
							'attr_value' => $attr
						);
					}
				}
				// dump($attr_data);die;
				//先删除商品原来的属性
				M('GoodsAttr') -> where("goods_id={$data['id']}") -> delete();
				//多条属性数据的批量添加操作
				M('GoodsAttr') -> addAll($attr_data);
				
				$this -> success('修改成功', U('Admin/Goods/index'));
			}else{
				$this -> error('修改失败');
			}
		}else{
			//接收id参数
			$id = I('get.id');
			//查询商品信息
			$goods = D('Goods') -> where(['id' => $id]) -> find();
			$this -> assign('goods', $goods);
			//查询商品相册图片信息
			$goodspics = D('Goodspics') -> where(['goods_id' => $id]) -> select();
			$this -> assign('goodspics', $goodspics);

			//查询商品类型信息
			$type = M('Type') -> select();
			$this -> assign('type', $type);

			//获取该商品对应的商品类型对应的所有属性（tpshop_attribute表）
			$attribute = M('Attribute') -> where("type_id={$goods['type_id']}") -> select();
			//把每个属性中的可选值转化为数组（方便页面上遍历操作）
			foreach($attribute as $k => &$v){
				$v['attr_values'] = explode(',', $v['attr_values']);
			}
			unset($k, $v);
			// dump($attribute);die;
			$this -> assign('attribute', $attribute);
			// dump($goods);dump($type);die;
			//获取当前商品拥有的所有属性（tpshop_goods_attr表）
			$goods_attr = M('GoodsAttr') -> where("goods_id=$id") -> select();
			//对goods_attr做处理，转化成
			// array('attr_id' => array('attr_value','attr_value'))即：
			// array('10' => array('北京昌平'),'11'=>array('210g'),'12'=>array('原味','奶油','炭烧'))
			// 形式，方便页面判断
			$new_goods_attr = array();
			foreach($goods_attr as $k => $v){
				$new_goods_attr[ $v['attr_id'] ][] = $v['attr_value'];
			}
			unset($k, $v);
			// dump($new_goods_attr);die;
			$this -> assign('new_goods_attr', $new_goods_attr);

			//查询商品分类数据
			$category = D('Category') -> select();
			$this -> assign('category', $category);
			$this -> display();
		}
		
	}

	public function detail(){
		//接收主键id字段
		$id = I('get.id');
		//查询当前商品信息
		$goods = D('Goods') -> where(['id' => $id]) -> find();
		$this -> assign('goods', $goods);
		//查询当前商品月销售额数据
		$saleonline = D('Saleonline') -> where(['goods_id' => $id]) -> order('month asc') -> select();
		// dump($saleonline);die;
		//遍历数组，取出所有的销售额字段值
		$online = [];
		foreach ($saleonline as $k => $v) {
			$online[] = (float)$v['money'];
			// $online[] = floatval($v['money']);
		}
		//将数组转化为json格式的字符串
		$online_json = json_encode($online);
		// dump($online_json);die;
		$this -> assign('online_json', $online_json);
		$this -> display();
	}

	public function del(){
		//接收id参数
		$id = I('get.id');
		//实例化模型，删除数据
		$res = D('Goods') -> where(['id' => $id]) -> delete();
		// dump($res);die;
		if($res !== false){
			//删除成功
			$this -> success('删除成功', U('Admin/Goods/index'));
		}else{
			//删除失败
			$this -> error('删除失败');
		}
	}

	//ajax异步删除相册图片
	public function delpics(){
		//接收参数
		$id = I('post.pics_id');
		//判断参数id是否是一个数字
		if(!is_numeric($id)){
			// $id != intval($id)
			$return = array(
				'code' => 10002,
				'msg' => '参数错误'
			);
			$this -> ajaxReturn($return);
		}
		//删除数据表中的记录
		$model = D('Goodspics');
		//获取原始的信息，用于后续删除图片
		$pics = $model -> where(['id' => $id]) -> find();
		//删除数据表中的记录
		$res = $model -> where(['id' => $id]) -> delete();
		if($res !== false){
			//删除成功
			//从磁盘上删除文件
			unlink( ROOT_PATH . $pics['pics_origin']);
			unlink( ROOT_PATH . $pics['pics_big']);
			unlink( ROOT_PATH . $pics['pics_mid']);
			unlink( ROOT_PATH . $pics['pics_sma']);
			$return = array(
				'code' => 10000,
				'msg' => 'success'
			);
			$this -> ajaxReturn($return);
		}else{
			//删除失败
			$return = array(
				'code' => 10001,
				'msg' => '删除失败'
			);
			$this -> ajaxReturn($return);
		}
	}

	//ajax根据type_id获取属性信息
	public function getattr(){
		//接收type_id参数
		$type_id = I('post.type_id', 0, 'intval');
		if($type_id <= 0){
			//参数不正确
			$return = array(
				'code' => 10001,
				'msg' => '参数不正确'
			);
			$this -> ajaxReturn($return);
		}
		//根据type_id 查询商品属性表
		$data = D('Attribute') -> where(['type_id' => $type_id]) -> select();
		if($data){
			$return = array(
				'code' => 10000,
				'msg' => 'success',
				'data' => $data
			);
			$this -> ajaxReturn($return);
		}else{
			$return = array(
				'code' => 10002,
				'msg' => '没有查询到数据',
			);
			$this -> ajaxReturn($return);
		}
	}
}