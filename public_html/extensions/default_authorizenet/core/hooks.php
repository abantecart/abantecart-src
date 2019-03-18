<?php

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
        if (IS_ADMIN && $current_ext_id == 'authorizenet' && $this->baseObject_method == 'edit') {
            $html = '<a class="btn btn-white tooltips" '
                        .'target="_blank" '
                        .'href="https://www.authorize.net" '
                        .'title="Visit authorizenet">
                        <i class="fa fa-external-link fa-lg"></i>
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