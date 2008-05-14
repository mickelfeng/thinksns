<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transititonal.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-cn" lang="zh-cn" dir="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="ThinkSNS" />
	<style>
	<!--
	#db{width:300px;background:#FAEBD7;padding:10px;}
	p{margin:1px;padding:4px;font-family:Arial;border:1px solid #DCDCDC;}
	-->
	</style>
	<title>安装ThinkSNS</title>
	<script language="javascript">
	<!--
	function chkform(oForm)
	{
		return true;
	}
	-->
	</script>
</head>

<?php
$phpversion	=	phpversion();
if($phpversion < '5.0.0'){
	echo "当前 php版本 ".$phpversion."低于系统最低要求！无法安装！";
}else{
	echo "当前 php版本 ".$phpversion."符合系统要求！<br> 请修改根目录下config.inc.php权限为0777<br>Public目录及子目录权限0777<br>SNS目录下Temp,Cache,Data,Logs 目录权限为0777";
}
function ErrorInfo()
{
return "<ul style='font-family:Courier;font-size:11px;background:#FDF5E6;color:#696969;margin:3px;padding:10px;border:1px solid #696969;'>Notice!: System Error<li style='font-family:Courier;list-style-type:none'>ErrInfo: ".mysql_error()."</li><li style='font-family:Courier;list-style-type:none'>ErrCode: ".mysql_errno()."</li><li style='font-family:Courier;list-style-type:none'>ErrURIs: ".$_SERVER['REQUEST_URI']."</li></ul>";
}

if(isset($_POST['install']))	//提交创建
{
	$dbserver=$_POST['dbhost'].(isset($_POST['dbport'])?(":".$_POST['dbport']):'');
	$conn=@mysql_connect($dbserver,$_POST['dbuser'],$_POST['dbpass']) or die(ErrorInfo());	//连接到MySQL Server
	if($conn)	//成功
	{
		if(isset($_POST['dropold']))@mysql_query("DROP DATABASE IF EXISTS `".$_POST['dbname']."` ;") or die(ErrorInfo());	 //按条件删除原DB
		echo "<br />创建数据库 ...";
		@mysql_query("CREATE DATABASE IF NOT EXISTS `".$_POST['dbname']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;") or die(ErrorInfo());		//创建DB
		echo "OK!";
		$linktodb=@mysql_select_db($_POST['dbname'],$conn) or die(ErrorInfo());	//打开数据库
		echo "<br />创建数据表<br /> ";
        $fp = @fopen('thinksns.sql', "r") or die("不能打开SQL文件");
		function GetNextSQL() {
                global $fp;
                $sql="";
                while ($line = @fgets($fp, 40960)) {
                        $line = trim($line);
                        //以下三句在高版本php中不需要，在部分低版本中也许需要修改
                        $line = str_replace("\\\\","\\",$line);
                        $line = str_replace("\'","'",$line);
                        $line = str_replace("\\r\\n",chr(13).chr(10),$line);
						//$line = stripcslashes($line);
                        if (strlen($line)>1) {
                                if ($line[0]=="-" && $line[1]=="-") {
                                        continue;
                                }
                        }
                        $sql.=$line.chr(13).chr(10);
                        if (strlen($line)>0){
                                if ($line[strlen($line)-1]==";"){
                                        break;
                                }
                        }
                }
                return $sql;
        }
		echo "<br>开始导入数据 <span><br>";
        while($SQL=GetNextSQL()){
                if (!mysql_query($SQL)){
                        echo "<font color=red>error：".mysql_error()."</font><br>";
                        echo "sql query：<br>".$SQL."<br>";
                };
        }
        echo "数据导入成功!</span>";

        fclose($fp) or die("Can't close file $file_name");//关闭文件
		$user_sql	=	"INSERT_INTO think_user (name,email,password) values ('管理员','".$_POST['adminname']."','".md5($_POST['adminpass'])."')";
		@mysql_query($user_sql);
		//把信息写入config.php
		if(file_exists('config.inc.php')){
			rename('config.inc.php','config_'.microtime().'.php');
		}
		if($fp=fopen("config.inc.php",'x'))
		{
			$configstr="<?\nreturn array(\n/* 数据库 设定 */\n'DB_TYPE'=>'mysql',\n'DB_HOST'=>'".$_POST['dbhost']."',\n'DB_NAME'=>'".$_POST['dbname']."',\n'DB_USER'=>'".$_POST['dbuser']."',\n'DB_PWD'=>'".$_POST['dbpass']."',\n'DB_PORT'=>'".$_POST['dbport']."',\n'DB_PREFIX'=>'think_',\n/* EMAIL 设置 */\n'SMTP_HOST'			=>	'',\n'SMTP_USER'			=>	'',\n'SMTP_PASSWORD'		=>	'',\n'SMTP_SENDER'		=>	'www.ThinkSNS.com',\n'SMTP_SENDER_EMAIL'	=>	'name@host.com',\n/* 网站 设置 */\n'SITE_HOST'			=>	'".$_POST['site_host']."',\n'SITE_NAME'			=>	'".$_POST['site_name']."',\n'SITE_TITLE'		=>	'".$_POST['site_title']."',\n'SITE_DESCRIPTION'	=>	'',\n'SITE_OPEN'	=>	'".$_POST['site_open']."',\n);\n?>";


			echo "<br />创建配置文件 <strong>config.inc.php</strong> ";
			if(fwrite($fp,$configstr))
			{
				echo "成功!<br>";
				fclose($fp);
			}
			else
			{
				echo "Failed!";
				fclose($fp);
				exit();
			}
		}
		else
		{
			echo "<br /><strong>Make config File Failed</strong> : you must manual Modify config file:<strong>config.inc.php</strong>!";
			exit();
		}
		//添加管理员
		$sql	=	"INSERT INTO think_user (email,name,password) VALUES ('".$_POST['adminname']."','管理员','".md5($_POST['adminpass'])."')";
		@mysql_query($sql) or die(ErrorInfo());
		//添加管理员权限
		$sql	=	"INSERT INTO think_user_power (userId,level,rank) VALUES ('1','1','quan')";
		@mysql_query($sql) or die(ErrorInfo());

		echo "<br />LOCK install.php <br />";
		$new_name	=	md5(microtime());
		if(rename('install.php','install_'.$new_name.'.php'))
		{
			echo "锁定成功!<br />";
		}
		else
		{
			echo "Failed!<br />";
		}

		echo "<br />安装成功! 请删除安装文件!<br><br><a href='index.php'>开始体验ThinkSNS吧</a>";
	}
	else
	{
		echo "<br /><strong>Install interrupt</strong> : Some data you input is not fit OR DATABASE server have an Error!";
		exit();
	}
}
else
{
?>
		<body>
		<h3>安装ThinkSNS</h3>
		<div id="db">
		<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" onsubmit="return chkform(this)">
		<p>数据库的服务器: <input type="text" name="dbhost" id="dbhost" value="localhost" /> *</p>
		<p>数据库通信端口: <input name="dbport" type="text" id="dbport" value="3306" />
		</p>
		<p>数据库用户名称: <input type="text" name="dbuser" id="dbuser" value="root" /> *</p>
		<p>数据库用户密码: <input type="password" name="dbpass" id="dbpass" /> *</p>
		<p>[以上数据请从数据库管理员处获取]</p>
		<p>需创建的数据库: <input type="text" name="dbname" id="dbname" value="thinksns_test"/>
		</p>
		<p><input type="checkbox" name="dropold" id="dropold" />
		  是否强制删除旧数据库? (不可恢复！)</p>
        <p>管理员Email:
          <input type="text" name="adminname" id="adminname" value="admin@admin.com" />
          *</p>
        <p>管理员密码: <input type="text" name="adminpass" id="adminpass" />
        *</p>
        <p>网站域名: <input type="text" name="site_host" id="site_host" value="http://beta.thinksns.com" />
        *</p>
        <p>网站名称: <input type="text" name="site_name" id="site_name" value="ThinkSNS官方站" />
        *</p>
        <p>网页Title信息: <input type="text" name="site_title" id="site_title" value="ThinkSNS | 免费开源SNS系统" />
        *</p>
        <p>是否开放注册:
          <label>
          <select name="site_open" id="site_open">
            <option value="1">开放注册</option>
            <option value="0">邀请注册</option>
          </select>
          </label>
        </p>
		<p><input type="submit" name="install" id="install" value="开始安装" /> <input type="reset" value="清除重写" /></p>
		</form>
		</div>
		</body>
	</html>
<?php
}
?>