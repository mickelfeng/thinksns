<?php
class ShareAction extends BaseAction
{
	//分享，网页page，用户peple，博客blog，相册album，照片photo，帖子thread，视频vedio，音乐music
	function _initialize(){
		parent::_initialize();
	}
	function index() {
		$uid	=	$this->uid;
		$user	=	D('User')->find($uid);

		$dao = D('UserShare');
		$map['userId']	=	$uid;
		$count	= $dao->count($map);
		$rows	=	10;
		$p		=	 new Page($count,$rows);
		$voList	= $dao->findAll($map,'*','cTime desc',$p->firstRow.','.$p->listRows);
		$page	= $p->show();
		$this->assign('list',$voList);
		$this->assign('count',$count);
		$this->assign("page",$page);

		$this->display();
	}
	function friends() {
		$friends	=	getUserFriends($this->mid);
		$friends	=	implode(',',$friends);
		$map		=	"userId in (".$friends.")";
		$dao = D('UserShare');
		$count	= $dao->count($map);
		$rows	=	10;
		$p		=	 new Page($count,$rows);
		$voList	= $dao->findAll($map,'*','cTime desc',$p->firstRow.','.$p->listRows);
		$page	= $p->show();
		$this->assign('list',$voList);
		$this->assign('count',$count);
		$this->assign("page",$page);

		$this->display();
	}
	function user() {
		$id	=	$_GET['id'];
		$content	=	getShareContent('Space',$id);
		$this->assign('module','Space');
		$this->assign('title','分享了一个用户');
		$this->assign('content',$content);
		$this->assign('recordId',$id);
		$this->display('share');
	}
	function blog() {
		$id	=	$_GET['id'];
		$content	=	getShareContent('Blog',$id);
		$this->assign('module','Blog');
		$this->assign('title','分享了一篇日志');
		$this->assign('content',$content);
		$this->assign('recordId',$id);
		$this->display('share');
	}
	function photo() {
		$id	=	$_GET['id'];
		$content	=	getShareContent('Photo',$id);
		$this->assign('module','Photo');
		$this->assign('title','分享了一张相片');
		$this->assign('content',$content);
		$this->assign('recordId',$id);
		$this->display('share');
	}
	function album() {
		$id	=	$_GET['id'];
		$content	=	getShareContent('Album',$id);
		$this->assign('module','Album');
		$this->assign('title','分享了一个相册');
		$this->assign('content',$content);
		$this->assign('recordId',$id);
		$this->display('share');
	}
	//添加分享
	function insert(){
		$map['userId']		=	$this->mid;
		$map['module']		=	$_POST['module'];
		$map['recordId']	=	$_POST['recordId'];
		$map['title']		=	$_POST['title'];
		$map['content']		=	getShareContent($map['module'],$map['recordId']);
		$map['info']		=	$_POST['info'];
		$dao = D('UserShare');
		$c = $dao->create($map);
		$result	=	$dao->add();
		if($result){
			//记录动态
			/* add_user_feed */
				$feedTitle	=	$_POST['title'];
				$feedContent	=	"<div class=\"share-content\">".$map['content']."</div>";
				$aa	=	$this->addUserFeed($this->mid,'add','share',$result,$feedTitle,$feedContent);
			/* /add_user_feed */
			$this->success("分享成功！",1);
		}else{
			$this->error("分享失败！",0);
		}
	}
	//添加网页分享
	function insertUrl(){
		$map['userId']		=	$this->mid;
		$map['module']		=	'Url';
		$map['title']		=	'分享了一个网站';
		$url	=	str_replace('http://','',$_POST['url']);
		$url	=	'http://'.$url;
		//$url_title	=	trim(getTitle($url));
		//需要解决编码问题
		$map['content']		=	"<a href=\"".$url."\" target=\"_blank\">".$url."</a>";
		$dao = D('UserShare');
		$dao->create($map);
		$result	=	$dao->add();
		if($result){
			//记录动态
			/* add_user_feed */
				$feedTitle	=	$map['title'];
				$feedContent	=	"<div class=\"share-content\">".$map['content']."</div>";
				$this->addUserFeed($this->mid,'add','share',$result,$feedTitle,$feedContent);
			/* /add_user_feed */
			$this->success("分享成功！",1);
		}else{
			$this->error("分享失败！",0);
		}
	}
	//删除分享
	function del(){
		$shareId	=	$_REQUEST['id'];
		$dao = D('UserShare');
		if($dao->find($shareId)->userId != $this->mid){
			$this->error("不能删除别人的分享！",-1);
		}
		if($dao->deleteById($shareId)){
			$this->success("删除分享成功！",1);
		}else{
			$this->error("删除分享失败！",0);
		}
	}
}
?>