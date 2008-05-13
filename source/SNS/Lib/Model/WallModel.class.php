<?php
class WallModel extends Model
{
	//表单验证
	protected  $_validate = array(
		array('content','require','留言内容不能为空！'),
	);

	//自动字段填充
	protected $_auto = array(
		//array('password','md5','ADD'),
	);
}
?>