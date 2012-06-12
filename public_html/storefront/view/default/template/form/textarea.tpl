<span class="textarea_element" >
    <textarea name="<?php echo $name ?>" id="<?php echo $id ?>" <?php echo $attr ?> ><?php echo $value ?></textarea>
</span>
<?php if ( $required == 'Y' ) : ?>
<span class="required">*</span>
<?php endif; ?>

