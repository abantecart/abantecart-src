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

/**
 * Class ControllerCommonHeader
 *
 * @property ModelToolOnlineNow $model_tool_online_now
 * @property ModelToolMPAPI     $model_tool_mp_api
 *
 */
class ControllerCommonHeader extends AController
{
    const TOP_ADMIN_GROUP = 1;

    public function main()
    {

        //use to init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->load->helper('html');
        $this->data['breadcrumbs'] = $this->document->getBreadcrumbs();

        if ($this->request->is_POST() && isset($this->request->post['language_code'])) {
            unset($this->session->data['content_language']);
            $this->session->data['language'] = $this->request->post['language_code'];

            if (!empty($this->request->post['redirect'])) {
                redirect($this->request->post['redirect']);
            } else {
                redirect($this->html->getURL('index/home'));
            }
        }

        $this->data['language_code'] = $this->session->data['language'];
        $this->data['languages'] = $this->language->getActiveLanguages();
        $this->data['content_language_id'] = $this->language->getContentLanguageID();
        $this->data['language_settings'] = $this->html->getSecureURL('localisation/language');

        $this->data['new_messages'] = $this->messages->getShortList();
        $this->data['messages_link'] = $this->html->getSecureURL('tool/message_manager');

        $this->data['action'] = $this->html->getSecureURL('index/home');
        $this->data['search_action'] = $this->html->getSecureURL('tool/global_search');

        $this->data['logo'] = $this->config->get('config_logo_'.$this->language->getLanguageID())
                                ?: $this->config->get('config_logo');
        if (is_numeric($this->data['logo'])) {
            $resource = new AResource('image');
            $image_data = $resource->getResource($this->data['logo']);
            $img_sub_path = $image_data['type_name'].'/'.$image_data['resource_path'];
            if (is_file(DIR_RESOURCE.$img_sub_path)) {
                $this->data['logo'] = $img_sub_path;
                $logo_path = DIR_RESOURCE.$img_sub_path;
                //get logo image dimensions
                $info = get_image_size($logo_path);
                $this->data['logo_width'] = $info['width'];
                $this->data['logo_height'] = $info['height'];
            } else {
                $this->data['logo'] = $image_data['resource_code'];
            }
        }

        //redirect after language change
        if (!$this->request->get['rt'] || $this->request->get['rt'] == 'index/home') {
            $this->data['redirect'] = $this->html->getSecureURL('index/home');
            $this->data['home_page'] = true;
        } else {
            $this->data['home_page'] = false;
            $this->data['redirect'] = HTTPS_SERVER.'?'.$_SERVER['QUERY_STRING'];
        }

        if (!$this->user->isLogged()
            || !isset($this->request->get['token'])
            || !isset($this->session->data['token'])
            || ($this->request->get['token'] != $this->session->data['token'])
        ) {
            $this->data['logged'] = '';
            $this->data['home'] = $this->html->getSecureURL('index/login', '', true);
        } else {
            $this->data['home'] = $this->html->getSecureURL('index/home', '', true);
            $this->data['logged'] = sprintf($this->language->get('text_logged'), $this->user->getUserName());
            $this->data['username'] = $this->user->getUserName();
            if ($this->user->getLastLogin()) {
                $this->data['last_login'] = sprintf(
                    $this->language->get('text_last_login'),
                    $this->user->getLastLogin()
                );
            } else {
                $this->data['last_login'] = sprintf(
                    $this->language->get('text_welcome'),
                    $this->user->getUserName()
                );
            }
            $this->data['account_edit'] = $this->html->getSecureURL('index/edit_details', '', true);
            $this->data['im_settings_edit'] = $this->html->getSecureURL(
                                                    'user/user/im',
                                                    '&user_id='.$this->user->getId(), true);
            $this->data['text_edit_notifications'] = $this->language->get('text_edit_notifications');

            $stores = [];
            $this->loadModel('setting/store');
            $results = $this->model_setting_store->getStores();
            foreach ($results as $result) {
                $stores[] = [
                    'name' => $result['name'],
                ];
            }
            $this->data['stores'] = $stores;

            $this->data['logout'] = $this->html->getSecureURL('index/logout');
            $this->data['store'] = $this->config->get('config_url');
            // add dynamic menu based on dataset scheme
            $this->addChild('common/menu', 'menu', 'common/menu.tpl');

            //Get current menu item
            $menu = new AMenu('admin');

            $current_menu = $menu->getMenuByRT($this->request->get['rt']);
            if (!$current_menu && substr_count($this->request->get['rt'], '/') >= 2) {
                $rt_parts = explode('/', $this->request->get['rt']);
                if ($rt_parts) {
                    //remove p,a,r prefixes from rt
                    if (strlen($rt_parts[0]) == 1) {
                        unset($rt_parts[0]);
                    }
                    //try to get icon from parent menu item
                    array_pop($rt_parts);
                    $menu_item = $menu->getMenuByRT(implode('/', $rt_parts));
                    if ($menu_item['item_icon_rl_id']) {
                        $current_menu = ['item_icon_rl_id' => $menu_item['item_icon_rl_id']];
                    }
                }
                unset($rt_parts, $menu_item);
            }
            if ($current_menu ['item_icon_rl_id']) {
                $rm = new AResourceManager();
                $rm->setType('image');
                $resource = $rm->getResource($current_menu ['item_icon_rl_id']);
                $current_menu['icon'] = $resource['resource_code'];
            }
            unset($current_menu['item_icon_rl_id']);
            $this->data['current_menu'] = $current_menu;
        }
        if ($this->user->isLogged()) {
            $ant_message = $this->messages->getANTMessage();
            $this->data['ant'] = $ant_message['html'];
            $this->data['mark_read_url'] = $this->html->getSecureURL(
                                                'common/common/antMessageRead',
                                                '&message_id='.$ant_message['id']
            );
            $this->data['ant_viewed'] = $ant_message['viewed'];
        }
        $this->data['config_voicecontrol'] = $this->config->get('config_voicecontrol');
        $this->data['voicecontrol_setting_url'] = $this->html->getSecureURL('setting/setting/system');
        $this->data['command_lookup_url'] = $this->html->getSecureURL('common/action_commands');
        $this->data['search_suggest_url'] = $this->html->getSecureURL('listing_grid/global_search_result/suggest');
        $this->data['latest_customers_url'] = $this->html->getSecureURL('common/tabs/latest_customers');
        $this->data['latest_orders_url'] = $this->html->getSecureURL('common/tabs/latest_orders');
        $this->data['rl_manager_url'] = $this->html->getSecureURL('tool/rl_manager');

        $this->data['server_date'] = date($this->language->get('date_format_short'));
        $this->data['server_time'] = date($this->language->get('time_format'));

        $this->data['search_everywhere'] = $this->language->get('search_everywhere');
        $this->data['text_all_matches'] = $this->language->get('text_all_matches');
        $this->data['dialog_title'] = $this->language->get('text_quick_edit_form');
        $this->data['button_go'] = $this->html->buildButton(
                                                        [
                                                            'name'  => 'searchform_go',
                                                            'text'  => $this->language->get('button_go'),
                                                            'style' => 'button5',
                                                        ]
        );

        //backwards compatibility from 1.2.1. Can remove this check in the future.
        if (!defined('ENCRYPTION_KEY')) {
            $cm_body = "To be compatible with v".VERSION
                ." add below line to configuration file: <br>\n"
                .DIR_ROOT.'/system/config.php';
            $cm_body .= "<br>\n"."define('ENCRYPTION_KEY', '".$this->config->get('encryption_key')."');\n";
            $this->messages->saveWarning('Compatibility warning for v'.VERSION, $cm_body);
        }

        $permissions = [];
        $this->loadModel('user/user_group');
        $groupID = (int)$this->user->getUserGroupId();
        if ($groupID !== self::TOP_ADMIN_GROUP) {
            $user_group = $this->model_user_user_group->getUserGroup($groupID);
            $permissions = $user_group['permission'];
        }

        //prepare quick stats
        if ($groupID == self::TOP_ADMIN_GROUP || $permissions['access']['sale/customer']) {
            $this->loadModel('tool/online_now');
            $this->data['viewcustomer'] = true;
            $this->data['online_new'] = $this->model_tool_online_now->getTotalTodayOnline('new');
            $this->data['online_registered'] = $this->model_tool_online_now->getTotalTodayOnline('registered');

            $this->loadModel('sale/customer');
            $filter = ['date_added' => date('Y-m-d', time())];
            $this->data['today_customer_count'] = $this->model_sale_customer->getTotalCustomers(['filter' => $filter]);
        } else {
            $this->data['viewcustomer'] = false;
        }

        if ($groupID == self::TOP_ADMIN_GROUP || $permissions['access']['sale/order']) {
            $this->loadModel('report/sale');
            $this->data['vieworder'] = true;

            $data = [
                'filter' => [
                    'order_status' => 'confirmed',
                    'date_start'   => dateISO2Display(date('Y-m-d', time()), $this->language->get('date_format_short')),
                    'date_end'     => dateISO2Display(date('Y-m-d', time()), $this->language->get('date_format_short')),
                ],
            ];

            $today_orders = $this->model_report_sale->getSaleReportSummary($data);
            $this->data['today_order_count'] = $today_orders['orders'];
            $this->data['today_sales_amount'] = $this->currency->format(
                            $today_orders['total_amount'],
                            $this->config->get('config_currency')
            );
        } else {
            $this->data['vieworder'] = false;
        }

        if ($groupID == self::TOP_ADMIN_GROUP || $permissions['access']['catalog/review']) {
            $this->loadModel('catalog/review');
            $this->data['viewreview'] = true;
            $this->data['today_review_count'] = $this->model_catalog_review->getTotalToday();
        } else {
            $this->data['viewreview'] = false;
        }

        $this->view->batchAssign($this->data);
        $this->processTemplate('common/header.tpl');
        //use to update data before render
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}