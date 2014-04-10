<h1 class="heading1">
  <span class="maintext"><i class="icon-lock"></i> <?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>

<?php if ($success) { ?>
<div class="alert alert-success">
<button type="button" class="close" data-dismiss="alert">&times;</button>
<?php echo $success; ?>
</div>
<?php } ?>

<?php if ($error) { ?>
<div class="alert alert-error">
<button type="button" class="close" data-dismiss="alert">&times;</button>
<?php echo $error; ?>
</div>
<?php } ?>

<section class="newcustomer">
	<h2 class="heading2"><?php echo $text_i_am_new_customer; ?></h2>
	<div class="loginbox">
		<h4 class="heading4"><?php echo $text_checkout; ?></h4>
		<?php echo $form1[ 'form_open' ]; ?>
		<fieldset>
			<div class="control-group mt20">
		      <?php echo $form1[ 'register' ];?>
			</div>
		<?php if ($guest_checkout) { ?>
			<div class="control-group mt20">
		      <?php echo $form1[ 'guest' ];?>
			</div>
		<?php } ?>
			<div class="control-group mt20 mb40">
		      <?php echo $text_create_account; ?>
			</div>
			<button type="submit" class="btn btn-orange"  title="<?php echo $form1['continue']->name ?>">
				<i class="<?php echo $form1['continue']->icon; ?> icon-white"></i>
				<?php echo $form1['continue']->name ?>
			</button>
		</fieldset>
		</form>
	</div>
</section> 

<section class="returncustomer">
	<h2 class="heading2"><?php echo $text_returning_customer; ?></h2>
	<div class="loginbox">
		<h4 class="heading4"><?php echo $text_i_am_returning_customer; ?></h4>
		<?php echo $form2[ 'form_open' ]; ?>
			<fieldset>
				<div class="control-group">
				  <label  class="control-label">
				  <?php 
				  	if ($noemaillogin) {
				  		echo $entry_loginname; 
				  	} else {
				  		echo $entry_email_address;
				  	}
				  ?>
				  </label>
				  <div class="controls">
					<?php echo $form2[ 'loginname' ]?>
				  </div>
				</div>
				<div class="control-group">
				  <label  class="control-label"><?php echo $entry_password; ?></label>
				  <div class="controls">
					<?php echo $form2[ 'password' ]?>
				  </div>
				</div>
				<a href="<?php echo $forgotten_pass; ?>"><?php echo $text_forgotten_password; ?></a>
				<?php if($noemaillogin) { ?>
				&nbsp;&nbsp;<a href="<?php echo $forgotten_login; ?>"><?php echo $text_forgotten_login; ?></a>
				<?php } ?>
				<br>
				<br>
				<button type="submit" class="btn btn-orange"  title="<?php echo $form2['login_submit']->name ?>">
					<i class="<?php echo $form2['login_submit']->{'icon'}; ?> icon-white"></i>
					<?php echo $form2['login_submit']->name ?>
				</button>
			</fieldset>
		</form>
	</div>
	<?php echo $this->getHookVar('login_extension'); ?>
</section> 

