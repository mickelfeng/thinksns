<?php
class PublicAction extends Action {

	public function index(){
		$this->redirect('checklogin');
	}

	public function login(){
		$this->display();
	}

	//登录检测
	public function checklogin(){

		$userEmail		=	isset($_POST["email"]) ? $_POST["email"] : Cookie::get("email");
		$userPassword	=	isset($_POST["password"]) ? md5($_POST["password"]) : Cookie::get("password");

		//验证用户信息
		$userDao	=	D('User');
		$user		=	$userDao->find("email='$userEmail' and password='$userPassword'",'id,name,email,sex');
		//验证成功
		if($user){

			//更新登录时间
			$userDao->setField('lastLoginTime',time(),"id=".$user->id);
			//D('Login')->add("userId='$user->id'");
			//记录登陆状态
			Session::set('mid',$user->id);
			Session::set('userInfo',$user);
			Cookie::set('email',$userEmail,36000000);
			//记住登录
			if($_POST["autologin"] == 'on'){
				Cookie::set("password",$userPassword,36000000);
			}
			unset($userEmail,$userPassword);
			//跳转到Home页
			$this->redirect('index','Home');
		//验证失败
		}else{
			//跳转到登陆页面
			$this->redirect("login");

		}

	}
	//注册
	public function reg(){
		if($_GET['code']){
			$code	=	jiemi($_GET['code']);
			$user	=	D('User')->find($code);
			$this->assign('invite',$user);
			$this->assign('beInvite','1');
		}elseif(!C('SITE_OPEN')){
			$this->error('开放注册已关闭，请通过好友邀请注册。');
		}
		$list	=	D('Network')->findAll('id>1');
		$this->assign('networks',$list);
		$this->display();
	}
	//注册操作
	public function doreg(){
		if(isset($_POST["email"])){
			$dao	=	D("User");
			$user	=	$dao->create();
			$dao->password	=	md5($_POST["password"]);
			if($userId = $dao->add()){
				$this->mid	=	$userId;
				$userInfo['name']	=	$user->name;
				$userInfo['email']	=	$user->email;
				$userInfo['sex']	=	$user->sex;
				Session::set('mid',$userId);
				Session::set('userInfo',$userInfo);
				$this->redirect("face","Account");
			}else{
				$this->error("注册失败！");
			}
		}else{
			$this->redirect("reg");
		}
	}
	//找回密码
	public function findpassword(){
		$this->display();
	}

	//发送修改链接 到信箱
	public function sendpassword(){
		$email	=	$_POST["email"];
		$dao	=	D('User');
		$record =	$dao->find("email='$email'");
		if($record){
			$code	=	jiami($email,'thinksns');
			import('@.Util.sendmail');
			$title	=	"ThinkSNS 密码修改";
			$content	=	'请点击下面的链接修改密码<br/><a href="http://'.$_SERVER['HTTP_HOST'].__APP__.'/Public/changepassword/code/'.$code.'">点此修改密码</a>';
			if(sendemail($email,$title,$content)){
				$this->success('发送成功！');
			}else{
				$this->error('发送失败！');
			}
		}else{
			$this->error("没有这个帐号！");
		}
	}
	public function changePassword() {
		$email	=	jiemi($_GET['code'],'thinksns');
		$this->assign('email',$email);
		$this->assign('code',$_GET['code']);
		$this->display();
	}
	//修改密码
	function doChangePassword() {
		$email	=	jiemi($_POST['code'],'thinksns');
		$map = new HashMap();
		$map->put('email',$email);
		$dao = D('User');
		//验证密码位数
		if(strlen($_POST['newpassword'])<6){
			$this->error("密码不得少于6位！");
			exit;
		}
		//验证两次输入
		if($_POST['newpassword']!=$_POST['newpassword2']){
			$this->error("两次输入的密码不一致！");
			exit;
		}
		//验证旧密码
		$list = $dao->find($map,"","password");
		if($list->password!=md5($_POST['password'])){
			$this->error("旧密码不正确！");
			exit;
		}
		//验证新密码和旧密码是否相同
		if($list->password==md5($_POST['newpassword'])){
			$this->success("密码修改成功！");
			exit;
		}
		//修改密码
		$map->put('password',md5($_POST['newpassword']));
		if($dao->save($map)){
			$this->success("密码修改成功，请下一次使用新密码登陆！");
		}else{
			$this->error("密码修改失败！");
		}
	}

	public function userInvite(){
		$toEmail	=	$_POST[userInviteEmail];
		$toEmail	=	str_replace("，",',',$toEmail);
		$toEmailArray	=	explode(",",$toEmail);
		$emailCount		=	count($toEmailArray);
		$code		=	jiami($this->mid);
		foreach ($toEmailArray as $k=>$toEmail){
			$toEmail=  trim($toEmail);
			$dao	=	D('UserInvite');
			$dao->create();
			$dao->userId	=	$this->mid;
			$dao->module	=	'Register';
			$dao->status	=	'0';
			$dao->add();
		}
		$result	=	$this->sendInviteMail($toEmailArray,$code);
		header('location:'.__APP__.'/Friend/invite/callback/'.$result);
	}

	protected function sendInviteMail($emails,$code) {
		import('@.Util.sendmail');
		$dao = D("User");
		$userInfo  = $dao->find($this->mid);
		$code	=	"http://".$_SERVER['HTTP_HOST'].__APP__."/Public/reg/code/".$code;
		$face	=	'http://'.$_SERVER['HTTP_HOST'].WEB_PUBLIC_URL.'/Uploads/User/'.$userInfo->id.'/face_m.jpg';
		$this->assign('fromuser',$userInfo->name);
		$this->assign('code',$code);
		$this->assign('face',$face);
		$content	=	$this->fetch('mail');
		$title		=	$userInfo->name."邀请您体验ThinkSNS";
		if(sendemail($emails,$title,$content)){
			return 'success';
		}else{
			return 'error';
		}
	}
	public function logout(){
		Session::clear();
		Cookie::delete("email");
		Cookie::delete("password");
		Cookie::clear();
		$this->redirect("index","Index");
	}
	public function checkemail() {
		$num = D("User")->count('email ="'.trim($_POST['email']).'"');
		if ( $num >0 ) {
			echo false;
		}else{
			echo true;
		}
	}
}
?>