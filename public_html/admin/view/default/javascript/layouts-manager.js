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
	        //check id block is allowed in the section 
	        validate_block_location(itemEl);
	 		//fill in empty if dragged from header or footer
	 		fillInPlaceholder('header', 1);
	  		fillInPlaceholder('footer', 8);
	      },
	      onUpdate: function(evt, ui) {
	      	var itemEl = evt.item;
	        formChanged(itemEl);
	        //check id block is allowed in the section 
	        validate_block_location(itemEl);
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
	
	validate_block_location = function(block) {
		var url = $(block).attr('data-validate-url');
		if(!url) {
			return;
		}
		var section_id = $(block).closest('.section').attr('data-section-id');
		url += '&parent_block_id='+section_id;
    	$.ajax({
    		type: 'GET',
    		url: url,
    		dataType: 'json',		
    		success: function(data) {
    			if(data.allowed === 'true'){
    				$(block).removeClass('off');
    			} else {
    				warning_alert(data.message, true);
	    			$(block).addClass('off');
    			}
    		},
   		});		
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

	  //specisal case for footer and header. Fill in empty slots (8 all the time)
	  fillInPlaceholder('header', 1);
	  fillInPlaceholder('footer', 8);
	
	  // Init bootstrap tooltip
	  container.find(".button[data-toggle|='tooltip']").tooltip();
	  // initializing block sorter
	  initBlockSorter();
	  // attaching event handlers
	  attachEventHandlers();
	},
	
	reStart = function() {
	  //specisal case for footer and header. Fill in empty slots (8 all the time)
	  fillInPlaceholder('header', 1);
	  fillInPlaceholder('footer', 8);
	  
	  container.find(".button[data-toggle|='tooltip']").tooltip();
	  restartBlockSorter();
	  reAttachEventHandlers();
	},

	fillInPlaceholder = function(plhl, section_id) {
		var selector = "."+plhl+"[data-section-id|='"+section_id+"']";
		var header_count = $(selector+" .block").size();
		var full_cnt = 0;
		$(selector).find("input.block-id").each(function() {
			if ($(this).val() != "_" && $(this).val() != "") {
				full_cnt++;
			}
	  	});
	  	if ( header_count < 8) {
	  		addEmptyBlock($(selector+" .blocks"));
	  	}
	  	//hide add option if all are taken
	  	if ( header_count < 8 || header_count != full_cnt) {
	  		$(selector+" .sec-add-block").show();
	  	} else {
	  		$(selector+" .sec-add-block").hide();
	  	}
	},
	
	addEmptyBlock = function(pl) {
		var empty_block = $(".empty_block").html();
		pl.append(empty_block);
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
	  var params = {
	    id: id
	  }
	  //first find any empty container in the sections and place block to it.
	  // not best approach. Need to pass exect empty conteiner cliecked. 
	  var blocksContainer;
	  section.find("input.block-id").each(function() {
	  	if ($(this).val() == "_" || $(this).val() == "") {
	  		blocksContainer = $(this).parents(".block")
	  		return false;
	  	}
	  });
	  if (blocksContainer) {
		  $.get(url, params, function(block) {
		    blocksContainer.replaceWith(block);
		    lmModal.modal('hide');
		    reStart();
		    formChanged();
		  });	  
	  } else {
	  	  blocksContainer = section.find('.blocks');
		  $.get(url, params, function(block) {
		    blocksContainer.append(block);
		    lmModal.modal('hide');
		    reStart();
		    formChanged();
		  });
	  }	
	},
	
	removeBlockFromSection = function(targetBlock) {
	  //var removeBlock = confirm("Are you sure you want to remove the block?");
	  //if (removeBlock == true) {
	    targetBlock.remove();
	    reStart();
	  //}
	},
	
	updateBlockData = function(block) {
	  var section = block.parents('.section'),
	  sectionId = section.data('sectionId'),
	  blockParent = block.find('.block-parent');    
	  blockParent.val(sectionId);
	  formChanged(blockParent);
	},
	
	formChanged = function (block) {
		$('#layout_form').prop('changed', 'true');
		var $inputs = $(block).children('.afield input');
		if($inputs.length) {
			$inputs.addClass('changed');
		} else {
			//find any element and mark changed
			$('#layout_form').find('.afield input').addClass('changed');
		}
		$('#content.panel').addClass('afield')	
		$('#content .panel-body').addClass('changed');
	},
	
	attachEventHandlers = function() {
	  // Events Handlers
	
	  // Handler for Block's button enable/disable
	  $('.block .blk-switch', container).on("click", function() {
	    handleBlockButtonSwitch(this);
	    formChanged();
	  });
	
	  // Handler for Section's button enable/disable
	  $('.section .sec-switch', container).on("click", function() {
	    handleSectionButtonSwitch(this);
	    formChanged();
	  });
	
	  // Handler for Add Block
	  $('.section .sec-add-block', container).on("click", function() {
	    var parentSection = $(this).parents('.section'),
	    addBlockUrl = $(this).data('addBlock'),
	    params = 'section_id=' + parentSection.data('sectionId');
	    showBlockFormModal(parentSection, addBlockUrl, params);
	  });
	
	  // Handler for Edit Block
	  $('.block .blk-info', container).on("click", function() {
	    var parentBlock = $(this).parents('.block');
	    var infoBlockUrl = $(this).data('infoBlock');
	    var block_id = parentBlock.find('.block-id').val();
	    var params = 'block_id=' + block_id;
	    var template = $("input[name*='tmpl_id']").val();
	    if (template) {
	    	params += '&template=' + template;
	    }
	    var page_id = $("input[name*='page_id']").val();
	    if (page_id) {
	    	params += '&page_id=' + page_id;
	    }
	    var layout_id = $("input[name*='layout_id']").val();
	    if (layout_id) {
	    	params += '&layout_id=' + layout_id;
	    }
	    showBlockFormModal(parentBlock, infoBlockUrl, params);
	  });
	
	  // Handler for Delete Block
	  $('.block .blk-delete', container).on("click", function() {
	    var block = $(this).parents('.block');
	    //check if special section and replace with empty block
	  	var section = $(this).parents(".section");
		var section_id = section.attr('data-section-id');
	    if ( section_id == 1 || section_id == 8 ) {
	    	block.replaceWith($(".empty_block").html());
	    	//update all parent section ids
	    	section.find(".block").each(function() {
	    		$(this).find('.block-parent').val(section_id);
	    	});
	    } else {
	    	removeBlockFromSection(block);
	    }
	    formChanged();
	  });
	
	  //place ivent to add block to empty container
	  $("input.block-id").each(function() {
	  	if ($(this).val() == "_" || $(this).val() == "") {
	  		var block = $(this).parents(".block");
	  		block.on("click", function() {
		    	var parentSection = $(this).parents('.section');
	    		var addBlockUrl = $(this).parents('.section').find('.sec-add-block').data('addBlock');
	    		var params = 'section_id=' + parentSection.data('sectionId');
	    		showBlockFormModal(parentSection, addBlockUrl, params);
	    	});	
	  	}
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
	
	$('.layout-form-save').on("click", function(e) {
	  e.stopPropagation();
	  e.preventDefault();
	  var form = $('#layout_form');
	  form.prop('changed', 'submit');
	  form.submit();
	});
	
	/* Not yet implemented
	$('.layout-form-preview').on("click", function(e) {
	  e.stopPropagation();
	  e.preventDefault();
	  var form = $('#layout_form');
	  form.prop('changed', 'submit');	  
	  form.attr('action', $(this).attr('href'));
	  form.submit();
	});
	*/
});

