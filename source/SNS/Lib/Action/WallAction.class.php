<?php
import('@.Action.BaseAction');
class WallAction extends BaseAction
{
	function index() {
		$userId	=	$this->uid;
		//我的留言
		$dao = D('Wall');
		$map	= new HashMap();
		$map->put("userId",$userId);

		$count	= $dao->count($map);
		$this->assign('count',$count);
		$listRows  =  10;
		$p	= new Page($count,$listRows);
		$voList	= $dao->findAll($map,'*','cTime desc',$p->firstRow.','.$p->listRows);
		$page	= $p->show();
		$this->assign('userId',$userId);
		$this->assign('guestId',$this->mid);
		$this->assign('list',$voList);
		$this->assign("page",$page);
		$this->display();
	}

	function insert(){
		//验证留言
		if($_POST['content'] == '') {
			$this->error('内容不能为空！');
			return false;
        }
		if(strlen($_POST['content'])>300) {
			$this->error('标题不能超过100个汉字！');
			return false;
        }
		//增加留言
		$dao = D("Wall");
		$vo	=	$dao->create();
		$vo->cTime	=	time();
		if($result = $dao->add($vo)){
			$this->addUserAction("addWall",$result,$vo->userId);
			$this->addUserAlert($friendId,"addFriend");
		    $this->myAjaxRetrun($result);

		}else{
			 $this->myAjaxRetrun($result);
		}
	}

	function delete(){
		$dao = D("Wall");
		$result  =  $dao->deleteById($_POST['id']);
		$this->myAjaxRetrun($result);
	}

}
?>