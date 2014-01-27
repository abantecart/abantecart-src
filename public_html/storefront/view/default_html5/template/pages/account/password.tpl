<h1 class="heading1">
  <span class="maintext"><i class="icon-key"></i> <?php echo $heading_title; ?></span>
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
	<?php echo $form_open; ?>
	
	<h4 class="heading4"><?php echo $text_password; ?></h4>
	<div class="registerbox form-horizontal">
		<fieldset>
		<?php
			$field_list = array();
			array_push($field_list, 'current_password', 'password', 'confirm');
			
			foreach ($field_list as $field_name) {
		?>
			<div class="control-group <?php if (${'error_'.$field_name}) echo 'error'; ?>">
				<label class="control-label"><?php echo ${'entry_'.$field_name}; ?></label>
				<div class="controls">
				    <?php echo ${$field_name}; ?>
					<span class="help-inline"><?php echo ${'error_'.$field_name}; ?></span>
				</div>
			</div>		
		<?php
			}
		?>	
		</fieldset>
	</div>

	<?php echo $this->getHookVar('password_edit_sections'); ?>
	
	<div class="control-group">
	    <div class="controls">
	    	<div class="span4 mt20 mb20">
	    		<button class="btn btn-orange pull-right" title="<?php echo $submit->name ?>" type="submit">
	    		    <i class="<?php echo $submit->{'icon'}; ?> icon-white"></i>
	    		    <?php echo $submit->name ?>
	    		</button>
	    		<a href="<?php echo $back; ?>" class="btn mr10" title="<?php echo $button_back->text ?>">
	    		    <i class="<?php echo $button_back->{'icon'}; ?>"></i>
	    		    <?php echo $button_back->text ?>
	    		</a>
	    	</div>	
	    </div>
	</div>
	</form>
</div>