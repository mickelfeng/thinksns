<?php
// +----------------------------------------------------------------------
// | ThinkSNS
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://www.thinksns.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: melec <melec@163.com>
// +----------------------------------------------------------------------
// $Id$
// 定义ThinkPHP框架路径
define('THINK_PATH', './ThinkPHP');
//定义项目名称和路径 默认放在SNS目录下
define('APP_NAME', 'SNS');
define('APP_PATH', './SNS');
// 加载框架入口文件
require(THINK_PATH."/ThinkPHP.php");
//实例化一个网站应用实例
$App = new App();
//应用程序初始化
$App->run();
?>