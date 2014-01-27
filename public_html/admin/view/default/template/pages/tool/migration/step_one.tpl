<?php if ($error_warning) { ?>
	<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
	<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>
<div class="contentBox">
	<div class="cbox_tl">
		<div class="cbox_tr">
			<div class="cbox_tc">
				<div class="heading icon_title_log"><?php echo $heading_title; ?></div>
				<div class="toolbar">
					<?php if (!empty ($help_url)) : ?>
						<div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img
										src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<div class="cbox_cl">
		<div class="cbox_cr">
			<div class="cbox_cc">
				<?php echo $form['form_open']; ?>
				<table class="form">
					<tr>
						<td><?php echo $entry_cart_type; ?></td>
						<td><?php echo $form['cart_type']; ?>
							<br/>
							<?php if ($error_cart_type) { ?>
								<span class="required"><?php echo $error_cart_type; ?></span>
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td><?php echo $entry_cart_url; ?></td>
						<td><?php echo $form['cart_url']; ?>
							<br/>
							<?php if ($error_cart_url) { ?>
								<span class="required"><?php echo $error_cart_url; ?></span>
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td colspan="2"><b><?php echo $text_db_info; ?></b></td>
					</tr>
					<tr>
						<td width="185"><?php echo $entry_db_host; ?></td>
						<td><?php echo $form['db_host']; ?>
							<br/>
							<?php if ($error_db_host) { ?>
								<span class="required"><?php echo $error_db_host; ?></span>
							<?php } ?></td>
					</tr>
					<tr>
						<td><?php echo $entry_db_user; ?></td>
						<td><?php echo $form['db_user']; ?>
							<br/>
							<?php if ($error_db_user) { ?>
								<span class="required"><?php echo $error_db_user; ?></span>
							<?php } ?></td>
					</tr>
					<tr>
						<td><?php echo $entry_db_password; ?></td>
						<td><?php echo $form['db_password']; ?></td>
					</tr>
					<tr>
						<td><?php echo $entry_db_name; ?></td>
						<td><?php echo $form['db_name']; ?>
							<br/>
							<?php if ($error_db_name) { ?>
								<span class="required"><?php echo $error_db_name; ?></span>
							<?php } ?></td>
					</tr>
					<tr>
						<td><?php echo $entry_db_prefix; ?></td>
						<td><?php echo $form['db_prefix']; ?></td>
					</tr>
				</table>

				<div class="buttons align_center">
					<a onclick="location = '<?php echo $cancel; ?>';" class="btn_standard"
					   href="<?php echo $cancel; ?>"><?php echo $form['button_cancel']; ?></a>
					<button type="submit" class="btn_standard"><?php echo $form['button_continue']; ?></button>
				</div>

				</form>
			</div>
		</div>
	</div>
	<div class="cbox_bl">
		<div class="cbox_br">
			<div class="cbox_bc"></div>
		</div>
	</div>
</div>
<script type="text/javascript">
	<!--
	$(function () {
		$('select[name=cart_type]').val('<?php echo $cart_type?>');
	});
	-->
</script>