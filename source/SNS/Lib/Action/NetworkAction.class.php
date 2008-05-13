<?php
class NetworkAction extends BaseAction
{
	function index() {
		$networkId	=	$_GET['id'];
		if(empty($networkId)){
			$networkId	=	1;
		}
		$dao	=	D('Network');
		$network=	$dao->find($networkId);
		if($network){
			$this->assign('net',$network);
		}else{
			header('location:'.__APP__.'/Network');
		}
		//网络会员
		$dao = D('User');
		$map	=	'networkId='.$networkId;
		$members	=	$dao->findAll($map,'id');
		foreach($members as $m){
			$membersArray[]	=	$m->id;
		}
		$voList	= $dao->findAll($map,'id,name','registerTime desc','6');
		$this->assign('user',$voList);

		$member	=	implode(',',$membersArray);

		//相册
		$dao = D('Album');
		$voList	= $dao->findAll("userId in ($member)",'*','rand()','6');
		$this->assign('album',$voList);

		//群组
		$dao = D('Group');
		$voList	= $dao->findAll("userId in ($member)",'*','rand()','6');
		$this->assign('group',$voList);

		//动态
		$dao	=	D('UserFeed');
		$today	=	date("Y-m-d",time());
		$list	=	$dao->findAll("userId in ($member)",'*','cTime desc','50');
		$this->assign('feed',$list);

		$this->display();

	}
}
?>