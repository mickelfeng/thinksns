<?php
class AlbumModel extends Model
{
	//表单验证
	protected  $_validate = array(
		array('title','require','内容不能为空！'),
	);
}
?>