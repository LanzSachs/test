<?php
namespace Admin\Model;
use Think\Model;
class AdviceModel extends Model{
	//通过属性指定要关联的数据表名称
	// 指定要关联的实际数据表名（包含表前缀）
    protected $trueTableName    =   'advice';
}