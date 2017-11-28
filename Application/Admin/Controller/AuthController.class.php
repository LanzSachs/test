<?php
namespace Admin\Controller;
class AuthController extends CommonController{
	//权限列表
	public function index(){
		//查询所有的权限
		$auth = D('Auth') -> select();
		// dump($auth);
		$auth = getTree($auth);
		// dump($auth);die;
		$this -> assign('auth', $auth);
		$this -> display();
	}

	//权限添加
	public function add(){
		//一个方法两个逻辑
		if(IS_POST){
			//表单提交，接收数据
			$data = I('post.');
			// dump($data);die;
			$res = D('Auth') -> add($data);
			if($res){
				//添加成功
				$this -> success('添加成功', U('Admin/Auth/index'));
			}else{
				//添加失败
				$this -> error('添加失败');
			}
		}else{
			//查询权限表中所有的顶级权限，用于页面下拉列表展示
			$top_all = D('Auth') -> where("pid = 0") -> select();
			$this -> assign('top_all', $top_all);
			$this -> display();
		}
		
	}

	//权限编辑
	public function edit(){
		if(IS_POST){
			$data = I('post.');
			if(empty($data['auth_name'])){
				$this -> error('必填项不能为空');
			}
			//如果要将当前权限修改为二级权限，且其下还有子权限，则阻止
			if($data['pid'] != 0 && M('Auth') -> where(['pid' => $data['id']]) -> find() ){
				$this -> error('当前权限下有子权限，无法修改为二级权限');
			}
			$res = M('Auth') -> save($data);
			if($res !== false){
				$this -> success('操作成功', U('Admin/Auth/index'));
			}else{
				$this -> error('操作失败');
			}
		}else{
			$id = I('get.id', 0, 'intval');
			if($id <= 0){
				$this -> error('参数错误');
			}
			//获取当前权限信息
			$auth = M('Auth') -> find($id);
			$this -> assign('auth', $auth);
			
			if($auth['pid'] == 0 && M('Auth') -> where(['pid' => $id]) -> find() ){
				$top = [];
			}else{
				//获取顶级权限
				$top = M('Auth') -> where("pid = 0 and id != $id") -> select();
			}
			
			$this -> assign('top', $top);
			$this -> display();
		}
	}

	//权限删除
	public function del(){
		$id = I('get.id', 0, 'intval');
		if($id <= 0){
			$this -> error('参数错误');
		}
		$info = M('Auth') -> where(['pid' => $id]) -> find();
		if($info){
			$this -> error('当前权限下有子权限，无法删除');
		}
		$res = M('Auth') -> delete($id);
		if($res !== false){
			$this -> success('操作成功', U('Admin/Auth/index'));
		}else{
			$this -> error('操作失败');
		}
	}
}