<?php
if ($show_payment == false) { ?>
</form>
<?php }

$login_form['form_open']->style .= ' needs-validation ';
//block native browser validation messages
$login_form['form_open']->attr .= ' novalidate ';
echo $login_form['form_open']; ?>
<div id="login_error_container">
    <?php if (in_array($action, ['', 'login']) && $error) {
        include($this->templateResource('/template/responses/checkout/alerts.tpl'));
     } ?>
</div>
<p class="text-center"> <?php echo $fast_checkout_text_please_login; ?>:</p>
<fieldset>
    <div class="form-floating mb-3 col-10 col-offset-2 col-sm-6 col-sm-offset-3 mx-auto">
            <input type="text" name="loginname" id="LoginFrm_loginname" value="<?php echo $login_form['loginname']->value;?>"
                   class="form-control w-100 " autocomplete="username email" required aria-required="true">
            <label for="<?php echo $login_form['loginname']->element_id; ?>">
                <?php echo_html2view($fast_checkout_text_login_or_email); ?>
            </label>
    </div>
    <div class="form-floating mb-3 col-10 col-sm-6 mx-auto">
        <input type="password" name="password" id="LoginFrm_password" value="" class="form-control w-100 " autocomplete="current-password" aria-required="true" required>
        <label for="<?php echo $login_form['password']->element_id; ?>">
            <?php echo_html2view($fast_checkout_text_password); ?>
            </label>
    </div>
</fieldset>
<br>
<div class=" text-center">
	<a href="<?php echo $reset_url; ?>" class="btn btn-outline-success" target="_login">
		<i class="fa fa-user-plus fa-fw"></i> <?php echo $fast_checkout_button_reset_login; ?>
	</a>
    <button id="LoginFrm_Submit" class="btn btn-primary me-2" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo_html2view($fast_checkout_text_authenticating); ?>">
        <i class="fa fa-lock fa-fw"></i> <?php echo $button_submit; ?>
    </button>
</div>
</form>
<?php echo $this->getHookVar('login_extension'); ?>
