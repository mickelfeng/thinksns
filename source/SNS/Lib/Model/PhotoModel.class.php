<?php
class PhotoModel extends Model
{
	//表单验证
	protected  $_validate = array(
		array('title','require','标题不能为空！'),
	);

	function updateAlbum($albumId='0') {
		$photos	=	array();
		$sql	=	"SELECT id FROM think_photo WHERE albumId='$albumId'";
		$result	=	$this->query($sql);
		foreach($result as $v){
			 $photos[]	=	$v->id;
		}
		$count	=	count($photos);
		$photoIds	=	implode(',',$photos);
		$sql	=	"UPDATE think_album SET photoCount='$count',photoIds='$photoIds' WHERE id='$albumId'";
		return $this->execute($sql);
	}
	function setAlbumCover($albumId='0',$coverPhotoId='') {
		$sql	=	"UPDATE think_album SET coverPhotoId='$coverPhotoId' WHERE id='$albumId'";
		return $this->execute($sql);
	}
	// 更新图片阅读数
	function updateReadCount($id) {
		$sql = "UPDATE think_photo SET readCount=readCount+1 WHERE id='$id' LIMIT 1 ";
		$result = $this->execute($sql);
		if($result===FALSE){
			return false;
		}else{
			return true;
		}
	}
}
?>