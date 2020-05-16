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
        $this->data = array();
        $this->data['title'] = $this->document->getTitle();
        $this->data['description'] = $this->document->getDescription();
        $this->data['base'] = $this->document->getBase();
        $this->data['charset'] = $this->document->getCharset();
        $this->data['language'] = $this->document->getLanguage();
        $this->data['direction'] = $this->document->getDirection();
        $this->data['links'] = $this->document->getLinks();
        $this->data['styles'] = $this->document->getStyles();
        $this->data['scripts'] = $this->document->getScripts();
        $this->data['breadcrumbs'] = $this->document->getBreadcrumbs();
        $this->data['ssl'] = HTTPS;

        $this->view->batchAssign($this->data);
        $this->processTemplate('common/header.tpl');
    }
}