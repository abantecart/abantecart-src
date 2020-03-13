<?php

class ExtensionDefaultStripe extends Extension
{
    protected $registry;
    protected $r_data;

    public function __construct()
    {
        $this->registry = Registry::getInstance();
    }

    //Hook to extension edit in the admin
    public function onControllerPagesExtensionExtensions_UpdateData()
    {
        $that = $this->baseObject;
        $current_ext_id = $that->request->get['extension'];
        if (IS_ADMIN && $current_ext_id == 'default_stripe' && $this->baseObject_method == 'edit') {
            $html = '<a class="btn btn-white tooltips" target="_blank" href="https://www.stripe.com" title="Visit stripe">
						<i class="fa fa-external-link fa-lg"></i>
					</a>';
            $that->view->addHookVar('extension_toolbar_buttons', $html);
        }
    }

    public function onControllerResponsesCheckoutPay_UpdateData()
    {
        $that = $this->baseObject;
        if( !$that->config->get('simple_checkout_payment_method')){
            return;
        }

        //get payment ext key from setting ID
        $results = $that->model_checkout_extension->getExtensions('payment');
        $payment_method = '';
        foreach ($results as $result){
            if($result['extension_id'] == $that->config->get('simple_checkout_payment_method')
                && $result['key'] == 'default_stripe'
            ){
                $payment_method = 'default_stripe';
                break;
            }
        }
        if(!$payment_method){
            return;
        }

        if($this->baseObject_method != 'main'
            || $that->data['show_payment'] !== true
        ){ return; }




        if($that->session->data['guest']){
            $address_1 = $that->session->data['guest']['address_1'];
            $address_2 = $that->session->data['guest']['address_2'];
            $address_city = $that->session->data['guest']['city'];
            $address_country_code = $that->session->data['guest']['iso_code_2'];
            $address_zone_code = $that->session->data['guest']['zone_code'];
            $address_postcode = $that->session->data['guest']['postcode'];
        }else{
            $address_id = $that->session->data['quick_checkout'][$that->data['cart_key']]['payment_address_id'];
            if($address_id){
                $address = $that->model_account_address->getAddress($address_id);
                $address_1 = $address['address_1'];
                $address_2 = $address['address_2'];
                $address_city = $address['city'];
                $address_country_code = $address['iso_code_2'];
                $address_zone_code = $address['zone_code'];
                $address_postcode = $address['postcode'];
            }
        }

        $that->document->addScript('https://js.stripe.com/v3');

        $that->view->addHookVar(
            'simple_checkout_main',
            '
<script type="text/javascript">


$(document).ready( function(){
    
    //replace card form elements with own container
    $("#cc_number")
        .parents("div.row")
        .html(\'<div class="row"><div class="form-group col-xxs-12"><div class="left-inner-addon"><div id="card-element" class="col-xs-12 input-group field" style="width:95%; border: 1px solid #ccc; border-radius: 6px; padding: 5px; margin: auto 15px;"></div><input type="hidden" name="cc_token" id="cc_token"></div></div></div>\');
    
    $("#cc_expire_date_month").parents("div.row").remove();

    var stripe = Stripe("'. $that->config->get('default_stripe_published_key').'");
    var elements = stripe.elements();
    var card = elements.create(\'card\', {
        hidePostalCode: true,
        style: {
            base: {
                iconColor: \'#337ab7\',
                color: \'#31325F\',
                lineHeight: \'40px\',
                fontWeight: 300,
                fontFamily: \'"Helvetica Neue", Helvetica, sans-serif\',
                fontSize: \'15px\',
                \'::placeholder\': {
                    color: \'#9F9F9F\',
                },
            },
        }
    });
    card.mount(\'#card-element\');
		
    $("#PayFrm").on("click", ".btn-pay", function(event) {
        event.preventDefault();
        if (validateForm($("#PayFrm")) !== true ){
            return false;
        }
        $(this).button("loading");
        var $form = $(this);
        var extraDetails = {
            name: $("input[name=cc_owner]").val(),
            address_line1: '. js_encode($address_1).',
            address_line2: '. js_encode($address_2) .',
            address_city: '. js_encode($address_city) .',
            address_state: '. js_encode($address_zone_code) .',
            address_zip: '. js_encode($address_postcode) .',
            address_country: '. js_encode($address_country_code) .'
        };
        stripe.createToken(card, extraDetails).then(function(result){
            
            if (result.error) {
                alert( result.error.message );
                $("#PayFrm .btn-pay").button("reset");
            } else {
                $(\'#cc_token\').val(result.token.id);
                $("#PayFrm").submit();
            }
        });
    });
});
</script>');
    }
}