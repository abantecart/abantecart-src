<?php if ( !empty($error['warning']) ) { ?>
<div class="warning alert alert-error"><?php echo $error['warning']; ?></div>
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
		<div class="fieldset">
		  <div class="heading"><a id="tab_image"><?php echo $tab_relations; ?></a></div>
		  <div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>
		  <div class="cont_left"><div class="cont_right"><div class="cont_mid">

		  <table class="form">
		  <tr>
            <td valign="bottom"><?php echo $entry_category; ?></td>
	        <td><div ><?php echo $form['fields']['category_list']; ?></div>
		        <div style="padding-top: 5px;"><?php echo $form['fields']['category_list_hidden'];?></div></td>
          </tr>
		   <tr>
            <td valign="bottom"><?php echo $entry_related; ?></td>
	        <td><div ><?php echo $form['fields']['list']; ?></div>
		        <div style="padding-top: 5px;"><?php echo $form['fields']['list_hidden'];?></div></td>
          </tr>
          <tr>
            <td><?php echo $entry_store; ?></td>
            <td><div class="scrollbox"><?php echo $form['fields']['product_store']; ?></div></td>
          </tr>
        </table>

		  </div></div></div>
		  <div class="bottom_left"><div class="bottom_right"><div class="bottom_mid"></div></div></div>
		</div>

	<div class="buttons align_center">
	  <a class="btn_standard" href="<?php echo $cancel; ?>" ><?php echo $form['cancel']; ?></a>
	  <button type="submit" class="btn_standard"><?php echo $form['submit']; ?></button>
    </div>
	</form>

  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>

<script type="text/javascript"><!--
var  addRelated = function() {
	$('#product_form_product :selected').each(function() {
		$(this).remove();
		$('#product_form_related option[value=\'' + $(this).attr('value') + '\']').remove();
		$('#product_form_related').append('<option value="' + $(this).attr('value') + '">' + $(this).text() + '</option>');
		$('#product_related input[value=\'' + $(this).attr('value') + '\']').remove();
		$('#product_related').append('<input type="hidden" name="product_related[]" value="' + $(this).attr('value') + '" />');
	});
}

var removeRelated = function() {
	$('#product_form_related :selected').each(function() {
		$(this).remove();
		$('#product_related input[value=\'' + $(this).attr('value') + '\']').remove();
	});
}

var getProducts = function() {
	$('#product_form_product option').remove();

	$.ajax({
		url: '<?php echo $category_products; ?>&category_id=' + $('#productFrm_categories').attr('value'),
		dataType: 'json',
		success: function(data) {
			for (i = 0; i < data.length; i++) {
	 			$('#product_form_product').append('<option value="' + data[i]['product_id'] + '">' + data[i]['name'] + ' (' + data[i]['model'] + ') </option>');
			}
		}
	});
}

var getRelated = function() {
	$('#product_form_related option').remove();

	$.ajax({
		url: '<?php echo $related_products; ?>',
		type: 'POST',
		dataType: 'json',
		data: $('#product_related input'),
		success: function(data) {
			$('#product_related input').remove();
			for (i = 0; i < data.length; i++) {
	 			$('#product_form_related').append('<option value="' + data[i]['product_id'] + '">' + data[i]['name'] + ' (' + data[i]['model'] + ') </option>');
				$('#product_related').append('<input type="hidden" name="product_related[]" value="' + data[i]['product_id'] + '" />');
			}
		}
	});
}



jQuery(function($){
	$('#productFrm_categories').change(function(){ getProducts(); });
	getProducts();
	getRelated();

});



//--></script>