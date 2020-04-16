<?php echo $head; ?>
<?php echo $header; ?>
    <div class="pay-form">
        <div class="text-center">
            <div class="btn-group">
                <?php
                if ($action == 'login') {
                    $login_button_style = 'btn-primary';
                    $pay_button_style = 'btn-default';
                } else {
                    $login_button_style = 'btn-default';
                    $pay_button_style = 'btn-primary';
                }
                ?>
                <?php if ($loggedin === true) { ?>
                    <a href="#new" id="new_user" role="tab" data-toggle="tab"
                       class="big btn <?php echo $pay_button_style; ?>">
                        <i class="fa fa-user fa-fw"></i>&nbsp;<span
                                class="hidden-xxs"><?php echo $customer_name; ?></span>
                    </a>
                    <a href="<?php echo $logout_url; ?>" id="logout_user" class="big btn btn-default">
                        <i class="fa fa-user-times fa-fw"></i>&nbsp;<span
                                class="hidden-xxs"><?php echo $fast_checkout_text_logout; ?></span>
                    </a>
                <?php } else { ?>
                    <?php if ($step == 'address') { ?>
                        <a href="#address" id="new_address" role="tab" data-toggle="tab"
                           class="big btn <?php echo $pay_button_style; ?>">
                            <i class="fa fa-map fa-fw"></i>&nbsp;<span class="hidden-xxs"><?php echo $type.' '
                                    .$fast_checkout_text_address; ?></span>
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
                <?php } ?>
            </div>
        </div>
        <div class="col-xxs-12 col-xs-8">
            <div class="tab-content">
                <?php if ($step == 'address') { ?>
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
                    <div class="tab-pane fade <?php if ($action == 'login') {
                        echo 'in active';
                    } ?>" id="user">
                        <?php include($this->templateResource('/template/responses/checkout/login.tpl')) ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="col-xxs-12 col-xs-4">
            <div id="cart_details">
                <ul class="list-group">
                    <li class="list-group-item active">
                        <h4 class="list-group-item-heading">
                            <i class="fa fa-shopping-cart fa-fw"></i>
                            <span class="pull-right">
                        <?php echo $total_string; ?>
                      </span>
                        </h4>
                    </li>
                    <?php foreach ($products as $p) { ?>
                        <li class="list-group-item">
                            <span class="badge"><?php echo $p['price']."  x  ".$p['quantity']; ?></span>
                            <h4 class="list-group-item-heading">
                                <a>
                                    <img src="<?php echo $p['thumbnail']['main_url'] ?>"
                                         style="width:<?php echo $this->config->get('config_image_grid_width')
                                             ."px; height:".$this->config->get('config_image_grid_height'); ?>px;"/>
                                    <small><?php echo $p['name'] ?></small>
                                </a>
                            </h4>
                            <p class="list-group-item-text">
                                <?php if ($p['option'] && is_array($p['option'])) { ?>
                            <ul>
                                <?php foreach ($p['option'] as $option) { ?>
                                    <li>
                          <span>
                            <?php if ($option['title']) { ?>
                                <span class="pull-left">
                            <small><b><?php echo $option['title'] ?></b></small>
                            </span>
                            <?php } ?>
                            <span class="pull-right">
                                <small><b><?php echo $option['name']; ?>:</b> <?php echo $option['value']; ?></small>
                            </span>
                          </span>
                                    </li>
                                <?php } ?>
                            </ul>
                            <?php } ?>
                            </p>
                        </li>
                    <?php } ?>

                    <?php
                    foreach ($totals as $ttl) {
                        ?>
                        <li class="list-group-item">
                            <?php
                            if ($ttl['id'] == 'total') {
                                $ttl['title'] = "<b>".$ttl['title']."</b>";
                                $ttl['text'] = "<b>".$ttl['text']."</b>";
                            }
                            ?>
                            <span class="badge"><?php echo $ttl['text']; ?></span>
                            <h4 class="list-group-item-heading"><?php echo $ttl['title']; ?></h4>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>

    </div>

    <script type="text/javascript">
        jQuery(document).ready(function () {

            var submitSent = false;

            $(".pay-form").on("click", "#new_user", function () {
                $(this).removeClass('btn-default').addClass('btn-primary');
                $("#login_user").removeClass('btn-primary').addClass('btn-default');
            });
            $(".pay-form").on("click", "#login_user", function () {
                $(this).removeClass('btn-default').addClass('btn-primary');
                $("#new_user").removeClass('btn-primary').addClass('btn-default');
                $("#new_address").removeClass('btn-primary').addClass('btn-default');
            });
            $(".pay-form").on("click", "#new_address", function () {
                $(this).removeClass('btn-default').addClass('btn-primary');
                $("#login_user").removeClass('btn-primary').addClass('btn-default');
            });

            //Form related: event to log creditcard entering, but we use it on all forms to show errors
            var form = $('.pay-form form');
            if (form.length) {
                form.aCCValidator({});

                $(".pay-form .form-group input").on("keypress", function () {
                    $.aCCValidator.reset($(this), '.form-group');
                });
                $(".pay-form .form-group select").on("change", function () {
                    $.aCCValidator.reset($(this), '.form-group');
                });

                $('.pay-form .button-checkbox').each(function () {
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
                        if ($button.find('.state-icon').length == 0) {
                            $button.prepend('<i class="state-icon ' + settings[$button.data('state')].icon + '"></i> ');
                        }
                    }

                    init();
                });

                form.submit(function (event) {
                    if (submitSent !== true) {
                        submitSent = true;
                        if (validateForm($(this)) !== true) {
                            submitSent = false;
                            return false;
                        }
                        $(this).find('.btn-primary').button('loading');
                        return true;
                    }
                    return false;
                });
            }

            //on submit validate
            validateForm = function (form) {
                var ret = true;
                form.find(':input').each(function () {
                    var el = $(this);
                    var name = el.attr('name');
                    if (name == undefined) {
                        return;
                    }

                    //coupon can be only applied, cannot submit
                    if (name == 'coupon_code' && !el.attr('disabled')) {
                        var str_val = el.val().replace(/\s+/g, '');
                        if (str_val.length > 0) {
                            $.aCCValidator.show_error(el, '.form-group');
                            ret = false;
                        }
                    }

                    if (name == 'loginname' && el.val().length < 3) {
                        $.aCCValidator.show_error(el, '.form-group');
                        ret = false;
                    }
                    if (name == 'password' && el.val().length < 3) {
                        $.aCCValidator.show_error(el, '.form-group');
                        ret = false;
                    }
                    if (name == 'cc_telephone' && !validatePhone(el.val())) {
                        $.aCCValidator.show_error(el, '.form-group');
                        ret = false;
                    }
                    if (name == 'cc_email' && !validateEmail(el.val())) {
                        $.aCCValidator.show_error(el, '.form-group');
                        ret = false;
                    }
                    if (name == 'shipping_address_id' && !el.val()) {
                        $.aCCValidator.show_error(el, '.form-group');
                        ret = false;
                    }
                    if (name == 'payment_address_id' && !el.val()) {
                        $.aCCValidator.show_error(el, '.form-group');
                        ret = false;
                    }
                    if (name == 'shipping_method' && !el.val()) {
                        $.aCCValidator.show_error(el, '.form-group');
                        ret = false;
                    }
                    //cc validation
                    if (ret !== false && name == 'cc_number') {
                        ret = $.aCCValidator.validate(form);
                    }
                });
                return ret;
            }

            showLoading = function (modal_body) {
                modal_body.html('<div class="modal_loader"><i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i></div>');
            }

            validateEmail = function (email) {
                var re = /^\s*(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))\s*$/;
                return re.test(email);
            }

            validatePhone = function (number) {
                var re = /^\s*[\+]?[0-9]{0,3}?[-\s\.]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}\s*$/im;
                return re.test(number);
            }

            pageRequest = function (url) {
                window.location.href = url;
            }

        });
    </script>
<?php echo $footer; ?>