<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2026 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerPagesAccountHistory extends AController
{
    /**
     * Main controller function to show order history
     */
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->html->getSecureURL('account/history');
            redirect($this->html->getSecureURL('account/login'));
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
                'href'      => $this->html->getSecureURL('account/history'),
                'text'      => $this->language->get('text_history'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        /** @var ModelAccountOrder $oMdl */
        $oMdl = $this->loadModel('account/order');
        $this->data['continue'] = $this->html->getSecureURL('account/account');

        $page = $this->request->get['page'] ? : 1;

        if (isset($this->request->get['limit'])) {
            $limit = (int) $this->request->get['limit'];
            $limit = min($limit, 50);
        } else {
            $limit = (int)$this->config->get('config_catalog_limit');
        }

        $orders = [];
        $results = $oMdl->getOrders(($page - 1) * $limit, $limit);
        if ($results) {
            $this->data['action'] = $this->html->getSecureURL('account/history');
            $productTotals = $oMdl->getTotalOrderProductsByOrderId(array_column($results,'order_id'));
            foreach ($results as $result) {
                $orders[] = array_merge(
                    $result,
                    [
                        'name'       => $result['firstname'].' '.$result['lastname'],
                        'datetime'   => $result['date_added'],
                        'date_added' => dateISO2Display(
                            $result['date_added'],
                            $this->language->get('date_format_short')
                        ),
                        'products'   => $productTotals[$result['order_id']],
                        'total'      => $this->currency->format(
                            $result['total'],
                            $result['currency'],
                            $result['value']
                        ),
                        'href'       => $this->html->getSecureURL(
                            'account/order_details',
                            '&order_id='.$result['order_id']
                        ),
                        'button'     => $this->html->buildElement(
                            [
                                'type'  => 'button',
                                'name'  => 'button_edit',
                                'text'  => $this->language->get('button_view'),
                                'style' => 'btn-default',
                                'icon'  => 'fa fa-info',
                                'attr'  => 'data-order-id="'.$result['order_id'].'" '
                                    .' onclick = "viewOrder('.$result['order_id'].');" ',
                            ]
                        ),
                    ]
                );
            }

            $this->data['order_url'] = $this->html->getSecureURL('account/order_details');
            $this->data['orders'] = $orders;

            $this->data['pagination_bootstrap'] = $this->html->buildElement(
                [
                    'type'       => 'Pagination',
                    'name'       => 'pagination',
                    'text'       => $this->language->get('text_pagination'),
                    'text_limit' => $this->language->get('text_per_page'),
                    'total'      => $results[0]['total_num_rows'],
                    'page'       => $page,
                    'limit'      => $limit,
                    'url'        => $this->html->getSecureURL('account/history', '&limit='.$limit.'&page=--page--'),
                    'style'      => 'pagination',
                ]
            );

            $this->view->setTemplate('pages/account/history.tpl');
        } else {
            $this->view->setTemplate('pages/error/not_found.tpl');
        }

        $this->data['button_continue'] = $this->html->buildElement(
            [
                'type'  => 'button',
                'name'  => 'continue_button',
                'text'  => $this->language->get('button_continue'),
                'style' => 'button',
            ]
        );

        $this->view->batchAssign($this->data);
        $this->processTemplate();
        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}