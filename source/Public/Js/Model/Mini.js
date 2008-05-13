//Author: Fantasy
//create-time:08/04/20
//last-modify-time:08/05/04

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

/**
 * 字数递减和限制字数
 *
 */
function fot(e){
	var d = document.getElementById("zishu");
	var c = e.value.length;
	var xxx = 100 - c;
	if(c<=100){
		d.innerHTML =xxx;
	}else{
		alert('只能输入100个字^_^')

	}

}
/**
 * 提交评论
 *
 */
$(function(){
//在好友心情处添加
	$("#ajaxmini").click(function(){

		var userId	= $("#userId").val();
		var userImg = $("#userImg").val();
		var userName= $("#userName").val();
		var userProv = $("#userProv").val();
		var content = $("#mbcontent").val();
		var ran=Math.random();
		//数据检验
		if(content==''){alert("不能为空!^_^");return false;}

		//提交之前
		$("#flag").html("");
		$("#load").fadeIn();
		$(this).attr("disabled","disabled");
		$(this).css("background","#D4D0C8");
		$(this).val("正在提交...");

		$.post(APP+"/Mini/insert",{content:content,ran:ran},function(txt){
			if(txt){
				//提交之后
				$("#load").fadeOut();
				$("#ajaxmini").attr("disabled","");
				$("#ajaxmini").css("background","#2782D6");
				$("#ajaxmini").val("发 布");
			//	$("#flag").html('<A HREF="javascript:delFlag()"><font color="green">添加成功^_^</font></A>');
				$("#flag").html('<A HREF="javascript:delFlag()"><font color="green">添加成功^_^</font></A>');

				//alert("aaa");
			   $("#remain").text("100");

				$("#mbcontent").val('');
				//心情总数加一
				var xqnum = parseInt($("#xqnum").text())+1;
				$("#xqnum").text(xqnum+" ");

				//显示最新心情的那条
				var xq='<ol class="minibloglist" id="0'+txt+'"><li><div class="miniblog-entry"><p class="image"><a href="'+APP+'/space/'+userId+'">'+
					'<img width="48" alt="'+userName+'" src="'+userImg+'"/></a></p><div class="content">'+
					'<span class="author"><a href="'+APP+'/space/'+userId+'">'+userName+'</a></span>'+content+'<span class="subinfo">('+userProv+')</span>'+
					'<span class="time">刚刚</span><span class="subinfo">通过网页</span><span class="delete"><a class="Qconfirm" onclick="Qconfirm_show(this.rel,this.id,this.title)" id="'+txt+'" rel="'+APP+'/Mini/delete" href="javascript:void(0)"  title="确定要删除?">删除</a></span></div></div></li>';

			   if((xqnum-1)==0)
			   {
			   		$(xq).appendTo($("#xqlist"));
			   }else{
				  	$(xq).insertBefore($("ol.minibloglist:first-child"));
			   }

				$("#zishu").html('100');

			}else{
				alert("添加心情失败!");
			}
		});
	});



//在我的心情处添加
	$("#ajaxminimy").click(function(){
		//alert("test");
		var content = $("#mbcontent").val();
		var ran=Math.random();
		if(content==''){alert("不能为空!^_^");return false;}

		//提交之前
		$("#flag").html("");
		$("#load").fadeIn();
		$(this).attr("disabled","disabled");
		$(this).css("background","#D4D0C8");
		$(this).val("正在提交...");

		$.post(APP+"/Mini/insert",{content:content,ran:ran},function(id){
			if(id){
				//提交之后
				$("#load").fadeOut();
				$("#ajaxminimy").attr("disabled","");
				$("#ajaxminimy").css("background","#2782D6");
				$("#ajaxminimy").val("发 布");
				$("#flag").html('<A HREF="javascript:delFlag()"><font color="green">添加成功^_^</font></A>');
				$("#mbcontent").val('');

				//心情总数加一
				var xqnum = parseInt($("#xqnum").text())+1;
				$("#xqnum").text(xqnum+" ");
				
				var xq	= '<ol class="minibloglist" id="0'+id+'">\
							<li>\
								<div class="miniblog-entry na">\
									<div class="content">\
											'+content+'<span class="time">刚刚</span><span class="subinfo">通过网页</span>\
											<span class="delete"><a class="Qconfirm" onclick="Qconfirm_show(this.rel,this.id,this.title)" id="'+id+'" rel="'+APP+'/Mini/delete" href="javascript:void(0)" title="确定要删除?">删除</a></span>\
										</div>\
								</div>\
							</li>\
						   </ol>';


			   if((xqnum-1)==0)
			   {
			   		$(xq).appendTo($("#xqlist"));
			   }else{

				  	$(xq).insertBefore($("ol.minibloglist:first-child"));
			   }
			   $("#zishu").html('100');

			}else{
				alert("添加心情失败!");
			}
		});
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
 *  删除心情
 *
 */
function doAjax(rel,id){
	$('#QC').html("<b><font size=2 color=green>正在提交...</font></b>");
	var delId="#"+"0"+id;
	
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
