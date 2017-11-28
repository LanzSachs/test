<?php
namespace Admin\Controller;
class TypeController extends CommonController{
	//商品类型添加
	public function add(){
		//两个逻辑
		if(IS_POST){
			$data = I('post.');
			if(empty($data['type_name'])){
				$this -> error('类型名称不能为空');
			}
			//添加数据到数据表
			$res = D('Type') -> add($data);
			if($res){
				$this -> success('添加成功', U('Admin/Type/index'));
			}else{
				$this -> error('添加失败');
			}
		}else{
			$this -> display();
		}
	}

	//类型列表
	public function index(){
		//查询商品类型表所有数据
		$data = D('Type') -> select();
		$this -> assign('data', $data);
		$this -> display();
	}
}