<?php
class PhotoAction extends BaseAction
{
	function _initialize(){
		parent::_initialize();
	}
	//相册首页
	function index() {
		$map['userId']	=	$this->uid;

		$dao	=	D("Album");
		$count	=	$dao->count($map,'id');
		$rows	=	20;
		$p		=	new Page($count,$rows);
		$list	=	$dao->findAll($map,'*','id asc',$p->firstRow.','.$p->listRows);
		$page	=	$p->show();

		$this->assign('albumCount',$count);
		$this->assign('userId',$userId);
		$this->assign('list',$list);
		$this->assign('page',$page);
		$this->display();
	}
	//好友相册
	function friends() {
		$friendArray	=	getUserFriends($this->mid);
		//加入我的ID
		array_push($friendArray,$this->mid);
		$friends=	implode(",",$friendArray);
		$map	=	"userId in (".$friends.")";

		$dao	=	D("Album");
		$count	=	$dao->count($map,'id');
		$rows	=	10;
		$p		=	new Page($count,$rows);
		$list	=	$dao->findAll($map,'*','cTime desc',$p->firstRow.','.$p->listRows);

		$page	=	$p->show();

		$this->assign('albumCount',$count);
		$this->assign('userId',$userId);
		$this->assign('list',$list);
		$this->assign('page',$page);
		$this->display();
	}
	//相册显示
	function album() {
		if($_REQUEST['albumId']){
			$albumId = $_REQUEST['albumId'];
		}else{
			$this->error('错误的相册ID！');
		}
		$dao	=	D('Album');
		$album	=	$dao->getById($albumId);

		$map	=	"albumId='$albumId'";

		$dao	= D("Photo");
		$count	= $dao->count($map);
		$listRows	=	20;
		$p			=	new Page($count,$listRows);
		$voList		=	$dao->findAll($map,'*','id asc',$p->firstRow.','.$p->listRows);
		$page		=	$p->show();

		$this->assign('album',$album);
		$this->assign('count',$count);
		$this->assign('list',$voList);
		$this->assign('page',$page);
		$this->display();
	}
	//相册显示
	function image() {
		if($_REQUEST['id']){
			$photoId = $_REQUEST['id'];
		}else{
			$this->error('错误的照片ID！');
		}
		$dao	=	D("Photo");
		$photo	=	$dao->find($photoId);

		if(!$photo->id){
			$this->error('照片不存在或已被删除！');
		}
		//更新图片阅读数
		$dao->setInc('readCount',"id='$photoId'");

		$dao	=	D('Album');
		$album	=	$dao->getById($photo->albumId);

		//相册的图片列表缓存在album里也可以读photo表获取

		$photos	=	explode(',',$album->photoIds);
		$pindex	=	array_flip($photos);
		//总数
		$photoCount	=	$album->photoCount;
		//当前位置
		$now	=	$pindex[$photoId];
		$pre	=	$photos[$now-1];
		$next	=	$photos[$now+1];
		$now	=	$now+1;
		//前一个photoId
		if($now == 1){
			$pre	=	$photos[$photoCount-1];
		}
		//下一个photoId
		if($now == $photoCount){
			$next	=	$photos[0];
		}

		//相册评论
		$map = "recordId='$photoId' and module='Photo'";
		$dao	=	D("Comment");
		$commentCount	= $dao->count($map);
		$rows	=	10;
		$p		=	new Page($count,$rows);
		$voList	=	$dao->findAll($map,'*','id asc',$p->firstRow.','.$p->listRows);

		$page	=	$p->show();
		$this->assign('comments',$voList);
		$this->assign('commentCount',$commentCount);
		$this->assign('page',$page);

		$this->assign('photo',$photo);
		$this->assign('album',$album);
		$this->assign('photoCount',$photoCount);
		$this->assign('now',$now);
		$this->assign('pre',$pre);
		$this->assign('next',$next);
		$this->assign('userId',Session::get(C('USER_AUTH_KEY')));
		$this->display();
	}
	//创建相册
	function create() {
		$this->assign('userId',$this->mid);
		$this->display();
	}
	//执行创建相册
	function doCreateAlbum() {
		$userId	=	$this->mid;
		if(empty($_POST[title])){
			$this->error("无标题!");
		}else{
			$title = $_POST[title];
		}
		$info = $_POST[info];
		$map = new HashMap();
		$map->put('userId',$userId);
		$map->put('title',$title);
		$map->put('info',$_POST[info]);
		$map->put('cTime',time());
		$map->put('mTime',time());
		$map->put('aTime',time());
		$dao = D('Album');
		if($result = $dao->add($map)){
			/* add_user_feed */
				$feedTitle	=	"创建了一个相册，<a href='".__APP__."/Photo/album/albumId/$result'>$title</a>";
				$this->addUserFeed($userId,'add','photo',$result,$feedTitle);
			/* /add_user_feed */
			$this->assign('albumTitle',$title);
			$this->assign('albumId',$result);
			$this->assign('userId',$userId);
			header('location:'.__APP__.'/Photo/uploads/albumId/'.$result);
		}else{
			$this->error("相册创建失败!");
		}
	}
	//编辑相册
	public function editAlbum() {
		if(empty($_GET['albumId'])){
			$this->error("相册不存在!");
		}else{
			$albumId = $_GET[albumId];
		}
		$dao	=	D('Album');
		$list	=	$dao->getById($albumId);
		if($list->userId!=$this->mid){
			$this->error('只能编辑自己的相册！');
		}
		$this->assign('vo',$list);
		$this->display();
	}
	public function doEditAlbum() {
		//更新相册数据
		$this->updateAlbum($_POST['id']);
		//更新相册信息
		$dao = D('Album');
		$vo = $dao->create();
		$vo->mTime	=	time();
		$vo->userId	=	$this->mid;
		if($dao->save($vo)){
			header("location:".__APP__."/Photo/album/albumId/".$_POST['id']);
		}else{
			$this->error("相册信息修改失败！");
		}
	}
	//编辑图片
	public function editPhotos() {
		$userId	=	$this->mid;
		if(empty($_GET['albumId'])){
			$this->error("相册不存在!");
		}else{
			$albumId = $_GET['albumId'];

			$dao	=	D('Album');
			$list	=	$dao->getById($albumId);

			if($list->userId!=$userId){
				$this->error('只能编辑自己的相片！');
			}

			$this->assign('album',$list);

			$dao	=	D('Photo');
			if($_GET['photoIds']){
				$list	=	$dao->findAll("albumId='$albumId' and id in($_GET[photoIds])");
			}else{
				$list	=	$dao->findAll("albumId='$albumId'");
			}
			$this->assign('list',$list);
		}
		$this->display();
	}
	public function doEditPhotos() {
		$albumId		=	intval($_POST['albumId']);
		$coverPhotoId	=	intval($_POST['coverPhotoId']);
		$dao = D('Photo');
		if($coverPhotoId>0){
			D('Album')->setField('coverPhotoId',$coverPhotoId,"id='$albumId'");
		}
		if($_POST['info']){
			foreach($_POST['info'] as $k=>$v){
				$photo['id']		= $k;
				$photo['info']		= $v;
				$photo['albumId']	= $albumId;
				$result[] = $dao->save($photo);
				unset($photo);
			}
		}
		if($_POST['delete']){
			foreach($_POST['delete'] as $k=>$v){
				$delete[] = $k;
				$result[] = $dao->deleteById($k);
			}
		}
		$this->updateAlbum($albumId);
		header("location:".__APP__."/album/".$albumId);
	}
	//删除照片
	public function deletePhoto() {
		$id	=	$_GET['id'];
		$dao = D('Photo');
		$photo		=	$dao->find($id,'id,albumId,userId,imagePath');
		$albumId	=	$photo->albumId;
		if( $photo->userId != $this->mid ){
			$this->error('只能删除自己的照片!');
		}else{
			//删除照片记录
			$result =	$dao->deleteById($id);
			//删除图片
			unlink($photo->imagePath);
			//重置相册数据
			//D('Album')->setDec('photoCount',"id='$albumId'");
			$this->updateAlbum($albumId);
		}
		if($result){
			header('location:'.__APP__.'/Photo/album/albumId/'.$albumId);
		}else{
			$this->error('删除照片失败！');
		}
	}
	//删除相册
	public function delAlbum() {
		$id	=	$_GET['albumId'];
		$dao = D('Album');
		$album	=	$dao->find($id,'id,userId');
		if( $album->userId != $this->mid ){
			$this->error('只能删除自己的相册!');
		}else{
			$result =	$dao->deleteById($id);
		}
		if($result){
			D('Photo')->deleteAlbumPhotos($id);
			header('location:'.__APP__.'/photos/'.$this->mid);
		}else{
			$this->error('删除相册失败！');
		}
	}

	//上传新图片
	public function upload() {
		$userId	=	Session::get(C('USER_AUTH_KEY'));
		$dao	=	D('Album');
		$count	=	$dao->count("userId='$userId'",'id');
		$list	=	$dao->findAll("userId='$userId'",'id,title,photoCount');
		foreach( $list	as $v ){
			$options .= "<option value='$v->id'>$v->title ($v->photoCount)</option>\n";
		}
		$this->assign('albumCount',$count);
		$this->assign('albumOptions',$options);
		$this->display();
	}

	//上传更多图片
	public function uploads() {
		$dao	=	D('Album');
		$id		=	$_GET['albumId'];
		$list	=	$dao->find($id);
		/* 限制照片数 * /
		if($list->photoCount > 100){
			$this->error('当个相册照片数目超过100张，请再建一个新相册！');
		}
		/**/
		$this->assign('albumTitle',$list->title);
		$this->assign('albumId',$id);
		$this->assign('userId',$list->userId);
		$this->assign('photoCount',$list->photoCount);
		$this->display();
	}
	//执行批量上传操作
	public function doUpload() {
		$userId	=	$this->mid;
		$albumId=	$_POST['albumId'];
		$path	=	'./Public/Uploads/Photo/'.$albumId.'/';
		$info	=	$this->_upload($path);
		if($info){
			//保存图片数据库
			$photoDao = D('Photo');
			foreach($info as $k=>$i){
				//更新photo数据库
				$photo['albumId']		=	$albumId;
				//$photo['imageId']		=	$imageid;
				$photo['userId']		=	$userId;
				$photo['cTime']			=	time();
				$photo['imagePath']		=	$i['savepath'].$i['savename'];
				if($photoid	=	$photoDao->add($photo)){
					$photos[]	=	$photoid;
					$feedInfo	.=	"<p class=\"image\"><a href=\"".__APP__."/Photo/image/id/{$photoid}\"><img src=\"".WEB_PUBLIC_URL."/Thumb/?w=100&h=100&t=f&url={$photo[imagePath]}\" alt=\"照片\" /></a></p>";
				}
			}
			$photoCount	=	count($photos);
			//更新相册
			D('Album')->setInc('photoCount',"id='$albumId'",$photoCount);
			//$this->updateAlbum($albumId);
			//记录动态
			$photoIds	=	implode(',',$photos);
			if($photoCount>0){
			/* add_user_feed */
				$feedTitle	=	"上传了{$photoCount}张照片";
				$this->addUserFeed($userId,'add','photo',$photoIds,$feedTitle,$feedInfo);
			/* /add_user_feed */
			}
			//跳转编辑照片
			header('location:'.__APP__.'/Photo/editPhotos/albumId/'.$albumId.'/photoIds/'.$photoIds);
		}else{
			$this->error('上传失败！');
		}
	}
	protected function updateAlbum($albumId) {

		$photoDao	=	D('Photo');
		$albumDao	=	D('Album');

		//重置相册数
		$result = $photoDao->findAll("albumId='$albumId'",'id','id asc');
		foreach($result as $v){
			 $photos[]	=	$v->id;
		}
		$count	=	$photoDao->count("albumId='$albumId'");

		$photoIds	=	implode(',',$photos);
		$albumDao->setField('photoCount',$count,"id='$albumId'");
		$albumDao->setField('photoIds',$photoIds,"id='$albumId'");
		$albumDao->setField('mTime',time(),"id='$albumId'");

		//重置相册封面
		$album		=	$albumDao->find($albumId);
		$coverId	=	$album->coverPhotoId;
		if($photoDao->count("id='$coverId'") == 0){
			$cover = $photoDao->findAll("albumId='$albumId'",'id','id desc',1);
			$albumDao->setField('coverPhotoId',$cover[0]->id,"id='$albumId'");
		}
	}
}
?>