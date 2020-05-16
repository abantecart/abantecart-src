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

class ControllerCommonHeader extends AController
{
    public $data = array();

    public function main()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->data['store'] = $this->config->get('store_name');
        $this->data['logo'] = $this->config->get('config_logo');
        $this->data['homepage'] = $this->html->getHomeURL();

        //see if we have a resource ID instead of path
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

        $this->view->batchAssign($this->data);
        $this->processTemplate('common/header.tpl');
        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
