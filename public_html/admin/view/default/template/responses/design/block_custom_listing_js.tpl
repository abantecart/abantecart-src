<script type="text/javascript">
	jQuery('#<?php echo $table_id;?>').setGridParam({
			'onSelectRow':function(id, status){
							var inputname = '#sort_order'+id;
							if(status){
								$('#jqg_<?php echo $table_id;?>_'+id).parents('.afield').addClass($.aform.defaults.checkedClass);
								$(inputname).removeProp('disabled');
								if($(inputname).parents('.aform').length==0){
									$(inputname).aform({ showButtons:false });
								}
							}else{
								$('#jqg_<?php echo $table_id;?>_'+id).parents('.afield').removeClass($.aform.defaults.checkedClass);
								$(inputname).val('');
								$(inputname).attr('disabled','disabled');
							}
							var sorting = $(inputname).val() ? $(inputname).val() : 0;
							var tmp = jQuery.parseJSON( $('#<?php echo $form_name; ?>_popup_buffer').html() );

							if(tmp[id]==null){
								tmp[id] = {};
							}

							tmp[id]['name'] = $('#'+id).find('td').eq(2).html() ;
							tmp[id]['status'] = status;
							//tmp[id]['sort_order'] = sorting;

							$('#<?php echo $form_name; ?>_popup_buffer').html( JSON.stringify(tmp, null, 2) ) ;
							}
	});
	// hide select-all checkbox
	$('#cb_<?php echo $table_id; ?>').parents('.afield').hide();

	$('#refresh_<?php echo $table_id; ?>').click(function(){
		$('#<?php echo $form_name; ?>_popup_buffer').html( $('#<?php echo $form_name; ?>_popup_selected').html() );
	});

	function write_sorting(id){
		var inputname = '#sort_order'+id';
		var sorting = $(inputname).val() ? $(inputname).val() : 0;
		var tmp = jQuery.parseJSON($('#<?php echo $form_name; ?>_popup_buffer').html());
		if(!tmp[id]){
			tmp[id] = {};
		}
		tmp[id]['name'] = $('#'+id).find('td').eq(2).html() ;
		tmp[id]['sort_order'] = sorting;
		$('#<?php echo $form_name; ?>_popup_buffer').html( JSON.stringify(tmp, null, 2) ) ;
	}
	function showPopup(url){
		window.open(url,'itemInfo','top=30, left=30, scrollbars=yes');
	}
</script>