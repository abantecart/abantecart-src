$(function () {
  'use strict';

  var sortableBlocks = [],
      modalId = 'lm-modal',
      lmModal = $('#' + modalId),
      modalContent = lmModal.find('.modal-content'),
      container = $('#page-layout'),

  initBlockSorter = function() {
    // Init blocks sorting
    container.find('.dropzone').each(function(i) {
      sortableBlocks[i] = new Sortable(this, {
<<<<<<< HEAD
        group: 'block',
        handle: '.block-content',
=======
        group: "block",
        handle: ".block-content",
>>>>>>> FETCH_HEAD
        draggable: ".block",
        onAdd: function (evt){
          console.log('Add: ', evt);
          var itemEl = evt.item;
        },
        onUpdate: function (evt){
          console.log('Update: ', evt);
          var itemEl = evt.item;
        },
        onRemove: function (evt){
          console.log('Remove: ', evt);
          var itemEl = evt.item;
        }
      });
    });
  },

  handleBlockButtonSwitch = function(btn) {
    var parentBlock = $(btn).parents('.block');
    if (parentBlock.hasClass('on')) {
      parentBlock.removeClass('on').addClass('off');
    } else {
      parentBlock.removeClass('off').addClass('on');
    }
  },

  handleSectionButtonSwitch = function(btn) {
    var parentSection = $(btn).parents('.section');
    if (parentSection.hasClass('on')) {
      parentSection.removeClass('on').addClass('off');
    } else {
      parentSection.removeClass('off').addClass('on');
    }
  },

<<<<<<< HEAD
  showBlockFormModal = function(url, params) {
=======
  showBlockFormModal = function() {
>>>>>>> FETCH_HEAD
    modalContent.html('<div class="loading"></div>');
    lmModal.modal({
      backdrop: 'static',
      keyboard: false,
      show: true,
    });
<<<<<<< HEAD

    $(modalContent).load(url, params);
=======
>>>>>>> FETCH_HEAD
  },

  start = function() {
    // Prepare block config modal
    if ($('#' + modalId).length == 0) {
      $('body').append('<div id="' + modalId + '" class="modal fade" tabindex="-1"><div class="modal-dialog"><div class="modal-content"></div></div></div>');
    }

    lmModal = $('#' + modalId);
    modalContent = lmModal.find('.modal-content');

    // Init bootstrap tooltip
    container.find(".button[data-toggle|='tooltip']").tooltip();

    initBlockSorter();
  };

  start();

  

  // Handler for Block's button enable/disable
  $('.block .blk-switch', container).on( "click", function() {
    handleBlockButtonSwitch(this);
  });

  // Handler for Section's button enable/disable
  $('.section .sec-switch', container).on( "click", function() {
    handleSectionButtonSwitch(this);
  });

  // Handler for Add Block
  $('.section .sec-add-block', container).on( "click", function() {
<<<<<<< HEAD
    var addBlockUrl = $(this).data('addBlock'),
    params = 'section_id=' + $(this).data('sectionId')

    showBlockFormModal(addBlockUrl, params);
=======
    showBlockFormModal();
>>>>>>> FETCH_HEAD
  });

  // Handler for Edit Block
  $('.block .blk-config', container).on( "click", function() {
<<<<<<< HEAD
    showBlockFormModal(editBlockUrl);
=======
    showBlockFormModal();
>>>>>>> FETCH_HEAD
  });
});