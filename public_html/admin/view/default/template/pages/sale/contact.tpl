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
				<div class="heading icon_title_mail"><?php echo $heading_title; ?></div>
				<div class="toolbar">
					<?php if (!empty ($help_url)) : ?>
					<div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img
							src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<div class="cbox_cl">
			<div class="cbox_cr">
				<div class="cbox_cc">
					<div class="fieldset">
						<div class="heading"><?php echo $form_title; ?></div>
						<div class="top_left">
							<div class="top_right">
								<div class="top_mid"></div>
							</div>
						</div>
						<div class="cont_left">
							<div class="cont_right">
								<div class="cont_mid">
									<?php echo $form_open; ?>
										<table class="form">
											<tr>
												<td><?php echo $entry_store; ?></td>
												<td><?php echo $form[ 'store' ]; ?></td>
											</tr>
											<tr>
												<td><?php echo $entry_to; ?></td>
												<td><?php echo $form[ 'recipient' ]; ?></td>
											</tr>
											<tr>
												<td></td>
												<td>
													<table  id="mail_personaly">
														<tr>
															<td colspan="3">
																<div class="flt_left"><?php echo $form[ 'search' ]; ?>
																	&nbsp;</div>
																<a onclick="getCustomers();"
																   class="button flt_left"><span><?php echo $text_search; ?></span></a>
																<br/><br/>
															</td>
														</tr>
														<tr>
															<td><select multiple="multiple" id="customer" size="10"
															            style="width: 350px; margin-bottom: 3px;">
															</select></td>
															<td style="text-align: center; vertical-align: middle;">
																<input type="button" value="--&gt;"
																       onclick="addCustomer();"/>
																<br/>
																<input type="button" value="&lt;--"
																       onclick="removeCustomer();"/></td>
															<td><select multiple="multiple" id="to" size="10"
															            style="width: 350px; margin-bottom: 3px;">
																<?php foreach ($customers as $customer) { ?>
																<option value="<?php echo $customer[ 'customer_id' ]; ?>"><?php echo $customer[ 'name' ]; ?></option>
																<?php } ?>
															</select></td>
														</tr>
													</table>
													<?php if ($error_recipient) { ?>
														<span class="error"><?php echo $error_recipient; ?></span>
													<?php } ?>													
												</td>
											</tr>
											<tr>
												<td><?php echo $entry_product; ?></td>
												<td>
													<table>
														<tr>
															<td colspan="3">
																<?php echo $form['category']; ?>
																<br/><br/>
															</td>
														</tr>
														<tr>
															<td style="padding: 0;"><select multiple="multiple"
															                                id="product" size="10"
															                                style="width: 350px;">
															</select></td>
															<td style="vertical-align: middle;">
																<input type="button" value="--&gt;" onclick="addItem();"/>
																<br/>
																<input type="button" value="&lt;--" onclick="removeItem();"/></td>
															<td style="padding: 0;"><select multiple="multiple"
															                                id="item" size="10"
															                                style="width: 350px;">
															</select></td>
														</tr>
													</table>
													<div id="product_item">
													</div>
												</td>
											</tr>
											<tr>
												<td><span class="required">*</span> <?php echo $entry_subject; ?></td>
												<td class="ml_field"><?php echo $form[ 'subject' ]; ?>
													<?php if ($error_subject) { ?>
														<span class="error"><?php echo $error_subject; ?></span>
														<?php } ?></td>
											</tr>
											<tr>
												<td><span class="required">*</span> <?php echo $entry_message; ?></td>
												<td><textarea name="message"
												              id="message"><?php echo $message; ?></textarea>
													<?php if ($error_message) { ?>
														<span class="error"><?php echo $error_message; ?></span>
														<?php } ?></td>
											</tr>
										</table>
										<div id="customer_to">
											<?php foreach ($customers as $customer) { ?>
											<input type="hidden" name="to[]"
											       value="<?php echo $customer[ 'customer_id' ]; ?>"/>
											<?php } ?>
										</div>
								</div>
							</div>
						</div>
						<div class="bottom_left">
							<div class="bottom_right">
								<div class="bottom_mid"></div>
							</div>
						</div>
					</div>
					<!-- <div class="fieldset"> -->
					<div class="buttons align_center">
						<a class="btn_standard" href="<?php echo $cancel; ?>"><?php echo $form[ 'cancel' ]; ?></a>
						<button type="submit" class="btn_standard button_loader"><?php echo $form[ 'submit' ]; ?></button>
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
	<script type="text/javascript"><!--
	$('#mail_form_recipient').change(function(){
		if($(this).val()=='' || $(this).val()=='FALSE'){
			$('#mail_personaly').fadeIn(500);
		}else{
			$('#mail_personaly').fadeOut(500);
		}
	});
	if($('#mail_form_recipient').val()){
		$('#mail_form_recipient').change();
	}
	var addCustomer = function () {
		$('#customer :selected').each(function() {
			$(this).remove();
			$('#to option[value=\'' + $(this).attr('value') + '\']').remove();
			$('#to').append('<option value="' + $(this).attr('value') + '">' + $(this).text() + '</option>');
			$('#customer_to').append('<input type="hidden" name="to[]" value="' + $(this).attr('value') + '" />');
		});
	}
	$.fn.addCustomer = addCustomer; // for chain call after ajax call

	function removeCustomer() {
		$('#to :selected').each(function() {
			$(this).remove();
			$('#customer_to input[value=\'' + $(this).attr('value') + '\']').remove();
		});
	}

	function getCustomers(emails) {
		$('#customer option').remove();
		var url = '<?php echo $customers_list; ?>';
		if(emails && emails.length>0){
			for(var k in emails){
				url += '&email[]=' + emails[k];
			}
		}else{
			url += '&keyword=' + encodeURIComponent($('#mail_form_search').attr('value'));
		}
		$.ajax({
			url: url,
			dataType: 'json',
			success: function(data) {
				for (i = 0; i < data.length; i++) {
					$('#customer').append('<option value="' + data[i]['customer_id'] + '">' + data[i]['name'] + '</option>');
				}
				if(emails && emails.length>0){
					$('#customer option').attr('selected', 'selected').addCustomer();
				}
			}
		});
	}

	function addItem() {
		$('#product :selected').each(function() {
			$(this).remove();
			$('#item option[value=\'' + $(this).attr('value') + '\']').remove();
			$('#item').append('<option value="' + $(this).attr('value') + '">' + $(this).text() + '</option>');
			$('#product_item input[value=\'' + $(this).attr('value') + '\']').remove();
			$('#product_item').append('<input type="hidden" name="product[]" value="' + $(this).attr('value') + '" />');
		});
	}

	function removeItem() {
		$('#item :selected').each(function() {
			$(this).remove();
			$('#product_item input[value=\'' + $(this).attr('value') + '\']').remove();
		});
	}

	function getProducts() {
		$('#product option').remove();
		$.ajax({
			url: '<?php echo $category_products; ?>&category_id=' + $('#mail_form_category_id').attr('value'),
			dataType: 'json',
			success: function(data) {
				for (i = 0; i < data.length; i++) {
					$('#product').append('<option value="' + data[i]['product_id'] + '">' + data[i]['name'] + ' (' + data[i]['model'] + ') </option>');
				}
			}
		});
	}

	getProducts();

	jQuery(function($){
		$("input, select, .scrollbox", '#form').aform({
			triggerChanged: false
		});
		$.aform.styleGridForm('#customer');
		$.aform.styleGridForm('#to');
		$.aform.styleGridForm('#product');
		$.aform.styleGridForm('#item');
	})

	$('#mail_form_category_id').change(function(){
		getProducts();
	});

	//load customers to selectbox by gival email address list
	$(document).ready(function(){
		<?php if($emails){?>
		var emails = [<?php echo  "'".implode("', '",$emails) ."'"?>];
		getCustomers(emails);

		<?php } ?>
	});

--></script>


<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
	$('#message').parents('.afield').removeClass('mask2');
	CKEDITOR.replace('message', {   filebrowserBrowseUrl : false,
									filebrowserImageBrowseUrl : '<?php echo $rl; ?>',
									filebrowserWindowWidth : '920',
									filebrowserWindowHeight : '520',
									language: '<?php echo $language_code; ?>'
	});
</script>