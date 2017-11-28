<?php
namespace Admin\Controller;
class RoleController extends CommonController{
	//列表页
	public function index(){
		//查询所有的角色信息
		$data = D('Role') -> select();
		$this -> assign('data', $data);
		$this -> display();
	}

	//为角色分配权限
	public function setauth(){
		//一个方法两个逻辑
		if(IS_POST){
			//表单提交
			$data = I('post.');
			// dump($data);
			//需要组装一条角色数据，修改到角色表
			$row['role_id'] = $data['role_id'];
			$row['role_auth_ids'] = implode(',', $data['id']);
			//$row中还需要role_auth_ac字段
			//根据role_auth_ids 查询权限表数据
			$auth = D('Auth') -> where("id in ({$row['role_auth_ids']})") -> select();
			// dump($auth);
			foreach($auth as $k => $v){
				//如果是顶级权限，没有控制器和方法名，不拼接
				if($v['pid'] > 0){
					$row['role_auth_ac'] .= $v['auth_c'] . '-' . $v['auth_a'] . ',';
				}
			}
			//去掉最后一个,
			$row['role_auth_ac'] = trim($row['role_auth_ac'], ',');
			// dump($row);die;
			//将$row数据保存到角色表
			$res = D('Role') -> save($row);
			if($res !== false){
				//成功
				$this -> success('操作成功', U('Admin/Role/setauth', ['role_id' => $data['role_id']]));
				//"role_id={$data['role_id']}"
			}else{
				//失败
				$this -> error('操作失败');
			}
		}else{
			//查询角色信息
			$role_id = I('get.role_id');
			$role = D('Role') -> where(["role_id" => $role_id]) -> find();
			$this -> assign('role', $role);
			//查询所有的顶级、二级权限
			$top_all = D('Auth') -> where(" pid = 0") -> select();
			$second_all = D('Auth') -> where(" pid > 0") -> select();
			$this -> assign('top_all', $top_all);
			$this -> assign('second_all', $second_all);
			$this -> display();
		}
		
	}

	//角色新增
	public function add(){
		if(IS_POST){
			$data = I('post.');
			if(empty($data['role_name'])){
				$this -> error('必填项不能为空');
			}
			$res = M('Role') -> add($data);
			if($res){
				$this -> success('操作成功', U('Admin/Role/index'));
			}else{
				$this -> error('操作失败');
			}
		}else{
			$this -> display();
		}
	}
	//角色编辑
	public function edit(){
		if(IS_POST){
			$data = I('post.');
			if(empty($data['role_name'])){
				$this -> error('必填项不能为空');
			}
			$res = M('Role') -> save($data);
			if($res !== false){
				$this -> success('操作成功', U('Admin/Role/index'));
			}else{
				$this -> error('操作失败');
			}
		}else{
			$id = I('get.role_id', 0, 'intval');
			if($id <= 0){
				$this -> error('参数错误');
			}
			$role = M('Role') -> find($id);
			$this -> assign('role', $role);
			$this -> display();
		}
	}
	//角色删除
	public function del(){
		$id = I('get.role_id', 0, 'intval');
		if($id <= 0){
			$this -> error('参数错误');
		}
		$manager = M('Manager') -> where(['role_id' => $id]) -> find();
		if($manager){
			$this -> error('当前角色使用中，无法删除');
		}
		$res = M('Role') -> delete($id);
		if($res !== false){
			$this -> success('操作成功', U('Admin/Role/index'));
		}else{
			$this -> error('操作失败');
		}
	}
}