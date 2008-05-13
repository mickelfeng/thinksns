<?php
class MessageAction extends BaseAction
{
	function _initialize(){
		parent::_initialize();
	}
	// 收件箱
	function index() {
		$map = new HashMap();
		$map->put('toUserId',$this->mid);
		$dao	= D("Message");
		$count	= $dao->count($map);

		$this->assign('msgNum',$count);
		$rows  =  '10';
		$p	= new Page($count,$rows);
		$voList	= $dao->findAll("toUserId='$this->mid'",'*','cTime desc',$p->firstRow.','.$p->listRows);
		$page	= $p->show();
		$this->assign('list',$voList);
		$this->assign("page",$page);
		$this->display();
	}
	// 通知
	function notify() {
		$map = new HashMap();
		$map->put('toUserId',$this->mid);
		$dao	= D("Alert");
		$count	= $dao->count($map);
		$this->assign('msgNum',$count);
		$rows  =  '10';
		$p	= new Page($count,$rows);
		$voList	= $dao->findAll("toUserId='$this->mid'",'*','cTime desc',$p->firstRow.','.$p->listRows);
		$page	= $p->show();
		$this->assign('list',$voList);
		$this->assign("page",$page);
		$this->display();
	}
	// 发件箱
	function sendbox() {
		$map = new HashMap();
		$map->put('fromUserId',$this->mid);
		$dao	= D("Message");
		$count	= $dao->count($map);
		$this->assign('msgNum',$count);
		$rows  =	'10';
		$p	= new Page($count,$rows);
		$voList	= $dao->findAll($map,'*','cTime desc',$p->firstRow.','.$p->listRows);
		$page	= $p->show();
		$this->assign('list',$voList);
		$this->assign("page",$page);
		$this->display();
	}
	//发送
	function send() {
		$toUserId	=	$_GET['to'];
		$map['userId']	=	$this->mid;
		$friends = D('UserFriend')->findAll($map);
		$this->assign('friends',$friends);

		$this->assign('toUserId',$toUserId);
		$this->assign('fromUserId',$this->mid);

		$this->display();
	}
	//阅读
	function read() {
		$id		=	$_GET['id'];
		$dao	=	D('Message');
		$message = $dao->find("id='$_GET[id]'");
		//只能阅读自己的短消息
		if($message->toUserId==$this->mid){
			//标记已读
			$map['lastReadTime'] = time();
			$map['status'] = 1;
			$dao->save($map,"id='$id'");

			$this->assign('vo',$message);
			$this->display();
		}else{
			$this->error("不能阅读这封短消息！");
		}
	}
	//插入
	function insert() {
		$toUserId	=	$_POST["toUserId"];
		$dao = D('Message');
		$dao->create();
		$dao->fromUserId=$this->mid;
		$result = $dao->add();
		if($result){
			$this->addUserAlert($toUserId,"sendMessage");
			header('location:'.__APP__.'/Message/sendbox');
			//$this->success("发送成功！",$result);
		}else{
			$this->error("发送失败！");
		}
	}
	//删除消息
	function delete() {
	}
}
?>