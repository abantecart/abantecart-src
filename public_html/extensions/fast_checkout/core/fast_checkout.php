<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ExtensionFastCheckout extends Extension
{
    private $init_loaded = false;
    protected $registry;
    protected $sc_rt =  'checkout/fast_checkout';

    public function __construct()
    {
        //CORS solution for http 2 https
        header("Access-Control-Allow-Origin: ".'http://'.REAL_HOST.get_url_path($_SERVER['PHP_SELF']));
        $this->registry = Registry::getInstance();
        if (!isset($this->registry->get('session')->data['fast_checkout'])) {
            $this->registry->get('session')->data['fast_checkout'] = array();
        }
    }

    public function onControllerPagesCheckoutShipping_InitData()
    {
        $that = $this->baseObject;
        $cart_key = randomWord(5);
        $that->session->data['cart_key'] = $cart_key;
        unset($that->session->data['used_balance']);
        redirect($that->html->getSecureURL($this->sc_rt, "&cart_key=".$cart_key));
    }

    public function onControllerCommonFooter_UpdateData()
    {
        $that = $this->baseObject;

        $that->loadLanguage('fast_checkout/fast_checkout');
    }

    public function onControllerResponsesEmbedHead_InitData()
    {
        $that = $this->baseObject;
        if (!$that->config->get('embed_mode')) {
            return null;
        }
        $this->_init($that);
    }

    /**
     * @param AController $that
     *
     * @return null
     */
    private function _init(&$that)
    {
        if ($this->init_loaded === true) {
            return null;
        }

        $that->document->addStyle(
            array(
                'href'  => $that->view->templateResource('/css/fast_checkout.css'),
                'rel'   => 'stylesheet',
                'media' => 'screen',
            )
        );
        $that->document->addScript($that->view->templateResource('/js/credit_card_validation.js'));
        $that->loadLanguage('fast_checkout/fast_checkout');
        $this->init_loaded = true;
    }

    //if generic checkout process - remove sign of fast checkout
    public function onControllerPagesCheckoutConfirm_InitData()
    {
        $this->baseObject->session->data['fast-checkout'] = false;
    }

    //forward to fast_checkout success page if checkout was simple
    public function onControllerPagesCheckoutSuccess_ProcessData()
    {
        $that =& $this->baseObject;
        if ($that->session->data['fast-checkout']) {
            header('Location: '.$that->html->getSecureURL('checkout/fast_checkout_success',
                    '&viewport=window&order_id='.$that->session->data['processed_order_id']));
            exit;
        }
    }

    public function onControllerCommonPage_InitData() {
        $that = $this->baseObject;
        $cart_key = $that->request->post_or_get('cart_key');

        if ($that->customer && $that->customer->getId()) {
            unset($that->session->data['guest']);
        }

        if ((!$cart_key || empty($cart_key)) && $that->request->get['rt'] === 'checkout/fast_checkout') {
            $cart_key = randomWord(5);
            $that->session->data['cart_key'] = $cart_key;
            redirect($that->html->getSecureURL($this->sc_rt, "&cart_key=".$cart_key));
        }
    }

    public function onControllerCommonPage_UpdateData()
    {
        $that = $this->baseObject;

        if ($that->request->get['rt'] === 'checkout/fast_checkout') {
            $that->processTemplate('common/fast_checkout_page.tpl');
        }
    }

}
