<div class="contentBox" >
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_product"><?php echo $heading_title; ?></div>
<?php
if ( !empty($search_form) ) {
    echo '<div class="filter">';
    echo $search_form['form_open'];
    foreach ($search_form['fields'] as $f) echo $f;
	echo '<button type="submit" class="btn_standard">'.$search_form['submit'].'</button>';
	echo '<button type="reset" class="btn_standard">'.$search_form['reset'].'</button>';
    echo '</form>';
    echo '</div>';
}
?>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc" style="background-color: white;">
    <?php echo $listing_grid; ?>
  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc" style="background-color: white;"></div></div></div>
</div>
<script type="text/javascript">

var keyword = '<?php echo $filter_product?>';
var pfrom = '0';
var pto = '<?php echo $filter_price_max?>';
$(function() {

    $('#product_listing_search div.aselect').width('140px');

	if ( $('#product_listing_search_keyword').val() == '' ) {
		$('#product_listing_search_keyword')
			.val(keyword)
			.blur(function(){ if ( $(this).val() == '' ) $(this).val(keyword) })
			.focus(function(){ if ( $(this).val() == keyword ) $(this).val('') })
	}

	$('#product_listing_search_pfrom').width(30);
	if ( $('#product_listing_search_pfrom').val() == '' ) {
		$('#product_listing_search_pfrom')
			.val(pfrom)
			.blur(function(){ if ( $(this).val() == '' ) $(this).val(pfrom)  })
			.focus(function(){ if ( $(this).val() == pfrom ) $(this).val('')  })
	}

	$('#product_listing_search_pto').width(30);
	if ( $('#product_listing_search_pto').val() == '' ) {
		$('#product_listing_search_pto')
			.val(pto)
			.blur(function(){ if ( $(this).val() == '' ) $(this).val(pto)  })
			.focus(function(){ if ( $(this).val() == pto ) $(this).val('') })
	}

});



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