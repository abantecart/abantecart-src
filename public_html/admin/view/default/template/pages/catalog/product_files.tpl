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
		<?php echo $form_language_switch; ?>
    </div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">

	<?php echo $summary_form; ?>

	<div id="notify_success" class="success alert alert-success" style="display: none;"></div>
	<div id="notify_error" class="error alert alert-success" style="display: none;"></div>

	<table id="product_download_form" class="list option ">
	    <tr>
	        <th class="left"><?php echo $entry_file_icon; ?></th>
	        <th class="left"><?php echo $entry_name; ?></th>
	        <th class="left"><?php echo $entry_file_max_downloads; ?></th>
	        <th class="left"><?php echo $entry_file_sort_order; ?></th>
	        <th class="left"><?php echo $entry_file_status; ?></th>
	        <th class="center"><?php echo $column_action; ?></th>
	    </tr>
	    <?php foreach ($file_rows as $file_html) { ?>
	        <?php echo $file_html; ?>
	    <?php } ?>
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

	$("#product_download_form a.expandRow").live('click', function (hide) {
		var additional_row = $(this).parents('tr').next().find('div.additionalRow');
		if ($(additional_row).is(':visible')) {
			$(additional_row).slideUp();
			$(this).html(text.text_expand);
			$(this).parents('tr').next().find('div.add_resource').html();
		} else {
			if(hide!=true){
				$('div.aform', additional_row).show();
				$(additional_row).slideDown();
				$(this).html(text.text_hide);
			}
		}

		return false;
	});

	$("#product_download_form a.add").on('click', function () {
		var additional_row = $(this).parents('tr').next().find('div.additionalRow');
		$('div.aform', additional_row).show();
		$(additional_row).slideDown();
		$(this).hide();
		return false;
	});

	$(".optionRow>td a.delete").on('click', function () {
		if(!confirm('<?php echo $text_confirm_delete;?>')){
			return false;
		}
	});
	$(".optionRow>td a.push").on('click', function () {
		if(confirm($(this).attr('title')+ '. <?php echo $text_confirm_push;?>')){
			$(this).append('<span class="ajax_loading">&nbsp;</span>').show();
			var t = $(this);
			$.ajax({
				url: $(this).attr('href'),
				type: 'GET',
				dataType: 'json',
				success: function (data) {
					if(data['progress']==100){
						$('#notify_success').html(data['text']).fadeIn(500).delay(2000).fadeOut(500);
						t.hide();
						t.find('.ajax_loading').remove();
					} else {
						$('#notify_error').html(data['text']).fadeIn(500).delay(2000).fadeOut(500);					
					}
				},
				error:function(data) {
					var $json = $.parseJSON(data.responseText);
					$('#notify_error').html($json.error_text).fadeIn(500).delay(2000).fadeOut(500);				
				}
				});
		}
		return false;
	});

	$('#downloadFrmnew_cancel').on('click',function(){
		$("#product_download_form a.add").show();
		var additional_row = $(this).parents('div.additionalRow');
		$('div.aform', additional_row).show();
		$(additional_row).slideUp();
		return false;
	});

	$(document).ready(function(){
		<?php if($download_id){?>
		$("#download_<?php echo $download_id?> a.expandRow").click();
		<?php } ?>
		$("#product_download_form tr").dblclick(function(){
			$(this).find('a.expandRow').click();
		});
	});

//--></script>