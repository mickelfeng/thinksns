//Author: Fantasy
//create-time:08/04/20
//last-modify-time:08/05/04

/**
 *计算字符串长度的函数
 *
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

/**
 * 表情的插入
 *
 */
function insert(num){
	var con = $("#p-cmt-body").val();
	var pic = "[face]"+num+"[/face]";
	$("#p-cmt-body").val(con+pic);
}

/**
 * 提交评论
 *
 */
$(function(){
	$("#ajaxcom").click(function(){
		//提取变量
		var userId	= $("#userId").val();
		var recordId	= $("#recordId").val();
		var module = $("#module").val();
		var content = $("#p-cmt-body").val();
		var dis = content.replace(/\[face\](\d*)\[\/face\]/g,'<img src='+PUBLIC+'/Images/biaoqing/$1.gif />');
		//alert(dis);

		var userImg = $("#userImg").val();
		var userName= $("#userName").val();
		var userProv = $("#userProv").val();


		var  ran=Math.random();
		//检测合法性
		if(content==""){alert("留言不能为空!");return false;}
		if(JHshStrLen(content)>200){alert("留言不能超过100个汉字!");return false;}
	
		//提示效果
		$("#flag").html("");
		$("#load").fadeIn();
		$(this).attr("disabled","disabled").css("background","#D4D0C8").val("正在提交...");
		
		//POST提交
		$.post(APP+"/Comment/insert",{userId:userId,recordId:recordId,module:module,content:content,ran:ran},function(txt){
			if(txt){
				
				//清除提示效果
				$("#load").fadeOut();
				$("#ajaxcom").attr("disabled","").css("background","#2782D6").val("评 论");
				$("#flag").html('<A HREF="javascript:delFlag()"><font color="green">评论成功</font></A>');
				
				//清除文本域
				$("#p-cmt-body").val('');
				//评论数加一
				var plnum = parseInt($("#plnum").text())+1;
				$("#plnum").text(plnum+" ");

				//显示你刚刚评论的那条
				var pl = '<li id=0'+txt+'><div class="post parent-post"><p class="image"><a href="'+APP+'/space/'+userId+'">'+
                         '<img width="48" alt="'+userName+'" src="'+userImg+'"/></a></p><div class="info"><span class="author">'+
						 '<a href="'+APP+'/space/'+userId+'">'+userName+'</a> ('+userProv+')</span><span class="time">刚刚</span>'+
                         '<span class="delete"><a class="Qconfirm" onclick="Qconfirm_show(this.rel,this.id,this.title)" id="'+txt+'" rel="'+APP+'/Comment/delete" href="javascript:void(0)"  title="确定要删除?">删除</a></span></div>'+
                         '<div class="content">'+dis+'</div></div></li>';
			   if((plnum-1)==0)
			   {
			   		$(pl).appendTo($("#threadlist"));
			   }else{
				 	$(pl).insertAfter($("#threadlist li:last-child"));
			   }
			}else{
				alert("评论失败!");
			}
		});

	});

});

function delFlag(){
	$("#flag").html("");
}

/**
 * 删除评论
 *
 */
function doAjax(rel,id){

	$('#QC').html("<b><font size=2 color=green>正在提交...</font></b>");
	var delId="#"+"0"+id;
	
	$.post(rel,{id:id},function(txt){
		  if(txt){
			$("#p-cmt-body").val('');
			//评论数减一
			var plnum = parseInt($("#plnum").text())-1;
			$("#plnum").text(plnum+" ");
			$(delId).fadeOut('slow');
			$('#QC').html("<b><font size=2 color=blue>操作成功!</font></b>");
			setTimeout(function(){$('#QC').remove();},500);
		}else{
			alert("操作失败!");
			setTimeout(function(){$('#QC').remove();},500);
		}
	});
}	




