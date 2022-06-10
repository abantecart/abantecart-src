<div class="input-group">
    <input type="file" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>" placeholder="<?php echo $placeholder ?>" <?php echo $attr; ?> class="form-control <?php echo $style?>" <?php if ( $required ) { echo 'required'; }?>/>
    <?php if ( $required ) { ?>
    <span class="input-group-text text-danger">*</span>
    <?php } ?>
</div>