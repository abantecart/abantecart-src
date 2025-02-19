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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerPagesReportSaleCoupons extends AController
{
    public function main()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $grid_settings = [
            //id of grid
            'table_id'       => 'report_coupons_grid',
            // url to load data from
            'url'            => $this->html->getSecureURL('listing_grid/report_sale/coupons'),
            // default sort column
            'sortname'       => 'date_end',
            'columns_search' => false,
            'multiselect'    => 'false',
        ];

        $form = new AForm();
        $form->setForm(
            [
                'form_name' => 'report_coupons_grid_search',
            ]
        );

        $this->data['grid_search_form'] = [];
        $this->data['grid_search_form']['id'] = 'report_coupons_grid_search';
        $this->data['grid_search_form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'report_coupons_grid_search',
                'action' => '',
            ]
        );
        $this->data['grid_search_form']['submit'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'submit',
                'text'  => $this->language->get('button_go'),
                'style' => 'button1',
            ]
        );
        $this->data['grid_search_form']['fields']['date_start'] = $form->getFieldHtml(
            [
                'type'       => 'date',
                'name'       => 'date_start',
                'value'      => dateISO2Display(
                    $this->data['date_start'],
                    $this->language->get('date_format_short')
                ),
                'default'    => dateInt2Display(strtotime('-7 day')),
                'dateformat' => format4Datepicker(
                    $this->language->get('date_format_short')
                ),
            ]
        );
        $this->data['grid_search_form']['fields']['date_end'] = $form->getFieldHtml(
            [
                'type'       => 'date',
                'name'       => 'date_end',
                'value'      => dateISO2Display(
                    $this->data['date_end'],
                    $this->language->get('date_format_short')
                ),
                'default'    => dateInt2Display(time()),
                'dateformat' => format4Datepicker(
                    $this->language->get('date_format_short')
                ),
            ]
        );
        $grid_settings['search_form'] = true;

        $grid_settings['colNames'] = [
            $this->language->get('column_coupon_name'),
            $this->language->get('column_code'),
            $this->language->get('column_orders'),
            $this->language->get('column_total'),
            $this->language->get('column_discount_total'),
        ];

        $grid_settings['colModel'] = [
            [
                'name'     => 'coupon_name',
                'index'    => 'coupon_name',
                'width'    => 100,
                'align'    => 'center',
                'sorttype' => 'string',
            ],
            [
                'name'     => 'code',
                'index'    => 'code',
                'width'    => 100,
                'align'    => 'center',
                'sorttype' => 'string',
            ],
            [
                'name'  => 'orders',
                'index' => 'orders',
                'width' => 100,
                'align' => 'center',
            ],
            [
                'name'  => 'total',
                'index' => 'total',
                'width' => 110,
                'align' => 'center',
            ],
            [
                'name'  => 'discount_total',
                'index' => 'discount_total',
                'width' => 110,
                'align' => 'center',
            ],
        ];

        $grid = $this->dispatch('common/listing_grid', [$grid_settings]);
        $this->view->assign('listing_grid', $grid->dispatchGetOutput());
        $this->view->assign('search_form', $this->data['grid_search_form']);

        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->initBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('report/sale/coupon'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        $this->processTemplate('pages/report/sale/coupons.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}