<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div class="tab-content">
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
				
	<?php echo $log; ?>

</div>
	
<div class="panel-footer">
    <div class="row">
       <div class="col-sm-6 col-sm-offset-3">
		<a class="btn btn-default" href="<?php echo $back; ?>">
			<i class="fa fa-arrow-left fa-fw"></i> <?php echo $button_back; ?>
		</a>       
       </div>
    </div>
</div>
</form>

</div>