/*
 * Facebox (for jQuery) @requires jQuery v1.2 or later Licensed under the MIT: http://www.opensource.org/licenses/mit-license.php Copyright 2007, 2008 Chris Wanstrath [ chris@ozmm.org ] ,2008-4-5 update by melec@163.cm
 */
(function($) {
  $.facebox = function(data) {
    $.facebox.init()
    $.facebox.loading()
    $.isFunction(data) ? data.call($) : $.facebox.reveal(data)
  }
	$.facebox.click = function(obj) {
		$.facebox.init()
		$.facebox.loading()
		$('#facebox .title').html(obj.title)
		if (obj.rel.match(/#/)) {
			var url    = window.location.rel.split('#')[0]
			var target = obj.rel.replace(url,'')
			$.facebox.reveal($(target).clone().show())
		}
		else {
			$.get(obj.rel, function(data) { $.facebox.reveal(data) })
		}
	}
	$.facebox.popup = function(title,url) {
		$.facebox.init()
		$.facebox.loading()
		$('#facebox .title').html(title)
		if (url.match(/#/)) {
			var urls    = window.location.url.split('#')[0]
			var target = url.replace(urls,'')
			$.facebox.reveal($(target).clone().show())
		}
		else {
			$.get(url, function(data) { $.facebox.reveal(data) })
		}
	}
  $.facebox.settings = {
    loading_image : 'http://cimg.bgquan.com/js/facebox/bigloading.gif',
	close_image : 'http://cimg.bgquan.com/js/facebox/close.gif',
	image_types   : [ 'png', 'jpg', 'jpeg', 'gif' ],
    facebox_html  : '\
	  <div id="facebox" style="display:none;"> \
		<table class="popup" align="center" border="0" border="0" cellpadding="0" cellspacing="0"><tr><td> \
		  <div class="body"> \
			<div class="header"> \
			  <div class="close"><a href="#">x</a></div> \
			  <div class="title"></div> \
			</div> \
			<div class="content"></div> \
		  </div> \
		</td></tr></table> \
	  </div>'
  }

  $.facebox.loading = function() {
    if ($('#facebox .loading').length == 1) return true

    $('#facebox .content').empty()
    $('#facebox .body').children().hide().end().
      append('<div class="loading"><img src="'+$.facebox.settings.loading_image+'"/></div>')

    var pageScroll = $.facebox.getPageScroll()
    $('#facebox').css({
      top:	pageScroll[1] + ($.facebox.getPageHeight() / 10),
      left:	pageScroll[0]
    }).show()

    $(document).bind('keydown.facebox', function(e) {
      if (e.keyCode == 27) $.facebox.close()
    })
  }

  $.facebox.reveal = function(data) {
    $('#facebox .content').append(data)
    $('#facebox .loading').remove()
    $('#facebox .body').children().fadeIn('fast')
  }

  $.facebox.close = function() {
    $(document).trigger('close.facebox')
    return false
  }

  $(document).bind('close.facebox', function() {
    $(document).unbind('keydown.facebox')
    $('#facebox').fadeOut(function() {
      $('#facebox .content').removeClass().addClass('content')
    })
  })

  $.fn.facebox = function(settings) {
    $.facebox.init(settings)

	    var image_types = $.facebox.settings.image_types.join('|')
	    image_types = new RegExp('\.' + image_types + '$', 'i')

    function click_handler() {
      $.facebox.loading(true)

	  // set title
	  $('#facebox .title').html(this.title)

	  // div
      if (this.href.match(/#/)) {
        var url    = window.location.href.split('#')[0]
        var target = this.href.replace(url,'')
        $.facebox.reveal($(target).clone().show())
      }
	 // image
	 else if(this.href.match(image_types)) {
		// var data = '<img s/="19.gif" />';
		// $.facebox.reveal(data);
		// alert("aaa");
       // var image = new Image()
       // image.onload = function() {
         $.facebox.reveal('<div class="image">11111<img src="' + this.href + '" /></div>')
      //  }
     //  image.src = this.href

		//var image = new Image()
       // image.onload = function() {
        //  $.facebox.reveal('<div class="image"><img src="' + image.src + '" /></div>', klass)
      //  }
       // image.src = this.href
    }
	   // ajax
	  else {
        $.get(this.href, function(data) { $.facebox.reveal(data) })
      }

      return false
    }

    this.click(click_handler)
    return this
  }

  $.facebox.init = function(settings) {
    if ($.facebox.settings.inited) {
      return true
    } else {
      $.facebox.settings.inited = true
    }

    if (settings) $.extend($.facebox.settings, settings)
    $('body').append($.facebox.settings.facebox_html)

    var preload = [ new Image(), new Image() ]
    preload[0].src = $.facebox.settings.close_image
    preload[1].src = $.facebox.settings.loading_image

    $('#facebox .close').click($.facebox.close)
	$('#facebox .fb-no').click($.facebox.close)
  }

  // getPageScroll() by quirksmode.com
  $.facebox.getPageScroll = function() {
    var xScroll, yScroll;
    if (self.pageYOffset) {
      yScroll = self.pageYOffset;
      xScroll = self.pageXOffset;
    } else if (document.documentElement && document.documentElement.scrollTop) {	 // Explorer 6 Strict
      yScroll = document.documentElement.scrollTop;
      xScroll = document.documentElement.scrollLeft;
    } else if (document.body) {// all other Explorers
      yScroll = document.body.scrollTop;
      xScroll = document.body.scrollLeft;
    }
    return new Array(xScroll,yScroll)
  }

  // adapter from getPageSize() by quirksmode.com
  $.facebox.getPageHeight = function() {
    var windowHeight
    if (self.innerHeight) {	// all except Explorer
      windowHeight = self.innerHeight;
    } else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
      windowHeight = document.documentElement.clientHeight;
    } else if (document.body) { // other Explorers
      windowHeight = document.body.clientHeight;
    }
    return windowHeight
  }


})(jQuery);

 jQuery(document).ready(function($) {
	$('a[rel*=facebox]').facebox();

})