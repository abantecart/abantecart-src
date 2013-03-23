<h1 class="heading1">
  <span class="maintext"><?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>

<?php if ($success) { ?>
<div class="alert alert-success">
<button type="button" class="close" data-dismiss="alert">&times;</button>
<?php echo $success; ?>
</div>
<?php } ?>

<?php if ($error_warning) { ?>
<div class="alert alert-error">
<button type="button" class="close" data-dismiss="alert">&times;</button>
<?php echo $error_warning; ?>
</div>
<?php } ?>

<div class="container-fluid">

	<?php echo $form['form_open'];?>
	<h4 class="heading4"><?php echo $text_shipping_address; ?></h4>
	<div class="registerbox">
		<table class="table table-striped table-bordered">
		<tr>
			<td><?php echo $address; ?></td>
			<td>
			<div class="control-group">
				<label class="control-label"><?php echo $text_shipping_to; ?></label>
				<div class="controls">
					<a href="<?php echo $change_address_href; ?>" class="btn mr10" title="<?php echo $change_address->name ?>">
					    <i class="icon-edit"></i>
					    <?php echo $change_address->name ?>
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
				<td width="5%"><label for="shipping_shipping_method<?php echo $quote['id']; ?>"><?php echo $quote['radio']; ?></label></td>
				<td><label for="shipping_shipping_method<?php echo $quote['id']; ?>" style="cursor: pointer;"><?php echo $quote['title']; ?></label></td>
				<td align="right"><label for="<?php echo $quote['id']; ?>" style="cursor: pointer;"><?php echo $quote['text']; ?></label></td>
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
	
	<h4 class="heading4"><?php echo $text_comments; ?></h4>	
	<div class="registerbox">		
		<div class="content">
			<?php echo $form['comment']; ?>
     	</div>
		
		<div class="control-group">
			<div class="controls">
    			<div class="mt20 mb20">
    			<?php echo $this->getHookVar('buttons_pre'); ?>
				<?php echo $buttons; ?>
				<?php echo $this->getHookVar('buttons_post'); ?>
    			</div>	
    		</div>
		</div>			
      	
	</div>
	
	</form>
		
</div>