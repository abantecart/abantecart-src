<span class="btn btn-file">
    <input type="file" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>" placeholder="<?php echo $placeholder ?>" <?php echo $attr; ?> <?php echo ($style ? 'class="'.$style.'"':''); ?>/>
</span>
<?php if ( $required == 'Y' ){ ?>
<span class="required">*</span>
<?php } ?>

