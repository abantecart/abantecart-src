<script type="text/javascript">
	jQuery("#<?php echo $table_id; ?>").setGridParam({
		'onSelectRow':function(id, status){
						if(status){
								$('#jqg_<?php echo $table_id; ?>_'+id).parents('.afield').addClass($.aform.defaults.checkedClass);
						}else{
								$('#jqg_<?php echo $table_id; ?>_'+id).parents('.afield').removeClass($.aform.defaults.checkedClass);
						}

						var tmp = jQuery.parseJSON( $('#<?php echo $form_name; ?>_buffer').html() );
						if(!tmp[id]){
							tmp[id] = {};
						}

						tmp[id]['name'] = $('#'+id).find('td').eq(2).html() ;
						tmp[id]['status'] = status;
						$('#<?php echo $form_name; ?>_buffer').html( JSON.stringify(tmp) ) ;
						}
		});
// hide select-all checkbox
$("#cb_<?php echo $table_id; ?>").parents('.afield').hide();
$("#refresh_<?php echo $table_id; ?>").click(function(){
	$('#<?php echo $form_name; ?>_buffer').html( $('#<?php echo $form_name; ?>_selected').html() );
});

function showPopup(url){
	window.open(url,'itemInfo','top=30, left=30, scrollbars=yes');
}
</script>