<?php if ($info) { ?>
    <div class="info alert alert-info"><i class="fa fa fa-check fa-fw"></i> <?php echo $info; ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="alert alert-danger" role="alert"><i class="fa fa-solid fa-triangle-exclamation fa-fw"></i> <?php echo $error; ?>
    </div>
<?php } ?>
