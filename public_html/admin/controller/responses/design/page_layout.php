<?php /** @noinspection SqlResolve */

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

class ControllerResponsesDesignPageLayout extends AController
{
    public $errors = [];
    public function main()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('design/layout');
        $this->view->batchAssign($this->language->getASet());

        $form = new AForm('ST');
        $form->setForm(['id' => 'pageLayoutFrm']);

        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'pageLayoutFrm',
                'action' => $this->html->getSecureURL('r/design/page_layout/create'),
                'attr'   => 'data-confirm-exit="true"  class="aform form-horizontal"',
            ]
        );
        $this->data['form']['submit'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'submit',
                'text'  => $this->language->get('button_insert'),
            ]
        );

        $layout = new ALayoutManager($this->request->get['tmpl_id']);

        $this->data['form']['fields']['template'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'tmpl_id',
                'options' => $layout->getTemplateList(),
                'value'   => $this->request->get['tmpl_id'],
                'require' => true
            ]
        );
        $this->data['entry_template'] = $this->language->get('text_select_template');

        $this->data['form']['fields']['page_name'] = $form->getFieldHtml(
            [
                'type'    => 'input',
                'name'    => 'page_name',
                'required' => true
            ]
        );
        $this->data['form']['fields']['page_title'] = $form->getFieldHtml(
            [
                'type'    => 'input',
                'name'    => 'page_title',
            ]
        );
        $routes = $layout->getExtensionsPageRoutes();
        $options = $disabled_options = [];
        foreach($routes as $extId=> $rts){
            $options[$extId] = mb_strtoupper($extId);
            $disabled_options[$extId] = $extId;
            foreach($rts as $rt){
                $options[$rt] = $rt;
            }
        }
        $this->data['form']['fields']['page_rt'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'page_rt',
                'options' => $options,
                'required' => true,
                'disabled_options' => $disabled_options
            ]
        );

        $this->data['form']['fields']['key_parameter_name'] = $form->getFieldHtml(
            [
                'type'    => 'input',
                'name'    => 'key_parameter_name',
                'value'   => '',
                'placeholder' => 'keep blank to cover all requests to controller'
            ]
        );
        $this->data['form']['fields']['key_parameter_value'] = $form->getFieldHtml(
            [
                'type'    => 'input',
                'name'    => 'key_parameter_value',
                'value'   => '',
            ]
        );

        //prepend button to generate keyword
        $this->data['keyword_button'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'generate_seo_keyword',
                'text'  => $this->language->get('button_generate'),
                //set button not to submit a form
                'attr'  => 'type="button"',
                'style' => 'btn btn-info',
            ]
        );
        $this->data['generate_seo_url'] = $this->html->getSecureURL('common/common/getseokeyword');
        $this->data['form']['fields']['seo_keyword'] = $form->getFieldHtml(
            [
                'type'         => 'input',
                'name'         => 'seo_keyword',
                'value'        => '',
                'help_url'     => $this->gen_help_url('seo_keyword'),
            ]
        );

        $pages = $layout->getAllPages();
        $this->data['entry_source_layout_id'] = $this->language->get('text_select_copy_layout');
        $sourceLayouts = array_column($pages, 'layout_name', 'layout_id');

        $this->data['form']['fields']['source_layout_id'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'source_layout_id',
                'value'   => '',
                'options' => $sourceLayouts,
            ]
        );

        $this->data['redirect_url'] = $this->html->getSecureURL('design/layout');
        $this->data['modal_title'] = $this->language->get('text_create_new_layout');
        $this->view->batchAssign($this->data);
        $this->data['output'] = $this->view->fetch('responses/design/add_layout.tpl');
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->response->setOutput($this->data['output']);
    }

    public function create()
    {
        $this->loadLanguage('design/layout');
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $post = $this->request->post;
        if(!$this->request->is_POST()){
            $err = new AError('Forbidden');
            $err->toJSONResponse('VALIDATION_ERROR_406',['error_text' => 'Forbidden']);
            return;
        }

        if(!$this->validate($this->request->post)){
            $err = new AError('Data Validation');
            $err->toJSONResponse(
                'VALIDATION_ERROR_406',
                [
                    'error_text' => current($this->errors),
                    'errors' => $this->errors
                ]
            );
            return;
        }

        $languageId = $this->language->getContentLanguageID();

        $post = $this->request->post;

        $pageData = [
            'controller' => $post['page_rt'],
            'key_param'  => $post['key_parameter_name'],
            'key_value'  => $post['key_parameter_value'],
            'page_descriptions' => [
                $languageId => [
                    'name' => $post['page_name'],
                    'title' => $post['page_title']
                ]
            ]
        ];
        $post['layout_name'] = $post['page_name'];

        $result = saveOrCreateLayout($post['tmpl_id'], $pageData, $post);
        if($result){
            $sql = " SELECT p.page_id, l.layout_id
                    FROM ".$this->db->table("pages")." p "."
                    INNER JOIN ".$this->db->table("pages_layouts")." pl 
                        ON pl.page_id = p.page_id
                    INNER JOIN ".$this->db->table("layouts")." l 
                        ON l.layout_id = pl.layout_id AND l.template_id = '".$post['tmpl_id']."'
                    WHERE p.controller = '".$post['page_rt']."'";
            if($post['key_parameter_value']){
                $sql .= " AND p.key_param = '".$post['key_parameter_name']."'";
                $sql .= " AND p.key_value = '".$post['key_parameter_value']."'";
            }
            $result = $this->db->query($sql);
            $this->data['output'] = [
                'layout_id' => $result->row['layout_id'],
                'page_id'   => $result->row['page_id']
            ];
            //add seo-keyword
            if($post['seo_keyword']){
                $this->language->replaceDescriptions(
                    'url_aliases',
                    [
                        'query' => "rt=".$post['page_rt']
                            .($post['key_parameter_value']
                                ? '&'.$post['key_parameter_name'].'='.$post['key_parameter_value']
                                : '')
                    ],
                    [(int) $languageId => ['keyword' => $post['seo_keyword']]]
                );
            }
        }else{
            $err = new AError(AC_ERR_LOAD);
            $err->toJSONResponse(
                'VALIDATION_ERROR_406',
                [
                    'error_text' => 'Cannot save layout',
                ]
            );
            return;
        }

        $this->view->batchAssign($this->data);
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->load->library('json');
        $this->response->setOutput(AJson::encode($this->data['output']));
    }

    protected function validate(array $inData)
    {
        $this->errors = [];
        if(!$inData['tmpl_id']){
            $this->errors['tmpl_id'] = $this->language->get('error_template');
        }
        if(!$inData['page_name']){
            $this->errors['page_name'] = $this->language->get('error_page_name');
        }else{
            $sql = "SELECT * 
                    FROM ".$this->db->table('page_descriptions')." 
                    WHERE name = '".$this->db->escape($inData['page_name'])."'";
            $result = $this->db->query($sql);
            if($result->num_rows){
                $this->errors['page_name'] = sprintf($this->language->get('error_page_exists'), $inData['page_name']);
            }
        }
        if(!$inData['page_rt']){
            $this->errors['page_rt'] = $this->language->get('error_page_rt');
        }
        if(
            (!$inData['key_parameter_name'] && $inData['key_parameter_value'])
            ||
            ($inData['key_parameter_name'] && !$inData['key_parameter_value'])
        ){
            if(!$inData['key_parameter_name']) {
                $this->errors['key_parameter_name'] = $this->language->get('error_key_parameter_name');
            }
            if(!$inData['key_parameter_value']) {
                $this->errors['key_parameter_value'] = $this->language->get('error_key_parameter_value');
            }
        }

        if($inData['seo_keyword'] && $this->html->isSEOKeywordExists('',$inData['seo_keyword']))
        {
            $this->errors['seo_keyword'] = sprintf($this->language->get('error_seo_keyword'), $inData['seo_keyword']);
        }

        //check if page already exists
        if(!$this->errors){
            $sql = "SELECT * FROM ".$this->db->table('pages')." p
                    INNER JOIN ".$this->db->table('pages_layouts')." pl
                        ON p.page_id = pl.page_id
                    INNER JOIN ".$this->db->table('layouts')." l
                        ON (l.layout_id = pl.layout_id AND l.template_id = '".$inData['tmpl_id']."')
                    WHERE p.controller = '".$this->db->escape($inData['page_rt'])."'";
            if($inData['key_parameter_name'] && $inData['key_parameter_value']){
                $sql .= " AND p.key_param='".$this->db->escape($inData['key_parameter_name'])."' "
                     ." AND p.key_value='".$this->db->escape($inData['key_parameter_value'])."'";
            }
            $result = $this->db->query($sql);
            if($result->num_rows){
                $this->errors['page_rt'] = $this->language->get('error_page_parameters');
            }
        }
        return !($this->errors);
    }
}