<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2016 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>
  
 UPGRADE NOTE: 
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.  
------------------------------------------------------------------------------*/

class ControllerCommonHeader extends AController {
	public function main() {
		$template_data = array();
        $template_data['title'] = $this->document->getTitle();
		$template_data['description'] = $this->document->getDescription();
		$template_data['base'] = $this->document->getBase();
		$template_data['charset'] = $this->document->getCharset();
		$template_data['language'] = $this->document->getLanguage();
		$template_data['direction'] = $this->document->getDirection();
		$template_data['links'] = $this->document->getLinks();	
		$template_data['styles'] = $this->document->getStyles();
		$template_data['scripts'] = $this->document->getScripts();		
		$template_data['breadcrumbs'] = $this->document->getBreadcrumbs();

		$this->view->batchAssign( $template_data );
        $this->processTemplate('common/header.tpl' );
	}
}
?>