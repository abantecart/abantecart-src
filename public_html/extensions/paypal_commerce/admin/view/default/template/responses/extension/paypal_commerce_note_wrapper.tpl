<style>
    #pp-info-block{
        width: 900px;
        margin: 0 auto;
        background-color: white;
        filter: opacity(1);
        border: 1px solid #c7c6c6;
        padding: 15px;
    }
    #pp-info-block .pp-logo{
        width: 225px;
    }
    #pp-info-block .pp-marks{
        width: 325px;
    }

    #pp-info-block .pp-desc, #pp-info-block .pp-desc>h3, #pp-info-block .pp-desc>h4 , #pp-info-block .pp-desc>h6 {
        color: #3a3a3a;
        font-weight: bold;
    }
    #pp-info-block .pp-desc>ul>li {
        margin-bottom: 10px;
    }
    #pp-info-block .pp-buttons {
        margin-top: 30px;
        margin-bottom: 20px;
    }
    #pp-info-block .pp-buttons a.pp-live-btn {
        height: 50px;
        width: 250px;
        padding: 15px;
        border-radius: 5px;
    }

</style>

<div class="container-fluid">
    <div id="pp-info-block">
        <?php if(!$this->config->get('paypal_commerce_client_id')){ ?>
        <div class="row ml10 mr10 mt20">
            <div class="pull-left pp-logo">
                <img src="extensions/paypal_commerce/image/pp-logo.png" alt="PayPal Recommended">
            </div>
            <div class="pull-right pp-marks">
                <img src="extensions/paypal_commerce/image/paypal_set.png" alt="payment marks" style="width: 329px; margin-top:40px;">
            </div>
        </div>
        <div class="pp-desc row ml30 mr10">
            <h3 class="mb20">PayPal Checkout</h3>
            <ul>
                <li>Enable users to pay with PayPal and Venmo digital wallets with no setup or monthly fees</li>
                <li>Provide buy now pay later solutions at no extra cost</li>
                <li>Accept major credit/debit cards and local payment methods through the PayPal wallet</li>
            </ul>
        </div>
        <div class="pp-buttons row">
            <a class="pp-live-btn inblock mr10 ml30" href="<?php
            /** @var ModelToolMPAPI $mpMdl */
            $mpMdl = $this->load->model('tool/mp_api');
            echo $mpMdl->getMPURL().'?rt=index/paypal_onboarding'
                . '&abc_onboard_url='.base64_encode(  $this->html->getSecureURL('extension/paypal_commerce/onboard'))
                . '&nonce='.base64_encode(UNIQUE_ID)
                . '&store_id='.(int)$this->session->data['current_store_id'];?>
            ">Get Started</a>

        </div>
        <?php } else { ?>
            <div class="row ml10 mr10 mt20">
                <div class="pull-left pp-logo">
                    <img src="extensions/paypal_commerce/image/pp-logo.png" alt="PayPal Recommended">
                </div>
                <div class="pp-desc col-sm-4" style="margin-top:45px">
                    <h4 class="mb20">PayPal Checkout</h4>
                    <h6 class="pp-connected-text"><i class="fa fa-check-circle"></i> &nbsp;Your PayPal account is connected</h6>
                    <div class="pp-buttons">
                        <?php $href = $this->config->get('paypal_commerce_test_mode')
                            ? "https://www.sandbox.paypal.com/businessmanage/account/aboutBusiness"
                            : "https://www.paypal.com/businessmanage/account/aboutBusiness";
                        ?>
                        <a class="pp-live-btn inblock mr10 ml30" target="_blank" href="<?php echo $href; ?>">Settings</a>
                    </div>
                </div>
                <div class="pull-right pp-marks">
                    <img src="extensions/paypal_commerce/image/paypal_set.png" alt="payment marks" style="width: 329px; margin-top:20px;">
                </div>
            </div>
        <?php } ?>
    </div>

</div>