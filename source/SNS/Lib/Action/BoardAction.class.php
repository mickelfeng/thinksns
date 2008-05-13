<?php
//公告板
class BoardAction extends BaseAction
{
	function index() {
		//博客评论

		$map['module']	=	'Board';
		$dao	=	D("Comment");
		$count	=	$dao->count($map);
		$rows	=	20;
		$p		=	new Page($count,$rows);
		$list	=	$dao->findAll($map,'*','replyId DESC,cTime ASC',$p->firstRow.','.$p->listRows);
		$page	=	$p->show();

		$this->assign('comments',$list);
		$this->assign('page',$page);

		$this->display();
	}
}
?>