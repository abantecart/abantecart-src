<?php echo $head; ?>
<h1 class="heading1">
  <span class="maintext"><i class="fa fa-edit"> </i><?php echo $heading_title; ?></span>
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

<div class="contentpanel">
	<?php echo $form['form_open'];?>

	<h4 class="heading4"><?php echo $text_your_details; ?></h4>
	<div class="registerbox form-horizontal">
		<fieldset>
		<?php
			$field_list = array();
			array_push($field_list, 'loginname', 'firstname', 'lastname', 'email', 'telephone', 'fax');
			
			foreach ($field_list as $field_name) {
		?>
			<div class="form-group <?php if (${'error_'.$field_name}) echo 'has-error'; ?>">
				<label class="control-label col-md-4"><?php echo ${'entry_'.$field_name}; ?></label>
				<div class="input-group col-md-4">
				    <?php echo $form[$field_name]; ?>
				</div>
				<span class="help-block"><?php echo ${'error_'.$field_name}; ?></span>
			</div>		
		<?php
			}
		?>	
		
		<?php echo $this->getHookVar('customer_attributes'); ?>
	
		</fieldset>
	</div>

	<div class="form-group">
	    <div class="col-md-12">
	    	<button class="btn btn-orange pull-right" title="<?php echo $form['continue']->name ?>" type="submit">
	    	    <i class="<?php echo $form['continue']->{'icon'}; ?>"></i>
	    	    <?php echo $form['continue']->name ?>
	    	</button>
			<a href="<?php echo $back; ?>" class="btn btn-default mr10" title="<?php echo $form['back']->text ?>">
			    <i class="<?php echo $form['back']->{'icon'}; ?>"></i>
			    <?php echo $form['back']->text ?>
			</a>
	    </div>
	</div>
	
</form>
</div>
<?php echo $footer; ?>