/**
 * 导航菜单
 *
 */
$(document).ready(function() {
	var obj = null;
	$('#globalnav .withmenu').click(function(){
		$(this).find('.menu').css('visibility','visible');
		$('#globalnav .withmenu').hover(function() {
			if (obj) {
				obj.find('.menu').css('visibility','hidden');
				obj = null;
			}
			$(this).find('.menu').css('visibility','visible');
		}, function() {
			obj = $(this);
			setTimeout(function(){
				if (obj) {
					obj.find('.menu').css('visibility','hidden');
				}
			},400);
		});
	});
});

/**
 * 删除照片
 *

function delPhoto(id){
	if(confirm("确定删除么?")){
		//location.href="__APP__/Photo/deletePhoto/id/"+id;
		return true;
	}
}
 */
$(function(){
	$("#deleteId").click(function(){
		if(confirm("确定删除么?")){
			return true;
		}else{
			return false;
		}
	});
});

/**
 *包含JS文件
 *
 */
function IncludeJS(jsFile)
{
  document.write('<script type="text/javascript" src="'
    + jsFile + '"></script>'); 
}

/**
 * 计算字符串长度
 *
 *@param string sString 检测的字符串
 */
function JHshStrLen(sString)
{
   var sStr,iCount,i,strTemp ;

   iCount = 0 ;
   sStr = sString.split("");
    for (i = 0 ; i < sStr.length ; i ++)
     {
         strTemp = escape(sStr[i]);
          if (strTemp.indexOf("%u",0) == -1)
          {
              iCount = iCount + 1 ;
          }
          else
          {
              iCount = iCount + 2 ;
          }
      }

      return iCount ;
}