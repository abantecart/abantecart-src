<input type="<?php echo $type ?>"
       name="<?php echo $name ?>"
       id="<?php echo $id ?>"
       value="<?php echo $value ?>"
       placeholder="<?php echo $placeholder ?>"
       x-autocompletetype="tel"
       class="form-control <?php echo $style; ?>"
		<?php echo $attr; ?>
		<?php echo $regexp_pattern ? 'pattern="'.$regexp_pattern.'"':'';?>
		<?php echo $error_text ? 'title="'.$error_text.'"':'';?>/>
<?php if ( $required == 'Y' ) { ?>
<span class="input-group-addon"><span class="required">*</span></span>
<?php } ?>

<script>
    jQuery(function($){
      var input = $('#<?php echo $id ?>')
      input.mobilePhoneNumber();
      //input.mobilePhoneNumber({allowPhoneWithoutPrefix: '+1'});
    });
  </script>
