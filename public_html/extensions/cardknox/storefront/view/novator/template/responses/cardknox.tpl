<style>
    .form-group iframe {
        border: none;
        height: 40px;
        padding: 0;
        margin: 0;
    }
    #cardknox_cc_month {
        width: 110px;
        padding: 5px;
    }
    #cardknox_cc_year {
        padding: 20px;
    }
    #accordion > h4 {
        background-color: #f6f6f6;
        padding: 10px;
    }
    #accordion > div {
        background-color: gainsboro;
        padding-top: 15px;
        margin-left: 3%;
        margin-right: 11px;
    }
    .ebt-types > label {
        width: 100%;
        clear: both;
    }
    .container-fluid-custom {
        width: 100%;
        padding: 0 15px;
        margin: 0 auto;
    }
</style>

<div class="container-fluid container-fluid-custom">
    <div class="card checkout_details col-12 mx-auto mb-5 bg-light">
        <h5 class="card-title bg-secondary bg-opacity-10 p-2"><?php echo $text_payment; ?></h5>
        <div class="card-body text-start">
            <p class="card-text"><?php echo $text_payable; ?> <b class="ms-4"><?php echo $payable; ?></b></p>
            <p class="card-text"><?php echo $text_address; ?> <b class="ms-4"><?php echo $address; ?></b></p>
        </div>
    </div>

    <form id="CqFrm" class="text-center">
        <div class="form-group action-buttons">
            <button id="checkout_btn" type="submit" class="btn btn-primary btn-lg lock-on-click" title="<?php echo $button_confirm->text; ?>">
                <i class="fa fa-check"></i>
                <?php echo $button_confirm->text; ?>
            </button>
        </div>
    </form>

    <div class="enter_card">
        <?php if($error) { ?>
            <div class="alert alert-danger"><i class="fa fa-bug"></i> <?php echo $error; ?></div>
        <?php } ?>
        <?php echo $form_open; ?>
        <?php echo $amount; ?>
        <?php echo $method; ?>
        <div class="accordion mb-3" id="accordion">
            <div data-idx="0" data-method="cc" class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <?php echo $text_credit_card; ?>
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordion">
                    <div class="accordion-body">
                        <div class="text-center mb-3">
                            <iframe data-ifields-id="card-number" data-ifields-placeholder="Card Number" width="100%" height="44" src="https://cdn.cardknox.com/ifields/<?php echo $iFieldsVersion; ?>/ifield.htm"></iframe>
                            <input data-ifields-id="card-number-token" name="xCardNum" type="hidden">
                        </div>
                        <div class="mb-3">
                            <div class="input-group">
                                <?php $cc_expire_date_month->no_wrapper = true; echo $cc_expire_date_month; ?>
                                <?php $cc_expire_date_year->no_wrapper = true; echo $cc_expire_date_year; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <iframe data-ifields-id="cvv" data-ifields-placeholder="CVV" width="50%" height="44" src="https://cdn.cardknox.com/ifields/<?php echo $iFieldsVersion; ?>/ifield.htm"></iframe>
                            <input data-ifields-id="cvv-token" name="xCVV" type="hidden">
                        </div>
                        <div class="text-center">
                            <label id="transaction-status"></label>
                            <label data-ifields-id="card-data-error" style="color: red;"></label>
                        </div>
                    </div>
                </div>
            </div>
            <div data-idx="1" data-method="ach" class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        <?php echo $cardknox_text_check_ach; ?>
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordion">
                    <div class="accordion-body">
                        <div class="center mb-3">
                            <?php echo $customer_name; ?>
                        </div>
                        <iframe class="mb-3" data-ifields-id="ach" width="100%" height="44" data-ifields-placeholder="Account Number" src="https://cdn.cardknox.com/ifields/<?php echo $iFieldsVersion; ?>/ifield.htm"></iframe>
                        <input data-ifields-id="ach-token" name="xACH" type="hidden"/>
                        <div class="mb-3">
                            <?php echo $routing_number; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div data-idx="2" data-method="ebt" class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        <?php echo $cardknox_text_ebt; ?>
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordion">
                    <div class="accordion-body">
                        <div class="mb-3 ebt-types"><?php echo $ebt_type; ?></div>
                        <div class="mb-3"><?php echo $ebt_number; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group action-buttons text-center">
            <button id="<?php echo $submit->name ?>" class="btn btn-primary" title="<?php echo $submit->text ?>" type="submit">
                <i class="fa fa-check"></i>
                <?php echo $submit->text; ?>
            </button>
        </div>
        </form>
    </div>
</div>

<form id="EBTPinPad" action="" method="post">
    <input type="hidden" id="AccuID" name="AccuId" value="">
    <input type="hidden" id="AccuReturnURL" name="AccuReturnURL" value="">
    <input type="hidden" id="method" name="method" value="">
    <input type="hidden" name="csrftoken" value="">
    <input type="hidden" name="csrfinstance" value="">
</form>


<script type="text/javascript">
    if (typeof window['loadScript'] !== "function") {
        //when try to load script from ajax-response
        function loadScript(url, callback){
            var script = document.createElement("script")
            script.type = "text/javascript";

            if (script.readyState){  //IE
                script.onreadystatechange = function(){
                    if (script.readyState === "loaded" || script.readyState === "complete")
                    {
                        script.onreadystatechange = null;
                        callback();
                    }
                };
            } else {  //Others
                script.onload = function(){
                    callback();
                };
            }

            script.src = url;
            document.getElementsByTagName("head")[0].appendChild(script);
        }
    }
    jQuery(document).ready(function () {
        loadScript('https://cdn.cardknox.com/ifields/<?php echo $iFieldsVersion; ?>/ifields.min.js', InitCardKnox);

        let method = getURLVar('method');
        if(method !== undefined && method.length>0){
            let div = $("div[data-method^='"+method+"']");
            div.find('.accordion-header>button').click();
            let idx = div.attr('data-idx');
            idx = parseInt(idx);
            $("#cardknox_method").val(div.attr('data-method'));
        }

        var myCollapsible = $('#accordion .accordion-collapse');
        myCollapsible.on('shown.bs.collapse', function () {
            let methd = $(this).parents('.accordion-item').attr('data-method')
            $('#cardknox_method').val(methd);
        });


        function InitCardKnox() {
            setAccount(
                "<?php echo $this->config->get('cardknox_ifields_key');?>",
                "AbanteCart",
                "<?php echo VERSION; ?>"
            );
            enableAutoFormatting(' ');
            enable3DS('cardknox_amount', 'cardknox_cc_month', 'cardknox_cc_year');
            let css = {
                'border-radius': '.25rem',
                'width': '90%',
                'height': '36px',
                'padding': '0px 12px',
                'font-size': '16px',
            };
            setIfieldStyle('ach', css);
            setIfieldStyle('card-number', css);
            setIfieldStyle(
                'cvv',
                {
                    'border-radius': '.25rem',
                    'height': '36px',
                    'padding': '0px 12px',
                    'font-size': '16px',
                    'width': '50px'
                }
            );

            $('#cardknox').on(
                'submit',
                function (e) {
                    e.preventDefault();
                    $('.alert').remove();
                    $('#cardknox .action-buttons')
                        .hide()
                        .before('<div class="wait alert alert-info text-center"><i class="fa fa-refresh fa-spin"></i> <?php echo $text_wait; ?></div>');
                    var submitBtn = document.getElementById('<?php echo $submit->name ?>');
                    //when EBT
                    if( $('div[data-method="ebt"]').find('.accordion-collapse').hasClass('show') ){
                        if( $('#cardknox_ebtcardnum').val().length > 12 ){
                            loadPinPad();
                        }else{
                            alert('Please fill EBT card Number!');
                            $('.wait').remove();
                            $('#cardknox .action-buttons').show().find('button').removeAttr('disabled');
                            return false;
                        }
                    }
                    //when other methods
                    else{
                        submitBtn.disabled = true;
                        getTokens(
                            function () {
                                confirmSubmit($('#cardknox'));
                            },
                            function () { //onError
                                submitBtn.disabled = false;
                            },
                            30000, //30 second timeout
                        );
                    }
                }
            );
        }
        function confirmSubmit($form) {
            $.ajax({
                type: 'POST',
                url: '<?php echo $action; ?>',
                data: $form.serialize(),
                dataType: 'json',
                success: function (data) {
                    if (!data) {
                        $('.wait').remove();
                        $('#cardknox .action-buttons').show().find('button').removeAttr('disabled');
                        $('#cardknox').before('<div class="alert alert-danger"><i class="fa fa-bug"></i> <?php echo $error_unknown; ?></div>');
                        submitSent = false;
                    } else {
                        if (data.error) {
                            $('.wait').remove();
                            $('#cardknox .action-buttons').show().find('button').removeAttr('disabled');
                            $('#cardknox').before('<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> ' + data.error + '</div>');
                            submitSent = false;
                            $form.find('input[name=csrfinstance]').val(data.csrfinstance);
                            $form.find('input[name=csrftoken]').val(data.csrftoken);
                        }
                        if (data.success) {
                            location = data.success;
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('.wait').remove();
                    $('#cardknox .action-buttons').show().find('button').removeAttr('disabled');
                    $('#cardknox').before('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> ' + textStatus + ' ' + errorThrown + '</div>');
                    submitSent = false;
                    $form.find('input[name=csrfinstance]').val(data.csrfinstance);
                    $form.find('input[name=csrftoken]').val(data.csrftoken);
                },
                complete: function(){
                    $('.spinner-overlay').fadeOut(100);
                }
            });
        }

        function loadPinPad() {
            let $form = $('form#cardknox');
            $.ajax({
                type: 'POST',
                url: '<?php echo $ebt_init_url; ?>',
                data: {
                    csrftoken: $form.find('input[name=csrftoken]').val(),
                    csrfinstance: $form.find('input[name=csrfinstance]').val(),
                    xCardNum: $form.find('[name=EBTCardNum]').val(),
                    fast_checkout: true
                },
                dataType: 'json',
                success: function (data) {
                    if (!data) {
                        $('.wait').remove();
                        $('#cardknox .action-buttons').show().find('button').removeAttr('disabled');
                        $('#cardknox').before('<div class="alert alert-danger"><i class="fa fa-bug"></i> <?php echo $error_unknown; ?></div>');
                    } else {
                        if (data.error) {
                            alert(data.error);
                        }
                        $form.find('input[name=csrfinstance]').val(data.csrfinstance);
                        $form.find('input[name=csrftoken]').val(data.csrftoken);
                        if (data.success) {
                            let form = $('#EBTPinPad');
                            form.prop('action',data.PinPadURL);
                            form.find('#AccuID').val(data.AccuID);
                            form.find('#AccuReturnURL').val(data.AccuReturnURL);
                            form.find('#method').val($('#cardknox_method').val());
                            form.find('input[name=csrfinstance]').val(data.csrfinstance);
                            form.find('input[name=csrftoken]').val(data.csrftoken);
                            form.submit();
                        }else {
                            $('.wait').remove();
                            $('#cardknox .action-buttons').show().find('button').removeAttr('disabled');
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('#cardknox').before('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> ' + textStatus + ' ' + errorThrown + '</div>');
                    submitSent = false;
                },
                complete: function(){
                    $('.spinner-overlay').fadeOut(100);
                }
            });
        }
        $('#cardknox_ebtcardnum').on('keyup', function(){
            let val = $(this).val();
            val = val.replace(/[^0-9]+/g, "");
            $(this).val(val);
        });
    });
</script>