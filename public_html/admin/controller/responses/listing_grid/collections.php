<?php

if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerResponsesListingGridCollections extends AController
{
    public $data = [];
    public $error = [];

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadModel('catalog/collection');

        $data = $this->request->post;
        $data['store_id'] = (int)$this->config->get('config_store_id');
        $result = $this->model_catalog_collection->getCollections($data);
        $response = new stdClass();
        $response->page = $result['page'];
        $response->total = ceil($result['total'] / $result['limit']);
        $response->records = $result['total'];
        $response->userdata = new stdClass();

        $i = 0;
        foreach ($result['items'] as $item) {
            $response->rows[$i]['id'] = $item['id'];
            $response->rows[$i]['cell'] = [
                $item['name'],
                $this->html->buildCheckbox([
                    'name'  => 'status['.$item['id'].']',
                    'value' => $item['status'],
                    'style' => 'btn_switch',
                ]),
                dateISO2Display($item['date_added']),
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

        $this->loadModel('catalog/collection');
        if (!$this->user->canModify('listing_grid/collections')) {
            $error = new AError('');
            return $error->toJSONResponse('NO_PERMISSIONS_402',
                array(
                    'error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/collections'),
                    'reset_value' => true,
                ));
        }
        $collectionId = $this->request->get['id'];
        if ($this->request->is_POST()) {
            $post = $this->request->post;
            if (is_array($post['status'])) {
                foreach ((array)$post['status'] as $key => $value) {
                    $this->model_catalog_collection->update($key, ['status' => (int)$value]);
                }
            } elseif($collectionId && $this->validate($post)) {
                $this->model_catalog_collection->update($collectionId, $post);
            } else {
                $error = new AError('');
                return $error->toJSONResponse('VALIDATION_ERROR_406', array('error_text' => $this->error));
            }
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

    }

    private function validate(array $data)
    {
        $this->loadModel('catalog/collection');
        $this->loadLanguage('catalog/collections');

        if (isset($data['name'])) {
            if (strlen(trim($data['name'])) === 0 || strlen(trim($data['name'])) > 254) {
                $this->error['name'] = $this->language->get('save_error_name');
            }
        }

        if (($error_text = $this->html->isSEOkeywordExists('collection_id='.$this->request->get['id'], $this->request->post['keyword']))) {
            $this->error['warning'][] = $this->language->get('save_error_unique_keyword');
        }

        if (empty($this->error)) {
            return true;
        }
        return false;
    }

    public function update()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadModel('catalog/collection');

        if ($this->request->is_POST()) {
            $post = $this->request->post;
            if ($post['oper'] === 'save') {
                if (!is_array($post['status'])) {
                    return;
                }
                foreach ((array)$post['status'] as $key => $value) {
                    $this->model_catalog_collection->update($key, ['status' => (int)$value]);
                }
            }

            if ($post['oper'] === 'del' && isset($post['id'])) {
                $ids = array_unique(explode(',', $post['id']));
                foreach ($ids as $id) {
                    $this->model_catalog_collection->delete($id);
                }
            }
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function getFieldsByConditionObject($value)
    {
        $this->loadLanguage('catalog/collections');

        $cond_objects = [
            'product_price',
            'categories',
            'brands',
            'products',
            'tags',
        ];

        if (!in_array($this->request->post['condition_object'], $cond_objects)) {
            return null;
        }

        $this->form = new AForm ('HT');
        $this->form->setForm(array(
            'form_name' => 'collectionsFrm',
            'update'    => $this->html->getSecureURL('listing_grid/collections/update_field', '&id='.$this->request->get['id']),
        ));
        $method = 'getFieldsFor'.str_replace(' ', '', ucwords(str_replace('_', ' ', $this->request->post['condition_object'])));

        if (method_exists($this, $method)) {
            $response = call_user_func(array($this, $method), $value);
        }
        if ($response['fields']) {
            $value = $this->request->post['condition_object'];
            $response['fields'] .= $this->form->getFieldHtml(
                array(
                    'type'  => 'hidden',
                    'name'  => 'conditions[conditions]['.$this->request->post['idx'].'][object]',
                    'value' => $value,
                )
            );
        }

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($response));
    }

    private function getFieldsForProducts($value = '')
    {

        $listing_data = [];
        if (is_array($value) && is_array($value['value']) && $value['value']) {

            $this->loadModel('catalog/product');
            $filter = array('subsql_filter' => 'p.product_id in ('.implode(',', $value['value']).')');

            $results = $this->model_catalog_product->getProducts($filter);
            if ($results) {
                $resource = new AResource('image');
                foreach ($results as $r) {
                    $product_id = $r['product_id'];
                    $thumbnail = $resource->getMainThumb(
                        'products',
                        $product_id,
                        (int)$this->config->get('config_image_grid_width'),
                        (int)$this->config->get('config_image_grid_height'),
                        true);
                    $listing_data[$product_id]['name'] = $r['name']." (".$r['model'].")";
                    $listing_data[$product_id]['image'] = $thumbnail['thumb_html'];
                }
            }
        }

        $response['text'] = $this->language->get('entry_products');
        $response['fields'] = $this->form->getFieldHtml(
            array(
                'type'    => 'selectbox',
                'name'    => 'conditions[conditions]['.$this->request->post['idx'].'][operator]',
                'options' => array(
                    'in'    => $this->language->get('text_in'),
                    'notin' => $this->language->get('text_not_in'),
                ),
                'value'   => ($value && $value['operator']) ? $value['operator'] : 'in',
            ));

        $response['fields'] .= $this->form->getFieldHtml(
            array(
                'type'        => 'multiselectbox',
                'name'        => 'conditions[conditions]['.$this->request->post['idx'].'][value][]',
                'value'       => !$value ? '' : $value['value'],
                'options'     => $listing_data,
                'style'       => 'chosen',
                'ajax_url'    => $this->html->getSecureURL('r/product/product/products'),
                'placeholder' => $this->language->get('text_select_from_lookup'),
            ));

        return $response;
    }

    private function getFieldsForProductPrice($value = array())
    {

        $response['text'] = $this->language->get('entry_product_price');
        $response['fields'] = $this->form->getFieldHtml(
            array(
                'type'    => 'selectbox',
                'name'    => 'conditions[conditions]['.$this->request->post['idx'].'][operator]',
                'options' => array(
                    'eq'   => $this->language->get('text_equal'),
                    'neq'  => $this->language->get('text_not_equal'),
                    'eqlt' => $this->language->get('text_equal_or_less'),
                    'eqgt' => $this->language->get('text_equal_or_greater'),
                    'lt'   => $this->language->get('text_less'),
                    'gt'   => $this->language->get('text_greater'),
                ),
                'value'   => ($value && $value['operator']) ? $value['operator'] : 'eq',
            ));
        $response['fields'] .= $this->form->getFieldHtml(
            array(
                'type'  => 'input',
                'name'  => 'conditions[conditions]['.$this->request->post['idx'].'][value]',
                'value' => !$value ? '' : $value['value'],
                'style' => 'small-field',
            )
        );
        $response['fields'] .= '('.$this->config->get('config_currency').')';
        return $response;
    }

    private function getFieldsForCategories($value = '')
    {
        $this->loadLanguage('catalog/collections');
        $response['text'] = $this->language->get('entry_categories');
        $response['fields'] = $this->form->getFieldHtml(
            array(
                'type'    => 'selectbox',
                'name'    => 'conditions[conditions]['.$this->request->post['idx'].'][operator]',
                'options' => array(
                    'in'    => $this->language->get('text_in'),
                    'notin' => $this->language->get('text_not_in'),
                ),
                'value'   => !$value ? '' : $value['operator'],
            ));
        $this->loadModel('catalog/category');
        $results = $this->model_catalog_category->getCategories(0, $this->config->get('config_store_id'));
        foreach ($results as $r) {
            $categories[$r['category_id']] = $r['name'];
        }

        $response['fields'] .= $this->form->getFieldHtml(array(
            'type'        => 'checkboxgroup',
            'name'        => 'conditions[conditions]['.$this->request->post['idx'].'][value][]',
            'value'       => !$value ? '' : $value['value'],
            'options'     => $categories,
            'style'       => 'chosen',
            'placeholder' => $this->language->get('text_select_category'),
        ));

        return $response;
    }

    private function getFieldsForBrands($value = '')
    {

        $response['text'] = $this->language->get('entry_brands');
        $response['fields'] = $this->form->getFieldHtml(
            array(
                'type'    => 'selectbox',
                'name'    => 'conditions[conditions]['.$this->request->post['idx'].'][operator]',
                'options' => array(
                    'in'    => $this->language->get('text_in'),
                    'notin' => $this->language->get('text_not_in'),
                ),
                'value'   => !$value ? '' : $value['operator'],
            ));
        $this->loadModel('catalog/manufacturer');
        $results = $this->model_catalog_manufacturer->getManufacturers();
        foreach ($results as $r) {
            $manufacturers[$r['manufacturer_id']] = $r['name'];
        }
        $response['fields'] .= $this->form->getFieldHtml(array(
            'type'        => 'checkboxgroup',
            'name'        => 'conditions[conditions]['.$this->request->post['idx'].'][value][]',
            'value'       => !$value ? '' : $value['value'],
            'options'     => $manufacturers,
            'style'       => 'chosen',
            'placeholder' => $this->language->get('text_select_manufacturer'),
        ));
        return $response;
    }

    private function getFieldsForTags($value = '')
    {

        $response['text'] = $this->language->get('entry_tags');
        $response['fields'] = $this->form->getFieldHtml(
            array(
                'type'    => 'selectbox',
                'name'    => 'conditions[conditions]['.$this->request->post['idx'].'][operator]',
                'options' => array(
                    'in'    => $this->language->get('text_in'),
                    'notin' => $this->language->get('text_not_in'),
                ),
                'value'   => !$value ? '' : $value['operator'],
            ));
        $this->loadModel('catalog/collection');
        $results = $this->model_catalog_collection->getUniqueTags();
        foreach ($results as $r) {
            $tags[$r['tag']] = $r['tag'];
        }
        $response['fields'] .= $this->form->getFieldHtml(array(
            'type'        => 'checkboxgroup',
            'name'        => 'conditions[conditions]['.$this->request->post['idx'].'][value][]',
            'value'       => !$value ? '' : $value['value'],
            'options'     => $tags,
            'style'       => 'chosen',
            'placeholder' => $this->language->get('text_select_tag'),
        ));
        return $response;
    }

}
