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
	<?php echo $form['form_open']; ?>

	<div id="notify" class="align_center success" style="display: none;"></div>
	<?php if ($success) { ?>
	<script type="text/javascript">
		$('#notify').html('<?php echo $success?>').fadeIn(500).delay(2000).fadeOut(500);
	</script>
	<?php } ?>

	<table id="option_values_tbl" class="list option ">
	    <tr>
	        <td class="left"><?php echo $entry_file_icon; ?></td>
	        <td class="left"><?php echo $entry_file_name; ?></td>
	        <td class="left"><?php echo $entry_file_max_downloads; ?></td>
	        <td class="left"><?php echo $entry_file_sort_order; ?></td>
	        <td class="left"><?php echo $entry_file_status; ?></td>
	        <td class="left"></td>
	        <td class="left"><?php echo $column_action; ?></td>
	    </tr>
	    <?php echo $product_id; ?>
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


	</form>

  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>

<script type="text/javascript"><!--

	$("#option_values_tbl a.expandRow").live('click', function () {
		var additional_row = $(this).parent().parent().next().find('div.additionalRow');
		if ($(additional_row).is(':visible')) {
			$(additional_row).hide();
			$(this).text(text.text_expand);
			$(this).parent().parent().next().find('div.add_resource').html();
		} else {
			$(additional_row).show();
			$(this).text(text.text_hide);
			$('div.aform', additional_row).show();
			setRLparams($(this).attr('id'));

			loadMedia('image');
		}

		return false;
	});

//--></script>