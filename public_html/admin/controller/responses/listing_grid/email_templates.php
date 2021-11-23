<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/

class ControllerResponsesListingGridEmailTemplates extends AController
{
    public $data = [];

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        /** @var ModelDesignEmailTemplate $mdl */
        $mdl = $this->loadModel('design/email_template');

        $data = $this->request->post;
        $data['store_id'] = (int) $this->config->get('current_store_id');
        $result = $mdl->getEmailTemplates($data);
        $response = new stdClass();
        $response->page = $result['page'];
        $response->total = ceil($result['total'] / $result['limit']);
        $response->records = $result['total'];
        $response->userdata = new stdClass();

        $i = 0;
        foreach ($result['items'] as $item) {
            $response->rows[$i]['id'] = $item['id'];
            $response->rows[$i]['cell'] = [
                $item['text_id'],
                $item['name'],
                $this->html->buildCheckbox(
                    [
                        'name'  => 'status['.$item['id'].']',
                        'value' => $item['status'],
                        'style' => 'btn_switch',
                    ]
                ),
                $item['subject'],
            ];
            $i++;
        }

        $this->data['response'] = $response;

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($this->data['response']));
    }

    public function update_field()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        /** @var ModelDesignEmailTemplate $mdl */
        $mdl = $this->loadModel('design/email_template');

        if ($this->request->is_POST()) {
            $post = $this->request->post;
            if (!is_array($post['status'])) {
                return;
            }
            foreach ((array) $post['status'] as $key => $value) {
                $mdl->update($key, ['status' => (int) $value]);
            }
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function update()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        /** @var ModelDesignEmailTemplate $mdl */
        $mdl = $this->loadModel('design/email_template');

        if ($this->request->is_POST()) {
            $post = $this->request->post;
            if ($post['oper'] === 'save') {
                if (!is_array($post['status'])) {
                    return;
                }
                foreach ((array) $post['status'] as $key => $value) {
                    $mdl->update($key, ['status' => (int) $value]);
                }
            }

            if ($post['oper'] === 'del' && isset($post['id'])) {
                $ids = array_unique(explode(',', $post['id']));
                foreach ($ids as $id) {
                    $mdl->delete($id);
                }
            }
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
