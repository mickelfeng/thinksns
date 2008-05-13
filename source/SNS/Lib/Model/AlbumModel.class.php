<?php
class AlbumModel extends Model
{
	//表单验证
	protected  $_validate = array(
		array('title','require','内容不能为空！'),
	);
	//自动字段填充
	protected $_auto = array(
		array('cTime','time','ADD','function'),
		array('mTime','time','UPDATE','function'),
	);
}
?>