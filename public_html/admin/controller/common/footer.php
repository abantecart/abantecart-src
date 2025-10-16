<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
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
/** @noinspection PhpMultipleClassDeclarationsInspection */

if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerCommonFooter extends AController
{
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('common/header');

        $menu = new AMenu('admin');
        $documentation = $menu->getMenuItem('documentation');
        $support = $menu->getMenuItem('support');
        $mp = $menu->getMenuItem('marketplace');
        $this->view->assign('doc_menu', $documentation);
        $this->view->assign('doc_menu_text', $this->language->get($documentation['item_text']));
        $this->view->assign('support_menu', $support);
        $this->view->assign('support_menu_text', $this->language->get($support['item_text']));
        $this->view->assign('mp_menu', $mp);
        $this->view->assign('mp_menu_text', $this->language->get($mp['item_text']));
        $this->view->assign('new_orders', $this->language->get('new_orders'));
        $this->view->assign('recent_customers', $this->language->get('recent_customers'));

        $this->view->assign('text_footer_left', $this->language->getAndReplace('text_footer_left', replaces: date('Y')));
        $this->view->assign('text_footer', $this->language->getAndReplace('text_footer', replaces: date('Y')) . ' ' . VERSION);

        if (!$this->user->isLogged()
            || !isset($this->request->get['token'])
            || !isset($this->session->data['token'])
            || ($this->request->get['token'] != $this->session->data['token'])
        ) {
            $this->view->assign('logged', '');
            $this->view->assign('home', $this->html->getSecureURL('index/login', '', true));
        } else {
            $this->view->assign(
                'logged',
                $this->language->getAndReplace('text_logged', replaces: $this->user->getUserName())
            );
            $this->view->assign('username', $this->user->getUserName());
            if ($this->user->getLastLogin()) {
                $this->view->assign(
                    'last_login',
                    $this->language->getAndReplace('text_last_login', replaces: $this->user->getLastLogin())
                );
            } else {
                $this->view->assign(
                    'last_login',
                    $this->language->getAndReplace('text_welcome', replaces: $this->user->getUserName())
                );
            }
            $this->view->assign('account_edit', $this->html->getSecureURL('index/edit_details', '', true));

            $footerAntMessage = $this->messages->getANTMessageByPlaceholder('footer');
            if ($footerAntMessage) {
                $this->view->addHookVar(
                    'footer_bottom',
                    '<div class="footer_ant_banner">' . $footerAntMessage['html'] . '</div>'
                );
                $this->messages->markViewedANT($footerAntMessage['id'], '*');
            }
            $rightAntMessage = $this->messages->getANTMessageByPlaceholder('right');
            if ($rightAntMessage) {
                $this->view->addHookVar(
                    'rightpanel_tabpanes_before',
                    '<div class="right_ant_banner">' . $rightAntMessage['html'] . '</div>'
                );
                $this->messages->markViewedANT($rightAntMessage['id'], '*');
            }
        }

        $this->processTemplate('common/footer.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
