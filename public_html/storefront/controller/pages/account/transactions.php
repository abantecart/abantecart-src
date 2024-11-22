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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerPagesAccountTransactions extends AController
{
    /**
     * Main Controller function to show transaction history.
     * Note: Regular orders are considered in the transactions.
     */
    public function main()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->html->getSecureURL('account/transactions');
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
                'href'      => $this->html->getSecureURL('account/transactions'),
                'text'      => $this->language->get('text_transactions'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $this->loadModel('account/customer');

        $trans_total = $this->model_account_customer->getTotalTransactions();

        $balance = $this->customer->getBalance();
        $this->data['balance_amount'] = $this->currency->format($balance);

        if ($trans_total) {
            $this->data['action'] = $this->html->getSecureURL('account/transactions');

            $page = (int)$this->request->get['page'] ?: 1;
            $limit = (int) $this->request->get['limit'] ?: $this->config->get('config_catalog_limit');

            $trans = [];

            $results = $this->model_account_customer->getTransactions(($page - 1) * $limit, $limit);

            foreach ($results as $result) {
                $trans[] = [
                    'customer_transaction_id' => $result['customer_transaction_id'],
                    'order_id'                => $result['order_id'],
                    'section'                 => $result['section'],
                    'credit'                  => $this->currency->format($result['credit']),
                    'debit'                   => $this->currency->format($result['debit']),
                    'transaction_type'        => $result['transaction_type'],
                    'description'             => $result['description'],
                    'date_added'              => dateISO2Display($result['date_added'], $this->language->get('date_format_short')),
                ];
            }

            $this->data['transactions'] = $trans;

            $this->data['pagination_bootstrap'] = $this->html->buildElement(
                [
                    'type'       => 'Pagination',
                    'name'       => 'pagination',
                    'text'       => $this->language->get('text_pagination'),
                    'text_limit' => $this->language->get('text_per_page'),
                    'total'      => $trans_total,
                    'page'       => $page,
                    'limit'      => $limit,
                    'url'        => $this->html->getSecureURL(
                        'account/transactions',
                        '&limit='.$limit.'&page=--page--'
                    ),
                    'style'      => 'pagination',
                ]
            );
        } else {
            $this->data['text_error'] = $this->language->get('text_error', 'account/transactions');
        }

        $this->data['continue'] = $this->html->getSecureURL('account/account');
        $this->view->setTemplate('pages/account/transactions.tpl');

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