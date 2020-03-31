<?php

class ControllerResponsesListingGridEmailTemplates extends AController
{
    public $data = [];

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadModel('design/email_template');

        $data = $this->request->post;
        $data['store_id'] = (int)$this->config->get('config_store_id');
        $result = $this->model_design_email_template->getEmailTemplates($data);
        $response = new stdClass();
        $response->page = $result['page'];
        $response->total = ceil($result['total']/$result['limit']);
        $response->records = $result['total'];
        $response->userdata = new stdClass();

        $i = 0;
        foreach ($result['items'] as $item) {
            $response->rows[$i]['id'] = $item['id'];
            $response->rows[$i]['cell'] = [
                $item['text_id'],
                $item['name'],
                $this->html->buildCheckbox([
                    'name'  => 'status['.$item['id'].']',
                    'value' => $item['status'],
                    'style' => 'btn_switch',
                ]),
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

        $this->loadModel('design/email_template');

        if ($this->request->is_POST()) {
            $post = $this->request->post;
            if (!is_array($post['status'])) {
                return;
            }
            foreach ((array)$post['status'] as $key=>$value) {
                $this->model_design_email_template->update($key, ['status' => (int)$value]);
            }
        }


        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

    }

    public function update()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadModel('design/email_template');

        if ($this->request->is_POST()) {
            $post = $this->request->post;
            if ($post['oper'] === 'save') {
                if (!is_array($post['status'])) {
                    return;
                }
                foreach ((array)$post['status'] as $key => $value) {
                    $this->model_design_email_template->update($key, ['status' => (int)$value]);
                }
            }

            if ($post['oper'] === 'del' && isset($post['id'])) {
                $ids = array_unique(explode(',', $post['id']));
                foreach ($ids as $id) {
                    $this->model_design_email_template->delete($id);
                }
            }
        }


        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
