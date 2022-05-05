<?php
if ($show_payment == false) { ?>
</form>
<?php }
echo $login_form['form_open']; ?>
<div id="login_error_container">
    <?php if (in_array($action, ['', 'login']) && $error) { ?>
		<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation fa-fw"></i> <?php echo $error; ?></div>
    <?php } ?>
</div>
<p class="text-center"> <?php echo $fast_checkout_text_please_login; ?>:</p>
<fieldset>
    <div class="form-floating mb-3 col-6 mx-auto">
        <?php
            $login_form['loginname']->set('no_wrapper',true);
            echo $login_form['loginname'];
        ?>
        <label for="<?php echo $login_form['loginname']->element_id; ?>">
            <?php echo_html2view($fast_checkout_text_login_or_email); ?>
        </label>
    </div>
    <div class="form-floating mb-3 col-6 mx-auto">
        <?php
        $login_form['password']->set('no_wrapper',true);
        echo $login_form['password']?>
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
        <i class="fa fa-lock fa-fw"></i> <?php echo $fast_checkout_button_login; ?>
    </button>
</div>
</form>
<?php echo $this->getHookVar('login_extension'); ?>
