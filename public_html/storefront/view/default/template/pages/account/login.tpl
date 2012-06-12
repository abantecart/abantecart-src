<div id="content" class="login">
	<div class="login">
		<div class="top">
			<div class="left"></div>
			<div class="right"></div>
			<div class="center">
				<h1><?php echo $heading_title; ?></h1>
			</div>
		</div>
		<div class="middle">
			<?php if ($success) { ?>
			<div class="success"><?php echo $success; ?></div>
			<?php } ?>
			<?php if ($error) { ?>
			<div class="warning"><?php echo $error; ?></div>
			<?php } ?>
			<div style="margin-bottom: 10px; display: inline-block; width: 100%;">

				<div class="content_block flt_left"><b class="block_heading"><?php echo $text_i_am_new_customer; ?></b>

					<div class="top">
						<div class="left"></div>
						<div class="right"></div>
						<div class="center"></div>
					</div>
					<div class="middle">
						<div class="middle_content" >
							<?php echo $form1[ 'form_open' ]; ?>


							<p><?php echo $text_checkout; ?></p><br/>
							<?php echo $form1[ 'register' ];?>
							<br/><br/>
							<?php if ($guest_checkout) { ?>
							<?php echo $form1[ 'guest' ]; ?>
							<br/>
							<?php } ?>
							<br/>
							<p><?php echo $text_create_account; ?></p>
							<br class="clr_both">
							<div class="flt_right"><?php echo $form1[ 'continue' ]; ?></div>
							</form>
							<div class="clr_both"></div>
						</div>
					</div>

					<div class="bottom">
						<div class="left"></div>
						<div class="right"></div>
						<div class="center"></div>
					</div>
				</div>

				<div class="content_block flt_right"><b
						class="block_heading"><?php echo $text_returning_customer; ?></b>

					<div class="top">
						<div class="left"></div>
						<div class="right"></div>
						<div class="center"></div>
					</div>
					<div class="middle">
						<div class="middle_content">
							<?php echo $form2[ 'form_open' ]; ?>
							<?php echo $text_i_am_returning_customer; ?><br/>
							<br/>
							<b><?php echo $entry_email_address; ?></b><br/>
							<?php echo $form2[ 'email' ]?>
							<br/>
							<br/>
							<b><?php echo $entry_password; ?></b><br/>
							<?php echo $form2[ 'password' ]?>
							<div class="clr_both"></div>
							<br/>
							<a href="<?php echo $forgotten; ?>"><?php echo $text_forgotten_password; ?></a><br/>

							<div class="flt_right"><?php echo $form2[ 'login' ]?></div>
							<?php if ($redirect) { ?>
							<?php echo $form1[ 'redirect' ]; ?>
							<?php } ?>
							</form>
							
						</div>
					</div>
					<div class="bottom">
						<div class="left"></div>
						<div class="right"></div>
						<div class="center"></div>
					</div>
				</div>

			</div>
		</div>

	</div>
</div>
<script type="text/javascript"><!--
$('#loginFrm input').keydown(function(e) {
	if (e.keyCode == 13) {
		$('#loginFrm').submit();
	}
});
//--></script>