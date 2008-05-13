<?php
return array(
	'login'		=> array('Public','login','referer'),
	'logout'	=> array('Public','logout'),
	'reg'		=> array('Public','register','inviteUserId'),
	'activate'	=> array('Public','activate','code'),
	'home'		=> array('Home','index'),
	//个人空间
	'space'		=> array('Space','index','uid'),
	//我的心情频道
	'mini'		=> array('Mini','index','uid'),
	//我的日志频道
	'blogs'		=> array('Blog','index','uid'),
	'blog'		=> array('Blog','content','id'),
	//我的相册频道
	'photos'	=> array('Photo','index','uid'),
	'album'		=> array('Photo','album','albumId'),
	'photo'		=> array('Photo','image','id'),
	//我的朋友频道
	//我的群组频道
	'group'	=> array('Group','show','id'),
	//我的网络频道
	//我的分享频道
	'share'	=> array('Share','index','id'),
	//我的留言频道
);
?>