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
class ControllerResponsesToolExtensionsStore extends AController {
	private $error = array();
	public function main() {
		$href = '/mp_api2';
		$GET = $this->request->get;
		// if set subfolder for request(seo requests) - concatenate it to url
		if(isset($GET['path'])){
			$href.= $GET['path'];
			unset($GET['path']);
		}
		$unset = array('s', 'rt', 'token','path','store_id','store_ip','store_url','store_version','language_code');
		foreach($unset as $key){
			unset($GET[$key]);
		}

		$GET['store_id'] = UNIQUE_ID;
		$GET['store_ip'] = $_SERVER ['SERVER_ADDR'];
		$GET['store_url'] = HTTP_SERVER;
		$GET['store_version'] = VERSION;		
		$GET['language_code'] = $this->request->cookie ['language'];
		
		// place your affiliate id here
		define('MP_AFFILIATE_ID','');
		if(MP_AFFILIATE_ID){
			$GET['aff_id'] = MP_AFFILIATE_ID;
		}

		$href .= '?'.http_build_query($GET);

		$connect = new AConnect();
		$html = $connect->getResponse($href);

		if(!$html){
			$this->loadLanguage('extension/extensions_store','silent');
			$error = is_array($connect->error) ? $connect->error : array($connect->error);
			foreach($error as $err){
				$this->log->write($err);
			}
			$html = '<div style="padding: 10px 10px 10px 20px;	margin-bottom: 15px; background: #FFDFE0 !important;	border: 1px solid #FF9999;	font-size: 12px;">'.$this->language->get('error_connect').'</div>';
        } else {
			//then parse response
			// get base href and remove it from response
			preg_match('/\<base.*?href=(\")(.*?)(\")/is', $html, $basehref);
			$basehref = $basehref[2];
			$html = str_replace('<base href="'.$basehref.'" />','',$html);
			// then replace relative url by absolute (css, js, img)
			$html = str_replace('<link href="/','<link href="'.$basehref,$html);
			$html = str_replace(' src="/',' src="'.$basehref,$html);
			// then need to replace url of http-links except anchors and absolute urls
			preg_match_all('/href=(\")(.*?)(\")/i', $html, $orig_hrefs);
			for($i = 0; $i < count($orig_hrefs[0]); $i++ ) {
				$replace = $orig_hrefs[0][$i];
				$ohref = $orig_hrefs[2][$i];
				$link1 = 'href="'.AEncryption::addEncoded_stid($ohref).(MP_AFFILIATE_ID ? '&aff_id='. MP_AFFILIATE_ID : '').'"';
				$link2 = 'href="'.$this->html->getSecureUrl('tool/extensions_store','&path='.$ohref.(MP_AFFILIATE_ID ? '&aff_id='. MP_AFFILIATE_ID : '')).'"';
				if( substr($ohref,0,4)=='http' || substr($ohref,0,1)=='#'){
					$html = str_replace($replace, $link1, $html);
				} else {
					$html = str_replace($replace, $link2, $html);
				}	
			}
			// construct action for search form
			$action = $this->html->getSecureUrl('tool/extensions_store', (MP_AFFILIATE_ID ? '&aff_id='. MP_AFFILIATE_ID : ''));
			$html = str_replace(' action="/',' action="'.$action,$html);
			$hidden = '<input type="hidden" name="search_performed" value="Y">';
			$hidden .= '<input type="hidden" name="rt" value="tool/extensions_store">';
			$hidden .= '<input type="hidden" name="s" value="'.ADMIN_PATH.'">';
			$hidden .= '<input type="hidden" name="token" value="'.$this->request->get['token'].'">';
			$hidden .= '<input type="hidden" name="aff_id" value="'.MP_AFFILIATE_ID.'">';
			$html = preg_replace('/<input.*name="search_performed".*>/', $hidden, $html);

			$html = $this->html->convertLinks( $html );
			$html = str_replace('<span>Store ID</span>','<span><font size="1"> '.UNIQUE_ID.'</font></span>',$html);

		}
        $this->response->setOutput($html);
	}
	
	public function connect() {
		
		//we get token back
		$mp_token = $this->request->get_or_post('mp_token');
		$html = "";
		if ($mp_token) {
			//save token and return
			$this->loadModel('setting/setting');
			$setting = array('mp_token' => $mp_token);
			$this->model_setting_setting->editSetting('api', $setting);
			
			$html = "
				<script type='text/javascript'>
				window.parent.reload_page();
				</script>
			";
		}
		
		$this->response->setOutput($html);
	}
	
	public function disconnect() {
		$return = '';
		$mp_token = $this->config->get('mp_token');
		if ( $mp_token ) {
			$this->loadModel('tool/mp_api');
			//disconnect remote marketplace fist 
			$result = $this->model_tool_mp_api->disconnect($mp_token);
			if($result['status'] == 1) {
				//reset token localy
				$this->loadModel('setting/setting');
				$setting = array('mp_token' => '');
				$this->model_setting_setting->editSetting('api', $setting);
				$return = 'success';
			} else {
				$return = 'error';			
			}
		}
		//sucess all the time
		$this->response->setOutput($return);
	}

	public function install() {
		//we get extension_key back
		$extension_key = $this->request->get_or_post('extension_key');
		$html = "";
		if ($extension_key) {
			//ready to install
			$url = $this->html->getSecureURL('tool/package_installer/download', '&extension_key='.$extension_key);	
		} else {
			$url = $this->html->getSecureURL('extension/extensions_store', '&purchased_only=1');
		}

		$html = "
				<script type='text/javascript'>
				window.top.location.href = '".$url."';
				</script>
		";
		
		$this->response->setOutput($html);
	}		
}
?>