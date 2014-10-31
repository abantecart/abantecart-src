<section>

	<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>
  
    <div class="lockedpanel">
        <div class="loginuser">
            <img src="<?php echo $template_dir; ?>image/login.png" alt="<?php echo $text_login; ?>" />
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
 		</div>
 		
 		<div class="form-group">
	 		<button type="submit" class="btn btn-primary btn-block"><i class="fa fa-lock fa-fw"></i> <?php echo $form['submit']->text; ?></button>
 		</div>
	
		<?php if ($redirect) { ?>
		<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
		 <?php } ?>
		</form>
		
		 <a href="<?php echo $forgot_password ?>"><?php echo $entry_forgot_password ?></a>

    </div><!-- lockedpanel -->
  
</section>