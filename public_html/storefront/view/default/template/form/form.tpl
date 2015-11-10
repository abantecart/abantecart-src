<?php echo $form_open ?>
<?php if ( !empty($description) ) { ?>
<h3 class="heading3 form_description"><?php echo $description ?></h3>
<?php } ?>
<?php echo $form ?>
<div class="form-group">
	<div class="col-md-4 col-md-offset-2">
		<button type="reset" class="btn btn-default pull-left"><i class="fa fa-refresh"></i></button>
	</div>
	<div class="col-md-6">
		<button type="<?php echo $submit->type ?>" class="btn btn-primary" title="<?php echo $submit->name ?>">
		<i class="fa fa-check"></i> <?php echo $submit->name ?>
		</button>
	</div>
</div>
<?php echo $form_close ?>