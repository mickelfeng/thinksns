<?php
class ThreadPostModel extends Model
{
	var $tableName	=	'thread_post';
	//表单验证
	protected  $_validate = array(
		array('title','require','标题不能为空！'),
		array('content','require','内容不能为空！'),
	);
}
?>