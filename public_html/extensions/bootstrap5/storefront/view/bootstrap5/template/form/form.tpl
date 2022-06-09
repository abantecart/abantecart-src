<?php echo $form_open ?>
<?php if ( !empty($description) ) { ?>
<h3 class="form_description"><?php echo $description ?></h3>
<?php } ?>
<?php echo $form ?>
<div class="d-flex justify-content-between">
    <button type="reset" class="btn btn-outline-secondary me-1"><i class="fa fa-refresh fa-fw"></i> <?php echo $button_reset ?> </button>
    <button type="<?php echo $submit->type ?>" class="btn btn-primary lock-on-click" title="<?php echo $submit->name ?>">
        <i class="fa fa-check"></i> <?php echo $submit->name ?>
    </button>
</div>
<?php echo $form_close ?>

