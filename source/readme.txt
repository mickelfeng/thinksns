ThinkSNS预览版安装说明
1,系统运行与PHP5.0 mysql4.1以上版本
2,如果是linux主机请检查目录和文件的权限.
  config.inc.php 可写 0777
  Public及子目录 可写 0777
  SNS/Cache 可写 0777
  SNS/Data  可写 0777
  SNS/Temp  可写 0777
  SNS/Logs  可写 0777

3,运行install.php
4,自行修改根目录下的config.inc.php文件需要清空 SNS/Temp目录。

============================================================
修正部分主机的兼容问题（可能还会存在）
修正不安装在根目录下的路径错误
修正短消息页面的隐藏问题
修正部分ajax错误