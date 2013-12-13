<?php if ( $error_warning ) { ?>
<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>
<a name="top"></a>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_product"><?php echo $form_title; ?></div>
	<?php echo $product_tabs ?>
	<div class="toolbar">
	    <?php if ( !empty ($help_url) ) : ?>
	        <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
	    <?php endif; ?>
    </div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">

	<?php echo $summary_form; ?>

	<div id="notify" class="align_center success" style="display: none;"></div>
	<?php if ($success) { ?>
	<script type="text/javascript">
		$('#notify').html('<?php echo $success?>').fadeIn(500).delay(2000).fadeOut(500);
	</script>
	<?php } ?>

	<table id="product_download_form" class="list option ">
	    <tr>
	        <th class="left"><?php echo $entry_file_id; ?></th>
	        <th class="left"><?php echo $entry_file_icon; ?></th>
	        <th class="left"><?php echo $entry_file_name; ?></th>
	        <th class="left"><?php echo $entry_file_max_downloads; ?></th>
	        <th class="left"><?php echo $entry_file_sort_order; ?></th>
	        <th class="left"><?php echo $entry_file_status; ?></th>
	        <th class="left"></th>
	        <th class="left"><?php echo $column_action; ?></th>
	    </tr>
	    <?php //echo $product_id; ?>
	    <?php foreach ($file_rows as $file_html) { ?>
	        <?php echo $file_html; ?>
	    <?php } ?>
	
	</table>
	<div style="margin-top: 13px;" align="center" style="width: 80%; float:right;">
		<a href="#" id="add_new_file" class="flt_right add"></a>
	</div>
	
	<table style="display:none;" id="new_row_table">
		<?php echo $new_file_row ?>
	</table>

  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>

<script type="text/javascript"><!--
	var text = {
		error_attribute_not_selected: '<?php echo $error_attribute_not_selected ?>',
		text_expand: '<?php echo $text_expand ?>',
		text_hide: '<?php echo $text_hide ?>'
	};

	$("#product_download_form a.expandRow").live('click', function () {
		var additional_row = $(this).parent().parent().next().find('div.additionalRow');
		if ($(additional_row).is(':visible')) {
			$(additional_row).slideUp();
			$(this).text(text.text_expand);
			$(this).parent().parent().next().find('div.add_resource').html();
		} else {
			$('div.aform', additional_row).show();
			$(additional_row).slideDown();
			$(this).text(text.text_hide);

			//setRLparams($(this).attr('id'));

			//loadMedia('image');
		}

		return false;
	});

//--></script>