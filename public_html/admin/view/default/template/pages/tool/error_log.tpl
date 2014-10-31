<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div class="tab-content">
	<div class="panel-heading">
		<div class="pull-left">
			<a href="<?php echo $clear_url; ?>" class="btn btn-primary lock-on-click" id="clear"><i class="fa fa-trash-o"></i> <?php echo $button_clear;?></a>
		</div>
	</div>

	<div class="panel-body panel-body-nopadding">
		<div class="error-log">
		<?php if( count($log) ) { ?>
			<table class="table table-striped">
			<?php
				foreach($log as $line){ ?>
					<tr><td><?php echo $line; ?></td></tr>
			<?php } ?>
			</table>
		<?php } else { ?>
			<div class="text-center">
			<h1><i class="fa fa-thumbs-o-up fa-lg"></i></h1>
			</div>
		<?php } ?>
		</div>
	</div>
</div><!-- <div class="tab-content"> -->