<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

/** @noinspection PhpUndefinedClassInspection */

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

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

    /**
     * @return bool
     */
    protected function isEnabled()
    {
        return ($this->baseObject->config->get('fast_checkout_status'));
    }

    /**
     * @throws AException
     */
    public function onControllerPagesProductProduct_InitData(){
        $this->baseObject->loadLanguage('fast_checkout/fast_checkout');
    }

    // add button BUY-NOW to sf product page
    public function onControllerPagesProductProduct_UpdateData(){
        $that = $this->baseObject;
        if(!$that->config->get('fast_checkout_buy_now_status')){
            return;
        }
        $data= [];
        $data['button_add_to_cart'] = $that->language->get('button_add_to_cart');
        $data['text_buynow'] = $that->language->get('fast_checkout_buy_now');
        $data['buynow_url'] = $that->html->getSecureURL('checkout/fast_checkout','&single_checkout=1');
        $data['add_to_cart'] = $that->language->get('button_add_to_cart');

        /** @var AView $view */
        $viewClass = get_class($that->view);

        $view = new $viewClass(Registry::getInstance(),0);
        $view->batchAssign($data);
        $that->view->addHookVar(
            'product_add_to_cart_html',
            $view->fetch('pages/product/add_to_cart_buttons.tpl')
        );
    }

    public function onControllerPagesCheckoutShipping_InitData()
    {
        $that = $this->baseObject;
            unset(
                $that->session->data['used_balance'],
                //remove fast checkout session to prevent wrong cart
                // of prior incomplete checkout process
                $that->session->data['fc']
        );

        if(!$this->isEnabled()){
            return;
        }

        redirect($that->html->getSecureURL($this->sc_rt));
    }

    public function onControllerCommonFooter_UpdateData()
    {
        if(!$this->isEnabled()){
            return;
        }

        $that = $this->baseObject;
        $that->loadLanguage('fast_checkout/fast_checkout');
        if (in_array( $that->request->get['rt'], ['checkout/fast_checkout','checkout/fast_checkout_success'])) {
            $that->processTemplate('responses/includes/page_footer.tpl');
        }

    }

    public function onControllerResponsesEmbedHead_InitData()
    {
        if(!$this->isEnabled()){
            return;
        }

        $that = $this->baseObject;
        if (!$that->config->get('embed_mode')) {
            return null;
        }
        $this->_init($that);
    }

    //replacing of cart ajax url inside head.tpl when checkout mode is fast
    public function onControllerCommonHead_UpdateData()
    {
        /** @var ControllerCommonHead $that */
        $that = $this->baseObject;
        $registry = Registry::getInstance();

        if(!$this->isEnabled() || !$registry){
            return;
        }
        if($registry->get('fast_checkout')){
            $that->view->assign(
                'cart_ajax_url',
                $that->html->getURL('r/product/product/addToCart', '&fc=1')
            );
            $that->document->addScriptBottom( $that->view->templateResource('/js/fast_checkout.js') );
        }
    }

    //replacing of cart when checkout mode is fast
    public function onControllerResponsesProductProduct_InitData()
    {
        if(!$this->isEnabled()){
            return;
        }
        /** @var ControllerResponsesProductProduct $that */
        $that = $this->baseObject;
        $registry = Registry::getInstance();
        if($that->request->get['fc']){
            $cartClassName = get_class($that->cart);
            $registry->set(
                'cart',
                new $cartClassName( $registry, $that->session->data['fc'])
            );
        }
    }

    /**
     * @param AController $that
     *
     * @throws AException
     */
    private function _init($that)
    {
        if ($this->init_loaded === true) {
            return;
        }

        $that->document->addStyle(
            [
                'href'  => $that->view->templateResource('/css/fast_checkout.css'),
                'rel'   => 'stylesheet',
                'media' => 'screen',
            ]
        );
        $that->document->addScript($that->view->templateResource('/js/credit_card_validation.js'));
        $that->loadLanguage('fast_checkout/fast_checkout');
        $this->init_loaded = true;
    }

    //forward to fast_checkout success page if checkout was simple
    public function onControllerPagesCheckoutSuccess_ProcessData()
    {
        if(!$this->isEnabled()){
            return;
        }

        $that =& $this->baseObject;
        header('Location: '.$that->html->getSecureURL(
            'checkout/fast_checkout_success',
            '&viewport=window&order_id='.$that->session->data['processed_order_id'])
        );
        exit;
    }

    public function onControllerCommonPage_UpdateData()
    {
        if(!$this->isEnabled()){
            return;
        }

        $that = $this->baseObject;

        if ($that->request->get['rt'] === 'checkout/fast_checkout') {
            $that->processTemplate('common/fast_checkout_page.tpl');
        }
    }

    public function onControllerPagesAccountEdit_InitData()
    {
        if(!$this->isEnabled()){
            return;
        }

        /** @var ControllerPagesAccountEdit $that */
        $that = $this->baseObject;
        //show error message if empty phone
        if ($that->request->is_GET()
            && isset($that->request->get['telephone'])
            && $that->customer->isLogged()
            && $that->config->get('fast_checkout_require_phone_number')
        ) {
            $that->loadLanguage('account/edit');
            $that->error['telephone'] = $that->language->get('error_telephone');
        }
    }

    public function onControllerPagesCheckoutGuestStep1_InitData()
    {
        $that = $this->baseObject;
        unset($that->session->data['fc']);
        if(!$this->isEnabled()){
            return;
        }
        redirect($that->html->getSecureURL('checkout/fast_checkout'));
    }

    public function onControllerPagesAccountLogin_ProcessData()
    {
        $this->onControllerPagesAccountLogout_UpdateData();
    }

    public function onControllerPagesAccountCreate_InitData()
    {
        $this->onControllerPagesAccountLogout_UpdateData();
    }

    public function onControllerPagesAccountLogout_UpdateData()
    {
        if(!$this->isEnabled()){
            return;
        }

        $that = $this->baseObject;
        unset( $that->session->data['fc'] );
    }

    public function onControllerPagesCheckoutPayment_InitData()
    {
        $that = $this->baseObject;
        unset( $that->session->data['fc'] );
    }

}
