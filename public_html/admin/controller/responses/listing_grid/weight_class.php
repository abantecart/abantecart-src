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
 * Class ControllerResponsesListingGridWeightClass
 *
 * @property AWeight $weight
 */
class ControllerResponsesListingGridWeightClass extends AController
{
    public $data = array();

    public function main()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('localisation/weight_class');
        $this->loadModel('localisation/weight_class');

        $page = $this->request->post['page']; // get the requested page
        $limit = $this->request->post['rows']; // get how many rows we want to have into the grid
        $sord = $this->request->post['sord']; // get the direction
        $sidx = $this->request->post['sidx']; // get index row - i.e. user click to sort

        // process jGrid search parameter
        $allowedDirection = array('asc', 'desc');

        if (!in_array($sord, $allowedDirection)) {
            $sord = $allowedDirection[0];
        }

        $data = array(
            'sort'                => $sidx,
            'order'               => strtoupper($sord),
            'start'               => ($page - 1) * $limit,
            'limit'               => $limit,
            'content_language_id' => $this->session->data['content_language_id'],
        );

        $total = $this->model_localisation_weight_class->getTotalWeightClasses();
        if ($total > 0) {
            $total_pages = ceil($total / $limit);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages) {
            $page = $total_pages;
            $data['start'] = ($page - 1) * $limit;
        }

        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $total;
        $response->userdata = new stdClass();

        $results = $this->model_localisation_weight_class->getWeightClasses($data);
        $i = 0;
        $a_weight = new AWeight(Registry::getInstance());
        foreach ($results as $result) {
            $is_predefined = in_array($result['weight_class_id'], $a_weight->predefined_weight_ids) ? true : false;
            $response->userdata->classes[$result['weight_class_id']] = $is_predefined ? 'disable-delete' : '';
            $response->rows[$i]['id'] = $result['weight_class_id'];
            $response->rows[$i]['cell'] = array(
                $this->html->buildInput(array(
                    'name'  => 'weight_class_description['.$result['weight_class_id'].']['.$this->session->data['content_language_id'].'][title]',
                    'value' => $result['title'],
                )),
                $this->html->buildInput(array(
                    'name'  => 'weight_class_description['.$result['weight_class_id'].']['.$this->session->data['content_language_id'].'][unit]',
                    'value' => $result['unit'],
                )),
                (!$is_predefined
                    ? $this->html->buildInput(array(
                        'name'  => 'value['.$result['weight_class_id'].']',
                        'value' => $result['value'],
                    ))
                    : $result['value']),
                $result['iso_code'],
            );
            $i++;
        }
        $this->data['response'] = $response;

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->load->library('json');
        $this->response->setOutput(AJson::encode($this->data['response']));
    }

    public function update()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if (!$this->user->canModify('listing_grid/weight_class')) {
            $error = new AError('');
            return $error->toJSONResponse('NO_PERMISSIONS_402',
                array(
                    'error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/weight_class'),
                    'reset_value' => true,
                ));
        }

        $this->loadModel('localisation/weight_class');
        $this->loadModel('catalog/product');
        $this->loadLanguage('localisation/weight_class');
        switch ($this->request->post['oper']) {
            case 'del':
                $ids = explode(',', $this->request->post['id']);
                if (!empty($ids)) {
                    foreach ($ids as $id) {
                        $err = $this->_validateDelete($id);
                        if (!empty($err)) {
                            $error = new AError('');
                            return $error->toJSONResponse('VALIDATION_ERROR_406', array('error_text' => $err));
                        }
                        $this->model_localisation_weight_class->deleteWeightClass($id);
                    }
                }
                break;
            case 'save':
                $allowedFields = array_merge(array('weight_class_description', 'value'), (array)$this->data['allowed_fields']);
                $ids = explode(',', $this->request->post['id']);
                if (!empty($ids)) {
                    foreach ($ids as $id) {
                        foreach ($allowedFields as $f) {
                            if (isset($this->request->post[$f][$id])) {
                                $err = $this->_validateField($f, $this->request->post[$f][$id]);
                                if (!empty($err)) {
                                    $error = new AError('');
                                    return $error->toJSONResponse('VALIDATION_ERROR_406', array('error_text' => $err));
                                }
                                $this->model_localisation_weight_class->editWeightClass($id, array($f => $this->request->post[$f][$id]));
                            }
                        }
                    }
                }

                break;
            default:
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    /**
     * update only one field
     *
     * @return void
     */
    public function update_field()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if (!$this->user->canModify('listing_grid/weight_class')) {
            $error = new AError('');
            return $error->toJSONResponse('NO_PERMISSIONS_402',
                array(
                    'error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/weight_class'),
                    'reset_value' => true,
                ));
        }

        $this->loadLanguage('localisation/weight_class');
        $this->loadModel('localisation/weight_class');
        if (isset($this->request->get['id'])) {
            //request sent from edit form. ID in url
            foreach ($this->request->post as $key => $value) {
                $err = $this->_validateField($key, $value);
                if (!empty($err)) {
                    $error = new AError('');
                    return $error->toJSONResponse('VALIDATION_ERROR_406', array('error_text' => $err));
                }
                $data = array($key => $value);
                $this->model_localisation_weight_class->editWeightClass($this->request->get['id'], $data);
            }
            return null;
        }

        //request sent from jGrid. ID is key of array
        $allowedFields = array_merge(array('weight_class_description', 'value', 'iso_code'), (array)$this->data['allowed_fields']);

        foreach ($allowedFields as $f) {
            if (isset($this->request->post[$f])) {
                foreach ($this->request->post[$f] as $k => $v) {
                    $err = $this->_validateField($f, $v);
                    if (!empty($err)) {
                        $error = new AError('');
                        return $error->toJSONResponse('VALIDATION_ERROR_406', array('error_text' => $err));
                    }
                    $this->model_localisation_weight_class->editWeightClass($k, array($f => $v));
                }
            }
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    private function _validateField($field, $value)
    {
        $this->data['error'] = '';
        switch ($field) {
            case 'weight_class_description' :
                foreach ($value as $v) {
                    if (isset($v['title'])) {
                        if (mb_strlen($v['title']) < 2 || mb_strlen($v['title']) > 32) {
                            $this->data['error'] = $this->language->get('error_title');
                        }
                    }

                    if (isset($v['unit'])) {
                        if (!$v['unit'] || mb_strlen($v['unit']) > 4) {
                            $this->data['error'] = $this->language->get('error_unit');
                        }
                    }
                }
                break;
            case 'iso_code':
                $iso_code = strtoupper(preg_replace('/[^a-z]/i', '', $value));
                if ((!$iso_code) || strlen($iso_code) != 4) {
                    $this->data['error'] = $this->language->get('error_iso_code');
                } //check for uniqueness
                else {
                    $weight = $this->model_localisation_weight_class->getWeightClassByCode($iso_code);
                    $weight_class_id = (int)$this->request->get['id'];
                    if ($weight) {
                        if (!$weight_class_id
                            || ($weight_class_id && $weight['weight_class_id'] != $weight_class_id)
                        ) {
                            $this->data['error'] = $this->language->get('error_iso_code');
                        }
                    }
                }
                break;
        }
        $this->extensions->hk_ValidateData($this, array(__FUNCTION__, $field, $value));
        return $this->data['error'];
    }

    private function _validateDelete($weight_class_id)
    {
        $this->data['error'] = '';
        $weight_class_info = $this->model_localisation_weight_class->getWeightClass($weight_class_id);
        if ($weight_class_info && ($this->config->get('config_weight_class') == $weight_class_info['unit'])) {
            $this->data['error'] = $this->language->get('error_default');
        }

        $product_total = $this->model_catalog_product->getTotalProductsByWeightClassId($weight_class_id);
        if ($product_total) {
            $this->data['error'] = sprintf($this->language->get('error_product'), $product_total);
        }

        $this->extensions->hk_ValidateData($this, array(__FUNCTION__, $weight_class_id));
        return $this->data['error'];
    }

}
