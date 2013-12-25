<div class="contentBox" >
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_product"><?php echo $heading_title; ?></div>

  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc" style="background-color: white;">
    <?php echo $listing_grid; ?>
  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc" style="background-color: white;"></div></div></div>
</div>
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
						tmp[id]['status'] = tmp[id]['status'] ? tmp[id]['status'] : 0;
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