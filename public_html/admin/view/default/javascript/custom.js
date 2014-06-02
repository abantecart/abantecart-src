jQuery(window).load(function() {
   
   // Page Preloader
   $('#status').fadeOut();
   $('#preloader').delay(350).fadeOut(function(){
      $('body').delay(350).css({'overflow':'visible'});
   });
});

jQuery(document).ready(function() {
	//Process selected menu on page load
    URL = String(document.location);
    var route = getURLVar(URL, 'rt');
    if (!route) {
        $('#menu_dashboard').addClass('active').addClass('nav-active');
    } else {
        part = route.split('/');
        url = part[0];
        if (part[1]) {
            url += '/' + part[1];
        }
        if (part[2]) {
            url += '/' + part[2];
        }
        var link = $('#menu_box a[href*=\'' + url + '&\']');
        if (link.length == 0) {
            var link = $('#menu_box a[href*=\'' + part[0] + '/' + part[1] + '&\']');
        }
        if (link.length) {
            link.parents('li').addClass('active').addClass('nav-active');
            link.parents('.children').css({display: 'block'});
        }
    }
   
   // Toggle Menu top level
   $('.leftpanel .nav-parent.level0 > a').live('click', function() {  
      var parent = $(this).parent();
      var sub = parent.find('> ul');
      // Dropdown works only when leftpanel is not collapsed
      if(!$('body').hasClass('leftpanel-collapsed')) {
         if(sub.is(':visible')) {
            sub.slideUp(200, function(){
               parent.removeClass('nav-active');
               $('.mainpanel').css({height: ''});
               adjustmainpanelheight();
            });
         } else {
            closeVisibleSubMenu();
            parent.addClass('nav-active');
            sub.slideDown(200, function(){
               adjustmainpanelheight();
            });
         }
      }
      return false;
   });

   // Toggle Menu second level   
   $('.children .nav-parent.level1 > a').live('click', function() {
      var parent = $(this).parent();
      var sub = parent.find('ul.child2');
      // Dropdown works only when leftpanel is not collapsed
      if(!$('body').hasClass('leftpanel-collapsed')) {
         if(sub.is(':visible')) {
            sub.slideUp(200, function(){
               parent.removeClass('nav-active');
               $('.mainpanel').css({height: ''});
               adjustmainpanelheight();
            });
         } else {
            parent.addClass('nav-active');
            sub.slideDown(200, function(){
               adjustmainpanelheight();
            });
         }
      }
      return false;
   });
      
   function closeVisibleSubMenu() {
      $('.leftpanel .nav-parent').each(function() {
         var t = $(this);
         if(t.hasClass('nav-active')) {
            t.find('> ul').slideUp(200, function(){
               t.removeClass('nav-active');
            });
         }
      });
   }
   
   function adjustmainpanelheight() {
      // Adjust mainpanel height
      var docHeight = jQuery(document).height() - $('#footer').height();
      var leftHeight = $('.leftpanel').height();
      if(leftHeight > $('.mainpanel').height())
         $('.mainpanel').height(docHeight);
   }
   
   // Tooltip
   $('.tooltips').tooltip({ container: 'body'});
   
   // Popover
   $('.popovers').popover();
   
   // Close Button in Panels
   $('.panel .panel-close').click(function(){
      $(this).closest('.panel').fadeOut(200);
      return false;
   });
   
   // Form Toggles
   $('.toggle').toggles({on: true});
   
   $('.toggle-chat1').toggles({on: false});
   
   // Sparkline
   $('#sidebar-chart').sparkline([4,3,3,1,4,3,2,2,3,10,9,6], {
	  type: 'bar', 
	  height:'30px',
      barColor: '#428BCA'
   });
   
   $('#sidebar-chart2').sparkline([1,3,4,5,4,10,8,5,7,6,9,3], {
	  type: 'bar', 
	  height:'30px',
      barColor: '#D9534F'
   });
   
   $('#sidebar-chart3').sparkline([5,9,3,8,4,10,8,5,7,6,9,3], {
	  type: 'bar', 
	  height:'30px',
      barColor: '#1CAF9A'
   });
   
   $('#sidebar-chart4').sparkline([4,3,3,1,4,3,2,2,3,10,9,6], {
	  type: 'bar', 
	  height:'30px',
      barColor: '#428BCA'
   });
   
   $('#sidebar-chart5').sparkline([1,3,4,5,4,10,8,5,7,6,9,3], {
	  type: 'bar', 
	  height:'30px',
      barColor: '#F0AD4E'
   });
   
   // Minimize Button in Panels
   $('.minimize').click(function(){
      var t = $(this);
      var p = t.closest('.panel');
      if(!$(this).hasClass('maximize')) {
         p.find('.panel-body, .panel-footer').slideUp(200);
         t.addClass('maximize');
         t.html('&plus;');
      } else {
         p.find('.panel-body, .panel-footer').slideDown(200);
         t.removeClass('maximize');
         t.html('&minus;');
      }
      return false;
   });
   
   // Add class everytime a mouse pointer hover over it on both levels
   $('.nav-bracket > li').hover(function(){
      $(this).addClass('nav-hover');
   }, function(){
      $(this).removeClass('nav-hover');
   });
   $('.nav-bracket .child1 > li').hover(function(){
      $(this).addClass('nav-hover');
   }, function(){
      $(this).removeClass('nav-hover');
   });
   
   // Menu Toggle
   $('.menutoggle').click(function(){
		if(jQuery.cookie('leftpanel-collapsed')) {
			$.removeCookie("leftpanel-collapsed");
		} else {
			$.cookie('leftpanel-collapsed', 1);
		}   
		var body = $('body');
		var bodypos = body.css('position');
		if(bodypos != 'relative') {
		   if(!body.hasClass('leftpanel-collapsed')) {
		      body.addClass('leftpanel-collapsed');
		      $('.nav-bracket ul').attr('style','');
		      $(this).addClass('menu-collapsed');
		   } else {
		      body.removeClass('leftpanel-collapsed chat-view');
		      $('.nav-bracket li.active ul').css({display: 'block'});
		      $(this).removeClass('menu-collapsed');
		   }
		} else {       
		   if(body.hasClass('leftpanel-show')) {
		      body.removeClass('leftpanel-show');
		   }
		   else {
		      body.addClass('leftpanel-show');
		   }
		   adjustmainpanelheight();         
		}
   });
   
   // Chat View
   $('#quickview').click(function(){
      var body = $('body');
      var bodypos = body.css('position');
      if(bodypos != 'relative') {
         if(!body.hasClass('chat-view')) {
            body.addClass('leftpanel-collapsed chat-view');
            $('.nav-bracket ul').attr('style','');
            $('#quickview').addClass('dropdown-toggle');
         } else {
            body.removeClass('chat-view');
            if(!$('.menutoggle').hasClass('menu-collapsed')) {
               $('body').removeClass('leftpanel-collapsed');
               $('.nav-bracket li.active ul').css({display: 'block'});
               $('#quickview').removeClass('dropdown-toggle');
            }
         }
      } else {
         if(!body.hasClass('chat-relative-view')) {
            body.addClass('chat-relative-view');
            body.css({left: ''});
         } else {
            body.removeClass('chat-relative-view');   
         }
      }
   });
   
   reposition_topnav();
   reposition_searchform();
   
   jQuery(window).resize(function(){
      if($('body').css('position') == 'relative') {
         $('body').removeClass('leftpanel-collapsed chat-view');
      } else {
         $('body').removeClass('chat-relative-view');         
         $('body').css({left: '', marginRight: ''});
      }
      reposition_searchform();
      reposition_topnav();
   });
   
   function reposition_searchform() {
      if($('.searchform').css('position') == 'relative') {
         $('.searchform').insertBefore('.leftpanelinner .userlogged');
      } else {
         $('.searchform').insertBefore('.header-right');
      }
   }

   function reposition_topnav() {
      if($('.nav-horizontal').length > 0) {
         if($('.nav-horizontal').css('position') == 'relative') {                         
            if($('.leftpanel .nav-bracket').length == 2) {
               $('.nav-horizontal').insertAfter('.nav-bracket:eq(1)');
            } else {
               // only add to bottom if .nav-horizontal is not yet in the left panel
               if($('.leftpanel .nav-horizontal').length == 0)
                  $('.nav-horizontal').appendTo('.leftpanelinner');
            }
            
            $('.nav-horizontal').css({display: 'block'})
                                  .addClass('nav-pills nav-stacked nav-bracket');
            
            $('.nav-horizontal .children').removeClass('dropdown-menu');
            $('.nav-horizontal > li').each(function() { 
               $(this).removeClass('open');
               $(this).find('a').removeAttr('class');
               $(this).find('a').removeAttr('data-toggle');
            });
            if($('.nav-horizontal li:last-child').has('form')) {
               $('.nav-horizontal li:last-child form').addClass('searchform').appendTo('.topnav');
               $('.nav-horizontal li:last-child').hide();
            }
         
         } else {
            if($('.leftpanel .nav-horizontal').length > 0) {
               
               $('.nav-horizontal').removeClass('nav-pills nav-stacked nav-bracket')
                                        .appendTo('.topnav');
               $('.nav-horizontal .children').addClass('dropdown-menu').removeAttr('style');
               $('.nav-horizontal li:last-child').show();
               $('.searchform').removeClass('searchform').appendTo('.nav-horizontal li:last-child .dropdown-menu');
               $('.nav-horizontal > li > a').each(function() {              
                  $(this).parent().removeClass('nav-active');
                  if($(this).parent().find('.dropdown-menu').length > 0) {
                     $(this).attr('class','dropdown-toggle');
                     $(this).attr('data-toggle','dropdown');  
                  }
               });              
            }
         }
      }
   }
   
	$('.sticky_header').click(function(){
		if(jQuery.cookie('sticky-header')) {
			$.removeCookie("sticky-header");
	   		$('body').removeClass('stickyheader');			
	   		$('.sticky_header').removeClass('pressed_pin');
		} else {
	   		$('body').addClass('stickyheader');
	   		$.cookie("sticky-header", 1);	
	   		$('.sticky_header').addClass('pressed_pin');
		}
	});
	if(jQuery.cookie('sticky-header')) {
		$('body').addClass('stickyheader');
		$('.sticky_header').addClass('pressed_pin');
	}  
   
	$('.sticky_left').click(function(){
		if(jQuery.cookie('sticky-leftpanel')) {
			$.removeCookie("sticky-leftpanel");
	   		$('.leftpanel').removeClass('sticky-leftpanel');		
	   		$('.sticky_left').removeClass('pressed_pin');
		} else {
	   		$('.leftpanel').addClass('sticky-leftpanel');
	   		$.cookie("sticky-leftpanel", 1);	
	   		$('.sticky_left').addClass('pressed_pin');
		}
	});
	if(jQuery.cookie('sticky-leftpanel')) {
		$('.leftpanel').addClass('sticky-leftpanel');
	}   
   
   if(jQuery.cookie('leftpanel-collapsed')) {
      $('body').addClass('leftpanel-collapsed');
      $('.menutoggle').addClass('menu-collapsed');
   }      
   if($('body').hasClass('leftpanel-collapsed')) {
      $('.nav-bracket .children').css({display: ''});
   }      
   $('.dropdown-menu').find('form').click(function (e) {
      e.stopPropagation();
    });

   //adjust main content height 	
   adjustmainpanelheight();

});