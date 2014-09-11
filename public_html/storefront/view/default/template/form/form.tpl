<?php echo $form_open ?>
<?php if ( !empty($description) ) { ?>
<h3 class="heading3 form_description"><?php echo $description ?></h3>
<?php } ?>
<?php echo $form ?>
<div class="form-group">
	<div class="col-md-7 pull-right">
	<button type="reset" class="btn btn-default pull-left"><i class="fa fa-refresh"></i></button>
	&nbsp;
	<?php echo $submit ?>
	</div>
</div>

<?php echo $form_close ?>