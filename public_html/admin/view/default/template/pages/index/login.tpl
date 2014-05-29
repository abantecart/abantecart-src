<section>

	<?php if ($error_warning) { ?>
	<div class="warning alert alert-error alert-danger"><?php echo $error_warning; ?></div>
	<?php } ?>
  
    <div class="lockedpanel">
        <div class="loginuser">
            <img src="<?php echo $template_dir; ?>image/login.png" alt="<?php echo $text_login; ?>" />
        </div>
        <div class="logged">
            <h4><?php echo $heading_title; ?></h4>
            <small class="text-muted"><?php echo $text_login; ?></small>
        </div>
        
 		<?php echo $form['form_open']; ?>
		<input type="<?php echo $form['fields']['username']->type; ?>" name="<?php echo $form['fields']['username']->name; ?>" class="form-control" id="<?php echo $form['fields']['username']->element_id; ?>" placeholder="<?php echo $entry_username; ?>" value="<?php echo $form['fields']['username']->value; ?>">
		<input type="<?php echo $form['fields']['password']->type; ?>" name="<?php echo $form['fields']['password']->name; ?>" class="form-control" id="<?php echo $form['fields']['password']->element_id; ?>" placeholder="<?php echo $entry_password; ?>">
		<button type="submit" class="btn btn-primary btn-block"><i class="fa fa-lock"></i> <?php echo $form['submit']; ?></button>
		<?php if ($redirect) { ?>
		<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
		 <?php } ?>
		</form>
		
		 <a href="<?php echo $forgot_password ?>"><?php echo $entry_forgot_password ?></a>

    </div><!-- lockedpanel -->
  
</section>