/*
 * Base on Jquery (jquery.com) & JTip (http://www.codylindley.com)
 * Qtip By melec@163.com
 * Qconfirm By melec@163.com
 * todo:现在必须给当前的tip或者confirm对象,加上ID属性才可以识别,还可以改进一下
 */

$(document).ready(Qtip_init);
function Qtip_init(){
	$("a.Qtip").hover(function(){Qtip_show(this.id,this.title)},function(){$('#QT').remove()});
	$("a.Qconfirm").click(function(){
		if(this.rel==''){
			this.rel=this.href;
		}
		this.href='javascript:void(0)';
		Qconfirm_show(this.rel,this.id,this.title);
	});
}
function Qtip_show(linkId,title){
	var de = document.documentElement;
	var w = self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
	var hasArea = w - getAbsoluteLeft(linkId);
	var clickElementy = getAbsoluteTop(linkId) - 3; //set y position
	var width = 250;

	if(hasArea>((width*1)+75)){
		$("body").append("<div id='QT' style='width:"+width*1+"px'><div id='QT_arrow_left'></div><div id='QT_close_left'><div id='QT_copy'>"+title+"</div></div></div>");//right side
		var arrowOffset = getElementWidth(linkId) + 11;
		var clickElementx = getAbsoluteLeft(linkId) + arrowOffset; //set x position
	}else{
		$("body").append("<div id='QT' style='width:"+width*1+"px'><div id='QT_arrow_right' style='left:"+((width*1)+1)+"px'></div><div id='QT_close_right'><div id='QT_copy'>"+title+"</div></div></div>");//left side
		var clickElementx = getAbsoluteLeft(linkId) - ((width*1) + 15); //set x position
	}
	$('#QT').css({left: clickElementx+"px", top: clickElementy+"px"});
	$('#QT').show();
}
function Qconfirm_show(rel,linkId,title){
	var de = document.documentElement;
	var w = self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
	var hasArea = w - getAbsoluteLeft(linkId);
	var clickElementy = getAbsoluteTop(linkId) - 3; //set y position
	var width = 250;

	if(hasArea>((width*1)+75)){
		$("body").append("<div id='QC' style='width:"+width*1+"px'><span>"+title+"</span><div class='button'><input name='yes' type='button' class='yes f-button' value='确定' rel='"+rel+"' /> <input name='no' type='button' class='no f-alt' value='取消' /></div></div>");//right side
		var arrowOffset = getElementWidth(linkId) + 11;
		var clickElementx = getAbsoluteLeft(linkId) + arrowOffset; //set x position
	}else{
		$("body").append("<div id='QC' style='width:"+width*1+"px'><span>"+title+"</span><div class='button'><input name='yes' type='button' class='yes f-button' value='确定' rel='"+rel+"' /> <input name='no' type='button' class='no f-alt' value='取消' /></div></div>");//left side
		var clickElementx = getAbsoluteLeft(linkId) - ((width*1) + 15); //set x position
	}
	$('#QC').css({left: clickElementx+"px", top: clickElementy+"px"});
	$("#QC .yes").click(function(){doAjax(rel,linkId);});
	$("#QC .no").click(function(){$('#QC').remove()});
	$('#QC').show();
}
function JT_show(url,linkId,title){
	if(title == false)title="&nbsp;";
	var de = document.documentElement;
	var w = self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
	var hasArea = w - getAbsoluteLeft(linkId);
	var clickElementy = getAbsoluteTop(linkId) - 3; //set y position

	var queryString = url.replace(/^[^\?]+\??/,'');
	var params = parseQuery( queryString );
	if(params['width'] === undefined){params['width'] = 250};
	if(params['link'] !== undefined){
	$('#' + linkId).bind('click',function(){window.location = params['link']});
	$('#' + linkId).css('cursor','pointer');
	}

	if(hasArea>((params['width']*1)+75)){
		$("body").append("<div id='JT' style='width:"+params['width']*1+"px'><div id='JT_arrow_left'></div><div id='JT_close_left'>"+title+"</div><div id='JT_copy'><div class='JT_loader'><div></div></div>");//right side
		var arrowOffset = getElementWidth(linkId) + 11;
		var clickElementx = getAbsoluteLeft(linkId) + arrowOffset; //set x position
	}else{
		$("body").append("<div id='JT' style='width:"+params['width']*1+"px'><div id='JT_arrow_right' style='left:"+((params['width']*1)+1)+"px'></div><div id='JT_close_right'>"+title+"</div><div id='JT_copy'><div class='JT_loader'><div></div></div>");//left side
		var clickElementx = getAbsoluteLeft(linkId) - ((params['width']*1) + 15); //set x position
	}

	$('#JT').css({left: clickElementx+"px", top: clickElementy+"px"});
	$('#JT').show();
	$('#JT_copy').load(url);

}
//判断位置的公共函数
function getElementWidth(objectId) {
	x = document.getElementById(objectId);
	return x.offsetWidth;
}
function getAbsoluteLeft(objectId) {
	// Get an object left position from the upper left viewport corner
	o = document.getElementById(objectId)
	oLeft = o.offsetLeft            // Get left position from the parent object
	while(o.offsetParent!=null) {   // Parse the parent hierarchy up to the document element
		oParent = o.offsetParent    // Get parent object reference
		oLeft += oParent.offsetLeft // Add parent left position
		o = oParent
	}
	return oLeft
}
function getAbsoluteTop(objectId) {
	// Get an object top position from the upper left viewport corner
	o = document.getElementById(objectId)
	oTop = o.offsetTop            // Get top position from the parent object
	while(o.offsetParent!=null) { // Parse the parent hierarchy up to the document element
		oParent = o.offsetParent  // Get parent object reference
		oTop += oParent.offsetTop // Add parent top position
		o = oParent
	}
	return oTop
}
//解析url
function parseQuery ( query ) {
   var Params = new Object ();
   if ( ! query ) return Params; // return empty object
   var Pairs = query.split(/[;&]/);
   for ( var i = 0; i < Pairs.length; i++ ) {
      var KeyVal = Pairs[i].split('=');
      if ( ! KeyVal || KeyVal.length != 2 ) continue;
      var key = unescape( KeyVal[0] );
      var val = unescape( KeyVal[1] );
      val = val.replace(/\+/g, ' ');
      Params[key] = val;
   }
   return Params;
}
function blockEvents(evt) {
	if(evt.target){
		evt.preventDefault();
	}else{
		evt.returnValue = false;
	}
}