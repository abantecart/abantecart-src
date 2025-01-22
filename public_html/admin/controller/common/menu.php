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

class ControllerCommonMenu extends AController
{
    protected $permissions = [];
    protected $groupID;
    const TOP_ADMIN_GROUP = 1;

    public function main()
    {
        $this->loadLanguage('common/header');
        $menu = new AMenu('admin');
        $this->data['menu_items'] = [];
        $items = $menu->getMenuItems();
        foreach ($items as $row) {
            $this->data['menu_items'] = array_merge($this->data['menu_items'], $row);
        }

        $this->loadModel('user/user_group');
        $this->groupID = (int)$this->user->getUserGroupId();
        if ($this->groupID !== self::TOP_ADMIN_GROUP) {
            $user_group = $this->model_user_user_group->getUserGroup($this->groupID);
            $this->permissions = $user_group['permission'];
        }

        //use to update data before render
        $this->extensions->hk_ProcessData($this);

        $this->view->assign(
            'menu_html',
            renderAdminMenu(
                $this->_buildMenuArray($this->data['menu_items']),
                0,
                $this->request->get_or_post('rt')
            )
        );
        $this->processTemplate('common/menu.tpl');
        //use to update data before render
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    /**
     * @param array $menu_items
     *
     * @return array
     * @throws AException
     */
    protected function _buildMenuArray($menu_items = [])
    {
        $dashboard = [
            'dashboard' => [
                'icon' => '<i class="fa fa-home"></i>',
                'id'   => 'dashboard',
                'rt'   => 'index/home',
                'href' => $this->html->getSecureURL('index/home'),
                'text' => $this->language->get('text_dashboard'),
            ],
        ];
        return array_merge($dashboard, $this->_getChildItems('', $menu_items));
    }

    /**
     * @param $item_id
     * @param array $menu_items
     *
     * @return array
     * @throws AException
     */
    protected function _getChildItems($item_id, $menu_items)
    {
        $rm = new AResourceManager();
        $rm->setType('image');
        $result = [];
        foreach ($menu_items as $item) {
            if ($item['parent_id'] == $item_id && $item['item_id']) {
                $rt = $menu_link = '';
                $http_rt = false;
                $isLink = str_starts_with($item['item_url'], 'http');
                if ($item['language']) {
                    $this->loadLanguage($item ['language'], 'silent');
                }
                $children = $this->_getChildItems($item['item_id'], $menu_items);

                if ($isLink) {
                    $menu_link = $item['item_url'];
                    $rt = trim($item['item_url']);
                    $http_rt = true;
                    $link_key_name = "href";
                } elseif(str_starts_with($item['item_url'], 'window.open')) {
                    preg_match('/(\'|")(.*?)(\'|")/', $item['item_url'], $matches);
                    if(!str_ends_with(parse_url($matches[2])['host'],base64_decode('LmFiYW50ZWNhcnQuY29t'))){
                        continue;
                    }
                    $menu_link = $item['item_url'];
                    $rt = trim($item['item_url']);
                    $link_key_name = "onclick";
                } else if ($item['item_url']) {
                    //rt based link, need to save rt
                    $menu_link = $this->html->getSecureURL($item['item_url'], '', true);
                    $rt = trim($item['item_url']);
                    $link_key_name = "href";
                }

                $icon = $rm->getResource($item ['item_icon_rl_id']);

                $iconHtml = $icon['resource_code']
                    ?: (
                    $icon['resource_path']
                        ? $this->html->buildResourceImage(
                        [
                            'url'    => $rm->getResizedImageURL($icon, 19, 19),
                            'width'  => 19,
                            'height' => 19
                        ]
                    )
                        : ''
                    );
                $temp = [
                    'id'           => $item['item_id'],
                    $link_key_name => $menu_link,
                    'text'         => $this->language->get($item ['item_text']),
                    'icon'         => $iconHtml,
                ];

                if ($rt) {
                    $temp['rt'] = $rt;
                }

                $controller_rt = $this->getControllerRt($rt);

                if ($children) {
                    $temp['children'] = $children;
                } elseif (!$rt) {
                    //skip empty parents
                    continue;
                } elseif ($this->groupID !== self::TOP_ADMIN_GROUP
                    && !$http_rt
                    && !$this->permissions['access'][$controller_rt]
                ) {
                    //skip top menus with no access permission
                    continue;
                }

                $result[$item['parent_id'] . '~' . $item['item_id']] = $temp;
            }
        }

        return $result;
    }

    /**
     * @param string $rt
     *
     * @return false|string
     */
    protected function getControllerRt($rt)
    {
        if (!$rt || str_starts_with($rt, 'http')) {
            return false;
        }
        $split = explode('/', $rt);
        return $split[0] . '/' . $split[1];
    }
}
