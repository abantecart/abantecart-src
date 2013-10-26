<div id="content">
	<div class="top">
		<div class="left"></div>
		<div class="right"></div>
		<div class="center">
			<h1><?php echo $heading_title; ?></h1>
		</div>
	</div>
	<div class="middle">
		<?php if ($error_warning) { ?>
		<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php	}
		echo $form[ 'form_open' ];?>
		<b style="margin-bottom: 2px; display: block;"><?php echo $text_your_details; ?></b>

		<div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
			<table>
				<tr>
					<td width="150"><?php echo $entry_loginname; ?></td>
					<td>
						<?php echo $form[ 'loginname' ]; ?>
						<?php if ($error_loginname) { ?>
						<span class="error"><?php echo $error_loginname; ?></span>
						<?php }?>
					</td>
				</tr>
				<tr>
					<td width="150"><?php echo $entry_firstname; ?></td>
					<td><?php echo $form[ 'firstname' ]; ?>
						<?php if ($error_firstname) { ?>
							<span class="error"><?php echo $error_firstname; ?></span>
							<?php } ?></td>
				</tr>
				<tr>
					<td><?php echo $entry_lastname; ?></td>
					<td><?php echo $form[ 'lastname' ]; ?>
						<?php if ($error_lastname) { ?>
							<span class="error"><?php echo $error_lastname; ?></span>
							<?php } ?></td>
				</tr>
				<tr>
					<td><?php echo $entry_email; ?></td>
					<td><?php echo $form[ 'email' ]; ?>
						<?php if ($error_email) { ?>
							<span class="error"><?php echo $error_email; ?></span>
							<?php } ?></td>
				</tr>
				<tr>
					<td><?php echo $entry_telephone; ?></td>
					<td><?php echo $form[ 'telephone' ]; ?>
						<?php if ($error_telephone) { ?>
							<span class="error"><?php echo $error_telephone; ?></span>
							<?php } ?></td>
				</tr>
				<tr>
					<td><?php echo $entry_fax; ?></td>
					<td><?php echo $form[ 'fax' ]; ?></td>
				</tr>
			</table>
		</div>

		<?php echo $this->getHookVar('customer_attributes'); ?>
		
		<div class="buttons">
			<table>
				<tr>
					<td align="left"><?php echo $form[ 'back' ]; ?></td>
					<td align="right"><?php echo $form[ 'continue' ]; ?></td>
				</tr>
			</table>
		</div>
		</form>
	</div>
	<div class="bottom">
		<div class="left"></div>
		<div class="right"></div>
		<div class="center"></div>
	</div>
</div>
<script type="text/javascript">
	$('#AccountFrm_back').click(function() {
		location = '<?php echo $back; ?>';
	});
</script>