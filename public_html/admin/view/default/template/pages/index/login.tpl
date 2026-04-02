<section>
    <?php include($tpl_common_dir.'action_confirm.tpl'); ?>
    <div class="lockedpanel">
        <div class="loginuser">
            <img src="<?php echo $template_dir; ?>image/login.png" alt="<?php echo $text_login; ?>"/>
        </div>
        <div class="logged">
            <h4><?php echo $heading_title; ?></h4>
            <small class="text-muted"><?php echo $text_login; ?></small>
        </div>
<?php echo $form['form_open']; ?>
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-addon"><i class="fa fa-user fa-fw"></i></div>
                <?php echo $form['fields']['username']; ?>
            </div>
        </div>
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-addon"><i class="fa fa-key fa-fw"></i></div>
                <?php echo $form['fields']['password']; ?>
            </div>
            <div class="pwdhelp text-danger" style="display: none;">
                <?php echo $this->language->get('warning_capslock'); ?>
            </div>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fa fa-lock fa-fw"></i>
                <?php echo $form['submit']->text; ?>
            </button>
        </div>
<?php if ($redirect) { ?>
            <input type="hidden" name="redirect" value="<?php echo $redirect; ?>"/>
<?php } ?>
    </form>

        <div class="form-group mt10">
            <a href="<?php
            echo $forgot_password ?>"><?php echo $entry_forgot_password ?></a>
            <?php echo $text_help_link ?>
        </div>
    </div>
</section>
<script type="application/javascript">
    $(document).ready( function (){
       $('#loginFrm_username').focus();
       $('.warning.alert-danger').addClass('blink');

       //capslock check for password fields
       function enableCapsLockWarnings(container = document) {
           const passwordFields = container.querySelectorAll('input[type="password"]');
           passwordFields.forEach(field => {

               const checkCapsLock = (e) => {
                   const warning = $(field).closest('.form-group').find('.pwdhelp');
                   if (!warning.length) return;
                   const capsOn = e.getModifierState && e.getModifierState('CapsLock');
                   if (capsOn) {
                       warning.show();
                   } else {
                       warning.hide();
                   }
               };

               const attach = () => {
                   window.addEventListener('keydown', checkCapsLock);
                   window.addEventListener('keyup', checkCapsLock);
               };

               const detach = (e) => {
                   window.removeEventListener('keydown', checkCapsLock);
                   window.removeEventListener('keyup', checkCapsLock);
                   $(field).closest('.form-group').find('.pwdhelp').hide();
               };

               field.addEventListener('keydown', checkCapsLock);
               field.addEventListener('keyup', checkCapsLock);
               field.addEventListener('focus', function(e){
                   attach(e);
                   checkCapsLock(e);
               });
               field.addEventListener('blur', detach);
               $(field).closest('.form-group').find('.pwdhelp').hide();
           });
       }
       // Call this once on page load or after dynamic content is added
       enableCapsLockWarnings();
    });

</script>
