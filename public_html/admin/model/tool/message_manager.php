<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ModelToolMessageManager extends Model {

	public function deleteMessage($message_id) {
		
		$this->messages->deleteMessage($message_id);		
		return true;
	}
	
	public function getMessage($message_id) {
			return $this->messages->getMessage($message_id);	
	}

	public function getMessages($data = array()) {
	
			if (!isset($data['sort'])){
				$data['sort']='viewed';
			}
			
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}					

			if ($data['limit'] < 1) {
				$data['limit'] = 10;
			}	
				
			return $this->messages->getMessages($data['start'],$data['limit'],$data['sort'],$data['order']);		
	}

	public function getTotalMessages( ) {
		return $this->messages->getTotalMessages();
	}
}
?>