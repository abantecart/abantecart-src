<?php if ($error_warning) { ?>
<div class="warning alert alert-error alert-danger"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="tab-content">
<div class="panel-heading">

	<div class="pull-right">
		<?php if (!empty ($help_url)) : ?>
		<a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="new" data-toggle="tooltip" title="" data-original-title="Help">
		<i class="fa fa-question-circle fa-lg"></i>
		</a>
		<?php endif; ?>
	</div>
  
</div>

<div class="panel-body panel-body-nopadding">
	 <?php echo $text_description; ?>
</div>
	
<div class="panel-footer">
    <div class="row">
       <div class="col-sm-6 col-sm-offset-4">

        <a href="<?php echo $start_migration; ?>" class="btn btn-primary">
            <i class="fa fa-download"></i> <?php echo $button_start_migration; ?>
        </a>
       
       </div>
    </div>
</div>

</div>