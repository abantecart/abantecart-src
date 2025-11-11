<?php /** @var AView|AController $this */?>
<div id="messaging-configurator"></div>
<script src="https://www.paypalobjects.com/merchant-library/merchant-configurator.js" defer></script>
<script>
    window.addEventListener('load', function () {
        const MessagingConfigHandler = (data) => {
            for(let i in data.config){
                let cfg = data.config[i];
                let placement = cfg.placement;
                $('input[name="paypal_commerce_pay_later_'+placement+'_message"]').val(
                    merchantConfigurators.generateMessagingCodeSnippet({messageConfig: cfg})
                )
                //mark form as changed
                $('#messaging-configurator').addClass('changed').parents('form').prop('changed','true');
            }
        };

        merchantConfigurators.Messaging({
                    bnCode: <?php js_echo(base64_decode(ExtensionPaypalCommerce::getBnCode()));?>,
                    merchantIdentifier: <?php js_echo($this->config->get('paypal_commerce_client_id'));?>,
                    partnerClientId: <?php js_echo(base64_decode(ExtensionPaypalCommerce::getPartnerClientId()));?>,
                    partnerName: "AbanteCart",
            onSave: MessagingConfigHandler,
            locale: <?php js_echo($this->language->getLanguageLocale('language-country')); ?>,
            placements: ['checkout', 'product', 'cart'],
        });
    });
</script>
