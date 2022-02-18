/*!
 * AbanteCart Embed optimized implementation 
 * Bootstrap v3.3.4 (http://getbootstrap.com)
 * Copyright 2011-2015 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 */

if (typeof jQuery_abc === 'undefined') {
  throw new Error('Bootstrap\'s JavaScript requires jQuery')
}

+function ($) {
  'use strict';
  var version = $.fn.jquery.split(' ')[0].split('.')
  if ((version[0] < 2 && version[1] < 9) || (version[0] == 1 && version[1] == 9 && version[2] < 1)) {
    throw new Error('Bootstrap\'s JavaScript requires jQuery version 1.9.1 or higher')
  }
}(jQuery_abc);


/* ========================================================================
 * Bootstrap: custom modal v3.3.4
 * http://getbootstrap.com/javascript/#modals
 * ========================================================================
 * Copyright 2011-2015 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';
  // abcmodal CLASS DEFINITION
  // ======================
  var abcmodal = function (element, options) {
    this.options             = options
    this.$body               = $(document.body)
    this.$element            = $(element)
    this.$dialog             = this.$element.find('.abcmodal-dialog')
    this.$backdrop           = null
    this.isShown             = null
    this.originalBodyPad     = null
    this.scrollbarWidth      = 0
    this.ignoreBackdropClick = false

    if (this.options.remote) {
      this.$element
        .find('.abcmodal-content')
        .load(this.options.remote, $.proxy(function () {
          this.$element.trigger('loaded.bs.abcmodal')
        }, this))
    }
  }

  abcmodal.VERSION  = '3.3.4'

  abcmodal.TRANSITION_DURATION = 300
  abcmodal.BACKDROP_TRANSITION_DURATION = 150

  abcmodal.DEFAULTS = {
    backdrop: true,
    keyboard: true,
    show: true
  }

  abcmodal.prototype.toggle = function (_relatedTarget) {
    return this.isShown ? this.hide() : this.show(_relatedTarget)
  }

  abcmodal.prototype.show = function (_relatedTarget) {
    var that = this
    var e    = $.Event('show.bs.abcmodal', { relatedTarget: _relatedTarget })

    this.$element.trigger(e)

    if (this.isShown || e.isDefaultPrevented()) return

    this.isShown = true

    this.checkScrollbar()
    this.setScrollbar()
    this.$body.addClass('abcmodal-open')

    this.escape()
    this.resize()

    this.$element.on('click.dismiss.bs.abcmodal', '[data-dismiss="abcmodal"]', $.proxy(this.hide, this))

    this.$dialog.on('mousedown.dismiss.bs.abcmodal', function () {
      that.$element.one('mouseup.dismiss.bs.abcmodal', function (e) {
        if ($(e.target).is(that.$element)) that.ignoreBackdropClick = true
      })
    })

    this.backdrop(function () {
      var transition = $.support.transition && that.$element.hasClass('fade')

      if (!that.$element.parent().length) {
        that.$element.appendTo(that.$body) // don't move abcmodals dom position
      }

      that.$element
        .show()
        .scrollTop(0)

      that.adjustDialog()

      if (transition) {
        that.$element[0].offsetWidth // force reflow
      }

      that.$element
        .addClass('in')
        .attr('aria-hidden', false)

      that.enforceFocus()

      var e = $.Event('shown.bs.abcmodal', { relatedTarget: _relatedTarget })

      transition ?
        that.$dialog // wait for abcmodal to slide in
          .one('bsTransitionEnd', function () {
            that.$element.trigger('focus').trigger(e)
          })
          .emulateTransitionEnd(abcmodal.TRANSITION_DURATION) :
        that.$element.trigger('focus').trigger(e)
    })
  }

  abcmodal.prototype.hide = function (e) {
    if (e) e.preventDefault()

    e = $.Event('hide.bs.abcmodal')

    this.$element.trigger(e)

    if (!this.isShown || e.isDefaultPrevented()) return

    this.isShown = false

    this.escape()
    this.resize()

    $(document).off('focusin.bs.abcmodal')

    this.$element
      .removeClass('in')
      .attr('aria-hidden', true)
      .off('click.dismiss.bs.abcmodal')
      .off('mouseup.dismiss.bs.abcmodal')

    this.$dialog.off('mousedown.dismiss.bs.abcmodal')

    $.support.transition && this.$element.hasClass('fade') ?
      this.$element
        .one('bsTransitionEnd', $.proxy(this.hideabcmodal, this))
        .emulateTransitionEnd(abcmodal.TRANSITION_DURATION) :
      this.hideabcmodal()
  }

  abcmodal.prototype.enforceFocus = function () {
    $(document)
      .off('focusin.bs.abcmodal') // guard against infinite focus loop
      .on('focusin.bs.abcmodal', $.proxy(function (e) {
        if (this.$element[0] !== e.target && !this.$element.has(e.target).length) {
          this.$element.trigger('focus')
        }
      }, this))
  }

  abcmodal.prototype.escape = function () {
    if (this.isShown && this.options.keyboard) {
      this.$element.on('keydown.dismiss.bs.abcmodal', $.proxy(function (e) {
        e.which == 27 && this.hide()
      }, this))
    } else if (!this.isShown) {
      this.$element.off('keydown.dismiss.bs.abcmodal')
    }
  }

  abcmodal.prototype.resize = function () {
    if (this.isShown) {
      $(window).on('resize.bs.abcmodal', $.proxy(this.handleUpdate, this))
    } else {
      $(window).off('resize.bs.abcmodal')
    }
  }

  abcmodal.prototype.hideabcmodal = function () {
    var that = this
    this.$element.hide()
    this.backdrop(function () {
      that.$body.removeClass('abcmodal-open')
      that.resetAdjustments()
      that.resetScrollbar()
      that.$element.trigger('hidden.bs.abcmodal')
    })
  }

  abcmodal.prototype.removeBackdrop = function () {
    this.$backdrop && this.$backdrop.remove()
    this.$backdrop = null
  }

  abcmodal.prototype.backdrop = function (callback) {
    var that = this
    var animate = this.$element.hasClass('fade') ? 'fade' : ''

    if (this.isShown && this.options.backdrop) {
      var doAnimate = $.support.transition && animate

      this.$backdrop = $('<div class="abcmodal-backdrop ' + animate + '" />')
        .appendTo(this.$body)

      this.$element.on('click.dismiss.bs.abcmodal', $.proxy(function (e) {
        if (this.ignoreBackdropClick) {
          this.ignoreBackdropClick = false
          return
        }
        if (e.target !== e.currentTarget) return
        this.options.backdrop == 'static'
          ? this.$element[0].focus()
          : this.hide()
      }, this))

      if (doAnimate) this.$backdrop[0].offsetWidth // force reflow

      this.$backdrop.addClass('in')

      if (!callback) return

      doAnimate ?
        this.$backdrop
          .one('bsTransitionEnd', callback)
          .emulateTransitionEnd(abcmodal.BACKDROP_TRANSITION_DURATION) :
        callback()

    } else if (!this.isShown && this.$backdrop) {
      this.$backdrop.removeClass('in')

      var callbackRemove = function () {
        that.removeBackdrop()
        callback && callback()
      }
      $.support.transition && this.$element.hasClass('fade') ?
        this.$backdrop
          .one('bsTransitionEnd', callbackRemove)
          .emulateTransitionEnd(abcmodal.BACKDROP_TRANSITION_DURATION) :
        callbackRemove()

    } else if (callback) {
      callback()
    }
  }

  // these following methods are used to handle overflowing abcmodals

  abcmodal.prototype.handleUpdate = function () {
    this.adjustDialog()
  }

  abcmodal.prototype.adjustDialog = function () {
    var abcmodalIsOverflowing = this.$element[0].scrollHeight > document.documentElement.clientHeight

    this.$element.css({
      paddingLeft:  !this.bodyIsOverflowing && abcmodalIsOverflowing ? this.scrollbarWidth : '',
      paddingRight: this.bodyIsOverflowing && !abcmodalIsOverflowing ? this.scrollbarWidth : ''
    })
  }

  abcmodal.prototype.resetAdjustments = function () {
    this.$element.css({
      paddingLeft: '',
      paddingRight: ''
    })
  }

  abcmodal.prototype.checkScrollbar = function () {
    var fullWindowWidth = window.innerWidth
    if (!fullWindowWidth) { // workaround for missing window.innerWidth in IE8
      var documentElementRect = document.documentElement.getBoundingClientRect()
      fullWindowWidth = documentElementRect.right - Math.abs(documentElementRect.left)
    }
    this.bodyIsOverflowing = document.body.clientWidth < fullWindowWidth
    this.scrollbarWidth = this.measureScrollbar()
  }

  abcmodal.prototype.setScrollbar = function () {
    var bodyPad = parseInt((this.$body.css('padding-right') || 0), 10)
    this.originalBodyPad = document.body.style.paddingRight || ''
    if (this.bodyIsOverflowing) this.$body.css('padding-right', bodyPad + this.scrollbarWidth)
  }

  abcmodal.prototype.resetScrollbar = function () {
    this.$body.css('padding-right', this.originalBodyPad)
  }

  abcmodal.prototype.measureScrollbar = function () { // thx walsh
    var scrollDiv = document.createElement('div')
    scrollDiv.className = 'abcmodal-scrollbar-measure'
    this.$body.append(scrollDiv)
    var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth
    this.$body[0].removeChild(scrollDiv)
    return scrollbarWidth
  }


  // abcmodal PLUGIN DEFINITION
  // =======================

  function Plugin(option, _relatedTarget) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.abcmodal')
      var options = $.extend({}, abcmodal.DEFAULTS, $this.data(), typeof option == 'object' && option)

      if (!data) $this.data('bs.abcmodal', (data = new abcmodal(this, options)))
      if (typeof option == 'string') data[option](_relatedTarget)
      else if (options.show) data.show(_relatedTarget)
    })
  }

  var old = $.fn.abcmodal

  $.fn.abcmodal             = Plugin
  $.fn.abcmodal.Constructor = abcmodal


  // abcmodal NO CONFLICT
  // =================

  $.fn.abcmodal.noConflict = function () {
    $.fn.abcmodal = old
    return this
  }


  // abcmodal DATA-API
  // ==============

  $(document).on('click.bs.abcmodal.data-api', '[data-toggle="abcmodal"]', function (e) {
    var $this   = $(this)
    var href    = $this.attr('href')
    var $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''))) // strip for ie7
    var option  = $target.data('bs.abcmodal') ? 'toggle' : $.extend({ remote: !/#/.test(href) && href }, $target.data(), $this.data())

    if ($this.is('a')) e.preventDefault()

    $target.one('show.bs.abcmodal', function (showEvent) {
      if (showEvent.isDefaultPrevented()) return // only register focus restorer if abcmodal will actually get shown
      $target.one('hidden.bs.abcmodal', function () {
        $this.is(':visible') && $this.trigger('focus')
      })
    })
    Plugin.call($target, option, this)
  })

}(jQuery_abc);