<style>
    .form-group iframe {
        border: none;
        height: 40px;
        padding: 0;
        margin: 0;
    }
    #cardknox_cc_month{
        width: 110px;
        padding: 5px;
    }
    #cardknox_cc_year{
        padding: 5px;
    }

    #accordion>h4{
        background-color: #f6f6f6;
        padding: 10px;
    }
    #accordion>div{
        background-color: gainsboro;
        padding-top: 15px;
        margin-left: 3%;
        margin-right: 11px;
    }
    .cardknox-300{
        width: 300px !important;
    }
    .ebt-types>label{
        width: 100%;
        clear: both;
    }
</style>
<div class="enter_card">
    <?php
    if($error){ ?>
        <div class="alert alert-danger"><i class="fa fa-bug"></i> <?php echo $error; ?></div>
    <?php }

    echo $form_open;
    echo $amount;
    echo $method;
    ?>
<div id="accordion">
    <h4 class="heading4"><?php echo $text_credit_card; ?></h4>
    <div data-idx="0" data-method="cc" class="form-group form-inline">
            <div class="text-center">
                <iframe data-ifields-id="card-number" data-ifields-placeholder="Card Number"
                        src="https://cdn.cardknox.com/ifields/<?php echo $iFieldsVersion; ?>/ifield.htm"></iframe>
                <input data-ifields-id="card-number-token" name="xCardNum" type="hidden">
            </div>
            <div class="center mb10 cardknox-300">
                <div class="input-group">
                    <?php echo $cc_expire_date_month; ?>
                </div>
                <div class="input-group">
                    <?php echo $cc_expire_date_year; ?>
                </div>
                <span class="help-block"></span>
            </div>
            <div class="text-center">
                <iframe data-ifields-id="cvv" data-ifields-placeholder="CVV"
                        src="https://cdn.cardknox.com/ifields/<?php echo $iFieldsVersion; ?>/ifield.htm"></iframe>
                <input data-ifields-id="cvv-token" name="xCVV" type="hidden">
            </div>
            <div class="text-center">
                <!--And a field for all errors from the iFields-->
                <label id="transaction-status"></label>
                <label data-ifields-id="card-data-error" style="color: red;"></label>
            </div>
        </div>
    <h4 class="heading4"><?php echo $cardknox_text_check_ach; ?></h4>
    <div data-idx="1" data-method="ach"  class="form-group form-inline">
        <div class="center mb10 cardknox-300">
            <?php echo $customer_name; ?>
        </div>
        <div class="text-center">
            <iframe data-ifields-id="ach"
                    data-ifields-placeholder="Account Number"
                    src="https://cdn.cardknox.com/ifields/<?php echo $iFieldsVersion; ?>/ifield.htm"></iframe>
            <input data-ifields-id="ach-token" name="xACH" type="hidden"/>
        </div>
        <div class="center cardknox-300">
            <?php echo $routing_number; ?>
        </div>
    </div>
    <h4 class="heading4"><?php echo $cardknox_text_ebt; ?></h4>
    <div data-idx="2" data-method="ebt" class="form-group form-inline mb10">
        <div class="center cardknox-300 ebt-types">
            <?php echo $ebt_type; ?>
        </div>
        <div class="center cardknox-300">
            <?php echo $ebt_number; ?>
        </div>
    </div>

</div>
    <div class="form-group action-buttons text-center">
        <a id="<?php
        echo $back->name ?>" href="<?php
        echo $back->href; ?>" class="btn btn-default mr10">
            <i class="fa fa-arrow-left"></i>
            <?php
            echo $back->text ?>
        </a>
        <button id="<?php
        echo $submit->name ?>"
                class="btn btn-orange"
                title="<?php
                echo $submit->text ?>" type="submit">
            <i class="fa fa-check"></i>
            <?php
            echo $submit->text; ?>
        </button>
    </div>
</div>
</form>
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
                    if (script.readyState == "loaded" ||
                            script.readyState == "complete"){
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
        loadScript(
            'https://code.jquery.com/ui/1.12.1/jquery-ui.js',
            function(){
                $( "#accordion" ).accordion(
                    {
                        activate: function( event, ui ) {
                            let method = $(ui.newPanel).attr('data-method');
                            $('#cardknox_method').val(method);
                        }
                    }
                );
                <?php if($this->request->get['method']){ ?>
                    let div = $("div[data-method^='<?php echo $this->request->get['method']?>']");
                    let idx = div.attr('data-idx');
                    idx = parseInt(idx);
                    $("#accordion").accordion({ active: idx });
                    $("#cardknox_method").val(div.attr('data-method'));
                <?php }?>
            });
        loadScript('https://cdn.cardknox.com/ifields/<?php echo $iFieldsVersion; ?>/ifields.min.js', InitCardKnox);

        function InitCardKnox() {
            setAccount(
                "<?php echo $this->config->get('cardknox_ifields_key');?>",
                "AbanteCart",
                "<?php echo VERSION; ?>"
            );
            enableAutoFormatting(' ');
            enable3DS('cardknox_amount', 'cardknox_cc_month', 'cardknox_cc_year');
            let css = {
                'width': '200px'
            };
            setIfieldStyle('ach', css);
            setIfieldStyle('card-number', css);
            setIfieldStyle('cvv', {'width': '50px'});

            document.getElementById('cardknox').addEventListener('submit', function (e) {
                e.preventDefault();
                $('.alert').remove();
                $('#cardknox .action-buttons')
                    .hide()
                    .before('<div class="wait alert alert-info text-center"><i class="fa fa-refresh fa-spin"></i> <?php echo $text_wait; ?></div>');
                var submitBtn = document.getElementById('<?php echo $submit->name ?>');
                //when EBT
                if( $('div[data-method="ebt"]').css('display') !== 'none' ){
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
            });
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
                            $('#cardknox').before('<div class="alert alert-warning"><i class="fa fa-exclamation"></i> ' + data.error + '</div>');
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
                    $('#cardknox').before('<div class="alert alert-danger"><i class="fa fa-exclamation"></i> ' + textStatus + ' ' + errorThrown + '</div>');
                    submitSent = false;
                    $form.find('input[name=csrfinstance]').val(data.csrfinstance);
                    $form.find('input[name=csrftoken]').val(data.csrftoken);
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
                    xCardNum: $form.find('[name=EBTCardNum]').val()
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
                    $('#cardknox').before('<div class="alert alert-danger"><i class="fa fa-exclamation"></i> ' + textStatus + ' ' + errorThrown + '</div>');
                    submitSent = false;
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