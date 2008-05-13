<?php
class BlogAction extends BaseAction
{//类定义开始
	function _initialize(){
		parent::_initialize();
	}
	// 博客频道首页
	function index() {

		//博客列表
		$map['userId']	=	$this->uid;
		$dao	= D("Blog");
		$count	= $dao->count($map);
		$rows	=	10;
		$p		=	new Page($count,$rows);
		$list	=	$dao->findAll($map,'*','cTime desc',$p->firstRow.','.$p->listRows);
		$page	=	$p->show();

		//博客数量
		$this->assign('count',$count);
		$this->assign('list',$list);
		$this->assign('page',$page);
		$this->display();
	}

	//好友的博客
	function friends() {

		$friendArray	=	getUserFriends($this->mid);
		//array_push($friendArray,$this->mid);
		$friends=	implode(",",$friendArray);
		$map	=	"userId in (".$friends.")";

		$dao	=	D("Blog");
		$count	=	$dao->count($map);

		$p		=	new Page($count,10);

		$list	=	$dao->findAll($map,'*','cTime desc',$p->firstRow.','.$p->listRows);

		$page	=	$p->show();

		//博客数量
		$this->assign('count',$count);
		$this->assign('list',$list);
		$this->assign('page',$page);
		$this->display();
	}

	// 博客内容显示
	function content() {

		$blogId	=	$_GET['id'];

		//博客内容
		$map['id']	=	$blogId;
		$dao	= D("Blog");
		$list	= $dao->find($map);
		$this->assign('vo',$list);

		//更新博客计数器
		$dao->updateReadCount($blogId);

		//博客评论
		unset($map);
		$map['recordId']	=	$blogId;
		$map['module']		=	'Blog';

		$dao	= D("Comment");
		$count	= $dao->count($map);
		$list	= $dao->findAll($map);
		$this->assign('count',$count);
		$this->assign('comments',$list);
		$this->display();
	}
	// 写博客
	function write() {
		$this->display();
	}
	// 插入日志
	public function insert(){
		$title	=	$_POST['title'];
		$content	=	$_POST['content'];
		if(empty($title) || empty($content)){
			$this->error('日志不能为空！');
			exit;
		}
		$dao	=	D('Blog');
		$dao->title		=	$title;
		$dao->content	=	$content;
		$dao->cTime		=	time();
		$dao->userId	=	$this->mid;
		//$dao->tagIds	=	$this->addTag($_POST['tags']);
		if($result = $dao->add()){
			/* 可以插入日志标签 * /
				$this->addTagIndex($_POST['tags'],$result);
			/**/
			/* add_user_feed */
				$feedTitle	=	"添加了新日志";
				$blogImages	=	matchImages(stripslashes($content));
				if($blogImages){
					$feedInfo	.=	"<a href=\"/Blog/content/id/{$result}\"><img src=\"".WEB_PUBLIC_URL."/Thumb/?w=100&h=100&t=f&url={$blogImages[0]}\" alt=\"{$title}\" /></a>";
				}
				$feedInfo	.=	"<strong><a href=\"/Blog/content/id/{$result}\">{$title}</a></strong><br />".getBlogShort($content);				$this->addUserFeed($this->mid,'add','blog',$result,$feedTitle,$feedInfo);
			/* /add_user_feed */
			header('location:'.__APP__.'/blog/'.$result);
			//$this->success("添加日志成功！",$result);
		}else{
			$this->error("添加日志失败！");
		}
	}
	//我的草稿箱
	function drafts(){
		$this->display();
	}
	//编辑日志
	function _before_edit() {
		$userId	=	$this->mid;
		if(!$userId){
			$this->error("登录后查看自己的日志！");
		}
		$map = new HashMap();

		//博客内容
		$map->put('id',$_GET['id']);
		$map->put('userId',$userId);
		$dao	= D("Blog");
		if($list	= $dao->find($map)){
			$this->assign('blog',$list);
		}else{
			$this->error("不能修改别人的日志！");
		}
	}
	function update() {
		//有被别人修改的嫌疑漏洞
		$userId	=	$this->mid;
		$dao	=	D("Blog");
		$vo		=	$dao->create();
		$vo->mTime	=	time();
		//判断当前修改日志是否是自己的
		$dao = D("Blog");
		$blog = $dao->getById($_POST['id']);
		if($blog->userId == $userId){
			$vo->userId	= $userId;
			if($result  = $dao->save($vo)){
				header('location:'.__APP__.'/Blog/content/id/'.$_POST['id']);
			}else{
				$this->error('修改失败！');
			}
		}else{
			$this->error("不允许修改别人的日志！");
		}
	}
	function delete() {
		$id		=	$_GET[id];
		$userId	=	$this->mid;
		$dao	=	D("Blog");
		$action =	$dao->getById($id);
		if($action->userId == $userId){
			if($dao->deleteById($id)){
				//$this->success("删除成功！");
				header('location:'.__APP__.'/Blog/');
			}else{
				$this->error("删除失败！");
			}
		}else{
			$this->error("不允许删除别人的日志！");
		}
	}
}//类定义结束
?>