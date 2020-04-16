<?php echo $head; ?>

    <div id="pay_error_container">
        <?php if ($info) { ?>
            <div class="info alert alert-info"><i class="fa fa fa-check fa-fw"></i> <?php echo $info; ?></div>
        <?php } ?>
        <?php if ($error) { ?>
            <div class="alert alert-danger" role="alert"><i class="fa fa-exclamation fa-fw"></i> <?php echo $error; ?>
            </div>
        <?php } ?>
    </div>

<?php echo $footer; ?>