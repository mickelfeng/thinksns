//Author: Fantasy
//create-time:08/04/12
//last-modify-time:08/05/05

/**
 * 忽略和载入动态
 *
 */
$(function(){
	//忽略
	$("a.delAlert").click(function(){
		var delId="#"+"0"+this.id;
		$.get(APP+"/Index/delAlert",{id:this.id},function(txt){
			if(txt){
				$(delId).remove();
			}else{
				alert("忽略失败，请稍后再试一试!")
			}

	});
});
	$("#feed").html('<br /><center><img src="'+PUBLIC+'/Images/bigloading.gif" /></center>');
	//载入动态
	$("#feed").load(APP+"/Home/feed",{},function(){
	});
});

/**
 * 回敬招呼
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
	$.post(APP+'/Friend/insert',{id:id,ran:ran},function(txt){
	    if(txt){
			$("#facebox .content").html('<center><font size=3 >你们成为了好朋友！</font></center>');
		}
		else{
			$("#facebox .content").html('<center><font size=3 >添加好友失败！</font></center>');
		}		
		setTimeout(function(){$.facebox.close();},1500);
	});
}

