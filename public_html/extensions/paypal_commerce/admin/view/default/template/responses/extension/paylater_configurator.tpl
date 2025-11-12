<?php /** @var AView|AController $this */
if($this->config->get('paypal_commerce_client_id')){ ?>
    <div id="paypal_commerce_client_configurator_container">
        <?php echo $this->html->buildElement(
            [
                'type' => 'modal',
                'id' => 'pp_cfg_modal',
                'modal_type' => 'xlg w-100',
                'title' => 'Paypal Commerce Configuration',
                'content' => '<div id="messaging-configurator"></div>'
            ]
        ); ?>
        <button class="btn btn-default tooltips" type="button" data-toggle="modal" data-target="#pp_cfg_modal">
            <i class="fa fa-paypal"></i>
            <?php echo $this->language->get('paypal_commerce_text_run_configurator'); ?>
        </button>
    </div>
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
                $('[data-target="#pp_cfg_modal"]').parent().addClass('changed').parents('form').prop('changed','true');
                $('#pp_cfg_modal').modal('hide');
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
<?php }else{ ?>
    <div class="alert alert-info"><?php echo $this->language->get('paypal_commerce_text_connect_for_configurator')?></div>
<?php } ?>