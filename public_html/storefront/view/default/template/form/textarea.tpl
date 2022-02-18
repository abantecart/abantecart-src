<?php
?>
<span class="textarea_element" >
    <textarea class="form-control <?php echo $style; ?>" name="<?php echo $name ?>" id="<?php echo $id ?>" placeholder="<?php echo $placeholder ?>" <?php echo $attr; ?> ><?php echo $value ?></textarea>
</span>
<?php if ( $required == 'Y' ) : ?>
<span class="input-group-addon"><span class="required">*</span></span>
<?php endif; ?>