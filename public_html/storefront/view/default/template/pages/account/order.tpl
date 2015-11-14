<h1 class="heading1">
  <span class="maintext"><i class="fa fa-book"></i> <?php echo $heading_title; ?></span>
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
	<?php echo $form['form_open']; ?>
	<div class="registerbox form-horizontal">
		<fieldset>
		<?php
			$field_list = array('order_id' => 'order_id',
								'email' => 'email'
								);
			
		foreach ($field_list as $field_name => $field_id) { ?>
			<div class="form-group <?php if ($error[$field_name]) echo 'has-error'; ?>">
				<label class="control-label col-md-4"><?php echo ${'entry_'.$field_name}; ?></label>
				<div class="input-group col-md-4">
				    <?php echo $form[$field_id]; ?>
				</div>
				<span class="help-block"><?php echo $error[$field_name]; ?></span>
			</div>		
		<?php }	?>

			<?php echo $this->getHookVar('check_order_sections'); ?>
			<div class="form-group">
				<div class="col-md-12">
					<button class="btn btn-orange pull-right" title="<?php echo $form['submit']->name ?>" type="submit">
					    <i class="<?php echo $form['submit']->{'icon'}; ?>"></i>
					    <?php echo $form['submit']->name ?>
					</button>
					<a href="<?php echo $back; ?>" class="btn btn-default mr10" title="<?php echo $form['back']->text ?>">
					    <i class="<?php echo $form['back']->{'icon'}; ?>"></i>
					    <?php echo $form['back']->text ?>
					</a>
				</div>
			</div>
			
		</fieldset>
	</div>
	</form>
</div>