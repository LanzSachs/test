<?php
namespace Admin\Controller;
class AttributeController extends CommonController{
	//商品属性添加
	public function add(){
		//两个逻辑
		if(IS_POST){
			//表单提交
			$data = I('post.');
			// dump($data);
			//直接添加到属性表
			$res = D('Attribute') -> add($data);
			if($res){
				$this -> success('添加成功', U('Admin/Attribute/index'));
			}else{
				$this -> error('添加失败');
			}
		}else{
			//查询所有的商品类型，用于下拉列表展示
			$type = D('Type') -> select();
			$this -> assign('type', $type);
			$this -> display();
		}
		
	}

	//商品属性列表
	public function index(){
		//查询属性表数据  多条数据，连表tpshop_type类型表查询类型名称
		$data = D('Attribute') -> alias('t1') -> field('t1.*, t2.type_name') -> join("left join tpshop_type t2 on t1.type_id = t2.type_id ") -> select();
		// dump($data);die;
		$this -> assign('data', $data);
		$this -> display();
	}
}