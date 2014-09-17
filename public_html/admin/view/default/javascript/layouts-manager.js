$(function () {
  'use strict';

  var sortableBlocks = [],
      modalId = 'lm-modal',
      lmModal = $('#' + modalId),
      modalContent = lmModal.find('.modal-content'),
      container = $('#page-layout'),

  initBlockSorter = function() {
    // Init blocks sorting
    container.find('.blocks').each(function(i) {
      sortableBlocks[i] = new Sortable(this, {
        group: 'block',
        handle: '.block-content',
        draggable: ".block",
        onAdd: function (evt){
          var itemEl = evt.item;
          updateBlockData($(itemEl));
        }
      });
    });
  },

  restartBlockSorter = function() {
    $.each(sortableBlocks, function(i, value) {
      sortableBlocks[i].destroy();
    });
    initBlockSorter();
  },

  handleBlockButtonSwitch = function(btn) {
    var parentBlock = $(btn).parents('.block'),
    blockStatus = parentBlock.find('.block-status');

    if (Number(blockStatus.val()) == 1)  {
      parentBlock.addClass('off');
      blockStatus.val(0);
    } else {
      parentBlock.removeClass('off');
      blockStatus.val(1);
    }
  },

  handleSectionButtonSwitch = function(btn) {
    var parentSection = $(btn).parents('.section'),
    sectionStatus = parentSection.find('.section-status');

    if (Number(sectionStatus.val()) == 1)  {
      parentSection.addClass('off');
      sectionStatus.val(0);
    } else {
      parentSection.removeClass('off');
      sectionStatus.val(1);
    }
  },

  showBlockFormModal = function(referrer, url, params) {
    modalContent.html('<div class="loading"></div>');
    lmModal.modal({
      backdrop: 'static',
      keyboard: false,
      show: true,
    });

    if (url) {
      $(modalContent).load(url, params, function() {
        blocksManager(referrer);
      });
    }
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
    // initializing block sorter
    initBlockSorter();
    // attaching event handlers
    attachEventHandlers();
  },

  reStart = function() {
    container.find(".button[data-toggle|='tooltip']").tooltip();
    restartBlockSorter();
    reAttachEventHandlers();
  },

  blocksManager = function(target) {
    var blocksMgrContainer = modalContent.find('.blocks-manager'),
    blocks = blocksMgrContainer.find('#blocks');

    blocks.find('.block-item').on('click', function() {
      var id = $(this).data('id'),
      addUrl = $(this).data('addUrl');
      addBlockToSection(target, addUrl, id);
    });
  },

  addBlockToSection = function(section, url, id) {
    var blocksContainer = section.find('.blocks'),
    params = {
      id: id
    }

    $.get(url, params, function(block) {
      blocksContainer.append(block);
      lmModal.modal('hide');
      reStart();
    });
  },

  removeBlockFromSection = function(targetBlock) {
    var removeBlock = confirm("Are you sure you want to remove the block?");
    if (removeBlock == true) {
      targetBlock.remove();
      reStart();
    }
  },

  updateBlockData = function(block) {
    var section = block.parents('.section'),
    sectionId = section.data('sectionId'),
    blockParent = block.find('.block-parent');
    
    blockParent.val(sectionId);
  },

  attachEventHandlers = function() {
    // Events Handlers

    // Handler for Block's button enable/disable
    $('.block .blk-switch', container).on("click", function() {
      handleBlockButtonSwitch(this);
    });

    // Handler for Section's button enable/disable
    $('.section .sec-switch', container).on("click", function() {
      handleSectionButtonSwitch(this);
    });

    // Handler for Add Block
    $('.section .sec-add-block', container).on("click", function() {
      var parentSection = $(this).parents('.section'),
      addBlockUrl = $(this).data('addBlock'),
      params = 'section_id=' + parentSection.data('sectionId');

      showBlockFormModal(parentSection, addBlockUrl, params);
    });

    // Handler for Edit Block
    $('.block .blk-config', container).on("click", function() {
      var parentBlock = $(this).parents('.block'),
      editBlockUrl = '',
      params = '';

      showBlockFormModal(parentBlock, editBlockUrl, params);
    });

    // Handler for Delete Block
    $('.block .blk-delete', container).on("click", function() {
      var block = $(this).parents('.block');

      removeBlockFromSection(block);
    });

  },

  reAttachEventHandlers = function() {
    $('.block .blk-switch', container).off("click");
    $('.section .sec-switch', container).off("click");
    $('.section .sec-add-block', container).off("click");
    $('.block .blk-config', container).off("click");
    $('.block .blk-delete', container).off("click");

    // attach
    attachEventHandlers();
  };

  // start App
  start();

  $('.layout-form-save').on("click", function(e){
    e.stopPropagation();
    e.preventDefault();

    var form = $(this).closest('form');
    form.submit();
  })

});

