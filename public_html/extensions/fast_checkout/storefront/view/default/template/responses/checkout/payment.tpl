<?php
$guest_data = $this->session->data['guest'];
?>

<div id="pay_error_container">
    <?php if ($info) { ?>
		<div class="info alert alert-info"><i class="fa fa fa-check fa-fw"></i> <?php echo $info; ?></div>
    <?php } ?>
    <?php if ($error) { ?>
		<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation fa-fw"></i> <?php echo $error; ?>
		</div>
    <?php } ?>
</div>

<fieldset>
	<form id="<?php echo $pay_form['form_open']->name; ?>"
		  action="<?php echo $pay_form['form_open']->action; ?>"
		  class="validate-creditcard"
		  method="post">

        <?php echo $this->getHookVar('payment_form_top'); ?>
        <?php
        if (count($all_addresses) == 1) {
            $readonly = ' readonly ';
        }

        //do we show payment details yet? Show only if shipping selected
        if ($show_payment == false) {
            ?>
			<div class="row">
				<div class="form-group col-xxs-12 text-center">
					<label class="h5 text-uppercase"><?php echo $fast_checkout_text_select_delivery; ?></label>
				</div>
			</div>
            <?php
        }
        ?>

		<div class="row">
        <?php if ($this->cart->hasShipping()) { ?>
			<div class="form-group <?php if ($show_payment) {
                echo "col-xxs-12 col-xs-6";
            } ?>">
                <?php if ($guest_data['shipping']) {
                $address = $this->customer->getFormattedAddress(
                        $guest_data['shipping'],
                        $guest_data['shipping']['address_format']
                );
?>
				<div class="left-inner-addon">
					<i class="fa fa-home" id="delivery_icon"></i>
					<a href="<?php echo $edit_address_url; ?>&type=shipping" class="address_edit"><i
								class="fa fa-edit"></i></a>
					<div class="well">
						<b><?php echo $fast_checkout_text_shipping_address; ?>:</b> <br/>
                        <?php echo $address; ?>
					</div>
                    <?php } else { ?>
					<div class="shipping_address_label"><?php echo $fast_checkout_text_shipping_address; ?>:</div>
					<div class="left-inner-addon">
						<i class="fa fa-home" id="delivery_icon"></i>
						<select data-placeholder="" class="form-control input-lg" id="shipping_address_id"
								name="shipping_address_id" <?php echo $readonly; ?>>
							<option disabled><?php echo $fast_checkout_text_shipping_address; ?>:</option>
							<option disabled></option>
                            <?php
                            if (count($all_addresses)) {
                                foreach ($all_addresses as $addr) {
                                    $current = '';
                                    if ($addr['address_id'] == $csession['shipping_address_id']) {
                                        $current = ' selected ';
                                    }
                                    $address = $this->customer->getFormattedAddress($addr, $addr['address_format']);
                                    $lines = explode("<br />", $address);
                                    echo '<option value="'.$addr['address_id'].'" '.$current.'>'.$lines[0].', '
                                        .$lines[1].'...</option>';
                                    for ($i = 0; $i <= count($lines); $i++) {
                                        echo '<option disabled>&nbsp;&nbsp;&nbsp;'.$lines[$i].'</option>';
                                    }
                                }
                            }
                            ?>
						</select>
						<div class="select_arrow"><i class="fa fa-angle-double-down"></i></div>
                        <?php } ?>
					</div>
                    <?php
                    if (count($all_addresses)) { ?>
						<div class="shipping_address_details"></div>
                        <?php
                    } ?>

				</div>

                <?php
                $readonly = '';
                if (count($csession['shipping_methods']) == 1) {
                    $readonly = ' readonly ';
                }
                ?>

                <?php } //eof if product has shipping

                if ($show_payment == true) {
                    if ($need_payment_address) { ?>
						<div class="form-group col-xxs-12 <?php if ($this->cart->hasShipping()) { ?>col-xs-6 <?php } ?>">
                            <?php if ($guest_data) {
                            $address = $this->customer->getFormattedAddress($guest_data, $guest_data['address_format']);
                            ?>
							<div class="left-inner-addon">
								<i class="fa fa-bank"></i>
								<a href="<?php echo $edit_address_url; ?>&type=payment" class="address_edit" id="payment_address_edit"><i
											class="fa fa-edit"></i></a>
								<div class="well">
									<b><?php echo $fast_checkout_text_payment_address; ?>:</b> <br/>
                                    <?php echo $address; ?>
								</div>
                                <?php } else { ?>
								<div class="payment_address_label"><?php echo $fast_checkout_text_payment_address; ?>:</div>
								<div class="left-inner-addon">
									<i class="fa fa-bank"></i>
									<select data-placeholder="" class="form-control input-lg" id="payment_address_id"
											name="payment_address_id" <?php echo $readonly; ?>>
										<option disabled><?php echo $fast_checkout_text_payment_address; ?>:</option>
										<option disabled></option>
                                        <?php
                                        if (count($all_addresses)) {
                                            foreach ($all_addresses as $addr) {
                                                $current = '';
                                                if ($addr['address_id'] == $csession['payment_address_id']) {
                                                    $current = ' selected ';
                                                }
                                                $address = $this->customer->getFormattedAddress($addr, $addr['address_format']);
                                                $lines = explode("<br />", $address);
                                                echo '<option value="'.$addr['address_id'].'" '.$current.'>'.$lines[0].', '
                                                    .$lines[1].'...</option>';
                                                for ($i = 0; $i <= count($lines); $i++) {
                                                    echo '<option disabled>&nbsp;&nbsp;&nbsp;'.$lines[$i].'</option>';
                                                }
                                            }
                                        }
                                        ?>
									</select>
									<div class="select_arrow"><i class="fa fa-angle-double-down"></i></div>
                                    <?php } ?>
								</div>
                                <?php
                                if (count($all_addresses)) { ?>
									<div class="payment_address_details"></div>
                                    <?php
                                } ?>
								<input name="cc_owner" type="hidden" value="<?php echo $customer_name; ?>">
							</div>
						</div>
                    <?php } else { ?>
						<div class="row">
							<div class="form-group col-xxs-12">
								<div class="left-inner-addon">
									<i class="fa fa-user"></i>
									<input class="form-control input-lg" placeholder="Your Name" name="cc_owner" type="text"
										   value="<?php echo $customer_name; ?>">
								</div>
							</div>
						</div>
                    <?php }
                } ?>

                <?php if ($this->cart->hasShipping() && count($csession['shipping_methods']) === 0) { ?>
					<div class="alert alert-danger" role="alert">
                        <?php echo $this->language->get('fast_checkout_no_shipments_available'); ?>
					</div>
                    <?php
                    $payment_available = false;
                } ?>

                <?php
                if ($this->cart->hasShipping() && count($csession['shipping_methods']) > 0) {
                    $readonly = '';
                    if (count($csession['shipping_methods']) == 1) {
                        $readonly = ' readonly ';
                    }
                    ?>
					<div class="row">
						<div class="registerbox">
							<table class="table table-striped table-shipments">
                                <?php
                                foreach ($csession['shipping_methods'] as $shipping_method) { ?>
									<tr>
										<td colspan="3"><b><?php echo $shipping_method['title']; ?></b></td>
									</tr>
                                    <?php if (!$shipping_method['error']) { ?>
                                        <?php foreach ($shipping_method['quote'] as $quote) { ?>
											<tr>
												<td style="width: 5%; text-align: center; vertical-align: middle;"><?php echo $quote['radio']; ?></td>
												<td style="vertical-align: middle;">
													<label for="<?php
                                                    $idd = str_replace('.', '', $quote['id']);
                                                    echo preg_replace('/[^a-zA-Z0-9\.-_]/', '', $idd.$quote['id']); ?>"
														   title="<?php echo has_value($quote['description']) ? $quote['description'] : $quote['title']; ?>"
														   style="cursor: pointer;">
                                                        <?php $icon = (array)$shipping_method['icon'];
                                                        if (sizeof($icon)) { ?>
                                                            <?php if (is_file(DIR_RESOURCE.$icon['image'])) { ?>
																<span class="shipping_icon mr10"><img style="width:<?php echo $this->config->get('config_image_grid_width'); ?>px; height:auto;"
																									  src="resources/<?php echo $icon['image']; ?>"
																									  title="<?php echo $icon['title']; ?>"/></span>
                                                            <?php } else {
                                                                if (!empty($icon['resource_code'])) { ?>
																	<span class="shipping_icon mr10"><?php echo $icon['resource_code']; ?></span>
                                                                <?php }
                                                            }
                                                        } ?>
                                                        <?php echo $quote['title']; ?>
													</label>
												</td>
												<td style="vertical-align: middle;" class="align_right"><label for="<?php echo $quote['radio']->element_id.$quote['radio']->id; ?>"
																											   style="cursor: pointer;"><?php echo $quote['text']; ?></label>
												</td>
											</tr>
                                        <?php } ?>
                                    <?php } else { ?>
										<tr>
											<td colspan="3">
												<div class="alert alert-danger"><i
															class="fa fa-exclamation"></i> <?php echo $shipping_method['error']; ?>
												</div>
											</td>
										</tr>
                                    <?php } ?>
                                <?php } ?>
							</table>
						</div>
					</div>
                <?php } ?>
                <?php
                //if not all required fields are selected, do not show payment fields
                if ($show_payment == true) {
                ?>

				<div class="row">
					<div class="form-group col-xxs-12">
						<div class="left-inner-addon">
							<i class="fa fa-envelope"></i>
							<div class="input-group">
								<input class="form-control input-lg"
									   placeholder="Your Email"
									   id="cc_email"
									   name="cc_email"
									   type="text"
									   value="<?php echo $customer_email; ?>"
									   readonly>
								<span class="input-group-btn">
									<button class="btn btn-default btn-lg btn-edit-email" type="button">
										<i class="fa fa-edit fa-fw"></i>
									</button>
							</span>
							</div>
						</div>
					</div>
				</div>

                <?php if ($require_telephone) { ?>
					<div class="row">
						<div class="form-group col-xxs-12">
							<div class="left-inner-addon">
								<i class="fa fa-phone"></i>
                                <div class="input-group">
                                    <input id="telephone"
                                           class="form-control input-lg"
                                           placeholder="<?php echo $fast_checkout_text_telephone_placeholder; ?>"
                                           name="telephone"
                                           type="text"
                                           value="<?php echo $customer_telephone; ?>"
                                           readonly>
                                    <span class="input-group-btn">
                                        <button class="btn btn-default btn-lg btn-edit-email" type="button">
                                            <i class="fa fa-edit fa-fw"></i>
                                        </button>
                                    </span>
                                </div>
							</div>
						</div>
					</div>
                <?php } ?>
                <?php echo $this->getHookVar('payment_form_fields'); ?>

                <?php if ($this->config->get('fast_checkout_show_order_comment_field')) { ?>
					<div class="row">
						<div class="form-group col-xxs-12" title="<?php echo $fast_checkout_text_order_comment; ?>">
							<div class="left-inner-addon">
								<i class="fa fa-comment"></i>
                                <div class="input-group">
                                    <textarea id="comment"
                                              class="form-control input-lg p"
                                              name="comment"
                                              placeholder="<?php echo $fast_checkout_text_telephone_placeholder; ?>"
                                    ><?php echo $comment; ?></textarea>
                                    <span class="input-group-btn">
                                       <button class="btn btn-default btn-lg btn-comment" type="button">
                                       <i class="fa fa-check fa-fw"></i>
                                       <span class="hidden-xxs"><?php echo $fast_checkout_text_apply; ?></span>
                                      </button>
                                    </span>
                                </div>
							</div>
						</div>
					</div>
                <?php } ?>

                <?php if ($enabled_coupon) { ?>
					<div class="row">
						<div class="form-group col-xxs-12">
							<div class="left-inner-addon">
								<i class="fa fa-ticket"></i>
								<div class="input-group">
									<input id="coupon_code"
										   class="form-control input-lg"
										   placeholder="<?php echo $fast_checkout_text_coupon_code; ?>"
										   name="coupon_code"
										   type="text"
										   value="<?php echo $csession['coupon']; ?>"
                                        <?php if ($csession['coupon']) {
                                            echo "disabled";
                                        } ?>
									>
									<span class="input-group-btn">
						<?php if ($csession['coupon']) { ?>
							<button class="btn btn-default btn-lg btn-remove-coupon" type="button">
							<i class="fa fa-trash fa-fw"></i> <span
										class="hidden-xxs"><?php echo $fast_checkout_text_remove; ?></span>
						  </button>
                        <?php } else { ?>
							<button class="btn btn-default btn-lg btn-coupon" type="button">
							<i class="fa fa-check fa-fw"></i> <span
										class="hidden-xxs"><?php echo $fast_checkout_text_apply; ?></span>
						  </button>
                        <?php } ?>
						</span>
								</div>
							</div>
						</div>
					</div>
                <?php } ?>

                <?php if ($allow_account_creation || $support_recurring_billing) { ?>
					<div class="row">
						<div class="form-group col-xxs-4 col-xs-4">
						</div>
                        <?php if ($support_recurring_billing) { ?>
							<div class="form-group col-xxs-8 col-xs-4 pull-right">
								<div class="input-group pull-right">
            <span class="button-checkbox">
                <button type="button" class="btn"
						data-color="primary"> <?php echo $fast_checkout_text_bill_me_monthly; ?></button>
                <input type="checkbox" name="cc_bill_monthly" class="hidden"/>
            </span>
								</div>
							</div>
                        <?php } ?>
                        <?php if ($allow_account_creation) { ?>
							<div class="form-group col-xxs-8 col-xs-4 pull-right">
								<div class="input-group pull-right">
            <span class="button-checkbox">
                <button type="button" class="btn"
						data-color="primary"> <?php echo $fast_checkout_text_create_account; ?></button>
                <input type="checkbox" name="create_account" class="hidden" checked="checked"/>
            </span>
								</div>
							</div>
                        <?php } ?>
					</div>
                <?php } ?>
                <?php echo $this->getHookVar('payment_form_bottom'); ?>
	</form>
<?php
    if ($payment_available === true) {
        ?>
        <div class="payment-select-container">
            <div class="div-cover"></div>
            <?php include($this->templateResource('/template/responses/checkout/payment_select.tpl')) ?>
            <?php
        } else { ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $this->language->get('fast_checkout_error_no_payment'); ?>
            </div>
        <?php } ?>
            <div id="returnPolicyModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="returnPolicyModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h3 id="returnPolicyModalLabel"><?php echo $text_accept_agree_href_link; ?></h3>
                        </div>
                        <div class="modal-body">
                        </div>
                        <div class="modal-footer">
                            <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo $text_close; ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="align_right"><?php echo $text_accept_agree ?>&nbsp;
                <a onclick="openModalRemote('#returnPolicyModal', '<?php echo $text_accept_agree_href; ?>'); return false;"
                   href="<?php echo $text_accept_agree_href; ?>"><b><?php echo $text_accept_agree_href_link; ?></b></a>
            </div>
        </div>
	<?php } ?>
</fieldset>

<script type="text/javascript">
	getUrlParams = function (key, value) {
		let searchParams = new URLSearchParams(window.location.search);
		//Remove old value
		if (searchParams.has('cart_key')) {
			searchParams.delete('cart_key')
		}
		if (searchParams.has('rt')) {
			searchParams.delete('rt')
		}
		if (searchParams.has('coupon_code')) {
			searchParams.delete('coupon_code')
		}
		if (searchParams.has('remove_coupon')) {
			searchParams.delete('remove_coupon')
		}

		//Set New Value
		if (searchParams.has(key)) {
			searchParams.set(key, value)
		} else {
			searchParams.append(key, value)
		}
		return searchParams.toString()
	};

	jQuery(document).ready(function () {

		$(".btn-comment").on('click', function () {
		    var that = $(this).closest('.form-group');
            $.ajax({
                type: "POST",
                url: '<?php echo $this->html->getSecureUrl('r/checkout/pay/updateOrderData'); ?>',
                data: {comment: $('textarea[name=comment]').val()},
                success: function(){
                    that
                        .removeClass('has-error')
                        .removeClass('has-success')
                        .addClass('has-success');
                },
                error: function(){
                    that
                        .removeClass('has-error')
                        .removeClass('has-success')
                        .addClass('has-error');
                }
            });

		});

		$("#payment_address_id").change(function () {
			let url = '<?php echo $main_url ?>&' + getUrlParams('payment_address_id', $(this).val());
			pageRequest(url);
		});

		$("#shipping_address_id").change(function () {
			let url = '<?php echo $main_url ?>&' + getUrlParams('shipping_address_id', $(this).val());
			pageRequest(url);
		});

		$("#shipping_method").change(function () {
			let url = '<?php echo $main_url ?>&' + getUrlParams('shipping_method', $(this).val());
            pageRequest(url);
		});

		$(".registerbox input[type='radio']").change(function () {
			let url = '<?php echo $main_url ?>&' + getUrlParams('shipping_method', $(this).val());
			if ($('#PayFrm').serialize()) {
				url = '<?php echo $main_url ?>&' + $('#PayFrm').serialize()
			}
			pageRequest(url);
		});

		$("#coupon_code").on('keyup', function (e) {
			if (e.keyCode === 13) {
				$(".btn-coupon").click()
			}
		});


		$(".pay-form").on("click", ".btn-coupon", function () {
			var $input = $(this).closest('.input-group').find('input');
			var coupon = $input.val().replace(/\s+/g, '');
			if (!coupon) {
				$.aCCValidator.show_error($(this), '.form-group');
				return false;
			}
			//let url = '<?php echo $main_url ?>&' + getUrlParams('coupon_code', coupon);
			let url = '<?php echo $main_url ?>&' + $('#PayFrm').serialize();
			pageRequest(url);
		});

		$(".pay-form").on("click", ".btn-remove-coupon", function () {
			//let url = '<?php echo $main_url ?>&' + getUrlParams('remove_coupon', true);
			let url = '<?php echo $main_url ?>&' + $('#PayFrm').serialize() + '&remove_coupon=true';
			pageRequest(url);
		});

		$(".pay-form").on("click", ".btn-apply-balance", function () {
			let url = '<?php echo $main_url ?>&'+ $('#PayFrm').serialize()+ '&' + getUrlParams('balance', 'apply');
			pageRequest(url);
		});

		$(".pay-form").on("click", ".btn-remove-balance", function () {
			let url = '<?php echo $main_url ?>&'+ $('#PayFrm').serialize()+ '&' + getUrlParams('balance', 'disapply');
			pageRequest(url);
		});


		$(".pay-form").on("click", ".payment-option", function () {
			if ($(this).hasClass('selected')) {
				return;
			}
			var payment_id = $(this).data('payment-id');
			const paymentAvailable = $(this).attr('data-payment-available');
			if (payment_id == 'account_balance' || paymentAvailable == 'false') {
				return;
			}
			//let url = '<?php echo $main_url ?>&' + getUrlParams('payment_method', payment_id);
			let url = '<?php echo $main_url ?>&' + $('#PayFrm').serialize() + '&payment_method=' + payment_id;
			//pageRequest(url);
			var form = $('#PayFrm');
			$('#payment_details').remove();
			$('form').unbind("submit");
			form.attr('action', url);
			$('.spinner-overlay').fadeIn(100);
			$.ajax({
				url: url,
				type: 'GET',
				dataType: 'html',
				success: function (data) {
					$('#fast_checkout_summary_block').trigger('reload');
					$('#fast_checkout_cart').hide().html(data).fadeIn(1000);
					$('.spinner-overlay').fadeOut(100);
				}
			});
		});

		//load first tab
        <?php if ($payment_available === true){ ?>
		$('#credit_card').html($('#hidden_credit_card').html());
        <?php } else { ?>
		$('#account_credit').html($('#hidden_account_credit').html());
        <?php } ?>
		$("a[href='#credit_card']").on('shown.bs.tab', function (e) {
			$('#account_credit').html('');
			$('#credit_card').html($('#hidden_credit_card').html());
		});

		$("a[href='#account_credit']").on('shown.bs.tab', function (e) {
			$('#credit_card').html('');
			$('#account_credit').html($('#hidden_account_credit').html());
		});

		$('form.validate-creditcard [name=telephone]').bind({
			change: function () {
				//check as telephone is entered
				if (validatePhone($(this).val())) {
					$.aCCValidator.show_success($(this), '.form-group');
				} else {
					$.aCCValidator.show_error($(this), '.form-group');
				}
			},
			blur: function () {
				//check full number as lost focus
				if (validatePhone($(this).val())) {
					$.aCCValidator.show_success($(this), '.form-group');
				} else {
					$.aCCValidator.show_error($(this), '.form-group');
				}
			}
		});

		$('form.validate-creditcard [name=cc_email]').bind({
			change: function () {
				//check as email is entered
				if (validateEmail($(this).val())) {
					$.aCCValidator.show_success($(this), '.form-group');
				} else {
					$.aCCValidator.show_error($(this), '.form-group');
				}
			},
			blur: function () {
				//check full number as lost focus
				if (validateEmail($(this).val())) {
					$.aCCValidator.show_success($(this), '.form-group');
				} else {
					$.aCCValidator.show_error($(this), '.form-group');
				}
			}
		});

		$('form.validate-creditcard [name=cc_number]').bind({
			change: function () {
				//check as number is entered
				$.aCCValidator.precheckCCNumber($(this));
			},
			blur: function () {
				//check full number as lost focus
				$.aCCValidator.checkCCNumber($(this));
			}
		});

		$('form.validate-creditcard [name=cc_owner]').bind({
			change: function () {
				$.aCCValidator.checkCCName($(this), 'reset');
			},
			blur: function () {
				$.aCCValidator.checkCCName($(this));
			}
		});

		$('form.validate-creditcard [name=cc_expire_date_month]').bind({
			change: function () {
				$.aCCValidator.checkExp($(this), 'reset');
			},
			blur: function () {
				$.aCCValidator.checkExp($(this));
			}
		});

		$('form.validate-creditcard [name=cc_expire_date_year]').bind({
			change: function () {
				$.aCCValidator.checkExp($(this), 'reset');
			},
			blur: function () {
				$.aCCValidator.checkExp($(this));
			}
		});

		$('form.validate-creditcard [name=cc_cvv2]').bind({
			change: function () {
				$.aCCValidator.checkCVV($(this), 'reset');
			},
			blur: function () {
				$.aCCValidator.checkCVV($(this));
			}
		});

		getAddressHtml = function (address) {
			let html = '';
			if (typeof address != "undefined") {
				if (address.firstname || address.lasttname) {
					html += address.firstname + ' ' + address.lastname + ' <br/>'
				}
				if (address.company) {
					html += address.company + ' <br/>'
				}
				if (address.address_2) {
					html += address.address_2 + ' <br/>'
				}
				if (address.address_1) {
					html += address.address_1 + ' <br/>'
				}
				if (address.city || address.postcode) {
					html += address.city + ' ' + address.postcode + ' <br/>'
				}
				if (address.zone) {
					html += address.zone + ' <br/>'
				}
				if (address.country) {
					html += address.country
				}
                <?php if ($address_edit_base_url) { ?>
				html += '<div class="address_edit_link"><a href="<?php echo $address_edit_base_url; ?>' + address.address_id + '"><i class="fa fa-edit"></i></a></div>'
                <?php } ?>
			}
			return html
		};

		updateShippingAddressDisplay = function () {
			let addresses = JSON.parse(atob('<?php echo base64_encode(json_encode($all_addresses)); ?>'))
			let shipping_address_id = $("#shipping_address_id").val();
			let address = addresses.find((el) => el.address_id == shipping_address_id);

			if (typeof address != "undefined") {
				$('.shipping_address_details').hide().html(getAddressHtml(address)).fadeIn(1000);
			}
		};

		updatePaymentAddressDisplay = function () {
			let addresses = JSON.parse(atob('<?php echo base64_encode(json_encode($all_addresses)); ?>'));
			let payment_address_id = $("#payment_address_id").val();
			let address = addresses.find((el) => el.address_id == payment_address_id);

			if (typeof address != "undefined") {
				$('.payment_address_details').hide().html(getAddressHtml(address)).fadeIn(1000);
			}
		};

		updateShippingAddressDisplay();
		updatePaymentAddressDisplay();

		$('.btn-edit-email').on('click', function () {
            <?php if ($this->customer && $this->customer->getId()) { ?>
			location.replace('<?php echo $this->html->getSecureUrl("account/edit");?>');
            <?php } else { ?>
			$('#payment_address_edit').click();
            <?php } ?>
		});

		$('#no_payment_confirm').on('click', function (e) {
			$('#PayFrm').submit();
		});

	});

</script>
