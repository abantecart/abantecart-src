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

class ControllerPagesLicense extends AController
{
    private $error = array();

    public function main()
    {

        $this->session->clear();

        if ($this->request->is_POST() && ($this->validate())) {
            $this->redirect(HTTP_SERVER.'index.php?rt=settings');
        }

        if (isset($this->error['warning'])) {
            $template_data['error_warning'] = $this->error['warning'];
        } else {
            $template_data['error_warning'] = '';
        }
        $this->view->assign('error_warning', $template_data['error_warning']);
        $this->view->assign('action', HTTP_SERVER.'index.php?rt=license');
        $text = nl2br(file_get_contents('../license.txt'));
        $this->view->assign('text', $text);

        $this->view->assign('checkbox_agree', $this->html->buildCheckbox(array(
            'name'     => 'agree',
            'value'    => '',
            'attr'     => '',
            'required' => '',
            'form'     => 'form',
        ))
        );

        $this->addChild('common/header', 'header', 'common/header.tpl');
        $this->addChild('common/footer', 'footer', 'common/footer.tpl');

        $this->processTemplate('pages/license.tpl');
    }

    private function validate()
    {
        if (!isset($this->request->post['agree'])) {
            $this->error['warning'] = 'You must agree to the license before you can install AbanteCart!';
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
