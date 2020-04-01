<div class="captcha">
	<div id="<?php echo $id ?>" align="center" class="g-recaptcha" data-sitekey="<?php echo $recaptcha_site_key; ?>" <?php echo $attr; ?>></div>
	<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?render=<?php js_echo($recaptcha_site_key); ?>&hl=<?php echo $language_code; ?>"></script>
    <script>
    grecaptcha.ready(function() {
        grecaptcha.execute('<?php js_echo($recaptcha_site_key); ?>', {action: 'homepage'});
    });
    </script>
</div>