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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

/**
 * Class ControllerPagesExtensionExtensionsStore
 *
 * @property ModelToolMPAPI $model_tool_mp_api
 */
class ControllerPagesExtensionExtensionsStore extends AController
{
    public $data;

    public function main()
    {
        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->addStyle(
            [
                'href' => RDIR_TEMPLATE.'stylesheet/marketplace.css',
                'rel'  => 'stylesheet',
            ]
        );

        $this->document->initBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('extension/extensions_store'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        if (isset($this->session->data['error'])) {
            $this->data['error_warning'] .= $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $this->data['error_warning'] = '';
        }

        $this->loadModel('tool/mp_api');
        $request_data = $this->request->get;

        $this->data['my_extensions'] = $this->html->getSecureURL('extension/extensions');
        //marketplace related
        //connection to marketplace
        $mp_token = $this->config->get('mp_token');
        if ($mp_token) {
            $this->data['mp_connected'] = true;
            $this->data['my_extensions'] = $this->html->getSecureURL(
                'extension/extensions_store',
                '&purchased_only=1'
            );
        }
        if ($request_data['purchased_only']) {
            $this->data['my_extensions_shown'] = true;
        }

        if (!has_value($request_data['sidx'])) {
            $request_data['sidx'] = 'date_modified';
        }
        if (!has_value($request_data['sord'])) {
            $request_data['sord'] = 'desc';
        }
        if (has_value($request_data['limit'])) {
            $request_data['rows'] = $request_data['limit'];
        }
        $token_param = "";
        if (has_value($mp_token)) {
            $request_data['mp_token'] = $mp_token;
            $token_param = "&mp_token=".$mp_token;
        }

        $return_url = base64_encode($this->html->getSecureURL('tool/extensions_store/connect'));
        $mp_params = '?rt=account/authenticate&return_url='.$return_url;
        $mp_params .= '&store_id='.UNIQUE_ID;
        $mp_params .= '&store_url='.HTTPS_SERVER;
        $mp_params .= '&store_version='.VERSION;
        $this->data['amp_connect_url'] = $this->model_tool_mp_api->getMPURL().$mp_params;
        $this->data['amp_disconnect_url'] = $this->html->getSecureURL('tool/extensions_store/disconnect');

        $return_url = base64_encode($this->html->getSecureURL('tool/extensions_store/install'));
        $mp_params = '?rt=r/product/product&return_url='.$return_url;
        $mp_params .= '&store_id='.UNIQUE_ID;
        $mp_params .= '&store_url='.HTTPS_SERVER;
        $mp_params .= '&store_version='.VERSION;
        $this->data['amp_product_url'] = $this->model_tool_mp_api->getMPURL().$mp_params.$token_param;

        $return_url = base64_encode($this->html->getSecureURL('tool/extensions_store/install'));
        $mp_params = '?rt=r/checkout/purchase&return_url='.$return_url;
        $mp_params .= '&store_id='.UNIQUE_ID;
        $mp_params .= '&store_url='.HTTPS_SERVER;
        $mp_params .= '&store_version='.VERSION;
        $this->data['amp_order_url'] = $this->model_tool_mp_api->getMPURL().$mp_params.$token_param;

        $result = $this->model_tool_mp_api->processRequest($request_data);
        $this->data['content'] = $result;
        $this->data['install_url'] = $this->html->getSecureURL('tool/package_installer/download', '');
        $this->data['edit_url'] = $this->html->getSecureURL('extension/extensions/edit', '');

        $this->data['marketplace_url'] = $this->model_tool_mp_api->getMPURL();
        $remote_store_product_url = $this->model_tool_mp_api->getMPURL()
            .'?mp_token='.$mp_token
            .'&mp_hash='.$mp_token
            .'&return_url='.$return_url;
        $this->data['remote_store_product_url'] = $remote_store_product_url;

        $form = new AForm('ST');
        $form->setForm(
            [
                'form_name' => 'extension_store_search',
            ]
        );

        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'extension_store_search',
                'action' => $this->html->getSecureURL('extension/extensions_store'),
                'method' => 'get',
            ]
        );
        $this->data['form']['input'] = $form->getFieldHtml(
            [
                'type'        => 'input',
                'name'        => 'keyword',
                'value'       => $request_data['keyword'],
                'style'       => 'pull-left',
                'placeholder' => $this->language->get('text_search'),
            ]
        );
        $this->data['form']['input'] .= $form->getFieldHtml(
            [
                'type'  => 'hidden',
                'name'  => 'rt',
                'value' => $request_data['rt'],
            ]
        );
        $this->data['form']['input'] .= $form->getFieldHtml(
            [
                'type'  => 'hidden',
                'name'  => 's',
                'value' => $request_data['s'],
            ]
        );
        $this->data['form']['input'] .= $form->getFieldHtml(
            [
                'type'  => 'hidden',
                'name'  => 'token',
                'value' => $request_data['token'],
            ]
        );

        if ($result['products']['rows']) {
            $uri = '&limit='.$result['products']['limit'];
            if (has_value($request_data['keyword'])) {
                $uri .= '&keyword='.$request_data['keyword'];
            }
            if (has_value($request_data['category_id'])) {
                $uri .= '&category_id='.$request_data['category_id'];
            }
            if (has_value($request_data['manufacturer_id'])) {
                $uri .= '&manufacturer_id='.$request_data['manufacturer_id'];
            }
            if (has_value($request_data['purchased_only'])) {
                $uri .= '&purchased_only='.$request_data['purchased_only'];
            }
            $sort_order = '&sidx='.$result['products']['sidx'].'&sord='.strtoupper($result['products']['sord']);

            $this->data['pagination_bootstrap'] = HtmlElementFactory::create(
                [
                    'type'       => 'Pagination',
                    'name'       => 'pagination',
                    'text'       => $this->language->get('text_pagination'),
                    'text_limit' => '',
                    'total'      => $result['products']['records'],
                    'page'       => $result['products']['page'],
                    'limit'      => $result['products']['limit'],
                    'url'        => $this->html->getSecureURL(
                        'extension/extensions_store', $uri.$sort_order.'&page={page}', '&encode'
                    ),
                    'size_class' => 'sm',
                    'no_perpage' => true,
                    'style'      => 'pagination',
                ]
            );

            $sorts = [];

            $sorts['&sidx=price&sord=ASC'] = $this->language->get('text_sorting_price_asc');
            $sorts['&sidx=price&sord=DESC'] = $this->language->get('text_sorting_price_desc');
            $sorts['&sidx=rating&sord=DESC'] = $this->language->get('text_sorting_rating_desc');
            $sorts['&sidx=rating&sord=ASC'] = $this->language->get('text_sorting_rating_asc');
            $sorts['&sidx=date_modified&sord=ASC'] = $this->language->get('text_sorting_date_modified_desc');
            $sorts['&sidx=date_modified&sord=DESC'] = $this->language->get('text_sorting_date_modified_asc');

            $this->data['listing_url'] = $this->html->getSecureURL('extension/extensions_store', $uri);
            $this->data['sorting'] = HtmlElementFactory::create(
                [
                    'type'    => 'selectbox',
                    'name'    => 'sorting',
                    'value'   => $sort_order,
                    'options' => $sorts,
                ]
            );
        }

        $this->data['my_account'] = $this->model_tool_mp_api->getMPURL()
            .'?rt=account/account&mp_token='.$mp_token
            .'&mp_hash='.$mp_token;
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/extension/extensions_store.tpl');
    }
}