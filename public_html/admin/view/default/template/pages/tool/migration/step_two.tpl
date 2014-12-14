<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php 

foreach($counts as &$val){
	if($val>1000){
		$val = '<span class="required">'.$val.'</span>';
	}
} unset($val);

$form['migrate_products_text'] = nl2br(sprintf($form['migrate_products_text'], $counts['products'], $counts['categories'], $counts['manufacturers']));
$form['migrate_customers_text'] = sprintf($form['migrate_customers_text'], $counts['customers']);
?>

<div class="tab-content">
<?php echo $form['form_open']; ?>
<div class="panel-heading">

	<div class="pull-left">
		<a class="btn btn-default" href="<?php echo $back; ?>">
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

	<div class="form-group">
	    <label class="control-label col-sm-offset-1 col-sm-3 col-xs-12">
	    <?php echo $entry_migrate_data; ?> <span class="help"><?php echo $form['migrate_products_text'];?></span>
	    </label>
	    <div class="input-group afield  col-sm-5 col-xs-12">
	    	<?php echo $form['migrate_products'] ?>	
	    </div>
	</div>

	<div class="form-group">
	    <label class="control-label col-sm-offset-1 col-sm-3 col-xs-12">
	    <?php echo $form['migrate_customers_text'] ?>
	    </label>
	    <div class="input-group afield  col-sm-5 col-xs-12">
	    	<?php echo $form['migrate_customers'] ?>
	    </div>
	</div>

	<div class="form-group">
	    <label class="control-label col-sm-offset-1 col-sm-3 col-xs-12">
	    <?php echo $entry_erase_existing_data; ?>
	    </label>
	    <div class="input-group afield  col-sm-5 col-xs-12">
	    	<?php echo $form['erase_existing_data'] ?>
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