<?php
class HomeAction extends BaseAction {

	protected  function _initialize(){
		parent::_initialize();
	}
	public function index(){
		//个人信息
		$dao = D('User');
		$list = $dao->find($this->mid);
		$this->assign("user",$list);

		//我的通知
		$dao	=	D('Notify');
		$map	=	new HashMap();
		$map->put("toUserId",$this->mid);
		$map->put("status",'0');
		$alertCount	=	$dao->count($map);
		$list	= $dao->findAll($map,'*','cTime desc');
		$this->assign('notify',$list);
		$this->assign('notifyCount',$alertCount);

		//最新注册的会员
		$members	=	D('User')->findAll('','id,name','cTime desc','12');
		$this->assign('members',$members);

		//好友动态
		//$friendFeed	=	D('UserFeed')->findAll('','*','cTime desc');
		//$this->assign('friendFeed',$friendFeed);
		 $this->display();
	}

	public function feed(){
		//好友动态
		$friendArray	=	getUserFriends($this->mid);
		$friends	=	implode(',',$friendArray);
		$map	=	"userId in (".$friends.")";
		$friendFeed	=	D('UserFeed')->findAll($map,'*','cTime desc','50');
		$this->assign('friendFeed',$friendFeed);
		$this->display();
	}
}
?>