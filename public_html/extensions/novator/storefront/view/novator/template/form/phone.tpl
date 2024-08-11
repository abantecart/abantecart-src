<?php if(!$no_wrapper){?>
    <div class="input-group h-100">
<?php } ?>
<input type="tel"
       name="<?php echo $name ?>"
       id="<?php echo $id ?>"
       value="<?php echo $value ?>"
       placeholder="<?php echo $placeholder ?>"
       class="form-control <?php echo $style; ?>"
        <?php echo $attr; ?>
        <?php echo $regexp_pattern ? 'pattern="'.$regexp_pattern.'"':'';?>
        <?php echo $error_text ? 'title="'.$error_text.'"':'';?>
        <?php if ( $required ) { echo 'required'; } ?> />
<?php if ( $required) { ?>
    <span class="input-group-text text-danger">*</span>
<?php } ?>

<?php if(!$no_wrapper){?>
    </div>
<?php } ?>