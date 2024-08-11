<div class="input-group">
    <textarea class="form-control <?php echo $style; ?>" name="<?php echo $name ?>" id="<?php echo $id ?>" placeholder="<?php echo $placeholder ?>" <?php echo $attr; ?> <?php if ( $required ) { echo 'required'; }?> ><?php echo $value ?></textarea>
    <?php if ( $required ){ ?>
    <span class="input-group-text text-danger">*</span>
    <?php } ?>
</div>
