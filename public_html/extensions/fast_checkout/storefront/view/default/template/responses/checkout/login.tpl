<?php
if ($show_payment == false) { ?>
</form>
<?php }
echo $login_form['form_open']; ?>

<p class="text-center"> <?php echo $fast_checkout_text_please_login; ?>:</p>
<fieldset>
	<div class="form-group">
		<div class="left-inner-addon">
			<i class="fa fa-envelope"></i>
			<input name="loginname" class="form-control input-lg" placeholder="<?php echo_html2view($fast_checkout_text_login_or_email); ?>" type="text">
		</div>
	</div>
	<div class="form-group">
		<div class="left-inner-addon">
			<i class="fa fa-key"></i>
			<input name="password" class="form-control input-lg" placeholder="<?php echo_html2view($fast_checkout_text_password); ?>" type="password">
		</div>
	</div>
</fieldset>
<br>
<div class=" text-center">
	<button id="LoginFrm_Submit" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo_html2view($fast_checkout_text_authenticating); ?>">
		<i class="fa fa-lock fa-fw"></i> <?php echo $fast_checkout_button_login; ?>
	</button>
	<a href="<?php echo $reset_url; ?>" class="btn btn-default" target="_login">
		<i class="fa fa-user-plus fa-fw"></i> <?php echo $fast_checkout_button_reset_login; ?>
	</a>
</div>
</form>
<?php echo $this->getHookVar('login_extension'); ?>
