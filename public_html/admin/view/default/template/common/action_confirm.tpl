<?php if ($error_warning) { ?>
	<div class="warning alert alert-error alert-danger"><i class="fa fa-thumbs-down fa-fw"></i> <?php echo $error_warning; ?></div>
<?php } else if (!empty($error['warning'])) { ?>
	<div class="warning alert alert-error alert-danger"><i class="fa fa-thumbs-down fa-fw"></i> <?php echo $error['warning']; ?></div>
<?php } ?>

<?php if ($success) { ?>
	<div class="success alert alert-success"><i class="fa fa fa-check fa-fw"></i> <?php echo $success; ?></div>
<?php } ?>