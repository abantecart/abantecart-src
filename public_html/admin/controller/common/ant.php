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
if (!defined('DIR_CORE') || !IS_ADMIN) {
	header('Location: static_pages/');
}

/**
 * ANT
 * class for retrieving messages from remote ANT-server and insert it into database of abantecart
 */
class ControllerCommonANT extends AController {

	public function main() {
		// disable for login-logout pages
		if (in_array($this->request->get['rt'], array('index/logout', 'index/login'))) {
			unset($this->session->data['ant_messages']);
			return null;
		}

		if (!has_value($this->session->data['ant_messages']['date_modified'])) {
			unset($this->session->data['ant_messages']);
		}

		// prevent repeats of requests or if last update older then 24hours
		if (has_value($this->session->data['ant_messages']) && (time() - $this->session->data['ant_messages']['date_modified'] < 86400)) {
			return null;
		}


		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$url = "/index.php?option=com_antresponses&format=raw";
		$url .= "&store_id=" . UNIQUE_ID;
		$url .= "&store_ip=" . $_SERVER ['SERVER_ADDR'];
		$url .= "&store_url=" . HTTP_SERVER;
		$url .= "&software_name=AbanteCart";
		$url .= "&store_version=" . VERSION;
		$url .= "&language_code=" . $this->request->cookie ['language'];
		//check if user login first time 
		if (!$this->user->getLastLogin()) {
			$url .= "&new_cart=1";
		}

		//send extension info
		$extensions_list = $this->extensions->getExtensionsList();
		if ($extensions_list) {
			foreach ($extensions_list->rows as $ext) {
				$url .= "&extension[]=" . $ext ['key'] . "~" . $ext ['version'];
			}
		}

		//do connect without any http-redirects
		$connect = new AConnect (true, true);
		$result = $connect->getResponse($url);
		$this->session->data ['ant_messages'] = array(); // prevent requests in future at this session
		// insert new messages in database
		if ($result && is_array($result)) {
			//set array for check response
			$check_array = array(
					'message_id',
					'type',
					'date_added',
					'date_modified',
					'start_date',
					'end_date',
					'priority',
					'title',
					'description',
					'version',
					'prior_version',
					'html',
					'url',
					'published',
					'language_code');
			$banners = array();
			foreach ($result as $notify) {
				$tmp = array();
				foreach ($notify as $key => $value) {
					if (!in_array($key, $check_array)) {
						continue;
					}
					$tmp [$key] = $value;
				}

				// lets insert
				switch ($tmp ['type']) {
					case 'W' :
						$this->messages->saveWarning($tmp ['title'], $tmp ['description']);
						break;
					case 'E' :
						$this->messages->saveError($tmp ['title'], $tmp ['description']);
						break;
					case 'B' :
						$banners[] = $tmp['message_id'];
						$this->messages->saveANTMessage($tmp);
						break;
					default :
						$this->messages->saveNotice($tmp ['title'], $tmp ['description']);
						break;
				}
			}
			// purge messages except just saved
			$this->messages->purgeANTMessages($banners);
		}
		// in case when answer from server is empty
		$this->session->data['ant_messages']['date_modified'] = time();

		// check for extensions updates
		$this->loadModel('tool/updater');
		$this->model_tool_updater->check4updates();
	}
}
