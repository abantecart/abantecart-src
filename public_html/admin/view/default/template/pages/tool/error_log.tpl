<?php if ($success) { ?>
	<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="tab-content">
	<div class="panel-heading">
		<div class="pull-left">
			<a href="clear" class="btn btn-primary" id="clear"><i class="fa fa-trash-o"></i> <?php echo $button_clear;?></a>
		</div>
	</div>

	<div class="panel-body panel-body-nopadding">
		<div class="error-log">
			<table class="table table-striped">
			<?php
				foreach($log as $line){ ?>
					<tr><td><?php echo $line; ?></td></tr>
			<?php } ?>
			</table>
		</div>
	</div>
</div><!-- <div class="tab-content"> -->
