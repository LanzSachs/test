<?php
// 声明命名空间
namespace Admin\Model;
//引入父类模型
use Think\Model;
//定义当前模型类
class GoodsModel extends Model{
	//属性和方法
	//字段映射
	protected $_map = array(
		//form表单中的字段名 =>  数据表中对应的字段名
		'name' => 'goods_name',
		'price' => 'goods_price',
		'number' => 'goods_number',
		'introduce' => 'goods_introduce'
	);

	//自动验证
	protected $_validate = array(
		//array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
		// array('goods_name', 'require', '商品名称不能为空', 0, 'regex', 3),
		array('goods_name', 'require', '商品名称不能为空'),
		array('goods_price', 'require', '商品价格不能为空'),
		array('goods_price', 'currency', '商品价格格式不正确'),
		array('goods_number', 'require', '商品数量不能为空'),
		array('goods_number', 'number', '商品数量格式不正确')
	);

	//自动完成
	protected $_auto = array(
		// array(完成字段1,完成规则,[完成条件,附加规则]),
		array('goods_create_time','time', 1, 'function'),
	);

	//封装一个上传logo图片的方法
	public function upload_logo($file, $data){
		//在添加商品基本数据之前，先实现文件上传，得到图片地址信息
			if($file && $file['error'] == 0){
				//实例化文件上传类
				//自定义配置数据
				$config = array(
					'maxSize'       =>  5 * 1024 * 1024, //上传的文件大小限制 (0-不做限制) byte
	        		'exts'          =>  array('jpg', 'png', 'gif', 'jpeg'), //允许上传的文件后缀
	        		'rootPath'      =>  ROOT_PATH . UPLOAD_PATH, //保存根路径
				);
				$upload = new \Think\Upload($config);
				$upload_res = $upload -> uploadOne($file);
				// dump($upload_res);die;
				if(!$upload_res){
					//上传失败，获取错误信息
					$error = $upload -> getError();
					//将上传类的错误信息，记录到模型类上error属性上
					$this -> error = $error;
					return false;
				}
				//上传成功，需要拼接文件保存路径，用于添加到数据表
				$data['goods_big_img'] = UPLOAD_PATH . $upload_res['savepath'] . $upload_res['savename'];

				//商品logo图片上传成功，生成缩略图
				// 实例化Image类
				$image = new \Think\Image();
				// 使用open方法打开原始图片（需要真实的文件路径）
				$image -> open( ROOT_PATH . $data['goods_big_img']);
				// 使用thumb方法生成缩略图（需要传递最大宽度和最大高度限制）
				$image -> thumb(188, 188);
				// 使用save方法保存缩略图图片（需要真实的文件路径）
				$thumb_img = UPLOAD_PATH . $upload_res['savepath'] . 'thumb_' . $upload_res['savename'];
				$image -> save( ROOT_PATH . $thumb_img);
				//将缩略图的路径保存到$data中，最终保存到数据表
				$data['goods_small_img'] = $thumb_img;
				//数据处理成功，返回新的$data
				return $data;
			}else{
				return $data;
			}
	}

	//封装一个上传相册图片的方法
	public function upload_pics($files, $goods_id){
		//判断是否有文件需要被上传  去error中的最小值，为0表示有文件需要被上传
		if( min($files['goods_pics']['error']) != 0 ){
			return false;
		}
		//实例化文件上传类
		//自定义配置数据
		$config = array(
			'maxSize'       =>  5 * 1024 * 1024, //上传的文件大小限制 (0-不做限制) byte
    		'exts'          =>  array('jpg', 'png', 'gif', 'jpeg'), //允许上传的文件后缀
    		'rootPath'      =>  ROOT_PATH . UPLOAD_PATH, //保存根路径
		);
		$upload = new \Think\Upload($config);
		$upload_res = $upload -> upload($files);
		// dump($upload_res);die;
		if(!$upload_res){
			//上传失败，获取错误信息
			$error = $upload -> getError();
			//将上传类的错误信息，记录到模型类上error属性上
			$this -> error = $error;
			return false;
		}
		//上传成功  $upload_res是一个二维数组  其中每一个文件都需要添加到相册表
		foreach($upload_res as $k => $v){
			//原始图片路径
			$origin_pics = UPLOAD_PATH . $v['savepath'] . $v['savename'];
			//生成三张不同尺寸的缩略图
			$image = new \Think\Image();
			$image -> open( ROOT_PATH . $origin_pics);

			//生成800*800尺寸缩略图
			$image -> thumb(800, 800);
			$pics_800 = UPLOAD_PATH . $v['savepath'] . 'thumb800_' . $v['savename'];
			$image -> save( ROOT_PATH . $pics_800);

			//生成350*350尺寸缩略图
			$image -> thumb(350, 350);
			$pics_350 = UPLOAD_PATH . $v['savepath'] . 'thumb350_' . $v['savename'];
			$image -> save( ROOT_PATH . $pics_350);

			//生成50*50尺寸缩略图
			$image -> thumb(50, 50);
			$pics_50 = UPLOAD_PATH . $v['savepath'] . 'thumb50_' . $v['savename'];
			$image -> save( ROOT_PATH . $pics_50);

			//将所有的图片路径添加到相册表
			$row = array(
				'goods_id' => $goods_id,
				'pics_origin' => $origin_pics,
				'pics_big' => $pics_800,
				'pics_mid' => $pics_350,
				'pics_sma' => $pics_50,
			);
			//添加到相册表
			D('Goodspics') -> add($row);
		}
		return true;
	}
}