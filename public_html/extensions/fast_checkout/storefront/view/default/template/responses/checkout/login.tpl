<?php
if ($show_payment == false) {
    ?>
	</form>
    <?php
}
?>
<?php echo $login_form['form_open']; ?>
<div id="login_error_container">
    <?php if (in_array($action, array('','login')) && $error) { ?>
		<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation fa-fw"></i> <?php echo $error; ?></div>
    <?php } ?>
</div>
<p class="text-center"> <?php echo $fast_checkout_text_please_login; ?>:</p>
<fieldset>
	<div class="form-group">
		<div class="left-inner-addon">
			<i class="fa fa-envelope"></i>
			<input name="loginname" class="form-control input-lg" placeholder="Login name or Email Address" type="text">
		</div>
	</div>
	<div class="form-group">
		<div class="left-inner-addon">
			<i class="fa fa-key"></i>
			<input name="password" class="form-control input-lg" placeholder="Password" type="password">
		</div>
	</div>
</fieldset>
<br>
<div class=" text-center">
	<button id="LoginFrm_Submit" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Authenticating ... ">
		<i class="fa fa-lock fa-fw"></i> <?php echo $fast_checkout_button_login; ?>
	</button>
	<a href="<?php echo $reset_url; ?>" class="btn btn-default" target="_login">
		<i class="fa fa-user-plus fa-fw"></i> <?php echo $fast_checkout_button_reset_login; ?>
	</a>
</div>
</form>
