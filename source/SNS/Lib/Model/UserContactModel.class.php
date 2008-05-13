<?php
class UserContactModel extends Model
{
	var $tableName	=	"user_contact";
	//表单验证
	protected  $_validate = array(
		array('email','email','Email格式不对！'),
	);

	//自动字段填充
	protected $_auto = array(
	);
}
?>