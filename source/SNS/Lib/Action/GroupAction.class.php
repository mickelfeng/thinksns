<?php
class GroupAction extends BaseAction
{
	function _initialize(){
		parent::_initialize();
	}
	// 群首页
	function index() {
		$dao	=	D("Group");
		$userGroups	= $dao->getUserGroupArray($this->mid);
		//dump($userGroups);exit;
		$groups	=	implode(",",$userGroups);
		//数据列表
		$dao	=	D("Thread");
		$map	=	"module='group' and recordId in (".$groups.")";
		$count	=	$dao->count($map);
		$listRows  =  20;
		$p		= new Page($count,$listRows);
		//$voList	= $dao->findAll($map,'*','cTime desc',$p->firstRow.','.$p->listRows);//我觉得应该按照最后回复的时间排序比较好
		$voList	= $dao->findAll($map,'*','top desc,lastReplyTime desc',$p->firstRow.','.$p->listRows);//fantasy修改
		//dump($voList);exit;
		$page	= $p->show();
		$this->assign('count',$count);
		$this->assign('list',$voList);
		$this->assign("page",$page);
		$this->display();
	}
	// 所有成员
	function member() {
		$groupId	=	$_GET['id'];
		$dao	=	D('GroupMember');
		$count	=	$dao->count("groupId='$groupId'");
		$p	= new Page($count,'10');
		$list	= $dao->findAll("groupId='$groupId'",'*','cTime DESC',$p->firstRow.','.$p->listRows);
		$page	= $p->show();
		$this->assign('groupId',$groupId);
		$this->assign("page",$page);
		$this->assign('count',$count);
		$this->assign('list',$list);
		$this->display();
	}
	// 进入群
	function show() {
		$groupId = $_GET['id'];
		if(!isset($groupId)){
			$groupId = '1';
		}
		//群信息
		$groupDao = D('Group');
		$list = $groupDao->find("id='$groupId'");
		$this->assign('vo',$list);

		//群成员
		$groupMemberDao = D('GroupMember');
		$list	=	$groupMemberDao->findAll("groupId='$groupId'",'userId','cTime DESC','30');
		$this->assign('members',$list);

		$count	=	$groupMemberDao->count("groupId='$groupId'");
		$groupDao->setField('memberCount',$count,"id='$groupId'");

		//群管理员
		$list	=	$groupMemberDao->findAll("groupId='$groupId' and level=2",'userId','cTime DESC','30');
		$this->assign('managers',$list);

		//群帖子
		$dao = D('Thread');
		$count	=	$dao->count("recordId='$groupId' and module='group'");
		$groupDao->setField('threadCount',$count,"id='$groupId'");
		$list = $dao->findAll("recordId='$groupId' and module='group'",'*','top desc,lastReplyTime DESC','20');
		$this->assign('threadCount',$count);
		$this->assign('threads',$list);
		$this->display();
	}
	// 我的群
	function my() {
		$dao	=	D("Group");
		$userGroups	=	$dao->getUserGroupArray($this->mid);
		$groups	=	implode(",",$userGroups);
		$count	=	count($userGroups);
		$dao	=	D("Group");
		$map	=	"id in (".$groups.")";
		$listRows  =  20;
		$p	= new Page($count,$listRows);
		$voList	= $dao->findAll($map,'*','cTime desc',$p->firstRow.','.$p->listRows);
		$page	= $p->show();
		$this->assign('count',$count);
		$this->assign('list',$voList);
		$this->assign("page",$page);

		//获取登录用户信息
		$dao = D('User');
		//getById($id,$table,$fields,$pk,$relation)
		$list = $dao->getById($userId);
		$this->assign('vo',$list);
		//dump($list);
		$this->display();
	}
	// 群列表
	function lists() {
		$dao	=	D("Group");
		if($_POST[name]){
			$map	=	" name like '%".$_POST[name]."%' ";
		}else{
			$map	=	'';
		}
		$count	=	$dao->count($map);
		$listRows  =  20;
		$p	= new Page($count,$listRows);
		$voList	= $dao->findAll($map,'*','memberCount desc',$p->firstRow.','.$p->listRows);
		//dump($voList);
		$page	= $p->show();
		$this->assign('count',$count);
		$this->assign('list',$voList);
		$this->assign("page",$page);
		$this->display();
	}
	// 帖子
	function thread() {
		$threadId = $_GET['id'];
		if(!isset($threadId)){
			$this->error("该帖子不存在或已被删除！");
		}
		//帖子信息
		$dao = D('Thread');
		$list = $dao->find("id='$threadId' and module='group'");
		$this->assign('vo',$list);
		//更新帖子阅读数
		$dao->updateThreadReadCount($threadId);

		//圈子信息
		$groupId	=	$list->recordId;
		$dao = D('Group');
		$list = $dao->find("id='$groupId'");
		$this->assign('group',$list);

		//帖子回复
		$dao = D('ThreadPost');
		$count	=	$dao->count("threadId='$threadId'");
		$listRows  =  20;
		$p	= new Page($count,$listRows);
		$voList	= $dao->findAll("threadId='$threadId'",'*','cTime asc',$p->firstRow.','.$p->listRows);
		$page	= $p->show();
		$this->assign('postCount',$count);
		$this->assign('list',$voList);
		$this->assign("page",$page);

		$this->display();
	}
	// 发帖
	function post() {
		$userId	=	$this->mid;
		$dao = D('Group');
		$groupUsers	=	$dao->getGroupUserArray($_GET['groupId']);
		if(!in_array($userId,$groupUsers)){
			$this->error("你不是圈子成员，不能发贴！");
		}
		$this->assign('groupId',$_GET['groupId']);
		$this->display();
	}
	// 编辑帖子
	function editPost() {
		$userId	=	$this->mid;
		$threadId = $_GET['id'];
		$dao = D('Thread');
		$list = $dao->getById($threadId);
		if($userId==$list->userId){
			$this->assign('vo',$list);
		}else{
			$this->error('对不起！不能编辑别人的帖子！');
		}
		$this->display();
	}
	// 执行发贴
	function doPost() {
		if(!isset($_POST[title]) || empty($_POST[title])){
			$this->error("标题不能为空！");
			exit;
		}
		if(!isset($_POST[content]) || empty($_POST[content])){
			$this->error("内容不能为空！");
			exit;
		}
		$userId	=	$this->mid;
		$dao = D('Group');
		$groupUsers	=	$dao->getGroupUserArray($_POST['recordId']);
		if(!in_array($userId,$groupUsers)){
			$this->error("你不是圈子成员，不能发贴！");
		}
		$map = new HashMap();
		$map->put('cTime',time());
		$map->put('lastReplyTime',time());
		$map->put('userId',$userId);
		$map->put('lastReplyUserId',$userId);
		$map->put('title',$_POST[title]);
		$map->put('module','group');
		$map->put('recordId',$_POST[recordId]);

		$dao = D('Thread');
		if($result = $dao->add($map)){
			$floor	=	$dao->find($result,'threadCount')->threadCount;

			$map->clear();
			$map->put('floor',$floor);
			$map->put('userId',$userId);
			$map->put('cTime',time());
			$map->put('threadId',$result);
			$map->put('content',$_POST[content]);
			$dao = D('ThreadPost');
			$dao->create($map);
			$result2	= $dao->add();
			if($result2){
				$dao = D('Group');
				//刷新圈子帖子统计
				$dao->resetGroupThreadCount($groupId);
				//刷新帖子回复数据
				$dao->resetThreadReply($result,$userId);

				/* add_user_feed */
					$feedTitle	=	"发表了新帖：<a href=\"/Group/thread/id/{$result}\">{$_POST[title]}</a>";
					$feedInfo	=	getShort($_POST['content'])."<br /><a href=\"/Group/thread/id/{$result}\">阅读全文</a>";
					$this->addUserFeed($userId,'add','group',$result,$feedTitle,$feedInfo);
				/* /add_user_feed */
				header('location:'.__APP__.'/Group/thread/id/'.$result);
				//$this->success("发帖成功！");
			}else{
				$this->error("发帖失败！");
			}
		}
	}
	// 执行编辑
	function updatePost() {
		$userId	=	$this->mid;
		$map = new HashMap();
		$map->put('userId',$userId);
		$map->put('mTime',time());
		$map->put('threadId',$_POST['threadId']);
		$map->put('content',$_POST['content']);
		$dao = D('ThreadPost');
		$result	= $dao->save($map);
		if($result){
			header('location:'.__APP__.'/Group/thread/id/'.$_POST['threadId']);
		}else{
			$this->error("发帖失败！");
		}
	}
	// 执行回复
	function doReply() {
		if(!isset($_POST[content]) || empty($_POST[content])){
			$this->error("内容不能为空！");
			exit;
		}
		$userId	=	$this->mid;
		$map = new HashMap();
		$map->put('userId',$userId);
		$map->put('cTime',time());
		$map->put('threadId',$_POST['threadId']);
		$map->put('content',$_POST['content']);
		$dao = D('ThreadPost');
		if($result	= $dao->add($map)){
			//刷新帖子回复数据
			$dao = D('Group');
			$dao->resetThreadReply($_POST['threadId'],$userId);
			/* add_user_feed */
				$feedTitle	=	"回复了帖子：<a href=\"/Group/thread/id/{$_POST['threadId']}\">".getThreadTitle($_POST['threadId'])."</a>";
				$feedInfo	=	'<div class="share-comment"><p>'.getShort($_POST['content']).'...</p></div>';
				$this->addUserFeed($userId,'add','group',$result,$feedTitle,$feedInfo);
			/* /add_user_feed */
			header('location:'.__APP__.'/Group/thread/id/'.$_POST['threadId']);
			//$this->success("回帖成功！");
		}else{
			$this->error("回帖失败！");
		}
	}
	// 申请加入
	function apply() {
		//判断圈子open属性，open=1，开放，2，成员开放，3，私密
		//$groupId = '';
		//$userId = '';
		//$dao = D('GroupDao');
		//dump($this->in($userId,$groupId));
	}
	// 设置
	function setting() {
		$this->display();
	}
	// 风格
	function style() {
		$this->display();
	}
	// 加入组织
	function in() {
		$groupId	=	$_GET['id'];
		$userId		=	$this->mid;
		if(!empty($groupId) && !empty($userId)){
			$dao = D('GroupMember');
			$dao->create();
			$dao->userId	=	$userId;
			$dao->groupId	=	$groupId;
			if($dao->add()){
				/* add_user_feed */
					$feedTitle	=	"加入了群：<a href=\"/Group/show/id/{$groupId}\">".getGroupName($groupId)."</a>";
					$this->addUserFeed($userId,'in','photo',$groupId,$feedTitle);
				/* /add_user_feed */
				$this->success("操作成功，你已成为该圈子成员。");
			}else{
				$this->error("系统错误！请稍后再试。");
			}
		}else{
			$this->error("操作错误！请登陆后选择要加入的办公圈。");
		}
	}
	// 退出组织
	function out() {
		$groupId	=	$_GET['id'];
		$userId		=	$this->mid;
		if(!empty($groupId)&&!empty($userId)){
			$dao = D('GroupMember');
			$user	=	$dao->find("userId='$userId' and groupId='$groupId'");
			if($user->level==2){
				$this->error("管理员不能直接退出群！",-1);
			}
			if($dao->delete("userId='$userId' and groupId='$groupId'")){
			/* add_user_feed */
				$feedTitle	=	"退出了群：<a href=\"/Group/show/id/{$groupId}\">".getGroupName($groupId)."</a>";
				$this->addUserFeed($userId,'out','group',$groupId,$feedTitle);
			/* /add_user_feed */
				$this->success("操作成功，你已退出该圈子。",1);
			}else{
				$this->error("系统错误！请稍后再试。",0);
			}
		}else{
			$this->error("操作错误！请登陆后选择要退出的办公圈。");
		}
	}
	//创建圈子
	function create() {
		$this->display();
	}
	function doCreate() {
		$userId	=	$this->mid;
		$dao = D('Group');
		$dao->create();
		$dao->userId	=	$userId;
		if($result = $dao->add()){
			/* add_user_feed */
				$feedTitle	=	"创建了群：<a href=\"/Group/show/id/{$result}\">".$vo->name."</a>";
				$this->addUserFeed($userId,'add','group',$result,$feedTitle);
			/* /add_user_feed */
			$gmDao = D('GroupMember');
			$gmDao->create();
			$gmDao->userId	=	$userId;
			$gmDao->groupId	=	$result;
			$gmDao->level	=	2;
			$gmDao->add();
			$dao->setInc('memberCount',"id='$groupId'");
			header("location:".__URL__."/changeFace/id/".$result);
		}else{
			$this->error("群组创建失败！");
		}
	}

	function changeFace() {
		$this->display();
	}
	function doChangeFace() {
		$groupId	=	$_POST['id'];
		if(!empty($_FILES[files][tmp_name][0])){
			$path	=	'./Public/Uploads/Group/'.$groupId.'/';
			$info	=	$this->_upload($path);
			$face	=	$info[0]['savepath'].$info[0]['savename'];
			D("Group")->save("cover=$face","id='$groupId'");
		}
		header("location:".__URL__."/show/id/".$_POST[id]);
	}
	function invite() {
		$groupId	=	$_GET['groupId'];
		$groupInfo	=	D('Group')->find($groupId);
		$this->assign('groupInfo',$groupInfo);
		$this->display();
	}
	function doInvite() {
		dump($_POST['member']);
		$dao = D('UserInvite');
	}
	// 管理群组
	function manage() {
		$groupId	=	$_GET['groupId'];
	}
	// 编辑群组资料
	function edit() {
		$groupId	=	$_GET[id];
		$userId		=	Session::get(C("USER_AUTH_KEY"));
		$dao = D('GroupMember');
		$count	=	$dao->count("groupId='$groupId' and userId='$userId' and level=2");
		if($count > 0){
			$dao = D('Group');
			$group	=	$dao->find($groupId);
			$this->assign('vo',$group);
			$this->display();
		}else{
			$this->error("你不是管理员，没有该权限！");
		}
	}
	// 更新群组资料
	function update() {
		$dao = D('Group');
		$dao->create();
		$result	=	$dao->save();
		if($result){
			header('location:'.__APP__.'/Group/picture/id/'.$_POST['id']);
		}else{
			$this->error("修改失败！");
		}
	}
	// 置顶
	function top() {
		$threadId	=	$_GET['id'];
		$dao = D('Thread');
		$thread	=	$dao->find($threadId);
		if(isGroupManager($this->mid,$thread->recordId)){
			if($dao->setField("top",1,"id='$threadId'")){
				$this->success('置顶成功！',1);
			}else{
				$this->error('置顶失败！',0);
			}
		}else{
			$this->error('你没有权限！',-1);
		}
	}
	// 提升管理员
	function up() {
		$userId	=	$_GET['userId'];
		$groupId=	$_GET['groupId'];
		if(isGroupManager($this->mid,$groupId)){
			$dao = D('Group');
			if($dao->up($userId,$groupId)){
				$this->success('操作成功！',1);
			}else{
				$this->error('操作失败！',0);
			}
		}else{
			$this->error('你没有权限！',-1);
		}
	}
	// 降级为普通会员
	function down() {
		$userId	=	$_GET['userId'];
		$groupId=	$_GET['groupId'];
		$count = D('GroupMember')->count("groupId='$groupId' and level=2");

		if($count<=1){
			$this->error('群组至少有一个管理员！',-2);
		}elseif(isGroupManager($this->mid,$groupId)){
			$dao = D('Group');
			if($dao->down($userId,$groupId)){
				$this->success('操作成功！',1);
			}else{
				$this->error('操作失败！',0);
			}
		}else{
			$this->error('你没有权限！',-1);
		}
	}
	// 编辑群组头像
	function picture() {
		$groupId	=	$_GET[id];
		$userId		=	Session::get(C("USER_AUTH_KEY"));
		$dao = D('GroupMember');
		$count	=	$dao->count("groupId='$groupId' and userId='$userId' and level=2");
		if($count > 0){
			$dao = D('Group');
			$group	=	$dao->find($groupId);
			$this->assign('vo',$group);
			$this->display();
		}else{
			$this->error("你不是管理员，没有该权限！");
		}
	}

}
?>