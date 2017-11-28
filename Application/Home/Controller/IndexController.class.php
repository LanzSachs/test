<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
	//前台首页
    public function index(){
    	//查询所有的商品分类
    	$cate = D('Category') -> select();
    	// dump($cate);die;
    	$this -> assign('cate', $cate);
    	//取指定分类下的商品 分类id为13 
    	//取分类名称信息
    	$cate_13 = D('Category') -> where(['id' => 13]) -> find();
    	$this -> assign('cate_13', $cate_13);
    	//取分类下的商品信息
    	$goods_13 = D('Goods') -> where(['cate_id' => 13]) -> limit(12) -> select();
    	$this -> assign('goods_13', $goods_13);

    	//定义页面的title
    	$this -> assign('title', '首页');
    	//调用模板文件
        $this -> display();
    }

    //商品详情页
    public function detail(){
    	//接收id参数
    	$id = I('get.id', 0, 'intval');
    	if($id <= 0){
    		$this -> error('参数不正确');
    	}
    	//查询商品表
    	$goods = D('Goods') -> where(['id' => $id]) -> find();
    	$this -> assign('goods', $goods);

    	//查询商品相册表
    	$goodspics = D('Goodspics') -> where(['goods_id' => $id]) -> select();
    	$this -> assign('goodspics', $goodspics);

    	//根据商品的type_id 查询商品属性表   $goods['type_id']
    	$attrs = D('Attribute') -> where(['type_id' => $goods['type_id']]) -> select();
    	$this -> assign('attrs', $attrs);
    	// dump($attrs);die;
    	//查询商品关联的属性值  商品属性关联表tpshop_goods_attr
    	$goodsattr = D('GoodsAttr') -> where(['goods_id' => $id]) -> select();
    	// dump($goodsattr);die;
    	$this -> assign('goodsattr', $goodsattr);
    	//定义页面的title
    	$this -> assign('title', '商品详情页');
    	$this -> display();
    }
}