<?php
class CommentAction extends BaseAction
{
	function insert(){
		$userId		=	Session::get(C('USER_AUTH_KEY'));
		$recordId	=	intval($_POST['recordId']);
		if($_POST['module']=="Blog"){
			$dao	=	D('Blog');
			if($blog	=	$dao->getById($recordId)){
				$toUserId	=	$blog->userId;
				$blogTitle	=	$blog->title;
			}else{
				$this->error("文章不存在，无法回复！",false);
			}
		}
		else
		if($_POST['module']=="Photo"){
			$dao	=	D('Photo');
			if($photo	=	$dao->getById($recordId)){
				$toUserId	=	$photo->userId;
				$photoPath	=	$photo->imagePath ;
			}else{
				$this->error("图片不存在，无法回复！",false);
			}
		}
		$dao = D("Comment");
		$vo	=	$dao->create();
		$vo->cTime	=	time();
		$vo->userId	=	$userId;
		if($result = $dao->add($vo)){
			if($vo->replyType==0){
				$map = new HashMap();
				$map->put('replyId',$result);
				$replyId = $dao->save($map,"id='$result'");
			}
			$content = ubb($vo->content);
			if($_POST['module']=='Blog'){
				/* add_user_feed */
					$feedTitle	=	"评论了日志：<a href=\"/blog/{$recordId}\">{$blogTitle}</a>";
					$feedInfo	=	'<div class="share-comment"><p>'.$content.'</p></div>';
					$this->addUserFeed($userId,'add','comment',$recordId,$feedTitle,$feedInfo);
				/* /add_user_feed */
				$this->addUserAlert($toUserId,"reply".$_POST['module'],$_POST['recordId']);
			}
			elseif($_POST['module']=='Photo'){
				/* add_user_feed */
					$feedTitle	=	"评论了照片：";
					$feedInfo	=	"<p class=\"image\"><a href=\"/photo/{$recordId}\"><img src=\"".__PUBLIC__."/Thumb/?w=100&h=100&url={$photoPath}\" alt=\"照片\" /></a></p>".'<div class="share-comment"><p>'.$content.'</p></div>';
					$this->addUserFeed($userId,'add','comment',$recordId,$feedTitle,$feedInfo);
				/* /add_user_feed */
				$this->addUserAlert($toUserId,"reply".$_POST['module'],$_POST['recordId']);
			}
			//$this->success('回复成功！');
		if($_POST['module']=='Board'){
			header("Location:".__APP__."/Board");
		}else{
			echo $result;
		}
		}else{
			//$this->error('回复失败！');
			echo false;
		}
	}

	//删除评论
	function delete(){
		//echo "a";
		$dao = D("Comment");
		$result  =  $dao->deleteById($_POST['id']);
		echo $result;
	}
}
?>