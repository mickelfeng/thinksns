<?php
class UserFriendModel extends Model
{
	var $tableName	=	'user_friend';
	//添加好友
	function addFriend($friendId,$userId) {
		if($friendId==$userId){
			return '-1';
		}
		$count	=	$this->getCount("friendId='$friendId' AND userId='$userId'");
		if($count>0){
			return '-2';
		}
		$map = new HashMap();
		$map->put('friendId',$friendId);
		$map->put('userId',$userId);
		$map->put('cTIme',time());
		if($result = $this->add($map)){
			return $result;
		}else{
			return '-3';
		}
	}
	//删除好友
	function delFriend($friendId,$userId) {
		$sql	=	"DELETE FROM $this->trueableName WHERE friendId='$friendId' AND userId='$userId' ";
		if($this->db->execute($sql)){
			return true;
		}else{
			return false;
		}
	}
	//关注好友
	function fave() {
	}
	//黑名单
	function hate() {
	}
}
?>