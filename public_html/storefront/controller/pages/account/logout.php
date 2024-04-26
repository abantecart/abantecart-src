<?php

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2023 Belavier Commerce LLC

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

class ControllerPagesAccountLogout extends AController
{
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if ($this->customer->isLogged() || $this->customer->isUnauthCustomer()) {
            $this->customer->logout();
            $this->cart->clear();

            unset(
                $this->session->data['shipping_address_id'],
                $this->session->data['shipping_method'],
                $this->session->data['shipping_methods'],
                $this->session->data['payment_address_id'],
                $this->session->data['payment_method'],
                $this->session->data['payment_methods'],
                $this->session->data['comment'],
                $this->session->data['order_id'],
                $this->session->data['coupon'],
                $this->session->data['merchant'],
                $this->session->data['used_balance'],
                $this->session->data['used_balance_full'],
                $this->session->data['csrftoken'],
                $this->session->data['fc']
            );

            if ($this->config->get('config_tax_store')) {
                $country_id = $this->config->get('config_country_id');
                $zone_id = $this->config->get('config_zone_id');
            } else {
                $country_id = $zone_id = 0;
            }
            $this->tax->setZone($country_id, $zone_id);
            $this->extensions->hk_ProcessData($this, __FUNCTION__);
            setcookie(SESSION_ID, '', 1);
            redirect($this->html->getSecureURL('account/logout'));
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $this->document->resetBreadcrumbs();

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getHomeURL(),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('account/account'),
                'text'      => $this->language->get('text_account'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('account/logout'),
                'text'      => $this->language->get('text_logout'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $this->view->assign('continue', $this->html->getHomeURL());
        $continue = $this->html->buildElement(
            [
                'type'  => 'button',
                'name'  => 'continue_button',
                'text'  => $this->language->get('button_continue'),
                'style' => 'button',
            ]
        );
        $this->view->assign('continue_button', $continue);

        if ($this->config->get('embed_mode') == true) {
            //load special headers
            $this->addChild('responses/embed/head', 'head');
            $this->addChild('responses/embed/footer', 'footer');
            $this->processTemplate('embed/common/success.tpl');
        } else {
            $this->processTemplate('common/success.tpl');
        }

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
