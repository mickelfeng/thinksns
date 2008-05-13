<?php
class MessageModel extends Model
{
	//表单验证
	protected  $_validate = array(
		array('title','require','标题不能为空！'),
		array('content','require','内容不能为空！'),
	);
	protected $_auto	=	array(
		array('status',0,'ADD'),
	);
}
?>