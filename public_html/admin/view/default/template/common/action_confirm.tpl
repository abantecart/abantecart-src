<?php if ($error_warning) { ?>
	<div class="warning alert alert-error alert-danger"><i class="fa fa-thumbs-down fa-fw"></i> <?php echo nl2br($error_warning); ?></div>
<?php } else if (!empty($error['warning'])) { ?>
	<div class="warning alert alert-error alert-danger"><i class="fa fa-thumbs-down fa-fw"></i> <?php echo nl2br($error['warning']); ?></div>
<?php } ?>

<?php if ($success) { ?>
	<div class="success alert alert-success"><i class="fa fa fa-check fa-fw"></i> <?php echo nl2br($success); ?></div>
<?php } ?>

<?php if ($info) { ?>
	<div class="info alert alert-info"><i class="fa fa fa-check fa-fw"></i> <?php echo $info; ?></div>
<?php } ?>

<?php if ($attention) { ?>
	<div class="info alert alert-warning"><i class="fa fa fa-exclamation-triangle fa-fw"></i> <?php echo $attention; ?></div>
<?php } ?>