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
		<div class="warning"><?php echo $error_warning; ?></div>
		<?php }
        echo $form['form_open'];
        ?>
		
			<p><?php echo $text_account_already; ?></p>
			<b style="margin-bottom: 2px; display: block;"><?php echo $text_your_details; ?></b>

			<div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
				<table>
					<?php if ($noemaillogin) { ?>
					<tr>
						<td width="150"><?php echo $entry_loginname; ?></td>
						<td><?php echo $form['loginname']; ?>
							<?php if ($error_loginname) { ?>
								<span class="error"><?php echo $error_loginname; ?></span>
								<?php } ?></td>
					</tr>
					<?php } ?>
					<tr>
						<td width="150"><?php echo $entry_firstname; ?></td>
						<td><?php echo $form['firstname']; ?>
							<?php if ($error_firstname) { ?>
								<span class="error"><?php echo $error_firstname; ?></span>
								<?php } ?></td>
					</tr>
					<tr>
						<td><?php echo $entry_lastname; ?></td>
						<td><?php echo $form['lastname']; ?>
							<?php if ($error_lastname) { ?>
								<span class="error"><?php echo $error_lastname; ?></span>
								<?php } ?></td>
					</tr>
					<tr>
						<td><?php echo $entry_email; ?></td>
						<td><?php echo $form['email']; ?>
							<?php if ($error_email) { ?>
								<span class="error"><?php echo $error_email; ?></span>
								<?php } ?></td>
					</tr>
					<tr>
						<td><?php echo $entry_telephone; ?></td>
						<td><?php echo $form['telephone']; ?>
							<?php if ($error_telephone) { ?>
								<span class="error"><?php echo $error_telephone; ?></span>
								<?php } ?></td>
					</tr>
					<tr>
						<td><?php echo $entry_fax; ?></td>
						<td><?php echo $form['fax']; ?></td>
					</tr>
				</table>
			</div>
			<b style="margin-bottom: 2px; display: block;"><?php echo $text_your_address; ?></b>

			<div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
				<table>
					<tr>
						<td width="150"><?php echo $entry_company; ?></td>
						<td><?php echo $form['company']; ?></td>
					</tr>
					<tr>
						<td><?php echo $entry_address_1; ?></td>
						<td><?php echo $form['address_1']; ?>
							<?php if ($error_address_1) { ?>
								<span class="error"><?php echo $error_address_1; ?></span>
								<?php } ?></td>
					</tr>
					<tr>
						<td><?php echo $entry_address_2; ?></td>
						<td><?php echo $form['address_2']; ?></td>
					</tr>
					<tr>
						<td><?php echo $entry_city; ?></td>
						<td><?php echo $form['city']; ?>
							<?php if ($error_city) { ?>
								<span class="error"><?php echo $error_city; ?></span>
								<?php } ?></td>
					</tr>
                    <tr>
                        <td><?php echo $entry_postcode; ?></td>
                        <td><?php echo $form['postcode']; ?><?php if ($error_postcode) { ?>
                            <span class="error"><?php echo $error_postcode; ?></span>
                            <?php } ?></td>
                    </tr>
					<tr>
						<td><?php echo $entry_country; ?></td>
						<td><?php echo $form['country_id']; ?></td>
					</tr>
					<tr>
						<td><?php echo $entry_zone; ?></td>
						<td><?php echo $form['zone_id']; ?>
							<?php if ($error_zone) { ?>
								<span class="error"><?php echo $error_zone; ?></span>
								<?php } ?></td>
					</tr>
				</table>
			</div>
			<b style="margin-bottom: 2px; display: block;"><?php echo $text_your_password; ?></b>

			<div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
				<table>
					<tr>
						<td width="150"><?php echo $entry_password; ?></td>
						<td><?php echo $form['password']; ?>
							<?php if ($error_password) { ?>
								<span class="error"><?php echo $error_password; ?></span>
								<?php } ?></td>
					</tr>
					<tr>
						<td><?php echo $entry_confirm; ?></td>
						<td><?php echo $form['confirm']; ?>
							<?php if ($error_confirm) { ?>
								<span class="error"><?php echo $error_confirm; ?></span>
								<?php } ?></td>
					</tr>
				</table>
			</div>

			<?php echo $this->getHookVar('customer_attributes'); ?>

			<b style="margin-bottom: 2px; display: block;"><?php echo $text_newsletter; ?></b>

			<div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
				<table>
					<tr>
						<td width="150"><?php echo $entry_newsletter; ?></td>
						<td><?php echo $form['newsletter']; ?></td>
					</tr>
				</table>
			</div>
			<?php if ($text_agree) { ?>
			<div class="buttons">
				<table>
					<tr>
						<td align="right" style="padding-right: 5px;"><?php echo $text_agree; ?><a class="thickbox" href="<?php echo $text_agree_href; ?>"><b><?php echo $text_agree_href_text; ?></b></a></td>
						<td width="5" style="padding-right: 10px;"><?php echo $form['agree']; ?></td>
						<td align="right" width="5"><?php echo $form['continue']; ?></td>
					</tr>
				</table>
			</div>
			<?php } else { ?>
			<div class="buttons">
				<table>
					<tr>
						<td align="right"><?php echo $form['continue']; ?></td>
					</tr>
				</table>
			</div>
			<?php } ?>
		</form>
	</div>
	<div class="bottom">
		<div class="left"></div>
		<div class="right"></div>
		<div class="center"></div>
	</div>
</div>
<script type="text/javascript"><!--
$('#AccountFrm_country_id').change( function(){
    $('select[name=\'zone_id\']').load('index.php?rt=common/zone&country_id=' + $(this).val() + '&zone_id=<?php echo $zone_id; ?>');
});
$('select[name=\'zone_id\']').load('index.php?rt=common/zone&country_id='+ $('#AccountFrm_country_id').val() +'&zone_id=<?php echo $zone_id; ?>');
//--></script>