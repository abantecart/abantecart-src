<div id="content">
	<div class="top">
		<div class="left"></div>
		<div class="right"></div>
		<div class="center">
			<h1><?php echo $heading_title; ?></h1>
		</div>
	</div>
	<div class="middle">
		<?php if ($success) { ?>
			<div class="success alert alert-success"><?php echo $success; ?></div>
		<?php } ?>
		<?php if ($error_warning) { ?>
			<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
		<?php } ?>
		<b style="margin-bottom: 2px; display: block;"><?php echo $text_payment_address; ?></b>

		<div class="content">
			<table width="100%">
				<?php echo $this->getHookVar('payment_extensions_pre_address_hook'); ?>
				<tr>
					<td width="50%" valign="top"><?php echo $text_payment_to; ?><br/>
						<br/>

						<div style="text-align: center;"><?php echo $change_address; ?></div>
					</td>
					<td width="50%" valign="top"><b><?php echo $text_payment_address; ?></b><br/>
						<?php echo $address; ?></td>
				</tr>
				<?php echo $this->getHookVar('payment_extensions_post_address_hook'); ?>
			</table>
		</div>
		<?php if ($coupon_status) {
			echo $coupon_form;
		}
		if ($balance) { ?>

		<div class="content">
			<div style="text-align: left;"><?php echo $balance; ?>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $apply_balance_button; ?></div>
		</div>

		<?php
		}
		echo $this->getHookVar('payment_extensions_pre_hook');
		echo $form['form_open'];
		?>
		<?php if ($payment_methods) { ?>
			<b style="margin-bottom: 2px; display: block;"><?php echo $text_payment_method; ?></b>
			<div class="content">
				<p><?php echo $text_payment_methods; ?></p>
				<table width="100%" cellpadding="3">
					<?php echo $this->getHookVar('payment_extensions_pre_payments_hook'); ?>
					<?php foreach ($payment_methods as $payment_method) { ?>
						<tr>
							<td width="1"><?php echo $payment_method['radio']; ?></td>
							<td><label for="payment_payment_method<?php echo $payment_method['id']; ?>"
									   style="cursor: pointer;">
									<?php $icon = $payment_method['icon'];
									if (count($icon)) {
										?>
										<?php if (is_file(DIR_RESOURCE . $icon['image'])) { ?>
											<span class="payment_icon mr10"><img
														src="resources/<?php echo $icon['image']; ?>"
														title="<?php echo $icon['title']; ?>"/>&nbsp;&nbsp;</span>
										<?php } else if (!empty($icon['resource_code'])) { ?>
											<span class="payment_icon mr10"><?php echo $icon['resource_code']; ?>&nbsp;&nbsp;</span>
										<?php }
									} ?>
									<?php echo $payment_method['title']; ?></label></td>
						</tr>
					<?php } ?>
					<?php echo $this->getHookVar('payment_extensions_post_payments_hook'); ?>
				</table>
			</div>
		<?php } ?>

		<?php echo $this->getHookVar('payment_extensions_hook'); ?>

		<?php echo $this->getHookVar('order_attributes'); ?>

		<b style="margin-bottom: 2px; display: block;"><?php echo $text_comments; ?></b>

		<div class="content">
			<?php echo $form['comment'] ?>
			<div class="clr_both"></div>
		</div>
		<?php echo $this->getHookVar('buttons_pre'); ?>
		<?php echo $buttons; ?>
		<?php echo $this->getHookVar('buttons_post'); ?>
		</form>
	</div>
	<div class="bottom">
		<div class="left"></div>
		<div class="right"></div>
		<div class="center"></div>
	</div>
</div>
<script type="text/javascript">
	$('#change_address').click(function () {
		location = '<?php echo $change_address_href; ?>';
	});
	$('#payment_back').click(function () {
		location = '<?php echo $back; ?>';
	});
</script>
