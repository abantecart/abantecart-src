<?php if ($error_warning) { ?>
<div class="warning alert alert-error alert-danger"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="row">
<div class="col-sm-12 col-lg-12">
<ul class="content-nav">
	<li>
<?php
if ( !empty($search_form) ) {
?>
	<form id="<?php echo $search_form['form_open']->name; ?>" method="<?php echo $search_form['form_open']->method; ?>" 
		name="<?php echo $search_form['form_open']->name; ?>" class="form-inline" role="form">
		
	<?php
    	foreach ($search_form['fields'] as $f) { 
    ?>
    	<div class="form-group">
    		<div class="input-group input-group-sm">
    		<?php echo $f; ?>
			</div>
		</div>
	<?php
    	}
	?>
    	<div class="form-group">
			<button type="submit" class="btn btn-xs btn-primary"><?php echo $search_form['submit'] ?></button>
			<button type="reset" class="btn btn-xs btn-default"><i class="fa fa-refresh"></i></button>
		</div>
	</form>
<?php
}
?>
	</li>
	<li>
	  <a class="itemopt" title="<?php echo $button_insert; ?>" href="<?php echo $insert; ?>"><i class="fa fa-plus-circle"></i></a>
	</li>
	
	<?php if(!empty ($form_language_switch) ) { ?>
	<li>
		<?php echo $form_language_switch; ?>
	</li>
	<?php }?>	
	<?php if(!empty ($help_url) ) { ?>
	<li>
	  <div class="help_element">
	  <a href="<?php echo $help_url; ?>" target="new">
	  <i class="fa fa-question-circle"></i>
	  </a></div>
	</li>
	<?php }?>
</ul>
</div>
</div>

<div class="row">
<div class="col-sm-12 col-lg-12">
	<div class="panel panel-default">
		<div class="panel-body">
    <?php echo $listing_grid; ?>
		</div>
	</div>
</div>
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
