<?php
class ThreadModel extends Model
{
	//表单验证
	protected  $_validate = array(
		array('title','require','标题不能为空！'),
	);
	// updateThreadReadCount
	function updateThreadReadCount($id) {
		$sql = "UPDATE think_thread SET readCount=readCount+1 WHERE id='$id' LIMIT 1 ";
		$result = $this->execute($sql);
		if($result===FALSE){
			return false;
		}else{
			return true;
		}
	}
}
?>