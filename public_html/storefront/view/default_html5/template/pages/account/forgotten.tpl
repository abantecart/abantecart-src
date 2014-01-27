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

<?php if ($error) { ?>
<div class="alert alert-error">
<button type="button" class="close" data-dismiss="alert">&times;</button>
<?php echo $error; ?>
</div>
<?php } ?>

<div class="container-fluid">
	<?php echo  $form[ 'form_open' ]; ?>
	
	<h4 class="heading4"><?php echo $help_text; ?></h4>
	<div class="registerbox form-horizontal">
		<fieldset>
		<?php
			foreach ( $form['fields'] as $field_name => $field) { 
		?>
			<div class="control-group">
				<label class="control-label"><?php echo ${'entry_'.$field_name}; ?></label>
				<div class="controls">
				    <?php echo $form['fields'][$field_name]; ?>
				</div>
			</div>		
		<?php
			}
		?>	
		</fieldset>
	</div>

	<?php echo $this->getHookVar('password_forgotten_sections'); ?>
	
	<div class="control-group">
	    <div class="controls">
	    	<div class="span6 mt20 mb20">
	    		<button class="btn btn-orange pull-right" title="<?php echo $form['continue']->name ?>" type="submit">
	    		    <i class="icon-ok icon-white"></i>
	    		    <?php echo $form['continue']->name ?>
	    		</button>
	    		<a href="<?php echo $back; ?>" class="btn mr10" title="<?php echo $form['back']->text ?>">
	    		    <i class="icon-arrow-left"></i>
	    		    <?php echo $form['back']->text ?>
	    		</a>
	    	</div>	
	    </div>
	</div>
	
	</form>
</div>