<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/

/**
 * Class ExtensionDefaultPpPro
 */
class ExtensionDefaultLocalDelivery extends Extension
{



    public function onControllerPagesCheckoutGuestStep1_ValidateData()
    {
        $that =& $this->baseObject;
        if( !$that->config->get('default_local_delivery_status') ) {
            return;
        }

        if( $that->request->is_POST() && $that->cart->hasShipping()){
            $that->loadLanguage('default_local_delivery/default_local_delivery');
            $telephone = preg_replace('/[^0-9\+]/', '', $that->request->post['telephone']);
            if(!$telephone){
                $that->error['telephone'] = $that->language->get('error_telephone');
            }else{
                $that->request->post['telephone'] = $telephone;
            }
        }
    }
    public function onControllerPagesCheckoutGuestStep1_UpdateData()
    {
        $that =& $this->baseObject;
        if( !$that->config->get('default_local_delivery_status')
            ||  !$that->cart->hasShipping()
        ) {
            return;
        }

        $form = $that->view->getData('form');
        $form['fields']['general']['telephone']->required = true;
        $that->view->assign('form', $form);
    }

    public function onControllerPagesCheckoutConfirm_InitData()
    {
        $that =& $this->baseObject;
        if(!$that->config->get('default_local_delivery_status')
            || $that->session->data['shipping_method']['id'] != 'default_local_delivery.default_local_delivery'
            || !$that->cart->hasShipping()
        ) {
            return;
        }
        $that->loadLanguage('default_local_delivery/default_local_delivery');
        if( $that->request->is_POST() && $that->request->post['telephone'] ){
            $telephone = preg_replace('/[^0-9\+]/', '', $that->request->post['telephone']);
            if($telephone) {
                $that->db->query(
                    "UPDATE ".$that->db->table('orders')."
                    SET telephone = '".$that->db->escape($telephone)."'
                    WHERE order_id = ".(int)$that->session->data['order_id']
                );

                if ($that->customer->isLogged()) {
                    $that->db->query(
                        "UPDATE ".$that->db->table('customers')."
                        SET telephone = '".$that->db->escape($telephone)."'
                        WHERE customer_id = ".(int)$that->customer->getId()
                    );
                    $that->cache->remove('customer');
                    //re-init customer
                    $that->customer = new ACustomer(Registry::getInstance());
                } else {
                    $that->session->data['guest']['telephone'] = $telephone;
                }
            }
        }
    }
    public function onControllerPagesCheckoutConfirm_UpdateData()
    {
        $that =& $this->baseObject;
        if(!$that->config->get('default_local_delivery_status')
            || $that->session->data['shipping_method']['id'] != 'default_local_delivery.default_local_delivery'
            || !$that->cart->hasShipping()
        ) {
            return;
        }

        $data = array();
        $order_info = $that->model_checkout_order->getOrder($that->session->data['order_id']);
        $that->loadLanguage('checkout/address');

        $data['text_telephone'] = $that->language->get('entry_telephone');
        $data['text_apply'] = $that->language->get('text_apply');
        $data['telephone'] = $that->html->buildInput(
            array(
                'name' => 'telephone',
                'value' => $order_info['telephone'],
                'required' => true
            )
        );

        $data['apply_telephone_button'] = $that->html->buildButton(
                    array(
                        'name' => 'apply',
                        'text' => 'apply',
                    )
                );

        if(!$order_info['telephone']){
            $data['error_telephone'] = $that->language->get('error_telephone');
        }

        $view = new AView(Registry::getInstance(), 0);
        $view->batchAssign($data);
        $that->view->addHookVar('order_attributes', $view->fetch('pages/checkout/default_local_delivery_fields.tpl'));
    }


    public function onControllerResponsesCheckoutPay_InitData(){
        /** @var ControllerResponsesCheckoutPay $that */
        $that = $this->baseObject;
        if( !$that->config->get('fast_checkout_status')){
            return;
        }
        $shipping_method = $that->session->data['fast_checkout'][$that->getCartKey()]['shipping_method'];
        if($shipping_method['id'] == 'default_local_delivery.default_local_delivery'){
            //show comment field for local delivery anyway
            $that->config->get('fast_checkout_show_order_comment_field');
        }
    }

    public function onControllerResponsesCheckoutPay_UpdateData(){
        /** @var ControllerResponsesCheckoutPay $that */
        $that = $this->baseObject;
        if( !$that->config->get('fast_checkout_status')){
            return;
        }
        $shipping_method = $that->session->data['fast_checkout'][$that->getCartKey()]['shipping_method'];
        if($shipping_method['id'] != 'default_local_delivery.default_local_delivery'){
            return;
        }

        if(!$that->config->get('fast_checkout_require_phone_number')) {
            $view = new AView(Registry::getInstance(), 0);
            $view->batchAssign($that->data);
            $view->batchAssign($that->view->getData());
            $view->assign('fast_checkout_text_apply', $that->language->get('fast_checkout_text_apply'));
            $html = $view->fetch('pages/checkout/fast_checkout_fields.tpl');
            $that->view->addHookVar('payment_form_fields', $html);
        }
    }
}
