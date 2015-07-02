<?php echo $head; ?>
<h1 class="heading1">
  <span class="maintext"><i class="fa fa-truck"></i> <?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>

<?php if ($success) { ?>
<div class="alert alert-success">
<button type="button" class="close" data-dismiss="alert">&times;</button>
<?php echo $success; ?>
</div>
<?php } ?>

<?php if ($error_warning) { ?>
<div class="alert alert-error alert-danger">
<button type="button" class="close" data-dismiss="alert">&times;</button>
<?php echo $error_warning; ?>
</div>
<?php } ?>

<div class="embed_contentpanel">

	<?php echo $form['form_open'];?>
	<h4 class="heading4"><?php echo $text_shipping_address; ?></h4>
	<div class="registerbox">
		<table class="table table-striped table-bordered">
		<tr>
			<td><address><?php echo $address; ?></address></td>
			<td>
			<div class="form-group">
				<label class="control-label"><?php echo $text_shipping_to; ?></label>
				<div class="input-group">
					<a href="<?php echo $change_address_href; ?>" class="btn btn-default mr10" title="<?php echo $button_change_address?>">
					    <i class="fa fa-edit"></i>
					    <?php echo $button_change_address ?>
					</a>				
				</div>
			</div>									
			</td>
		</tr>
		</table>		
	</div>
		
	<?php if( $shipping_methods ) { ?>			
	<h4 class="heading4"><?php echo $text_shipping_method; ?></h4>	
	<p><?php echo $text_shipping_methods; ?></p>		
	<div class="registerbox">		
        <table class="table table-striped table-bordered">
        <?php
	      foreach ($shipping_methods as $shipping_method) { ?>
          <tr>
            <td colspan="3"><b><?php echo $shipping_method['title']; ?></b></td>
          </tr>
          <?php if (!$shipping_method['error']) { ?>
          <?php foreach ($shipping_method['quote'] as $quote) { ?>
			  <tr>
				<td style="width: 5%"><?php echo $quote['radio']; ?></td>
				<td>
				<label for="shipping_shipping_method<?php echo $quote['id']; ?>" title="<?php echo has_value($quote['description']) ? $quote['description'] : ''; ?>" style="cursor: pointer;">
				<?php $icon = $shipping_method['icon'];
				if ( count ($icon) ) {  ?>
				<?php if ( is_file(DIR_RESOURCE . $icon['image']) ) { ?>
					<span class="shipping_icon mr10"><img src="resources/<?php echo $icon['image']; ?>" title="<?php echo $icon['title']; ?>" /></span>
					<?php } else if (!empty( $icon['resource_code'] )) { ?>
					<span class="shipping_icon mr10"><?php echo $icon['resource_code']; ?></span>
				<?php } } ?>								
				<?php echo $quote['title']; ?>
				</label>
				</td>
				<td class="align_right"><label for="shipping_shipping_method<?php echo $quote['id']; ?>" style="cursor: pointer;"><?php echo $quote['text']; ?></label></td>
			  </tr>
          <?php } ?>
          <?php } else { ?>
          <tr>
            <td colspan="3"><div class="error"><?php echo $shipping_method['error']; ?></div></td>
          </tr>
          <?php } ?>
        <?php } ?>
        </table>
	</div>
	<?php } ?>
	<?php echo $this->getHookVar('shipping_extensions_hook'); ?>	
	
	<?php echo $this->getHookVar('order_attributes'); ?>
	
	<div class="registerbox">		
		<div class="form-group">
    		<div class="col-md-12 mt20 mb20">
    			<?php echo $this->getHookVar('buttons_pre'); ?>
				<?php echo $buttons; ?>
				<?php echo $this->getHookVar('buttons_post'); ?>
    		</div>
		</div>
	</div>
	</form>
</div>
<script type="text/javascript"><!--

	$('input[name^=\'shipping_method\']').change(function () {
		$(this).closest('form').submit(); return false;
	});

//--></script>
<?php echo $footer; ?>