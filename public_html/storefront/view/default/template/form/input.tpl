<span class="text_element" >
    <input type="<?php echo $type ?>" placeholder="<?php echo $placeholder ?>" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>" <?php echo $attr; ?> <?php echo ($style ? 'class="'.$style.'"':''); ?>/>
</span>
<?php if ( $required == 'Y' ) : ?>
<span class="required">*</span>
<?php endif; ?>

