<?php
class MiniAction extends BaseAction{

	protected  function _initialize(){
		parent::_initialize();
	}
	public function index() {
		$map['userId']	=	$this->uid;

		$dao	=	D('Mini');
		$count	=	$dao->count($map);
		$rows	=	20;
		$p		=	new Page($count,$rows);
		$list	=	$dao->findAll($map,'*','cTime desc',$p->firstRow.','.$p->listRows);
		$page	=	$p->show();

		$this->assign('count',$count);
		$this->assign('mini',$list);
		$this->assign('page',$page);
		$this->display();
	}
	public function friends() {
		$friendArray	=	getUserFriends($this->mid);
		array_push($friendArray,$this->mid);
		$friends=	implode(",",$friendArray);
		$map	=	"userId in (".$friends.")";

		$dao	=	D('Mini');
		$count	=	$dao->count($map);
		$rows	=	20;
		$p		=	new Page($count,$rows);
		$list	=	$dao->findAll($map,'*','cTime desc',$p->firstRow.','.$p->listRows);
		$page	=	$p->show();

		$this->assign('count',$count);
		$this->assign('mini',$list);
		$this->assign('page',$page);
		$this->display();
	}
	public function insert(){
		$dao	=	D('Mini');
		$dao->create();
		$dao->userId	=	$this->mid;
		$dao->tagId		=	$this->addTag($_POST['tag']);
		if($result = $dao->add()){
			/* 可以插入心情标签 * /
				$this->addTagIndex($_POST['tag'],$result);
			/**/
			/* add_user_feed */
				$feedTitle	=	"更新了心情：{$_POST[content]}";
				$this->addUserFeed($this->mid,'add','miniblog',$result,$feedTitle);
			/* /add_user_feed */
			$this->success("添加心情成功！",$result);
		}else{
			$this->error("添加心情失败！");
		}
	}
	public function delete(){
		parent::delete('Mini');
	}
}//类定义结束
?>