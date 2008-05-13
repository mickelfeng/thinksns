<?php
Class SpaceModel extends Model{
	var $tableName	=	"user";
	var $_link = array(
		/*
		// 用户联系信息
		array(
			'mapping_type'=>HAS_ONE,
			'class_name'=>'UserContact',
			'foreign_key'=>'id',
			'mapping_name'=>'contacts',
			'mapping_fields'=>'userId',
		),
		// 用户的工作信息
		array(
			'mapping_type'=>HAS_One,
			'class_name'=>'UserWorks',
			'foreign_key'=>'id',
			'mapping_name'=>'works',
			//'condition'=>'',
		),
		// 用户的教育信息
		array(
			'mapping_type'=>HAS_One,
			'class_name'=>'UserEducation',
			'foreign_key'=>'id',
			'mapping_name'=>'edus',
			//'condition'=>'',
		),
		// 用户的兴趣爱好
		array(
			'mapping_type'=>HAS_One,
			'class_name'=>'UserInterest',
			'foreign_key'=>'id',
			'mapping_name'=>'interests',
			//'condition'=>'',
		),
		*/
		// 用户的设定
		array(
			'mapping_type'=>HAS_One,
			'class_name'=>'UserSetting',
			'foreign_key'=>'id',
			'mapping_name'=>'settings',
			//'condition'=>'',
		),
		// 用户的网络
		array(
			'mapping_type'=>HAS_MANY,
			'class_name'=>'Network',
			'foreign_key'=>'userId',
			'mapping_name'=>'networks',
			//'condition'=>'',
		),
		// 用户的群组
		array(
			'mapping_type'=>HAS_MANY,
			'class_name'=>'GroupMember',
			'foreign_key'=>'userId',
			'mapping_name'=>'groups',
			'mapping_fields'=>'groupId',
			//'condition'=>'',
		),
		// 用户的朋友
		array(
			'mapping_type'=>HAS_MANY,
			'class_name'=>'UserFriend',
			'foreign_key'=>'userId',
			'mapping_name'=>'friends',
			'mapping_order'=>'cTime desc',
			'mapping_limit'=>'6',
			'mapping_fields'=>'friendId',
			//'condition'=>'',
		),
		// 访客
		array(
			'mapping_type'=>HAS_MANY,
			'class_name'=>'UserBrower',
			'foreign_key'=>'userId',
			'mapping_name'=>'browers',
			'mapping_order'=>'cTime desc',
			'mapping_limit'=>'6',
			'mapping_fields'=>'browerId',
			//'condition'=>'',
		),
		// 用户的feed
		array(
			'mapping_type'=>HAS_MANY,
			'class_name'=>'UserFeed',
			'foreign_key'=>'userId',
			'mapping_name'=>'feeds',
			'mapping_order'=>'cTime desc',
			'mapping_limit'=>'5',
			//'condition'=>'status=1',
		),
		// 用户的Mini博客
		array(
			'mapping_type'=>HAS_MANY,
			'class_name'=>'Mini',
			'foreign_key'=>'userId',
			'mapping_name'=>'minis',
			'mapping_order'=>'cTime desc',
			'mapping_limit'=>'1',
			//'condition'=>'status=1',
		),
		// 用户的博客
		array(
			'mapping_type'=>HAS_MANY,
			'class_name'=>'Blog',
			'foreign_key'=>'userId',
			'mapping_name'=>'blogs',
			'mapping_order'=>'cTime desc',
			'mapping_limit'=>'5',
			//'condition'=>'status=1',
		),
		// 用户的相册
		array(
			'mapping_type'=>HAS_MANY,
			'class_name'=>'Album',
			'foreign_key'=>'userId',
			'mapping_name'=>'albums',
			'mapping_order'=>'cTime desc',
			'mapping_limit'=>'2',
			//'condition'=>'status=1',
		),
		// 用户的留言
		array(
			'mapping_type'=>HAS_MANY,
			'class_name'=>'Wall',
			'foreign_key'=>'userId',
			'mapping_name'=>'walls',
			'mapping_order'=>'cTime desc',
			'mapping_limit'=>'20',
			//'condition'=>'status=1',
		),
	);
}
?>