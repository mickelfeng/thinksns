//Author: Fantasy
//create-time:08/04/12
//last-modify-time:08/05/03
/**
 * 提交留言
 *
 */
$(function(){
	$("#ajaxly").click(function(){

		var userId	= $("#userId").val();
		var guestId	= $("#guestId").val();
		var content = $("#con").val();
		var guestImg = $("#guestImg").val();
		var guestName= $("#guestName").val();
		var guestProv = $("#guestProv").val();
		var  ran=Math.random();
		
		//过滤
		if(content==""){alert("留言不能为空!");return false;}
		if(JHshStrLen(content)>200){alert("留言不能超过100个汉字!");return false;}
		
		//提示效果
		$("#flag").html("");
		$("#load").fadeIn();
		$(this).attr("disabled","disabled");
		$(this).css("background","#D4D0C8");
		$(this).val("正在提交...");
		
		//Ajax的POST提交
		$.post(APP+"/Wall/insert",{userId:userId,guestId:guestId,content:content,ran:ran},function(txt){
		    if(txt){

				//清除提示效果
				$("#load").fadeOut();
				$("#ajaxly").attr("disabled","");
				$("#ajaxly").css("background","#2782D6");
				$("#ajaxly").val("留 言");
				$("#flag").html('<A HREF="javascript:delFlag()"><font color="green">留言成功</font></A>');

		    	//清除文本域
				$("#con").val('');
				
				//留言总数加一
				var gbnum = parseInt($("#gbnum").text())+1;
				$("#gbnum").text(gbnum+" ");
				
				//如果留言已经满10条了，那么删除最后一条
				var lynum=$("#wall-ul li").size();
				if(lynum>=10){
					$("#wall-ul li:last-child").remove();
				}
				
				//最新添加的那条
				var ly=	'<li id=0'+txt+'><div class="post"><p class="image"><a href="'+APP+'/space/'+guestId+'">'+
						'<img width="48" alt='+guestName+' src='+guestImg+'></a></p><div class="info">'+
						'<span class="author"><a href="'+APP+'/space/'+guestId+'">'+guestName+'</a> ('+guestProv+')</span>'+
						'<span class="time">刚刚留言</span><span class="delete">'+
						'<a class="Qconfirm" onclick="Qconfirm_show(this.rel,this.id,this.title)" id="'+txt+'" rel="'+APP+'/Wall/delete" href="javascript:void(0)"  title="确定要删除?">删除</a></span></div>'+
				  		'<div class="content">'+content+' </div></div></li>';
						
			   //插入最新留言
			   if(lynum==0)
			   {
			   		$(ly).appendTo($("#wall-ul"));
			   }else{
				  	$(ly).insertBefore($("#wall-ul li:first-child"));
			   }
			}else{
				alert("留言失败!");
			  	return;
			}
		});//.post的结尾

  });
});
/**
 * 删除提示信息
 *
 */
function delFlag(){
	$("#flag").html("");
}

/**
 * 删除留言
 *
 */
function doAjax(rel,id){

	$('#QC').html("<b><font size=2 color=green>正在提交...</font></b>");
	var delId="#"+"0"+id;
	
	$.post(rel,{id:id},function(txt){
		  if(txt){
			//留言总数减一
			var gbnum = parseInt($("#gbnum").text())-1;
			$("#gbnum").text(gbnum+" ");
			$(delId).fadeOut('slow');
			$('#QC').html("<b><font size=2 color=blue>操作成功!</font></b>");
			setTimeout(function(){$('#QC').remove();},500);
		}else{
			alert("操作失败!");
			setTimeout(function(){$('#QC').remove();},500);
		}
	});
}	

/**
 * 打招呼
 *
 */	

 function sayHello(id){
	var  ran=Math.random();
	$("#facebox .content").html('<div class="loading">&nbsp;</div>');
	$.post(APP+'/Friend/hello',{id:id,ran:ran},function(txt){
	    if(txt){
			$("#facebox .content").html('<center><font size=3>操作成功！</font></center>');
			setTimeout(function(){$.facebox.close();},1500);
		}else{
			$("#facebox .content").html('操作失败！');
		}
	});
}

/**
 * 加为好友
 *
 */	 
 function addFriend(id){
	var  ran=Math.random();
	$("#facebox .content").html('<div class="loading">&nbsp;</div>');
	$.post(APP+'/Friend/newadd',{id:id,ran:ran},function(txt){
	    if(txt=='1'){
			$("#facebox .content").html('<center><font size=3 >成功发送好友请求！</font></center>');
		}
		else
		if(txt=='-1'){
			$("#facebox .content").html('<center><font size=3 >对方已经在你的好友名单中！</font></center>');
		}
		else
		if(txt=='-2'){
			$("#facebox .content").html('<center><font size=3 >不能加自己为好友！</font></center>');
		}
		setTimeout(function(){$.facebox.close();},2000);
	});
}