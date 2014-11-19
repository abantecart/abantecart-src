<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title"><?php echo $extension_info['name']; ?></h4>
	</div>
	<div class="panel-body panel-body-nopadding">
		<div class="row">
			<div class="col-md-1">
				<img src="<?php echo $extension_info['icon'] ?>" alt="<?php echo $exrension['name'] ?>" />
			</div>
			<?php if ($extension_info['version']) { ?>
				<div class="col-md-2"><?php echo $text_version . ': <br/>' . $extension_info['version']; ?></div>
			<?php
			}
			if ($extension_info['installed']) {
				?>
				<div class="col-md-3"><?php echo $text_installed_on . ': <br/>' . $extension_info['installed']; ?></div>
			<?php
			}
			if ($extension_info['date_added']) {
				?>
				<div class="col-md-3"><?php echo $text_date_added . ': <br/>' . $extension_info['date_added']; ?></div>
			<?php
			}
			//Licence key if present
			if ($extension_info['license']) {
				?>
				<div class="col-md-3"><?php echo $text_license . ': <br/>' . $extension_info['license']; ?></div>
			<?php
			}
			if ( $upgrade_button ) { ?>
				<div class="col-md-1"><a class="btn btn-primary" href="<?php echo $upgrade_button->href ?>"><?php echo $upgrade_button->text ?></a></div>
			<?php } ?>
			<?php echo $this->getHookVar('extension_summary_item'); ?>
		</div>
	</div>
</div>