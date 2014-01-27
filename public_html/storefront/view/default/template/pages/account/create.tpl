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
		<?php }
        echo $form['form_open'];
        ?>
		
			<p><?php echo $text_account_already; ?></p>
			<span id="main_account_info" >
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
								<?php } ?>
							<?php echo $form['subscriber']; ?>
						</td>
					</tr>
					<tr id="telephone_tr" style="<?php echo $subscriber ? 'display: none;' :'' ;?>">
						<td><?php echo $entry_telephone; ?></td>
						<td><?php echo $form['telephone']; ?>
							<?php if ($error_telephone) { ?>
								<span class="error"><?php echo $error_telephone; ?></span>
								<?php } ?></td>
					</tr>
					<tr id="fax_tr" style="<?php echo $subscriber ? 'display: none;' :'' ;?>">
						<td><?php echo $entry_fax; ?></td>
						<td><?php echo $form['fax']; ?></td>
					</tr>
				</table>
			</div>
			</span>
			<span id="address_account_info" style="<?php echo $subscriber ? 'display: none;' :'' ;?>">
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
			</span>
			<span id="password_account_info" style="<?php echo $subscriber ? 'display: none;' :'' ;?>">

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
			</span>
			<span id="subscribe_account_info" style="<?php echo $subscriber ? 'display: none;' :'' ;?>">
			<b style="margin-bottom: 2px; display: block;"><?php echo $text_newsletter; ?></b>

			<div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
				<table>
					<tr>
						<td width="150"><?php echo $entry_newsletter; ?></td>
						<td><?php echo $form['newsletter']; ?></td>
					</tr>
				</table>
			</div>
			</span>
			<div class="buttons">
				<table>
					<tr>
					<?php if($subscriber){?>
						<td align="left" style="padding-right: 5px;"><a id="form_expander"><?php echo $subscriber_switch_text_full; ?></a></td>
					<?php }
					if($text_agree){ ?>
						<td id="agree_td1" align="right" style="<?php echo $subscriber ? 'display:none; ':''?>padding-right: 5px;"><?php echo $text_agree; ?><a class="thickbox" href="<?php echo $text_agree_href; ?>"><b><?php echo $text_agree_href_text; ?></b></a></td>
						<td id="agree_td2"  width="5" style="<?php echo $subscriber ? 'display:none; ':''?>padding-right: 10px;"><?php echo $form['agree']; ?></td>
					<?php } ?>
						<td id="agree_td3"  align="right" width="5" style="<?php echo $subscriber ? 'display:none; ':''?>"><?php echo $form['continue']; ?></td>
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
<script type="text/javascript"><!--
$('#AccountFrm_country_id').change( function(){
    $('select[name=\'zone_id\']').load('index.php?rt=common/zone&country_id=' + $(this).val() + '&zone_id=<?php echo $zone_id; ?>');
});
$('select[name=\'zone_id\']').load('index.php?rt=common/zone&country_id='+ $('#AccountFrm_country_id').val() +'&zone_id=<?php echo $zone_id; ?>');

$('#form_expander').click(function(){
	var text = '<?php echo $subscriber_switch_text?>';
	var text_full = '<?php echo $subscriber_switch_text_full?>';
	var spans = ['telephone_tr','fax_tr','address_account_info','password_account_info','subscribe_account_info','agree_td1','agree_td2','agree_td3'];
	for(var k in spans){
		$('#'+spans[k]).fadeToggle();
	}

	if($('#AccountFrm_subscriber').attr('disabled')=='disabled'){
		$('#form_expander').html(text_full);
		$('#AccountFrm_subscriber').removeAttr('disabled');
	}else{
		$('#AccountFrm_subscriber').attr('disabled','disabled');
		$('#form_expander').html(text);
	}
});
//--></script>