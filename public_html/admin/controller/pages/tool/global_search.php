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

class ControllerPagesToolGlobalSearch extends AController
{
    public $error = [];

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('tool/global_search');
        $this->document->setTitle($this->language->get('heading_title'));

        $searchKeyword = $this->request->post_or_get('search');
        $this->data['error_warning'] = $this->error ['warning'] ?? '';
        $this->data['success'] = $this->session->data ['success'] ?? '';
        unset($this->session->data ['success']);

        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL(
                    'tool/global_search',
                    '&' . http_build_query(['search' => $searchKeyword])
                ),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        $this->data['search_url'] = $this->html->getSecureURL('listing_grid/global_search_result');
        $this->data['search_keyword'] = $searchKeyword;

        $form = new AForm();
        $form->setForm(
            [
                'form_name' => 'global_search',
            ]
        );

        $search_form = [];
        $search_form['id'] = 'global_search';
        $search_form['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'global_search',
                'method' => 'post',
                'action' => $this->html->getSecureURL('tool/global_search'),
            ]
        );
        $search_form['submit'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'submit',
                'text'  => $this->language->get('button_go'),
                'style' => 'button1',
            ]
        );
        $search_form['reset'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'reset',
                'text'  => $this->language->get('button_reset'),
                'style' => 'button2',
            ]
        );
        $search_form['fields']['search'] = $form->getFieldHtml(
            [
                'type'        => 'input',
                'name'        => 'search',
                'value'       => $searchKeyword,
                'placeholder' => $this->language->get('search_everywhere'),
            ]
        );
        $this->data['search_form'] = $search_form;

        // list of js-functions names for initialization all jqgrids
        $this->data['grid_inits'] = [];
        /** @var ModelToolGlobalSearch $mdl */
        $mdl = $this->load->model('tool/global_search');
        if ($this->_validate()) {
            $searchSectionsIcons = $mdl->getSearchSources($searchKeyword);
            $searchSections = array_keys($searchSectionsIcons);
            $searchSectionsNames = [];
            if ($searchSections) {
                $this->data['no_results_message'] = '';
                foreach ($searchSections as $section) {
                    $searchSectionsNames[$section] = $this->language->get("text_" . $section);

                    // set grids for each search category as child
                    $grid_settings = [
                        'table_id'       => $section . '_grid',
                        'url'            => $this->html->getSecureURL(
                            'listing_grid/global_search_result',
                            '&' . http_build_query(
                                [
                                    'search_category' => $section,
                                    'keyword'         => $searchKeyword
                                ]
                            )
                        ),
                        'editurl'        => null,
                        'columns_search' => false,
                        'sortable'       => false,
                        'hidden_head'    => true,
                        'grid_ready'     => "grid_ready('" . $section . '_grid' . "', data);",
                    ];

                    $grid_settings ['colNames'] = ['#', $this->language->get("text_" . $section),];
                    $grid_settings ['colModel'] = [
                        [
                            'name'     => 'num',
                            'index'    => 'num',
                            'width'    => 40,
                            'align'    => 'center',
                            'classes'  => 'search_num',
                            'sortable' => false,
                        ],
                        [
                            'name'     => 'search_result',
                            'index'    => 'search_result',
                            'width'    => 830,
                            'align'    => 'left',
                            'sortable' => false,
                        ],
                    ];
                    $grid_settings ['multiselect'] = "false";
                    $grid_settings ['hoverrows'] = "false";
                    $grid_settings ['altRows'] = "true";
                    $grid_settings ['ajaxsync'] = "false";
                    $grid_settings ['history_mode'] = false;
                    // need to disable initializations all grid on page load
                    $grid_settings ['init_onload'] = false;
                    $this->data['grid_inits'][$section] = 'initGrid_' . $grid_settings ['table_id'];

                    $grid = $this->dispatch('common/listing_grid', [$grid_settings]);
                    $this->data['listing_grid_' . $section] = $grid->dispatchGetOutput();
                }
                $this->data['search_categories'] = $searchSections;
                $this->data['search_categories_icons'] = $searchSectionsIcons;
                $this->data['search_categories_names'] = $searchSectionsNames;
            } else {
                $this->view->assign('no_results_message', $this->language->get('no_results_message'));
                $this->view->assign('search_categories', []);
            }
        }
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/tool/global_search.tpl');
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    /**
     * function check access rights to search results
     *
     * @param string $permissions
     *
     * @return boolean
     * @throws AException
     */
    protected function _validate($permissions = null)
    {
        // check access to global search
        if (!$this->user->canAccess('tool/global_search')) {
            $this->error ['warning'] = $this->language->get('error_permission');
        }
        $this->extensions->hk_ValidateData($this, ['permissions' => $permissions]);
        return !$this->error;
    }
}
