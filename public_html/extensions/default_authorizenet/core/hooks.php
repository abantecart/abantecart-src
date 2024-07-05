<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */

class ExtensionDefaultAuthorizeNet extends Extension
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
        if (IS_ADMIN && $current_ext_id == 'default_authorizenet' && $this->baseObject_method == 'edit') {
            $html = '<a class="btn btn-white tooltips" '
                        .'target="_blank" '
                        .'href="https://account.authorize.net/signUpNow?resellerID=34073" '
                        .'title="Signup with Authorize.net">
                        <i class="fa fa-user-plus fa-lg"></i> New Account
                    </a>';
            $that->view->addHookVar('extension_toolbar_buttons', $html);
        }
    }

    //Hook to extension edit in the admin
    public function onControllerPagesSaleOrderSummary_UpdateData()
    {
        $that = $this->baseObject;
        if ( IS_ADMIN !== true) {
            return null;
        }
        $order_info = $that->model_sale_order->getOrder($that->request->get['order_id']);
        if($order_info['payment_method_key'] != 'default_authorizenet'){
            return null;
        }
        $method_info = unserialize($order_info['payment_method_data']);

        $view_order_details = $that->view->getData('order');
        if($method_info) {
            $view_order_details['payment_method'] = $view_order_details['payment_method']
                .'<br>'
                .($method_info['authorizenet_transaction_id']
                    ? 'Transaction ID: '.$method_info['authorizenet_transaction_id'].'('.$method_info['cc_type'].')'
                    : '');
            $that->view->assign('order', $view_order_details);
        }
    }
}