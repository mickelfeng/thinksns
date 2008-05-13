<?php
/*
 * 用户状态
 * 1,已经来注册激活。灰色
 * 2,被锁定用户。 红色
 * 3,不真实用户，提醒。灰色
 * 4,真实用户。 蓝色
 * 5,VIP推荐用户。绿色
 */
class ManageAction extends BaseAction
{
	function _initialize(){
		$userId	=	Session::get(C('USER_AUTH_KEY'));
		if(!checkAdmin('quan',$userId)){
			echo "you are not a manager !";
		}
		parent::_initialize();
	}
	// 用户管理
	function index() {

		//新会员
		$dao	= D('User');
		$count	= $dao->count();
		$p	= new Page($count,20);
		$voList	= $dao->findAll('','*','id desc',$p->firstRow.','.$p->listRows);
		$page	= $p->show();
		$this->assign('count',$count);
		$this->assign('list',$voList);
		$this->assign("page",$page);
		$this->display();
	}

	// 用户的详细资料
	function profile() {
		$this->display();
	}
	// 发送站内信件
	function sendMessage() {
	}

	// 更新用户状态
	function updateStatus() {
		$dao = D('User');
		$ids	=	$_GET['id'];
		if($dao->t($ids)){
			header("location:/Manage");
		}else{
			$this->error('设置失败！');
		}
	}

	// 发送电子邮件
	function sendEmail() {
	}

	//设为真实
	function t() {
		$dao = D('User');
		$ids	=	$_GET['id'];
		if($dao->t($ids)){
			header("location:/Manage");
		}else{
			$this->error('设置失败！');
		}
	}

	//设为不真实
	function f() {
		$dao = D('User');
		$ids	=	$_GET['id'];
		if($dao->f($ids)){
			header("location:".__APP__."/Manage");
		}else{
			$this->error('设置失败！');
		}
	}

	//锁定
	function lock() {
		$dao = D('User');
		$ids	=	$_GET['id'];
		if($dao->lock($ids)){
			header("location:".__APP__."/Manage");
		}else{
			$this->error('设置失败！');
		}
	}
}
?>