<?php if ($error_warning) { ?>
<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="contentBox">
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
	<div class="toolbar">
		<?php if ( !empty ($help_url) ) : ?>
	        <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
	    <?php endif; ?>
		<?php echo $form_language_switch; ?>
		<div class="buttons">
		  <a class="btn_toolbar" title="<?php echo $button_insert; ?>" href="<?php echo $insert; ?>">
			<span class="icon_add">&nbsp;</span>
		  </a>
        </div>

	</div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
    <?php echo $listing_grid; ?>
  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>

<script type="text/javascript"><!--

var keyword = '<?php echo $filter_product?>';
var pfrom = '0';
var pto = '<?php echo $filter_price_max?>';
$(function() {

	if ( $('#product_grid_search_keyword').val() == '' ) {
		$('#product_grid_search_keyword')
			.val(keyword)
			.blur(function(){ if ( $(this).val() == '' ) $(this).val(keyword) })
			.focus(function(){ if ( $(this).val() == keyword ) $(this).val('') })
	}

	$('#product_grid_search_pfrom').width(30);
	if ( $('#product_grid_search_pfrom').val() == '' ) {
		$('#product_grid_search_pfrom')
			.val(pfrom)
			.blur(function(){ if ( $(this).val() == '' ) $(this).val(pfrom)  })
			.focus(function(){ if ( $(this).val() == pfrom ) $(this).val('')  })
	}

	$('#product_grid_search_pto').width(30);
	if ( $('#product_grid_search_pto').val() == '' ) {
		$('#product_grid_search_pto')
			.val(pto)
			.blur(function(){ if ( $(this).val() == '' ) $(this).val(pto)  })
			.focus(function(){ if ( $(this).val() == pto ) $(this).val('') })
	}

});
//--></script>
