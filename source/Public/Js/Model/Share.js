/**
 *  删除分享
 *
 */
function doAjax(rel,id){
	$('#QC').html("<b><font size=2 color=green>正在提交...</font></b>");
	var delId="#"+"share-"+id;
	
	$.post(rel,{id:id},function(txt){
		  if(txt){
			//心情数减一
			var xqnum = parseInt($("#xqnum").text())-1;
			$("#xqnum").text(xqnum+" ");
			//删除的消失
			$(delId).fadeOut('slow');
			//提示信息
			$('#QC').html("<b><font size=2 color=blue>操作成功!</font></b>");
			setTimeout(function(){$('#QC').remove();},500);
		}else{
			alert("操作失败!");
			setTimeout(function(){$('#QC').remove();},500);
		}
	});
}	