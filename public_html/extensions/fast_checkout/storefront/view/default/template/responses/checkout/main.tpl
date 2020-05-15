<?php echo $head; ?>
<?php echo $header; ?>
    <div class="pay-form">
        <div class="text-center">
            <?php if ($loggedin !== true) { ?>
            <div class="btn-group mb10">
                <?php
                if ($action == 'login') {
                    $login_button_style = 'btn-primary';
                    $pay_button_style = 'btn-default';
                } else {
                    $login_button_style = 'btn-default';
                    $pay_button_style = 'btn-primary';
                }
                ?>
                    <?php if ($step == 'address' && ($loggedin === true || $this->config->get('config_guest_checkout'))) { ?>
                        <a href="#address" id="new_address" role="tab" data-toggle="tab"
                           class="big btn <?php echo $pay_button_style; ?>">
                            <i class="fa fa-map fa-fw"></i>&nbsp;
                            <span class="hidden-xxs"><?php echo $type.' '.$fast_checkout_text_address; ?></span>
                        </a>
                    <?php }
                    if ($step == 'payment' && $this->config->get('config_guest_checkout')) { ?>
                        <a href="#new" id="new_user" role="tab" data-toggle="tab"
                           class="big btn <?php echo $pay_button_style; ?>">
                            <i class="fa fa-user-plus fa-fw"></i>&nbsp;<span
                                    class="hidden-xxs"><?php echo $fast_checkout_text_new_customer; ?></span>
                        </a>
                    <?php } ?>
                    <a href="#user" id="login_user" role="tab" data-toggle="tab"
                       class="big btn <?php echo $login_button_style; ?>">
                        <i class="fa fa-user fa-fw"></i>&nbsp;<span
                                class="hidden-xxs"><?php echo $fast_checkout_text_login; ?></span>
                    </a>
            </div>
            <?php } ?>
        </div>
        <div class="col-xxs-12" >
            <div class="tab-content">
                <?php if ($step == 'address' && ($loggedin === true || $this->config->get('config_guest_checkout'))) { ?>
                    <div class="tab-pane fade in <?php if (!$action || $action == 'enter') {
                        echo 'active';
                    } ?>" id="address">
                        <?php include($this->templateResource('/template/responses/checkout/address.tpl')) ?>
                    </div>
                <?php }
                if ($step == 'payment' && ($loggedin === true || $this->config->get('config_guest_checkout'))) { ?>

                    <div class="tab-pane fade in <?php if (!$action || $action == 'payment') {
                        echo 'active';
                    } ?>" id="new">
                        <?php include($this->templateResource('/template/responses/checkout/payment.tpl')) ?>
                    </div>
                <?php }
                if ($step == 'confirm' && ($loggedin === true || $this->config->get('config_guest_checkout'))) { ?>
                    <div class="tab-pane fade in <?php if (!$action || $action == 'confirm') {
                        echo 'active';
                    } ?>" id="new">
                        <?php include($this->templateResource('/template/responses/checkout/payment_form.tpl')) ?>
                    </div>
                <?php } ?>
                <?php if ($loggedin !== true) { ?>
                    <div class="tab-pane fade <?php if ($action == 'login' ||  ($step =='address' && !$this->config->get('config_guest_checkout'))) {
                        echo 'in active';
                    } ?>" id="user">
                        <?php include($this->templateResource('/template/responses/checkout/login.tpl')) ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

    <script type="text/javascript">
        jQuery(document).ready(function () {
            var submitSent = false;
            var payFormDiv = $(".pay-form");

            payFormDiv.on("click", "#new_user", function () {
                $(this).removeClass('btn-default').addClass('btn-primary');
                $("#login_user").removeClass('btn-primary').addClass('btn-default');
            });

            payFormDiv.on("click", "#login_user", function () {
                $(this).removeClass('btn-default').addClass('btn-primary');
                $("#new_user").removeClass('btn-primary').addClass('btn-default');
                $("#new_address").removeClass('btn-primary').addClass('btn-default');
            });

            payFormDiv.on("click", "#new_address", function () {
                $(this).removeClass('btn-default').addClass('btn-primary');
                $("#login_user").removeClass('btn-primary').addClass('btn-default');
                $('#login_error_container').html('');
				$('#pay_error_container').html('');
            });

            //Form related: event to log creditcard entering, but we use it on all forms to show errors
            var form = $('.pay-form #PayFrm, .pay-form #AddressFrm,  .pay-form #Address2Frm');
            if (form.length) {
                form.aCCValidator({});

                form.find(".form-group input").on("keypress", function () {
                    $.aCCValidator.reset($(this), '.form-group');
                });
                form.find(".form-group select").on("change", function () {
                    $.aCCValidator.reset($(this), '.form-group');
                });

                form.find(".button-checkbox").each(function () {
                    var $widget = $(this),
                        $button = $widget.find('button'),
                        $checkbox = $widget.find('input:checkbox'),
                        color = $button.data('color'),
                        settings = {
                            on: {
                                icon: 'fa fa-check-square-o'
                            },
                            off: {
                                icon: 'fa fa-square-o'
                            }
                        };
                    $button.on('click', function () {
                        $checkbox.prop('checked', !$checkbox.is(':checked'));
                        $checkbox.triggerHandler('change');
                        updateDisplay();
                    });
                    $checkbox.on('change', function () {
                        updateDisplay();
                    });

                    function updateDisplay() {
                        var isChecked = $checkbox.is(':checked');
						$.post('<?php echo $onChangeCheckboxBtnUrl; ?>', {
							fieldName: $checkbox.attr('name'),
							isOn: $checkbox.is(':checked')
						})
						$button.data('state', (isChecked) ? "on" : "off");
						$button.find('.state-icon')
							.removeClass()
							.addClass('state-icon ' + settings[$button.data('state')].icon);
						if (isChecked) {
							$button.removeClass('btn-default').addClass('btn-' + color + ' ');
						} else {
							$button.removeClass('btn-' + color + ' ').addClass('btn-default');
						}
					}

					function init() {
						updateDisplay();
						if ($button.find('.state-icon').length === 0) {
							$button.prepend('<i class="state-icon ' + settings[$button.data('state')].icon + '"></i> ');
						}
					}

					init();
				});

				form.submit(function () {
					if (submitSent !== true) {
						submitSent = true;
						if (validateForm($(this)) !== true) {
							submitSent = false;
							return false;
						}
						$(this).find('.btn-primary').button('loading');
						//All Good send form
						$('.spinner-overlay').fadeIn(100);
						$.post(form.attr('action'), form.serialize(), function (data) {
							try {
								parsedData = JSON.parse(data);
							} catch (e) {

							}
							if (typeof parsedData != "undefined" && typeof parsedData.url != "undefined") {
								location.href = parsedData.url
							} else {

								$('.spinner-overlay').fadeOut(500);
								$('#fast_checkout_summary_block').trigger('reload');
								$('#fast_checkout_cart').hide().html(data).fadeIn(1000)
								if ($('form#PayFrm')) {
									validateForm($('form#PayFrm'));
								}
							}
						});
						return false;
					}
					return false;
				});
			}

			$('#LoginFrm').on('submit', function () {
				$('#LoginFrm').aCCValidator({});
				if (submitSent !== true) {
					submitSent = true;
					if (validateForm($(this)) !== true) {
						submitSent = false;
						return false;
					}
					$(this).find('.btn-primary').button('loading');
					//All Good send form
					$.post($(this).attr('action'), $(this).serialize(), function (data) {
						loadPage('<?php echo $cart_key; ?>')
					});
					return false;
				}
				return false;
			});

			$('#LoginFrm_Submit').on('click', function () {
				$('#LoginFrm').aCCValidator({});
				loginFrm = $('#LoginFrm')
				if (submitSent !== true) {
					submitSent = true;
					if (validateForm(loginFrm) !== true) {
						submitSent = false;
						return false;
					}
					loginFrm.find('.btn-primary').button('loading');
					//All Good send form
					$.post(loginFrm.attr('action'), loginFrm.serialize(), function (data) {
						$('.spinner-overlay').fadeOut(500);
						$('#fast_checkout_summary_block').trigger('reload');
						$('#fast_checkout_cart').hide().html(data).fadeIn(1000)
					});
					return false;
				}
				return false;
			});

            showLoading = function (modal_body) {
                modal_body.html('<div class="modal_loader" style="text-align: center"><i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i></div>');
            };
            validateEmail = function (email) {
                var re = /^\s*(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))\s*$/;
                return re.test(email);
            };
            validatePhone = function (number) {
                var re = /^\s*[\+]?[0-9]{0,3}?[-\s\.]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}\s*$/im;
                return re.test(number);
            };

            pageRequest = function (url) {
				$('.spinner-overlay').fadeIn(100);
				$.get(url, {} , function (data) {
					$('#fast_checkout_summary_block').trigger('reload');
					$('#fast_checkout_cart').hide().html(data).fadeIn(1000);
					$('.spinner-overlay').fadeOut(500);
                    if($('form#PayFrm')) {
                        validateForm($('form#PayFrm'));
                    }
				});
            };

			$('a.address_edit').on('click', function (event) {
				event.preventDefault();
				$('.spinner-overlay').fadeIn(100);
				$.ajax({
					url: $(this).attr('href'),
					type: 'GET',
					dataType: 'html',
					success: function (data) {
						$('#fast_checkout_summary_block').trigger('reload');
						$('#fast_checkout_cart').hide().html(data).fadeIn(1000);
						$('.spinner-overlay').fadeOut(500);
					}
				});
			});
        });

        //on submit validate
        validateForm = function(form) {
            var ret = true;
            form.find(':input').each(function () {
                var el = $(this);
                var name = el.attr('name');
                if (name === undefined) {
                    return;
                }

                //coupon can be only applied, cannot submit
                if (name === 'coupon_code' && !el.attr('disabled')) {
                    var str_val = el.val().replace(/\s+/g, '');
                    if (str_val.length > 0) {
                        $.aCCValidator.show_error(el, '.form-group');
                        ret = false;
                    }
                }

                if (name === 'loginname' && el.val().length < 3) {
                    $.aCCValidator.show_error(el, '.form-group');
                    ret = false;
                }
                if (name === 'password' && el.val().length < 3) {
                    $.aCCValidator.show_error(el, '.form-group');
                    ret = false;
                }
                if (name === 'telephone' && !validatePhone(el.val())) {
                    $.aCCValidator.show_error(el, '.form-group');
                    ret = false;
                }
                if (name === 'cc_email' && !validateEmail(el.val())) {
                    $.aCCValidator.show_error(el, '.form-group');
                    ret = false;
                }
                if (name === 'shipping_address_id' && !el.val()) {
                    $.aCCValidator.show_error(el, '.form-group');
                    ret = false;
                }
                if (name === 'payment_address_id' && !el.val()) {
                    $.aCCValidator.show_error(el, '.form-group');
                    ret = false;
                }
                if (name === 'shipping_method' && !el.val()) {
                    $.aCCValidator.show_error(el, '.form-group');
                    ret = false;
                }
                if (name === 'firstname' && !el.val()) {
                    $.aCCValidator.show_error(el, '.form-group');
                    ret = false;
                }
                if (name === 'lastname' && !el.val()) {
                    $.aCCValidator.show_error(el, '.form-group');
                    ret = false;
                }
                if (name === 'address_1' && !el.val()) {
                    $.aCCValidator.show_error(el, '.form-group');
                    ret = false;
                }
                if (name === 'city' && !el.val()) {
                    $.aCCValidator.show_error(el, '.form-group');
                    ret = false;
                }
                if (name === 'postcode' && !el.val()) {
                    $.aCCValidator.show_error(el, '.form-group');
                    ret = false;
                }
                if (name === 'zone_id' && (!el.val() || el.val().toLowerCase() == 'false') ) {
                    $.aCCValidator.show_error(el, '.form-group');
                    ret = false;
                }
                if (name === 'country_id' && (!el.val() || el.val().toLowerCase() == 'false')) {
                    $.aCCValidator.show_error(el, '.form-group');
                    ret = false;
                }
                if (name === 'cc_cvv2' && (!el.val() || !$.aCCValidator.checkCVV(el)) ) {
                    $.aCCValidator.show_error(el, '.form-group');
                    ret = false;
                }
                if (name === 'cc_number' && (!el.val() || !$.aCCValidator.checkCCNumber(el)) ) {
                    $.aCCValidator.show_error(el, '.form-group');
                    ret = false;
                }
            });
            var cover = $('.div-cover');
            if(cover){
                <?php //show cover only for non-payment form and selectors! ?>
                if( ret === false && form.parents('.payment-select-container').length == 0) {
                    cover.css('height', $('.payment-select-container').height());
                    cover.show();
                }else{
                    cover.hide();
                }
            }

            return ret;
        };
    </script>
<?php
echo $footer;
?>
