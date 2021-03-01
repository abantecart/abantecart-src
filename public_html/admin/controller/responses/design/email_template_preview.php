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
class ControllerResponsesDesignEmailTemplatePreview extends AController
{
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        if($this->request->is_POST()){
            $this->session->data['email_template_preview'] = html_entity_decode($this->request->post['email_template_preview'], ENT_QUOTES,'UTF-8');
            return;
        }
        $this->response->setOutput($this->session->data['email_template_preview']);
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

    }

}
