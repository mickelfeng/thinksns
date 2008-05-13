<?php
class GroupModel extends Model
{
	//表单验证
	protected	$_validate = array(
		array('name','require','内容不能为空！'),
	);
	protected	$_auto	=	array(
		array('cTime','time','ADD','function'),
	);
	// 加入圈子
	function in($userId,$groupId) {
		$cTime	=	time();
		$sql = "INSERT INTO think_group_member (groupId,userId,cTime) VALUES ('$groupId','$userId','$cTime')";
		$result = $this->execute($sql);
		if($result===FALSE){
			return false;
		}else{
			$this->resetGroupMemberCount($groupId);
			return true;
		}
	}
	// 退出圈子
	function out($userId,$groupId) {
		$sql = "DELETE FROM think_group_member WHERE groupId='$groupId' and userId='$userId'";
		$result = $this->execute($sql);
		if($result===FALSE){
			return false;
		}else{
			$this->resetGroupMemberCount($groupId);
			return true;
		}
	}
	// 提升为管理员
	function up($userId,$groupId) {
		$sql = "UPDATE think_group_member SET level=2 WHERE groupId='$groupId' and userid='$userId' and level=1";
		$result = $this->execute($sql);
		if($result===FALSE){
			return false;
		}else{
			return true;
		}
	}
	// 降级为普通成员
	function down($userId,$groupId) {
		$sql = "UPDATE think_group_member SET level=1 WHERE groupId='$groupId' and userid='$userId' and level=2";
		$result = $this->execute($sql);
		if($result===FALSE){
			return false;
		}else{
			return true;
		}
	}
	// 删除帖子
	// 删除/屏蔽 回复
	// 隐藏帖子
	// 隐藏回复
	// 批准会员
	// getGroupMember
	function getGroupMember($groupId,$other='') {
		$sql = "select * from think_group_member where groupId = '$groupId' $other ";
		$result = $this->query($sql);
		if($result===FALSE){
			return false;
		}else{
			return $result;
		}
	}
	// getUserGroup
	function getUserGroup($userId,$other='') {
		$sql = "select * from think_group_member where userId = '$userId' $other ";
		$result = $this->query($sql);
		if($result===FALSE){
			return false;
		}else{
			return $result;
		}
	}
	// getUserGroupArray
	function getUserGroupArray($userId,$other='') {
		$groups	=	$this->getUserGroup($userId,$other);
		foreach ($groups as $g){
			$groupArray[]	=	$g->groupId;
		}
		if(count($groupArray)==0){
			$groupArray[0]	=	'0';
		}else{
			return $groupArray;
		}
	}
	// getGroupUserArray
	function getGroupUserArray($groupId) {
		$users	=	$this->getGroupMember($groupId);
		foreach ($users as $u){
			$userArray[]	=	$u->userId;
		}
		if(count($userArray)==0){
			$userArray[0]	=	'0';
		}else{
			return $userArray;
		}
	}
	// resetGroupThreadCount
	function resetGroupThreadCount($groupId) {
		$sql = "UPDATE think_group SET threadCount=threadCount+1 WHERE id='$groupId'";
		$result	=	$this->execute($sql);
		if($result===FALSE){
			return false;
		}else{
			return true;
		}
	}
	// resetGroupMemberCount
	function resetGroupMemberCount($groupId){
		$sql = "UPDATE think_group SET memberCount=memberCount+1 WHERE id='$groupId'";
		$result	=	$this->execute($sql);
		if($result===FALSE){
			return false;
		}else{
			return true;
		}
	}
	// resetThreadReply
	function resetThreadReply($threadId,$userId) {
		$lastReplyTime	=	time();
		$sql	=	"UPDATE think_thread SET replyCount=replyCount+1,lastReplyTime='$lastReplyTime',lastReplyUserId='$userId' WHERE id='$threadId'";
		$result	=	$this->execute($sql);
		if($result===FALSE){
			return false;
		}else{
			return true;
		}
	}
}
?>