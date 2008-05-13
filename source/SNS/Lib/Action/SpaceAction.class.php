<?php
class spaceAction extends BaseAction {

	protected  function _initialize(){
		parent::_initialize();
	}
	public function index(){

		$dao	=	D('Space');
		$space	=	$dao->xFind($this->uid);

		//格式化生日
		$space->birth	=	date($space->birth_setting,$space->birthday);
		//获取用户信息
		$contacts	=	D('UserContact')->find('userId='.$this->uid);
		$works		=	D('UserWorks')->find('userId='.$this->uid);
		$educations	=	D('UserEducation')->find('userId='.$this->uid);
		$interests	=	D('UserInterest')->find('userId='.$this->uid);
		//dump($educations);
		//隐私设定，如果不是我朋友，则搬出隐私规则,如果隐私规则=2，则不能浏览
		if(!isMyFriend($this->uid)){
			$setting	=	D('UserSetting')->find('userId='.$this->uid);
			$setting	=	unserialize($setting->privacy);
			$this->assign('setting',$setting);
		}
		$this->assign('contacts',$contacts);
		$this->assign('works',$works);
		$this->assign('educations',$educations);
		$this->assign('interests',$interests);

		$browerNum	=	D('UserBrower')->count('userId='.$this->uid);
		$firendNum	=	D('UserFriend')->count('userId='.$this->uid);
		$groupNum	=	D('GroupMember')->count('userId='.$this->uid);
		$photoNum	=	D('Album')->count('userId='.$this->uid);
		$blogNum	=	D('Blog')->count('userId='.$this->uid);
		$wallNum	=	D('Wall')->count('userId='.$this->uid);

		$this->assign('friendNum',$firendNum);
		$this->assign('browerNum',$browerNum);
		$this->assign('groupNum',$groupNum);
		$this->assign('photoNum',$photoNum);
		$this->assign('blogNum',$blogNum);
		$this->assign('wallNum',$wallNum);

		$this->assign('space',$space);


		//记录最近我浏览的朋友,需要优化.
		$userId	=	$this->uid;
		$loginUserId	=	$this->mid;
		if(isset($userId) && isset($loginUserId) && $userId!=$loginUserId){
			$dao	=	D('UserBrower');
			$num	=	$dao->count("userId='$userId' and browerId = '$loginUserId'");
			if($num==0){
				$map = new HashMap();
				$map->put('userId',$userId);
				$map->put('browerId',$loginUserId);
				$map->put('cTime',time());
				$map->put('type',1);
				$dao->add($map);
			}
		}

		$this->display();
	}
}
?>