<?php
//账户中心: 用户资料，修改密码，隐私设定
class AccountAction extends BaseAction
{
	function _initialize(){
		parent::_initialize();
		$dao	=	D('User');
		$user	=	$dao->find($this->mid);
		$this->assign('user',$user);
	}
	//隐私设置
	function privacy() {
		$userId	=	$this->mid;
		$dao	=	D('UserSetting');
		$list	=	$dao->find("userId='$userId'");
		$privacy	=	unserialize($list->privacy);
		$this->assign('privacy',$privacy);
		$this->display();
	}
	//基本资料
	function basic() {
		$userId	=	$this->mid;
		$dao	=	D('User');
		$list	=	$dao->find("id='$userId'");
		$this->assign('user',$list);
		$birthday	=	date("Y-m-d",$list->birthday);
		$birth	=	explode("-",$birthday);
		$this->assign('birth_y',$birth[0]);
		$this->assign('birth_m',$birth[1]);
		$this->assign('birth_d',$birth[2]);
		$this->display();
	}
	//账号设置
	function account() {
		$userId	=	$this->mid;
		$dao	=	D('User');
		$list	=	$dao->find($userId);
		$this->assign('user',$list);
		$this->display();
	}
	//网络设置
	function network() {
		$dao	=	D('User');
		$user	=	$dao->find($this->mid,'networkId');
		$this->assign('user',$user);

		$dao	=	D('Network');
		$list	=	$dao->findAll();

		$this->assign('networks',$list);
		$this->display();
	}
	//联系信息
	function contact() {
		$userId	=	$this->mid;
		$dao	=	D('UserContact');
		$list	=	$dao->find("userId='$userId'");
		$this->assign('contact',$list);
		$dao	=	D('User');
		$list	=	$dao->find($userId);
		$this->assign('user',$list);
		$this->display();
	}
	//个人信息/兴趣爱好
	function interest() {
		$userId	=	$this->mid;
		$dao = D('UserInterest');
		$list = $dao->find("userId='$userId'");
		$this->assign('interest',$list);
		$this->display();
	}
	//教育信息
	function education() {
		$userId	=	$this->mid;
		$dao	=	D('UserEducation');
		$list	=	$dao->find("userId='$userId'");
		$this->assign('edu',$list);
		$this->display();
	}
	//工作信息
	function work() {
		$userId	=	$this->mid;
		$dao	=	D('UserWorks');
		$list	=	$dao->find("userId='$userId'");
		$this->assign('works',$list);
		$this->display();
	}
	//头像
	function face() {
		if($_GET['up']=='ok'){
			$this->assign('up','ok');
		}
		$this->display();
	}
	//修改名字
	function doChangeName() {
		$oldName	=	getUserName($this->mid);
		$map = new HashMap();
		$map->put('id',$this->mid);
		$map->put('name',$_POST['name']);
		$dao = D('User');
		if($dao->save($map)){
			/* add_user_feed */
				$feedTitle	=	"把名字从‘".$oldName."’改成了‘".$_POST['mame']."’";
				$this->addUserFeed($userId,'add','profile',$userId,$feedTitle);
			/* /add_user_feed */
			$this->success("名字修改成功！");
		}else{
			$this->error("名字修改失败！");
		}
	}
	//修改密码
	function doChangePassword() {

		$map = new HashMap();
		$map->put('id',$this->mid);
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
	//修改头像
	function doChangeFace() {
		$userId	=	$this->mid;
		$path	=	'./Public/Uploads/User/'.$userId.'/images/';
		checkDir($path);
		$info	=	$this->_upload($path);
		if($info){
			$file	=	$info[0]['savepath'].$info[0]['savename'];
			include "./Include/Image.class.php";
			//生成头像缩略图
			$img	=	new Image();
			//小图
			$w=48;$h=48;
			$face	=	'./Public/Uploads/User/'.$userId.'/face_s.jpg';
			$img->cutThumb($file,$face,$w,$h);
			//中图
			$w=96;$h=96;
			$face	=	'./Public/Uploads/User/'.$userId.'/face_m.jpg';
			$img->cutThumb($file,$face,$w,$h);
			//大图
			$w=200;$h=400;
			$face	=	'./Public/Uploads/User/'.$userId.'/face_b.jpg';
			$img->thumb($file,'',$face,$w,$h);
			//原图大小
			$face	=	'./Public/Uploads/User/'.$userId.'/face_o.jpg';
			copy($file,$face);
			//保存头像附件
			$dao = D('Attach');
			$dao->create($info[0]);
			$dao->module	=	'Face';
			$dao->recordId	=	$this->mid;
			$dao->userId	=	$this->mid;
			$dao->uploadTime=	time();
			$dao->add();
			/* add_user_feed */
				$feedTitle	=	"上传了新头像";
				$this->addUserFeed($userId,'add','face',$userId,$feedTitle);
			/* /add_user_feed */
		}
		//header("location:".__URL__."/face?up=ok");
		$this->redirect('face');
	}
	function doChangeBasic() {
		$userId		=	$this->mid;
		$birthday	=	$_POST["birth_y"]."-".$_POST["birth_m"]."-".$_POST["birth_d"];
		$birth		=	strtotime($birthday);
		$map = new HashMap();
		$map->put('id',$userId);
		$map->put('sex',$_POST["sex"]);
		$map->put('birthday',$birth);
		$map->put('birth_setting',$_POST["birth_setting"]);
		$map->put('home_province',$_POST["home_province"]);
		$map->put('home_city',$_POST["home_city"]);
		$map->put('home_area',$_POST["home_area"]);
		$dao = D('User');
		if($dao->save($map)){
			/* add_user_feed */
				$feedTitle	=	"更新了基本资料";
				$this->addUserFeed($userId,'add','profile',$userId,$feedTitle);
			/* /add_user_feed */
			header('location:'.__URL__.'/basic');
		}else{
			$this->error("基本信息修改失败！");
		}
	}
	function doChangeNetwork() {
		$map['id']		=	$this->mid;
		$map['networkId']	=	$_POST['network'];
		$dao = D('User');
		if($dao->save($map)){
			header('location:'.__URL__.'/network');
		}else{
			$this->error("网络修改失败！");
		}
	}
	//隐私设定
	function setPrivacy() {
		$userId	=	$this->mid;

		$map['privacy_space']	=	$_POST["privacy_space"];
		$map['privacy_profile']	=	$_POST["privacy_profile"];
		$map['privacy_friend']	=	$_POST["privacy_friend"];
		$map['privacy_wall']	=	$_POST["privacy_wall"];
		$privacy	=	serialize($map);
		$dao = D('UserSetting');
		$user	=	$dao->count("userId='$userId'");
		if($user==1){
			$save['cTime']	=	time();
			$save['privacy']=	$privacy;
			if($result = $dao->save($save,"userId='$userId'")){
				header('location:'.__URL__.'/privacy');
			}else{
				$this->error("隐私设定修改失败！");
			}
		}else{
			$add['userId']	=	$userId;
			$add['cTime']	=	time();
			$add['privacy']	=	$privacy;
			if($result = $dao->add($add)){
				header('location:'.__URL__.'/privacy');
			}else{
				$this->error("隐私设定添加失败！");
			}
		}
	}
	//更新设定
	function setUpdate() {
		$userId	=	$this->mid;
		if(!isset($_POST["update_profile"])){
			$_POST["update_profile"]	=	'0';
		}
		if(!isset($_POST["update_friend"])){
			$_POST["update_friend"]	=	'0';
		}
		if(!isset($_POST["update_mini"])){
			$_POST["update_mini"]	=	'0';
		}
		if(!isset($_POST["update_blog"])){
			$_POST["update_blog"]	=	'0';
		}
		if(!isset($_POST["update_photo"])){
			$_POST["update_photo"]	=	'0';
		}
		if(!isset($_POST["update_forum"])){
			$_POST["update_forum"]	=	'0';
		}
		if(!isset($_POST["update_wall"])){
			$_POST["update_wall"]	=	'0';
		}
		if(!isset($_POST["update_group"])){
			$_POST["update_group"]	=	'0';
		}
		if(!isset($_POST["update_share"])){
			$_POST["update_share"]	=	'0';
		}
		$map = new HashMap();
		$map->put('userId',$userId);
		$map->put('update_profile',$_POST["update_profile"]);
		$map->put('update_friend',$_POST["update_friend"]);
		$map->put('update_mini',$_POST["update_mini"]);
		$map->put('update_blog',$_POST["update_blog"]);
		$map->put('update_photo',$_POST["update_photo"]);
		$map->put('update_forum',$_POST["update_forum"]);
		$map->put('update_wall',$_POST["update_wall"]);
		$map->put('update_group',$_POST["update_group"]);
		$map->put('update_share',$_POST["update_share"]);

		$dao = D('UserSetting');
		$user	=	$dao->getBy("userId",$userId);
		if($user){
			$map->put('mTime',time());
			if($result = $dao->save($map)){
				header('location:'.__URL__.'/privacy');
			}else{
				$this->error("隐私设定修改失败！");
			}
		}else{
			$map->put('cTime',time());
			if($result = $dao->add($map)){
				header('location:'.__URL__.'/privacy');
			}else{
				$this->error("隐私设定修改失败！");
			}
		}
	}
	function editContact() {
		$userId	=	$this->mid;
		$map = new HashMap();
		$map->put('userId',$userId);
		$map->put('msn',$_POST["msn"]);
		$map->put('gtalk',$_POST["gtalk"]);
		$map->put('qq',$_POST["qq"]);
		$map->put('skype',$_POST["skype"]);
		$map->put('cell',$_POST["cell"]);
		$map->put('tel',$_POST["tel"]);
		$map->put('address',$_POST["address"]);
		$map->put('homepage',$_POST["homepage"]);
		$map->put('yahoo',$_POST["yahoo"]);
		$map->put('blog',$_POST["blog"]);

		$dao = D('UserContact');
		$user	=	$dao->getBy("userId",$userId);
		if($user){
			$map->put('mTime',time());
			if($result = $dao->save($map)){
				/* add_user_feed */
					$feedTitle	=	"更新了联系方式";
					$this->addUserFeed($userId,'add','profile',$userId,$feedTitle);
				/* /add_user_feed */
				header('location:'.__URL__.'/contact');
			}else{
				$this->error("联系信息修改失败！");
			}
		}else{
			$map->put('cTime',time());
			if($result = $dao->add($map)){
				/* add_user_feed */
					$feedTitle	=	"更新了联系方式";
					$this->addUserFeed($userId,'add','profile',$userId,$feedTitle);
				/* /add_user_feed */
				header('location:'.__URL__.'/contact');
			}else{
				$this->error("联系信息添加失败！");
			}
		}
	}
	function doChangeInterest() {
		$userId	=	$this->mid;
		$tags	=	$_POST[tags];
		$tagIds	=	$this->addUserTag($tags,'user_interest');
		$tagIds	=	implode(',',$tagIds);
		$map = new HashMap();
		$map->put('userId',$userId);
		$map->put('tags',$tags);
		$map->put('tagIds',$tagIds);

		$dao	=	D('UserInterest');
		$user	=	$dao->find("userId='$userId'",'userId');
		if($user->userId){
			$map->put('mTime',time());
			if($result = $dao->save($map,"userId='$userId'")){
				/* add_user_feed */
					$feedTitle	=	"更新了兴趣爱好";
					$this->addUserFeed($userId,'add','profile',$userId,$feedTitle);
				/* /add_user_feed */
				header('location:'.__URL__.'/interest');
			}else{
				$this->error("兴趣爱好修改失败！");
			}
		}else{
			$map->put('cTime',time());
			if($result = $dao->add($map)){
				/* add_user_feed */
					$feedTitle	=	"更新了兴趣爱好";
					$this->addUserFeed($userId,'add','profile',$userId,$feedTitle);
				/* /add_user_feed */
				header('location:'.__URL__.'/interest');
			}else{
				$this->error("兴趣爱好添加失败！");
			}
		}
	}
	function doChangeWorks() {
		$userId	=	$this->mid;
		//插入标签
		$companyTagId	=	$this->addUserTag($_POST['company'],'company');
		$map = new HashMap();
		$map->put('userId',$userId);
		$map->put('province',$_POST['province']);
		$map->put('city',$_POST['city']);
		$map->put('area',$_POST['area']);
		$map->put('company',$_POST['company']);
		$map->put('company_info',$_POST['company_info']);
		$map->put('companyTagId',$companyTagId);
		$dao = D('UserWorks');
		$user	=	$dao->find("userId='$userId'",'userId');
		if($user->userId){
			$map->put('mTime',time());
			if($result = $dao->save($map,"userId='$userId'")){
				/* add_user_feed */
					$feedTitle	=	"更新了工作信息";
					$this->addUserFeed($userId,'add','profile',$userId,$feedTitle);
				/* /add_user_feed */
				header('location:'.__URL__.'/work');
			}else{
				$this->error("工作信息修改失败！");
			}
		}else{
			$map->put('cTime',time());
			if($result = $dao->add($map)){
				/* add_user_feed */
					$feedTitle	=	"更新了工作信息";
					$this->addUserFeed($userId,'add','profile',$userId,$feedTitle);
				/* /add_user_feed */
				header('location:'.__URL__.'/work');
			}else{
				$this->error("工作信息添加失败！");
			}
		}
	}
	//增加大学库
	function doChangeEducation() {
		$userId		=	$this->mid;
		$map = new HashMap();
		$map->put('userId',$userId);
		$map->put('school_type',$_POST["school_type"]);
		$map->put('school_name',$_POST["school_name"]);
		$map->put('school_in',$_POST["school_in"]);
		$dao = D('UserEducation');
		$user	=	$dao->find("userId='$userId'",'userId');
		if($user->userId){
			if($dao->save($map)){
				/* add_user_feed */
					$feedTitle	=	"更新了教育信息";
					$this->addUserFeed($userId,'add','profile',$userId,$feedTitle);
				/* /add_user_feed */
				header('location:'.__URL__.'/education');
			}else{
				$this->error("教育信息修改失败！");
			}
		}else{
			if($dao->add($map)){
				/* add_user_feed */
					$feedTitle	=	"更新了教育信息";
					$this->addUserFeed($userId,'add','profile',$userId,$feedTitle);
				/* /add_user_feed */
				header('location:'.__URL__.'/education');
			}else{
				$this->error("教育信息修改失败！");
			}
		}
	}
}
?>