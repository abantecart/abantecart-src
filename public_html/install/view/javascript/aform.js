(function($){
  $.aform = {
    defaults: {
	  textClass:      'atext',
      selectClass:    'aselect',
      radioClass:     'aradio',
      checkboxClass:  'acheckbox',
      checkedClass:   'checked',
	  readonlyClass:  'readonly',
	  changedClass:   'changed',
      focusClass:     'focus',
      disabledClass:  'disabled',
      activeClass:    'active',
      hoverClass:     'hover',
      btnGrpSelector: '.abuttons_grp',
      triggerChanged: true,
      autoHide:       true,
      url:       	  ''
    },
	wrapper: '<div class="aform" />',
	mask: '<div class="afield"><div class="cl"><div class="cr"><div class="cc"></div></div></div></div>',
	maskTop: '<div class="tl"><div class="tr"><div class="tc"></div></div></div>',
	maskBottom: '<div class="bl"><div class="br"><div class="bc"></div></div></div>'
  };

  $.fn.aform = function(op) {
	var o = $.extend({},$.aform.defaults,op);
	var $buttons = $(o.btnGrpSelector);
    
    function doInput(elem){
	  var $el = $(elem);
	  
      $el.attr('ovalue', $el.val()).addClass(o.textClass)
	     .wrap($.aform.mask).closest('.afield').addClass('mask1')
		 .wrap($.aform.wrapper);
	  
	  var $form = $el.closest('.aform'), $field = $el.closest('.afield');
	  
	  if($el.is(':hidden') && o.autoHide){
        $form.hide();
      }
	  if($el.prop("readonly")){
        $field.addClass(o.readonlyClass);
      }
	  
	  $el.bind({
		"focus.aform": function(){
          $field.addClass(o.focusClass);
        },
        "blur.aform": function(){
          $field.removeClass(o.focusClass);
        },
		"keyup.aform": function(e){
		  if(e.keyCode == 13) {
			$(o.btnGrpSelector, $form).find('a:eq(0)').trigger('click');
		  }else{
			onChangedAction($el, $(this).val(), $(this).attr('ovalue'));
		  }
        },
        "change.aform": function(){
          onChangedAction($el, $(this).val(), $(this).attr('ovalue'));
        }
	  });
    }
	
	function doTextarea(elem){
	  var $el = $(elem);
	  
	  $el.attr('ovalue', $el.val()).addClass(o.textClass)
	     .wrap($.aform.mask).closest('.afield').addClass('mask2')
		 .prepend($.aform.maskTop).append($.aform.maskBottom)
		 .wrap($.aform.wrapper);
	  
	  var $form = $el.closest('.aform'), $field = $el.closest('.afield');
	  
	  if($el.is(':hidden') && o.autoHide){
        $form.hide();
      }
	  if($el.prop("readonly")){
        $field.addClass(o.readonlyClass);
      }
	  
      $el.bind({
		"focus.aform": function(){
          $field.addClass(o.focusClass);
        },
        "blur.aform": function(){
          $field.removeClass(o.focusClass);
        },
		"keyup.aform": function(e){
		  if(e.keyCode == 13) {
			$(o.btnGrpSelector, $form).find('a:eq(0)').trigger('click');
		  }else{
			onChangedAction($el, $(this).val(), $(this).attr('ovalue'));
		  }
        },
        "change.aform": function(){
		  onChangedAction($el, $(this).val(), $(this).attr('ovalue'));
        }
	  });
    }
	
	function doScrollbox(elem){
	  var $el = $(elem);
	  
	  $el.wrap($.aform.mask).closest('.afield').addClass('mask2')
		 .prepend($.aform.maskTop).append($.aform.maskBottom)
		 .wrap($.aform.wrapper);
	  
	  var $form = $el.closest('.aform');
			   
	  if($el.is(':hidden') && o.autoHide){
        $form.hide();
      }
	}
	
	function doCheckbox(elem){
	  var $el = $(elem);
	  
      $el.attr('ovalue', $el.prop("checked")).css("opacity", 0)
	     .wrap('<div class="afield"><span></span></div>');
	  
	  var $form = '', $field = $el.closest('.afield');
	  
	  if(!$el.parents('.scrollbox').length){
		$field.wrap($.aform.wrapper);
		$form = $el.closest('.aform');
	  }
	  
      $field.addClass(o.checkboxClass);
	  
	  if($el.is(':hidden') && o.autoHide){
        $form.hide();
      }
	  if($el.prop("checked")){
        $field.addClass(o.checkedClass);
      }
      if($el.prop("disabled")){
        $field.addClass(o.disabledClass);
      }

      $el.bind({
        "change.aform": function(){
          if(!$el.prop("checked")){
            $field.removeClass(o.checkedClass);
          }else{
            $field.addClass(o.checkedClass);
          }
		  onChangedAction($el, $(this).prop("checked"), $(this).attr('ovalue'));
        }
      });
    }
	
	function doSwitchButton(elem){
	  var $el = $(elem);
	  
      $el.attr('ovalue', $el.prop("checked")).css("opacity", 0)
	     .wrap('<div class="afield aswitcher"><span></span></div>')
		 .closest('.afield').wrap($.aform.wrapper);
	  
	  var $form = $el.closest('.aform'), $field = $el.closest('.afield');
	  
	  if($el.prop("checked")){
        $field.addClass(o.checkedClass);
      }
	  
	  $field.bind({
        "click.acform": function(){
          if($el.prop("checked")){
			$(this).removeClass(o.checkedClass);
			$el.removeAttr('checked');
          }else{
			$(this).addClass(o.checkedClass);
			$el.attr('checked', 'checked');
          }
		  onChangedAction($el, $el.prop("checked"), $el.attr('ovalue'));
        }
      });
	}
	
	function doSelect(elem){
	  var $el = $(elem), $elWidth = parseInt($el.outerWidth())+2;
	  
	  $el.attr('ovalue', $el.val()).css('opacity', 0).wrap($.aform.mask).before('<span />')
	     .closest('.afield').addClass('mask1 ' + o.selectClass).width($elWidth).wrap($.aform.wrapper);
		 
	  var $form = $el.closest('.aform'), $field = $el.closest('.afield'), spanTag = $el.siblings('span');
      
      var $selected = $el.find(":selected:first");
      if($selected.length == 0){
        $selected = $el.find("option:first");
      }
      spanTag.html($selected.html());
      
	  if($el.is(':hidden') && o.autoHide){
        $form.hide();
      }
	  if($el.prop("disabled")){
        $field.addClass(o.disabledClass);
      }
	  
      $.aform.noSelect(spanTag);
	  
	  $el.bind({
        "change.aform": function() {
          spanTag.text($(this).find(":selected").html());
          $field.removeClass(o.activeClass);
		  onChangedAction($el, $(this).val(), $(this).attr('ovalue'));
        },
        "focus.aform": function(){
          $field.addClass(o.focusClass);
        },
        "blur.aform": function(){
          $field.removeClass(o.focusClass);
          $field.removeClass(o.activeClass);
        },
        "mousedown.aform touchbegin.aform": function(){
          $field.addClass(o.activeClass);
        },
        "mouseup.aform touchend.aform": function(){
          $field.removeClass(o.activeClass);
        },
        "click.aform touchend.aform": function(){
          $field.removeClass(o.activeClass);
        },
        "keyup.aform": function(e){
		  if(e.keyCode == 13) {
			$(o.btnGrpSelector, $form).find('a:eq(0)').trigger('click');
		  }else{
			spanTag.text($(this).find(":selected").html());
		    $field.removeClass(o.activeClass);
		    onChangedAction($el, $(this).val(), $(this).attr('ovalue'));
		  }
        }
      });
    }
    
    $.aform.noSelect = function(elem){
      function f(){
        return false;
      };
      $(elem).each(function(){
        this.onselectstart = this.ondragstart = f; // Webkit & IE
        $(this).mousedown(f) // Webkit & Opera
        	   .css({ MozUserSelect: 'none' }); // Firefox
      });
    };
	
	function onChangedAction(elem, value, ovalue){
	  var $el = $(elem), $triggerOnEdit = true, $field = $el.parents('.afield');
	  var $form = $el.closest('.aform');
	  
	  if(!o.triggerChanged || $el.hasClass('static_field') || $el.prop("readonly")){
		$triggerOnEdit = false;
	  }
	  
	  if($triggerOnEdit){
		var $btn = $buttons.clone(), $changed = 0;
		if($form.find(o.btnGrpSelector).length == 0){
		  $form.append($btn);
	    }
	  
		$form.find(':checkbox').each(function(){
		  if(String($(this).prop("checked")) != $(this).attr('ovalue')){
			$changed ++;
			return false;
		  }
		});
		
		if(String(value) != String(ovalue) || $changed > 0){
		  $field.addClass(o.changedClass);
		  $('.ajax_result, .field_err', $form).remove();
		  $(o.btnGrpSelector, $form).show();
		}else{
		  $field.removeClass(o.changedClass);
		  $(o.btnGrpSelector, $form).remove();
		  $('.field_err', $form).remove();
		}
		
		$(o.btnGrpSelector, $form).find('a:eq(0)').bind({
		  "click.aform": function(){
			$field.removeClass(o.hoverClass+" "+o.focusClass+" "+o.activeClass);
			saveField(this, $(this).attr('rel'));
          }
	    });
	    $(o.btnGrpSelector, $form).find('a:eq(1)').bind({
		  "click.aform": function(){
			resetField(this);
		    $field.removeClass(o.hoverClass+" "+o.focusClass+" "+o.activeClass+" "+o.changedClass);
		    $(o.btnGrpSelector, $form).remove();
			$('.field_err', $form).remove();
          }
	    });
	  }
	}

    return this.each(function(){
        var elem = $(this);
		
		if(elem.is("select")){
          if(elem.prop("multiple") != true){
            if(elem.attr("size") == undefined || elem.attr("size") <= 1){
              doSelect(elem);
            }else{
			  doTextarea(elem);
			}
          }else{
			  doTextarea(elem);
		  }
        }else if(elem.is(":checkbox")){
		  if(elem.hasClass('btn_switch')){
			doSwitchButton(elem);
		  }else{
			doCheckbox(elem);
		  }
        }else if(elem.is(":radio")){
        }else if(elem.is(":text, :password, input[type='email']")){
          doInput(elem);
        }else if(elem.is("textarea")){
          doTextarea(elem);
        }else if(elem.hasClass('scrollbox')){
		  doScrollbox(elem);
		}
    });
  };
})(jQuery);