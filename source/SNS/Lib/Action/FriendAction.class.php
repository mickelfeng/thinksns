<?php
class FriendAction extends BaseAction
{
	function _initialize(){
		parent::_initialize();
	}
	function index() {
		$map['userId']	=	$this->uid;
		$dao	= D("UserFriend");
		$count	= $dao->count($map);
		$this->assign('friendNum',$count);
		$listRows  =  "10";
		$p	= new Page($count,$listRows);
		$voList	= $dao->findAll($map,'*','cTime desc',$p->firstRow.','.$p->listRows);
		$page	= $p->show();
		$this->assign('list',$voList);
		$this->assign('page',$page);
		$this->display();
	}
	function rand() {
		$dao	= D("User");
		$voList	= $dao->findAll('','*','rand()','10');
		$this->assign('list',$voList);
		$this->display();
	}
	function invite() {
		$dao = D('User');
		$list = $dao->find("id='$this->mid'");
		$this->assign('user',$list);
		$this->assign('callback',$_GET['callback']);
		$this->display();
	}

	function mkfriend() {
		$this->assign("toUserId",$_GET['id']);
		$this->display();
	}

	function insert(){
		//Fantasy新加上的这句判断，为了在home页添加好友用
		if($_POST['id'] != ''){
			$friendId = $_POST['id'];
		}else{
			$friendId	=	$this->uid;
		}
		$userId		=	$this->mid;
		$dao = D('UserFriend');
		$result1	=	$dao->addFriend($friendId,$userId);
		$result2	=	$dao->addFriend($userId,$friendId);
		if( $result1 > 0 && $result2 > 0 ){
			//记录动态，发送通知
			/* add_user_feed */
				$feedTitle	=	"和 <a href=\"/Space/index/id/{$friendId}\">".getUserName($friendId)."</a> 成为好友";
				$this->addUserFeed($userId,'add','friend',$friendId,$feedTitle);
			/* /add_user_feed */

			/* add_user_feed */
				$feedTitle	=	"和 <a href=\"/Space/index/id/{$userId}\">".getUserName($userId)."</a> 成为好友";
				$this->addUserFeed($friendId,'add','friend',$userId,$feedTitle);
			/* /add_user_feed */
			$this->addUserAlert($friendId,"makeFriend");
			//$this->success("你们已经成为好朋友！");
			//echo true;
			$this->myAjaxRetrun(true);
		}else{
			//$this->error("添加好友失败！");
			//echo false;
			$this->myAjaxRetrun(false);
		}
	}

	function del(){
		$friendId	=	$this->uid;
		$userId		=	$this->mid;
		$dao = D('UserFriend');
		if($dao->delFriend($friendId,$userId)){
			$this->success("删除好友成功！");
		}else{
			$this->error("删除好友失败！");
		}
	}
	function search() {
		if(empty($_REQUEST['keyword'])){

		}else{
			$name	=	$_REQUEST['keyword'];
			$map	=	"name like '%".$name."%'";
			$dao = D('User');
			$count	= $dao->count();
			$listRows  =  "50";
			$p	= new Page($count,$listRows);
			$voList	= $dao->findAll($map,'*','id desc',$p->firstRow.','.$p->listRows);
			$page	= $p->show();
			$this->assign('list',$voList);
			$this->assign("page",$page);
		}
		$this->display();
	}
	function dosearch() {
		$map = new HashMap();
		$dao = D('User');
		//find($condition,$table,$fields,$cache,$relation)
		$list = $dao->findAll($map);
		$this->ajaxReturn($list->_elements,'会员列表','1','json');
	}
	function grabber() {
		$userId	=	$this->mid;
		$user	=	D('User')->find($userId);
		$this->assign('user',$user);
		$this->display();
	}
	function getFromMsn() {
		if(isset($_POST['msn-name']) && isset($_POST['msn-password'])){
			$username	=	$_POST['msn-name'];
			$password	=	$_POST['msn-password'];
			$emails	=	array();
			$emails	=	$this->getMSN($username,$password);
			$dao = D('User');
			$myemail	=	$dao->find($this->mid)->email;
			foreach($emails as $user){
				if($dao->count("email='$user[0]'")>0){
					if($myemail!=$user[0]){
						$email_in[] = $user;
					}
				}else{
					$email_out[] = $user;
				}
			}
			$this->assign('inCount',count($email_in));
			$this->assign("in",$email_in);
			$this->assign('outCount',count($email_out));
			$this->assign("out",$email_out);
			$this->display();
		}
	}
	private function getMSN($username,$password) {
		set_time_limit(120);
		import('@.Util.MSN');
		$msn	=	new msn;
		return $msn->qGrab($username, $password);
	}
	private function get163($username,$password) {
		set_time_limit(120);
		import('@.util.Grabber_163');
		$get163	=	new Grabber_163;
		return $get163->get($username, $password);
	}
	function inviteEmail() {
		$toEmails		=	$_POST['invites'];
		$emailCount		=	count($toEmails);
		$dao = D("User");
		$userInfo  = $dao->getById(Session::get(C('USER_AUTH_KEY')));
		foreach ($toEmails as $k=>$toEmail){
			$rand	=	rand(1111,9999);
			$toEmail =  trim($toEmail);
			$code	 =	base64_encode($toEmail."|".md5($toEmail.$rand));
			$map = new HashMap();
			$map->put('email',$toEmail);
			$map->put('active',$rand);
			$map->put('inviteUser',Session::get(C('USER_AUTH_KEY')));
			$dao = D('User');
			if($id = $dao->add($map)){
				$code	=	"http://".$_SERVER['HOST_NAME'].__APP__."/Public/activate/code/".$code;
				$face	=	str_replace(WEB_PUBLIC_URL.'/Uploads/',"http://".$_SERVER['HOST_NAME'].WEB_PUBLIC_URL.'/Uploads/',$userInfo->face);
				$this->assign('fromuser',$userInfo->name);
				$this->assign('code',$code);
				$this->assign('face',$face);
				$content	=	$this->fetch('mail');
				$title		=	$userInfo->name."邀请您体验ThinkSNS！\n";
				if(sendemail($toEmail,$title,$content)){
					$success_info .= "<li>$toEmail 发送成功！</li>\n";
				}else{
					$success_info .= "<li><font color=red>$toEmail 发送失败！</font></li>\n";
				}
			}
		}
		$this->assign('count',$emailCount);
		$this->assign('info',$success_info);
		$this->display();
	}
	function hello() {
		$dao = D("Hello");
		$dao->fromUserId	=	$this->mid;
		$dao->toUserId		=	$_POST['id'];
		$dao->cTime = time();
		if($result = $dao->add()){
			$this->addUserAlert($_POST['id'],"sayHello");
			//echo true;
			$this->myAjaxRetrun(true);
		}else{
			//echo 0;
			$this->myAjaxRetrun(false);
		}
	}
	function add() {
		$friendId	=	$_POST['id'];
		$userId		=	$this->mid;
		if($friendId==$userId){
			//$this->error("不能加自己为好友！");
			echo '-2';
			exit;
		}
		$dao		=	D('UserFriend');
		$count		=	$dao->count("friendId='$friendId' AND userId='$userId'");
		if($count > 0){
			//$this->error("对方已经在你的好友名单中！");
			echo '-1';
			exit;
		}else{
			$this->addUserAlert($friendId,"addFriend");
			echo '1';
			//$this->success("成功发送好友请求！");
		}
	}

	function newadd() {
		$friendId	=	$_POST['id'];
		$userId		=	$this->mid;
		if($friendId==$userId){
			//$this->error("不能加自己为好友！");
			echo '-2';
			exit;
		}
		$dao		=	D('UserFriend');
		$count		=	$dao->count("friendId='$friendId' AND userId='$userId'");
		if($count > 0){
			//$this->error("对方已经在你的好友名单中！");
			echo '-1';
			exit;
		}else{
			$this->addUserAlert($friendId,"addFriend");
			echo '1';
			//$this->success("成功发送好友请求！");
		}
	}
}
?>