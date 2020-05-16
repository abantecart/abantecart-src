<?php
if ($recaptcha_v3) {
?>
	<div class="row">
		<div class="col-md-12">
			<input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
            <script src="https://www.google.com/recaptcha/api.js?onload=ReCaptchaCallbackV3&render=<?php echo $recaptcha_site_key; ?>&hl=<?php echo $language_code; ?>"></script>
            <script type="text/javascript">
                var ReCaptchaCallbackV3 = function() {
                    grecaptcha.ready(function() {
                        grecaptcha.execute('<?php echo $recaptcha_site_key; ?>').then(
                            function(token) {
                                $('input#g-recaptcha-response').val(token);
                            });
                        $('.grecaptcha-badge').css('bottom', '64px');
                    });
                };
            </script>
		</div>
	</div>
<?php
} else {
    ?>
	<div class="row">
		<div class="col-md-12">
			<div id="<?php echo $id ?>" align="center" class="g-recaptcha" data-sitekey="<?php echo $recaptcha_site_key; ?>" <?php echo $attr; ?>></div>
		<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=<?php echo $language_code; ?>">
			</script>
		</div>
	</div>
    <?php
}
?>
