<?php if ( $label_text ){ ?><label class="checkbox" for="<?php echo $id ?>"><?php } ?>
<input style="position: relative; margin-left: 0;" type="checkbox" class="<?php echo $style; ?>" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>" <?php echo ($checked ? 'checked="checked"':'') ?> <?php echo $attr ?> />
<?php if ( $required == 'Y' ){ ?><span class=" required">*</span><?php } ?>
<?php if ( $label_text ){  echo $label_text; ?></label><?php } ?>
