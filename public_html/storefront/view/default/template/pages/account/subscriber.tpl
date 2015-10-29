<?php if ($success) { ?>
	<h1 class="heading1">
	  <span class="maintext"><i class="fa fa-thumbs-up"></i> <?php echo $text_subscribe_register; ?></span>
	  <span class="subtext"></span>
	</h1>

	<div class="contentpanel">

	<section class="mb40">
		<p></p>
		<p><?php echo $success; ?></p>
	</section>
	</div>



	<div class="form-group">
		<div class="input-group">
			<div class="pull-right col-md-2 mt20 mb40">
				<?php echo $continue;?>
			</div>
		</div>
	</div>

<?php }else{ ?>
	<h1 class="heading1">
	  <span class="maintext"><i class="fa fa-group"></i> <?php echo $text_subscribe_register; ?></span>
	  <span class="subtext"></span>
	</h1>
<?php if ($error_warning) { ?>
<div class="alert alert-error alert-danger">
<button type="button" class="close" data-dismiss="alert">&times;</button>
<?php echo $error_warning; ?>
</div>
<?php } ?>

<div class="contentpanel">
	<?php echo $form['form_open']; ?>
	
	<p><?php echo $text_account_already; ?></p>
	
	<h4 class="heading4"><?php echo $text_your_details; ?></h4>
	<div class="registerbox form-horizontal">
		<fieldset>
		<?php
		if($form){
			foreach ($form as $field_name=>$field) { ?>
			<div class="form-group <?php echo (${'error_'.$field_name} ? 'has-error' : '')?>">
				<?php if( $field->type == 'recaptcha')  { ?>
				<label class="control-label col-sm-4"></label>
				<?php } else { ?>
				<label class="control-label col-sm-4"><?php echo ${'entry_'.$field_name}; ?></label>
				<?php } ?>
				<div class="input-group col-sm-4">
				    <?php echo $field; ?>
				</div>
				<span class="help-block"><?php echo ${'error_'.$field_name}; ?></span>
			</div>		
		<?php }
			}?>
		</fieldset>
	</div>

	<?php echo $this->getHookVar('subscriber_hookvar'); ?>
	
	<div class="form-group">
	    <div class="col-md-12">
			<div class="pull-left col-md-8">
				<?php echo $create_account ?>
			</div>
	    	<div class="pull-right col-md-4">
				<?php echo $continue ?>
	    	</div>	
	    </div>
	</div>
	
</form>
</div>

<div id="privacyPolicyModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="privacyPolicyModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="privacyPolicyModalLabel"><?php echo $text_agree_href_text; ?></h3>
	</div>
	<div class="modal-body">
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo $text_close; ?></button>
	</div>
</div>
</div>  
</div>

<?php } ?>