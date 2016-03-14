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

<script type="application/javascript">
	$(document).ready(function () {
		$('#<?php echo $id ?>').intlTelInput({
			autoHideDialCode: false,
			nationalMode: <?php echo $value ? 'false' : 'true'; ?>,
			utilsScript: "<?php echo $this->templateResource('/javascript/intl-tel-input/js/utils.js'); ?>"
		});

		$('#<?php echo $id ?>').on("blur", function () {
			var intlNumber = $(this).intlTelInput("getNumber");
			intlNumber = intlNumber.replace(/[^0-9\+]+/g, '');
			$(this).val(intlNumber);
		});
	});
</script>
