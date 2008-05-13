<?php
class BlogModel extends Model
{
	//表单验证
	protected  $_validate = array(
		array('title','require','内容不能为空！'),
		array('content','require','内容不能为空！'),
	);
	// 更新博客阅读数
	function updateReadCount($id) {
		$sql = "UPDATE think_blog SET readCount=readCount+1 WHERE id='$id' LIMIT 1 ";
		$result = $this->execute($sql);
		if($result===FALSE){
			return false;
		}else{
			return true;
		}
	}
}
?>