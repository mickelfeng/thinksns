<?php

//获取某模块记录总数
function getCount($model,$id){
	$count = D($model)->count($id);
	return $count;
}
function getUserNotify($id) {
	if(is_numeric($id)){
		$dao = D('Notify');
		$list = $dao->find($id);
		switch($list->action){
			case "sayHello":
				$info	= "向你打招呼 <a href='".__APP__."/space/".$list->fromUserId."'>去回敬</a>";
				break;
			case "addWall":
				$info	= "给你留了言 <a href='".__APP__."/Wall' target='_blank'>查看</a>";
				break;
			case "replyWall":
				$info	= "回复了你的留言 <a href='".__APP__."/Wall' target='_blank'>查看</a>";
				break;
			case "replyBlog":
				$info	= "评论了你的日志 <a href='".__APP__."/Blog/content/id/".$list->info."' target='_blank'>查看</a>";
				break;
			case "replyPhoto":
				$info	= "评论了你的照片 <a href='".__APP__."/Photo/image/id/".$list->info."' target='_blank'>查看</a>";
				break;
			case "addFriend":
				$info	= "申请加为好友 <a href='".__APP__."/Index/re/id/".$id."' id='friend_".$id."' target='_blank'  rel='facebox' title='确认加为好友'>同意</a>";
				break;
			case "makeFriend":
				$info	= "确认我成为好友";
				break;
			case "sendMessage":
				$info	= "给你发了消息 <a href='".__APP__."/Index/re/id/".$id."' target='_blank'>查看</a>";
				break;
			case "system":
				$info	=  $list->info;
				break;
		}
		return $info;
	}else{
		return false;
	}
}
function friendlyDate($sTime,$type = 'normal',$alt = 'false') {
	//sTime=源时间，cTime=当前时间，dTime=时间差
	$cTime		=	time();
	$dTime		=	$cTime - $sTime;
	$dDay		=	intval(date("Ymd",$cTime)) - intval(date("Ymd",$sTime));
	$dYear		=	intval(date("Y",$cTime)) - intval(date("Y",$sTime));
	//normal：n秒前，n分钟前，n小时前，日期
	if($type=='normal'){
		if( $dTime < 60 ){
			echo $dTime."秒前";
		}elseif( $dTime < 3600 ){
			echo intval($dTime/60)."分钟前";
		}elseif( $dTime >= 3600 && $dDay == 0  ){
			echo intval($dTime/3600)."小时前";
		}elseif($dYear==0){
			echo date("m-d ,H:i",$sTime);
		}else{
			echo date("Y-m-d ,H:i",$sTime);
		}
	//full: Y-m-d , H:i:s
	}elseif($type=='full'){
		echo date("Y-m-d , H:i:s",$sTime);
	}
}
function currentBrower(){
	if($loginUser = Session::get(C('USER_AUTH_KEY'))){
		$dao = D("UserLog");
		$list	=	$dao->findAll("userId='$loginUser'");
		return $list;
	}
}
function jsGo($path,$msg) {
	if($msg!=""){
		echo "<script>alert('".$msg."');javascript:location.href='".$path."'</script>";
	}else{
		echo "<script>javascript:location.href='".$path."'</script>";
	}
}
function getCommentCount($recordId,$module) {
	//评论数量
	$map = new HashMap();
	$map->put('recordId',$recordId);
	$map->put('module',$module);
	$dao	= D("Comment");
	$count	= $dao->count($map);
	return $count;
}
function ubb($Text) {
	$Text=trim($Text);
	$Text=htmlspecialchars($Text);
	$Text=ereg_replace("\n","<br>",$Text);
	$Text=preg_replace("/\\t/is","  ",$Text);
	$Text=preg_replace("/\[hr\]/is","<hr>",$Text);
	$Text=preg_replace("/\[separator\]/is","<br/>",$Text);
	$Text=preg_replace("/\[h1\](.+?)\[\/h1\]/is","<h1>\\1</h1>",$Text);
	$Text=preg_replace("/\[h2\](.+?)\[\/h2\]/is","<h2>\\1</h2>",$Text);
	$Text=preg_replace("/\[h3\](.+?)\[\/h3\]/is","<h3>\\1</h3>",$Text);
	$Text=preg_replace("/\[h4\](.+?)\[\/h4\]/is","<h4>\\1</h4>",$Text);
	$Text=preg_replace("/\[h5\](.+?)\[\/h5\]/is","<h5>\\1</h5>",$Text);
	$Text=preg_replace("/\[h6\](.+?)\[\/h6\]/is","<h6>\\1</h6>",$Text);
	$Text=preg_replace("/\[center\](.+?)\[\/center\]/is","<center>\\1</center>",$Text);
	//$Text=preg_replace("/\[url=([^\[]*)\](.+?)\[\/url\]/is","<a href=\\1 target='_blank'>\\2</a>",$Text);
	$Text=preg_replace("/\[url\](.+?)\[\/url\]/is","<a href=\"\\1\" target='_blank'>\\1</a>",$Text);
	$Text=preg_replace("/\[url=(http:\/\/.+?)\](.+?)\[\/url\]/is","<a href='\\1' target='_blank'>\\2</a>",$Text);
	$Text=preg_replace("/\[url=(.+?)\](.+?)\[\/url\]/is","<a href=\\1>\\2</a>",$Text);
	$Text=preg_replace("/\[img\](.+?)\[\/img\]/is","<img src=\\1>",$Text);
	$Text=preg_replace("/\[img\s(.+?)\](.+?)\[\/img\]/is","<img \\1 src=\\2>",$Text);

	$Text=preg_replace("/\[face\](.+?)\[\/face\]/is","<img src=\"./Public/Images/biaoqing/\\1.gif>",$Text);

	$Text=preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/is","<font color=\\1>\\2</font>",$Text);
	$Text=preg_replace("/\[colorTxt\](.+?)\[\/colorTxt\]/eis","color_txt('\\1')",$Text);
	$Text=preg_replace("/\[style=(.+?)\](.+?)\[\/style\]/is","<div class='\\1'>\\2</div>",$Text);
	$Text=preg_replace("/\[size=(.+?)\](.+?)\[\/size\]/is","<font size=\\1>\\2</font>",$Text);
	$Text=preg_replace("/\[sup\](.+?)\[\/sup\]/is","<sup>\\1</sup>",$Text);
	$Text=preg_replace("/\[sub\](.+?)\[\/sub\]/is","<sub>\\1</sub>",$Text);
	$Text=preg_replace("/\[pre\](.+?)\[\/pre\]/is","<pre>\\1</pre>",$Text);
	$Text=preg_replace("/\[emot\](.+?)\[\/emot\]/eis","emot('\\1')",$Text);
	$Text=preg_replace("/\[email\](.+?)\[\/email\]/is","<a href='mailto:\\1'>\\1</a>",$Text);
	$Text=preg_replace("/\[i\](.+?)\[\/i\]/is","<i>\\1</i>",$Text);
	$Text=preg_replace("/\[u\](.+?)\[\/u\]/is","<u>\\1</u>",$Text);
	$Text=preg_replace("/\[b\](.+?)\[\/b\]/is","<b>\\1</b>",$Text);
	$Text=preg_replace("/\[quote\](.+?)\[\/quote\]/is","<blockquote>引用:<div style='border:1px solid silver;background:#EFFFDF;color:#393939;padding:5px' >\\1</div></blockquote>", $Text);
	$Text=preg_replace("/\[code\](.+?)\[\/code\]/eis","highlight_code('\\1')", $Text);
	$Text=preg_replace("/\[php\](.+?)\[\/php\]/eis","highlight_code('\\1')", $Text);
	$Text=preg_replace("/\[sig\](.+?)\[\/sig\]/is","<div style='text-align: left; color: darkgreen; margin-left: 5%'><br><br>--------------------------<br>\\1<br>--------------------------</div>", $Text);
	return $Text;
}
//过滤脚本代码
function cleanJs($text){
	$text	=	trim($text);
	$text	=	stripslashes($text);
	//完全过滤动态代码
	$text	=	preg_replace('/<\?|\?>/','',$text);
	//完全过滤js
	$text	=	preg_replace('/<script?.*\/script>/','',$text);
	//过滤多余html
	$text	=	preg_replace('/<\/?(html|head|meta|link|base|body|title|style|script|form|iframe|frame|frameset)[^><]*>/i','',$text);
	//过滤on事件lang js
	while(preg_match('/(<[^><]+)(lang|onfinish|onmouse|onexit|onerror|onclick|onkey|onload|onchange|onfocus|onblur)[^><]+/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1],$text);
	}
	while(preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1].$mat[3],$text);
	}
	return $text;
}
//纯文本输出
function t($text){
	$text	=	h($text);
	$text	=	strip_tags($text);
	return $text;
}
//输出安全的html
function h($text){
	$text	=	trim($text);
	$text	=	stripslashes($text);
	//完全过滤注释
	$text	=	preg_replace('/<!--?.*-->/','',$text);
	//完全过滤动态代码
	$text	=	preg_replace('/<\?|\?>/','',$text);
	//完全过滤js
	$text	=	preg_replace('/<script?.*\/script>/','',$text);

	$text	=	str_replace('[','&#091;',$text);
	$text	=	str_replace(']','&#093;',$text);
	$text	=	str_replace('|','&#124;',$text);
	//过滤换行符
	$text	=	preg_replace('/\r?\n/','',$text);
	//br
	$text	=	preg_replace('/<br(\s\/)?>/i','[br]',$text);
	$text	=	preg_replace('/(\[br\]\s*){10,}/i','[br]',$text);
	//hr img area input
	$text	=	preg_replace('/<(hr|img|input|area|isindex)( [^><\[\]]*)>/i','[\1\2]',$text);
	//过滤多余html
	$text	=	preg_replace('/<\/?(html|head|meta|link|base|body|title|style|script|form|iframe|frame|frameset)[^><]*>/i','',$text);
	//过滤on事件lang js
	while(preg_match('/(<[^><]+)( lang|onfinish|onmouse|onexit|onerror|onclick|onkey|onload|onchange|onfocus|onblur)[^><]+/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1],$text);
	}
	while(preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1].$mat[3],$text);
	}
	//过滤合法的html标签
	while(preg_match('/<([a-z]+)[^><\[\]]*>[^><]*<\/\1>/i',$text,$mat)){
		$text=str_replace($mat[0],str_replace('>',']',str_replace('<','[',$mat[0])),$text);
	}
	//转换引号
	while(preg_match('/(\[[^\[\]]*=\s*)(\"|\')([^\2=\[\]]+)\2([^\[\]]*\])/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1].'|'.$mat[3].'|'.$mat[4],$text);
	}
	//过滤错误的单个引号
	while(preg_match('/\[[^\[\]]*(\"|\')[^\[\]]*\]/i',$text,$mat)){
		$text=str_replace($mat[0],str_replace($mat[1],'',$mat[0]),$text);
	}
	//转换其它所有不合法的 < >
	$text	=	str_replace('<','&lt;',$text);
	$text	=	str_replace('>','&gt;',$text);
	$text	=	str_replace('"','&quot;',$text);
	 //反转换
	$text	=	str_replace('[','<',$text);
	$text	=	str_replace(']','>',$text);
	$text	=	str_replace('|','"',$text);
	//过滤多余空格
	$text	=	str_replace('  ',' ',$text);
	return $text;
}
function GFW($text) {
	//替换成开头大写字母
	$word	=	"共产党|法轮功|藏独|毛泽东|江泽民|台独|胡锦涛|fuck";
	$words	=	explode('|',$word);
	foreach($words as $v){
		$text	=	str_replace($v,' ** ',$text);
	}
	return $text;
}
function getBlogShort($content) {
	$content	=	stripslashes($content);
	$content	=	t($content);
	$content	=	getShort($content,'60');
	return $content;
}
function getBlogSummary($content,$num='100') {
	$content	=	stripslashes($content);
	$images		=	matchImages($content);
	$content	=	t($content);
	$content	=	getShort($content,$num);
	if(false===$images){
		$retrun		=	$content;
	}else{
		$retrun		=	"<img src='$images[0]' style='width:100px;border:0px;float:right;margin:5px;' />".$content;
	}
	return $retrun;
}
function matchImages($content) {
	$src	=	array();
	preg_match_all('/<img.*src=(.*)\\s.*>/iU',$content,$src);
	if(count($src[1])>0){
		foreach($src[1] as $v){
			$image	=	str_replace("'",'',$v);
			$image	=	str_replace('"','',$v);
			$images[] =	$image;
		}
		return $images;
	}else{
		return false;
	}

}
function cleanImages($content) {
	$content	=	preg_replace("/<img.*>/iU"," ",$content);
	return $content;
}
//获取用信息
function getUserInfo($uid){
	$userinfo	=	unserialize(S('userinfo_'.$uid));
	if($userinfo){
		return $userinfo;
	}else{
		$userinfo	=	D('user')->find($uid);
		S('userinfo_'.$uid,serialize($userinfo));
		return $userinfo;
	}
}
//获取用户名
function getUserName($uid){
	$info	=	getUserInfo($uid);
	return $info->name;
}
function getUserProvince($userId){
	$dao	=	D("UserWorks");
	$list	=	$dao->find("userId='$userId'",'province');
	$province	=	$list->province;
	return $province;
}
function getUserNetwork($userId){
	$dao	=	D("UserWorks");
	$list	=	$dao->find("userId='$userId'",'networkId');
	$networkId	=	getNetworkName($list->networkId);
	return $networkId;
}
function getUserNews($userId){
	$dao	=	D("Mini");
	$list	=	$dao->findAll("userId='$userId'",'content','cTime DESC','1');
	$news	=	$list[0]->content;
	return $news;
}
function getSID($userId = '999') {
	$member['id']	=	1000000+intval($userId);
	$member['m']	=	floor($member['id']/1000000);
	$member['k']	=	floor($member['id']/1000);
	return $member;
}
function getUserFace($userId,$size='s') {
	//切图 s=48x48 维持比例 m=100x200 b=200x* o=原图大小
	$sizearray	=	array('s','m','b','o');
	if(in_array($size,$sizearray)){
		$s	=	$size;
	}else{
		$s	=	's';
	}
	$p	=	str_replace('/index.php','',__APP__);
	$sid	=	getSID($userId);
	$face	=	'http://'.$_SERVER['HTTP_HOST'].'/'.$p.'/Public/Uploads/User/'.$userId.'/face_'.$s.'.jpg';
	$outface=	'./Public/Uploads/User/'.$userId.'/face_'.$s.'.jpg';
	/** /
	//图片分离的时候可以启用 判断远程文件是否存在
	$exists	=	remote_file_exists($face);
	/**/
	if(!file_exists($outface)){
		return $p.'/Public/Images/noface.gif';
		exit;
	}
	return $face;
}
function remote_file_exists($url_file){
 //检测输入
 $url_file = trim($url_file);
 if (empty($url_file)) { return false; }
 $url_arr = parse_url($url_file);
 if (!is_array($url_arr) || empty($url_arr)){ return false; }

 //获取请求数据
 $host = $url_arr['host'];
 $path = $url_arr['path'] ."?". $url_arr['query'];
 $port = isset($url_arr['port']) ? $url_arr['port'] : "80";

 //连接服务器
 $fp = fsockopen($host, $port, $err_no, $err_str, 30);
 if (!$fp){ return false; }

 //构造请求协议
 $request_str = "GET ".$path." HTTP/1.1\r\n";
    $request_str .= "Host: ".$host."\r\n";
    $request_str .= "Connection: Close\r\n\r\n";

 //发送请求
    fwrite($fp, $request_str);
 $first_header = fgets($fp, 1024);
    fclose($fp);

 //判断文件是否存在
 if (trim($first_header) == ""){ return false; }
 if (!preg_match("/200/", $first_header)){
  return false;
 }
 return true;
}
function getUserFriends($userId) {
	$friends	=	array();
	$dao	=	D('UserFriend');
	$list	=	$dao->findAll("userId='$userId'");
	if($list){
		foreach($list as $k=>$v){
			$friends[]	=	$v->friendId;
		}
	}
	return $friends;
}
/* 检查用户缓存 */
function checkUF($userId,$name) {
	$path	=	C("USER_DATA_PATH").$userId."/";
	return	is_file($path.$name.".php");
}
/* 生成用户缓存 */
function UF($userId,$name,$value='') {
	$path	=	C("USER_DATA_PATH").$userId."/";
	if($value=='clean'){
		unlink($path.$name.".php");
	}else{
		checkDir($path);
		return	F($name,$value,'-1',$path);
	}
}
function getUserRelation($userId) {
	if(intval($userId)<=0){
		return false;
	}
	$dao	=	D('Friend');
	$loginUser	=	Session::get(C("USER_AUTH_KEY"));
	$count		=	$dao->count("friendId='$userId' and userId='$loginUser'");
	if( $count > 0 ){
		$relaction[] = 'friend';
		return $relaction;
	}else{
		return false;
	}
}
function getUserSetting($userId) {
	if(intval($userId)<=0){
		return false;
	}
	$dao = D('UserSetting');
	//find($condition,$table,$fields,$cache,$relation)
	$list = $dao->find("userId='$userId'");
	return $list;
}
function getUserSex($id) {
	$dao	=	D("User");
	$list	=	$dao->find($id);
	$sex	=	getSex($list[sex]);
	return $sex;
}
function getShort($title,$length=40){
	return msubstr($title,0,$length);
}
function getSex($id) {
	if($id==1){
		echo "男";
	}elseif($id==2){
		echo "女";
	}else{
		echo "未知";
	}
}
function getAreaName($areaId){
	if($areaId==0) {
		return '';
	}
	if(Session::is_set('areaName')) {
		$name	=	Session::get('areaName');
		return $name[$userId];
	}
	$dao	=	D("Area");
	$list	=	$dao->findAll('','areaId,name');
	$nameList	=	$list->getCol('areaId,name');
	$name	=	$nameList[$areaId];
	Session::set('areaName',$nameList);
	return $name;
}
//ipEnCode
function ipEncode($ip) {
	$ip	=	explode('.',$ip);
	//直接位运算会溢出
	foreach($ip as $k=>$v){
		$ipBin	.=	str_pad(decbin($v), 8, "0", STR_PAD_LEFT);
	}
	$ipDec	=	bindec($ipBin);
	return	$ipDec;
}
//ipDeCode
function ipDecode($ipDec) {
	$ipBin	=	decbin($ipDec);
	$a	=	bindec(substr($ipBin,0,8));
	$b	=	bindec(substr($ipBin,8,8));
	$c	=	bindec(substr($ipBin,16,8));
	$d	=	bindec(substr($ipBin,24,8));
	$ip	=	$a.$b.$c.$d;
	return	$ip;
}
//getGroup
function getGroup($id) {
	$dao = D('Group');
	//getById($id,$table,$fields,$pk,$relation)
	$list = $dao->getById($id);
	return $list;
}
//getGroupName
function getGroupName($id) {
	$dao = D('Group');
	//getById($id,$table,$fields,$pk,$relation)
	$list = $dao->getById($id);
	return $list->name;
}
function getNetworkName($id) {
	$dao = D('Network')->find($id);
	$name	=	$dao->title;
	return $name;
}
function getTagName($tagId,$module='network') {
	$dao = D('Tag');
	//find($condition,$table,$fields,$cache,$relation)
	$list = $dao->find("id='$tagId' and $module='$module'");
	return $list->name;
}
function getTagId($tagName,$module='network') {
	$dao = D('Tag');
	//find($condition,$table,$fields,$cache,$relation)
	$list = $dao->find("name='$tagName' and $module='$module'");
	return $list->id;
}
function getImageByFiles($image) {
	$dot_place	=	strrpos($image,'.');
	$imageHash	=	substr($image,0,$dot_place);
	$hashPath	=	substr($imageHash,0,2).'/'.substr($imageHash,2,2).'/'.$image;
	return	$hashPath;
}
//检查并创建多级目录
function checkDir($path){
	$pathArray = explode('/',$path);
	$nowPath = '';
	array_pop($pathArray);
	foreach ($pathArray as $key=>$value){
		if ( ''==$value ){
			unset($pathArray[$key]);
		}else{
			if ( $key == 0 )
				$nowPath .= $value;
			else
				$nowPath .= '/'.$value;
			if ( !is_dir($nowPath) ){
				if ( !mkdir($nowPath, 0777) ) return false;
			}
		}
	}
	return true;
}
//取得文件后缀名
function getSuffix($filename) {
	$dot_place = strrpos($filename,'.');
	$ext_name = substr($filename,$dot_place+1);
	return $ext_name;
}
function getAlbumCover($albumId='0') {
	$albumDao	=	D('Album');
	$album		=	$albumDao->find("id = $albumId",'id,coverPhotoId');
	$photoDao	=	D('Photo');
	if($album->coverPhotoId){
		$photoId	=	$album->coverPhotoId;
		$photo		=	$photoDao->find("id='$photoId'",'id,imageId,imagePath');
		return $photo->imagePath;
	}else{
		return "./Public/Images/noface.gif";
	}
}
function getOnlineStatus($userId,$showLoginTime=false) {
	$dao = D("Online");
	$online  =  $dao->getBy('userId',$userId);
	if($online){
		return '<font color=green size=2>online!</font>';
	}elseif($showLoginTime){
		$dao	=	D('User');
		$user	=	$dao->find("id='$userId'",'id,lastLoginTime');
		return '<font size="2">上次登陆'.date('m-d,H:i',$user->lastLoginTime).'</font>';
	}else{
		return '';
	}
}

//检查用户的隐私设定
function checkPrivate($level='1',$name='Space',$id){
	//检查我是否具有 浏览 模块名为space,记录为id隐私级别为1 的数据的权限
	//读数据库,调出系统的隐私设定列表.
	$setting	=	D('UserSetting')->find('userId='.$id);
	$setting	=	unserialize($setting->privacy);
	//仅好友可见
	if(($setting[$name]) == 2){
		if(isMyFriend($id)){
			return true;
		}else{
			return false;
		}
	}else{
		return true;
	}
}
//检查用户的管理权限
function checkAdmin($name='quan',$id){
	$userId	=	Session::get('mid');
	//取得id的name的管理权限
	if($name == 'Group'){
		$dao	=	D('GroupMember');
		$info	=	$dao->find("userId='$userId' and groupId='$id'");
		if($info->level == 2){
			return true;
		}
	}else
	if($name == 'quan'){
		$dao	=	D('UserPower');
		$power	=	$dao->getBy('userId',$userId);
		if($power->rank==$name && $power->level==1){
			return true;
		}
	}
	return false;
}
//判断是否是我的朋友
function isMyFriend($userId) {
	$mid	=	$_SESSION['mid'];
	$fid	=	$userId;
	$result	=	D('UserFriend')->count("userId='$mid' and friendId='$fid'");
	if($result==0){
		return false;
	}else{
		return true;
	}
}
//判断userId是否在groupId中
function isInGroup($userId,$groupId) {
	$result	=	D('GroupMember')->count("userId='$userId' and groupId='$groupId'");
	if($result==0){
		return false;
	}else{
		return true;
	}
}
//判断userId是否在networkId中
function isInNetwork($userId,$networkId) {
	$result	=	D('NetworkMember')->count("userId='$userId' and networkId='$networkId'");
	if($result==0){
		return false;
	}else{
		return true;
	}
}
function isGroupManager($userId,$groupId){
	$users	=	D('Group')->getGroupMember($groupId,'and level=2');
	foreach ($users as $u){
			$userArray[]	=	$u->userId;
	}
	if(in_array($userId,$userArray)){
		return true;
	}else{
		return false;
	}
}
function isGroupMember($userId,$groupId){
	$users	=	D('Group')->getGroupMember($groupId);
	foreach ($users as $u){
			$userArray[]	=	$u->userId;
	}
	if(in_array($userId,$userArray)){
		return true;
	}else{
		return false;
	}
}
function getUserStatus($userId){
	if(intval($userId)>0){
		$dao	=	D("User");
		$list	=	$dao->find("id='$userId'",'id,name,email,status');
		$status	=	$list->status;
		return $status;
	}
}
function getUserStatusColor($userId) {
	$status	=	getUserStatus($userId);
	switch($status) {
		case 1:$color="#000000";break;
		case 2:$color="#ff0000";break;
		case 3:$color="#999999";break;
		case 4:$color="#0000ff";break;
		case 5:$color="#00ff00";break;
		default:$color="#0000ff";break;
	}
	return $color;
}
//加密函数
function jiami($txt,$key='thinksns'){
	$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+";
	$nh = rand(0,64);
	$ch = $chars[$nh];
	$mdKey = md5($key.$ch);
	$mdKey = substr($mdKey,$nh%8, $nh%8+7);
	$txt = base64_encode($txt);
	$tmp = '';
	$i=0;$j=0;$k = 0;
	for ($i=0; $i<strlen($txt); $i++) {
		$k = $k == strlen($mdKey) ? 0 : $k;
		$j = ($nh+strpos($chars,$txt[$i])+ord($mdKey[$k++]))%64;
		$tmp .= $chars[$j];
	}
	return $ch.$tmp;
}
//解密函数
function jiemi($txt,$key='thinksns'){
	$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+";
	$ch = $txt[0];
	$nh = strpos($chars,$ch);
	$mdKey = md5($key.$ch);
	$mdKey = substr($mdKey,$nh%8, $nh%8+7);
	$txt = substr($txt,1);
	$tmp = '';
	$i=0;$j=0; $k = 0;
	for ($i=0; $i<strlen($txt); $i++) {
		$k = $k == strlen($mdKey) ? 0 : $k;
		$j = strpos($chars,$txt[$i])-$nh - ord($mdKey[$k++]);
		while ($j<0) $j+=64;
		$tmp .= $chars[$j];
	}
	return base64_decode($tmp);
}
function getThreadTitle($threadId) {
	$dao = D('Thread')->find("id='$threadId'",'title');
	return $dao->title;
}
function getTitle($url) {
	$lines_array = file($url);
	$lines_string = implode('', $lines_array);
	eregi("<title>(.*)</title>", $lines_string, $head);
	return $head[1];
}
function getShare($id) {
	$dao = D('UserShare');
	$share = $dao->find($id);
	if($share->module=='Url'){
		$content	.=	"<div class=\"share-content-desc\"><strong>".$share->content."</strong></div>";
	}else{
		$content	.=	getShareContent($share->module,$share->recordId);
	}
}
function getShareContent($module='Space',$id) {
	$content	=	'';
	$p	=	str_replace('/index.php','',__APP__);
	if($module=='Space'){
		$blog	=	D('User')->find($id);
		$userFace	=	getUserFace($id,'m');
		$userName	=	getUserName($id);
		$userProvince	=	getUserProvince($id);
		$userNews	=	getUserNews($id);
		$content	.=	"<div class=\"share-content-photo\"><a href=\"".__APP__."/space/".$id."\"><img src=\"".$userFace."\" alt=\"".$userName."\"></a></div>";
		$content .=
		"<div class=\"share-content-desc\"><strong><a href=\"".__APP__."/space/".$id."\">".$userName."</a></strong><br>".$userProvince."<br><br>".$userNews."</div>";
	}else
	if($module=='Blog'){
		$blog	=	D('Blog')->find($id);
		$titleImage	=	getBlogTitleImage($id);
		if($titleImage){
			$content	.=	"<div class=\"share-content-photo\"><a href=\"".__APP__."/blog/".$blog->id."\"><img src=\"".$p."/Thumb/?w=72&h=72&url=".$titleImage."\" alt=\"".$blog->title."\"></a></div>";
		}
		$content .=
		"<div class=\"share-content-desc\"><strong><a href=\"".__APP__."/blog/".$blog->id."\">".$blog->title."</a></strong> - <a href=\"".__APP__."/space/".$blog->userId."\">".getUserName($blog->userId)."</a><br>".getBlogShort($blog->content)."<br><a href=\"".__APP__."/blog/".$blog->id."\">阅读全文</a></div>";
	}else
	if($module=='Photo'){
		$photo	=	D('Photo')->find($id);
		$titleImage	=	$photo->imagePath;
		$album	=	D('Album')->find($photo->albumId);
		if($titleImage){
			$content	.=	"<div class=\"share-content-photo\"><a href=\"".__APP__."/photo/".$photo->id."\"><img src=\"".$p."/Thumb/?w=130&h=200&t=f&url=".$titleImage."\" alt=\"".$photo->title."\"></a></div>";
		}
		$content .=
		"<div class=\"share-content-desc\">相册:<a href=\"".__APP__."/album/".$album->id."\">".$album->title."</a><br>用户:<a href=\"".__APP__."/space/".$photo->userId."\">".getUserName($photo->userId)."</a><br><strong><a href=\"__APP__/photo/".$photo->id."\">".$photo->title."</a></strong></div>";
	}else
	if($module=='Album'){
		$album	=	D('Album')->find($id);
		$titleImage	=	$album->coverPhotoId;
		if($titleImage>0){
			$titleImage	=	D('Photo')->find($album->coverPhotoId)->imagePath;
			$content	.=	"<div class=\"share-content-photo\"><a href=\"".__APP__."/album/".$album->id."\"><img src=\"".$p."/Thumb/?w=130&h=200&t=f&url=".$titleImage."\" alt=\"".$album->title."\"></a></div>";
		}
		$content .=
		"<div class=\"share-content-desc\"><strong><a href=\"".__APP__."/album/".$album->id."\">".$album->title."</a></strong> - <a href=\"".__APP__."/space/".$album->userId."\">".getUserName($album->userId)."</a><br>共".$album->photoCount."张照片<br>".$album->info."</div>";
	}
	return $content;
}
function getBlogTitleImage($id) {
	$dao = D('Blog');
	$content	=	$dao->find($id)->content;
	$content	=	stripslashes($content);
	$images		=	matchImages($content);
	if(false===$images){
		$retrun		=	false;
	}else{
		$retrun		=	$images[0];
	}
	return $retrun;
}
function getAlbumTitleImage($id) {
	$dao = D('Album');
	$album	=	$dao->find($id);
	if($album->coverPhotoId > 0 ){
		$image	=	D('Photo')->find($album->coverPhotoId)->imagePath;
	}else{
		$image	=	false;
	}
	return $image;
}
?>