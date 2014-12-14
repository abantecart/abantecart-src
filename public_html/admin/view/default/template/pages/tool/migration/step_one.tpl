<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div class="tab-content">
<?php echo $form['form_open']; ?>
<div class="panel-heading">

	<div class="pull-left">
		<a class="btn btn-default" onclick="window.history.back();">
			<i class="fa fa-arrow-left fa-fw"></i> <?php echo $button_back; ?>
		</a>       
	</div>

	<div class="pull-right">
		<?php if (!empty ($help_url)) : ?>
		<a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="new" data-toggle="tooltip" title="" data-original-title="Help">
		<i class="fa fa-question-circle fa-lg"></i>
		</a>
		<?php endif; ?>
	</div>
  
</div>

<div class="panel-body panel-body-nopadding">

	<label class="h4 heading"><?php echo $text_cart_info; ?></label>
				
	<div class="form-group <?php if ($error_cart_type) { echo "has-error"; } ?>">
	    <label class="control-label col-sm-offset-1 col-sm-3 col-xs-12"><?php echo $entry_cart_type; ?></label>
	    <div class="input-group afield  col-sm-5 col-xs-12">
	    	<?php echo $form['cart_type']; ?>
	    </div>
	    <?php if ($error_cart_type) { ?>
	    <span class="help-block field_err"><?php echo $error_cart_type; ?></span>
	    <?php } ?>
	</div>

	<div class="form-group <?php if ($error_cart_url) { echo "has-error"; } ?>">
	    <label class="control-label col-sm-offset-1 col-sm-3 col-xs-12"><?php echo $entry_cart_url; ?></label>
	    <div class="input-group afield  col-sm-5 col-xs-12">
	    	<?php echo $form['cart_url']; ?>
	    </div>
	    <?php if ($error_cart_url) { ?>
	    <span class="help-block field_err"><?php echo $error_cart_url; ?></span>
	    <?php } ?>
	</div>

	<label class="h4 heading"><?php echo $text_db_info; ?></label>

	<div class="form-group <?php if ($error_db_host) { echo "has-error"; } ?>">
	    <label class="control-label col-sm-offset-1 col-sm-3 col-xs-12"><?php echo $entry_db_host; ?></label>
	    <div class="input-group afield  col-sm-5 col-xs-12">
	    	<?php echo $form['db_host']; ?>
	    </div>
	    <?php if ($error_db_host) { ?>
	    <span class="help-block field_err"><?php echo $error_db_host; ?></span>
	    <?php } ?>
	</div>

	<div class="form-group <?php if ($error_db_user) { echo "has-error"; } ?>">
	    <label class="control-label col-sm-offset-1 col-sm-3 col-xs-12"><?php echo $entry_db_user; ?></label>
	    <div class="input-group afield  col-sm-5 col-xs-12">
	    	<?php echo $form['db_user']; ?>
	    </div>
	    <?php if ($error_db_user) { ?>
	    <span class="help-block field_err"><?php echo $error_db_user; ?></span>
	    <?php } ?>
	</div>

	<div class="form-group <?php if ($error_db_password) { echo "has-error"; } ?>">
	    <label class="control-label col-sm-offset-1 col-sm-3 col-xs-12"><?php echo $entry_db_password; ?></label>
	    <div class="input-group afield  col-sm-5 col-xs-12">
	    	<?php echo $form['db_password']; ?>
	    </div>
	    <?php if ($error_db_password) { ?>
	    <span class="help-block field_err"><?php echo $error_db_password; ?></span>
	    <?php } ?>
	</div>
				
	<div class="form-group <?php if ($error_db_name) { echo "has-error"; } ?>">
	    <label class="control-label col-sm-offset-1 col-sm-3 col-xs-12"><?php echo $entry_db_name; ?></label>
	    <div class="input-group afield  col-sm-5 col-xs-12">
	    	<?php echo $form['db_name']; ?>
	    </div>
	    <?php if ($error_db_name) { ?>
	    <span class="help-block field_err"><?php echo $error_db_name; ?></span>
	    <?php } ?>
	</div>

	<div class="form-group">
	    <label class="control-label col-sm-offset-1 col-sm-3 col-xs-12"><?php echo $entry_db_prefix; ?></label>
	    <div class="input-group afield col-sm-2 col-xs-12">
	    	<?php echo $form['db_prefix']; ?>
	    </div>
	</div>

</div>
	
<div class="panel-footer">
    <div class="row">
       <div class="col-sm-6 col-sm-offset-3">
        <button type="submit" class="btn btn-primary">
           <i class="fa fa-arrow-right fa-fw"></i>  <?php echo $button_continue; ?>
        </button>
		&nbsp;
		<button class="btn btn-default" type="reset">
			<i class="fa fa-refresh fa-fw"></i> <?php echo $button_reset; ?>
		</button>       
       </div>
    </div>
</div>
</form>

</div>