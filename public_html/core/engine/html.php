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
if (!defined('DIR_CORE')) {
	header('Location: static_pages/');
}

class AHtml extends AController {
	protected $registry;
	protected $args = array();

	/**
	 * @param Registry $registry
	 * @param array $args
	 */
	public function __construct($registry, $args = array()) {
		$this->registry = $registry;		
	}

	/**
	 * PR Build sub URL
	 * @param string $rt
	 * @param string $params
	 * @return string
	 */
	private function buildURL($rt, $params = '') {
		$suburl = '';
		//#PR Add admin path if we are in admin
		if (IS_ADMIN) {
			$suburl .= '&s=' . ADMIN_PATH;
		}
		//add template if present
		if (!empty($this->registry->get('request')->get['sf'])) {
			$suburl .= '&sf=' . $this->registry->get('request')->get['sf'];
		}
		
		//if in embed mode add respoce prefix
		if ($this->registry->get('config')->get('embed_mode') == true) {
			$suburl .= '&embed_mode=1';
			if(substr($rt, 0, 2) != 'r/'){
				$rt = 'r/'.$rt;
			}
		}		

		$suburl = '?' . ($rt ? 'rt=' . $rt : '') . $params . $suburl;
		return $suburl;
	}

	/**
	 * Build non-secure URL
	 * @param string $rt
	 * @param string $params
	 * @param string $encode
	 * @return string
	 */
	public function getURL($rt, $params = '', $encode = '') {
		if (isset($this->registry->get('request')->server['HTTPS'])
				&& (($this->registry->get('request')->server['HTTPS'] == 'on') || ($this->registry->get('request')->server['HTTPS'] == '1'))) {
			$server = HTTPS_SERVER;
		} else {
			//to prevent garbage session need to check constant HTTP_SERVER
			$server = defined('HTTP_SERVER') ? HTTP_SERVER : 'http://' . REAL_HOST . get_url_path($_SERVER['PHP_SELF']);
		}

		if ($this->registry->get('config')->get('storefront_template_debug') 
			&& isset($this->registry->get('request')->get['tmpl_debug'])
			) {
			$params .= '&tmpl_debug=' . $this->registry->get('request')->get['tmpl_debug'];
		}
		// add session id for crossdomain transition in secure mode
		if($this->registry->get('config')->get('config_shared_session')	&& HTTPS===true){
			$params .= '&session_id='.session_id();
		}

		//add token for embed mode with forbidden 3dparty cookies
		if($_SESSION['session_mode'] == 'embed_token'){
			$params .= '&'.EMBED_TOKEN_NAME.'='.session_id();
		}
		$url = $server . INDEX_FILE . $this->url_encode($this->buildURL($rt, $params), $encode);
		return $url;
	}

	/**
	 * Build secure URL with session token
	 * @param string $rt
	 * @param string $params
	 * @param string $encode
	 * @return string
	 */
	public function getSecureURL($rt, $params = '', $encode = '') {
		// add session id for crossdomain transition in non-secure mode
		if($this->registry->get('config')->get('config_shared_session')	&& HTTPS!==true){
			$params .= '&session_id='.session_id();
		}

		$suburl = $this->buildURL($rt, $params);
		//#PR Add session
		if (isset($this->session->data['token']) && $this->session->data['token']) {
			$suburl .= '&token=' . $this->session->data['token'];
		}

		//add token for embed mode with forbidden 3dparty cookies
		if($_SESSION['session_mode'] == 'embed_token'){
			$suburl .= '&'.EMBED_TOKEN_NAME.'='.session_id();
		}

		if ($this->registry->get('config')->get('storefront_template_debug') && isset($this->registry->get('request')->get['tmpl_debug'])) {
			$suburl .= '&tmpl_debug=' . $this->registry->get('request')->get['tmpl_debug'];
		}

		$url = HTTPS_SERVER . INDEX_FILE . $this->url_encode($suburl, $encode);
		return $url;
	}

	/**
	 * Build non-secure SEO URL
	 * @param string $rt
	 * @param string $params
	 * @param string $encode
	 * @return string
	 */
	public function getSEOURL($rt, $params = '', $encode = '') {
		//skip SEO for embed mode
		if ($this->registry->get('config')->get('embed_mode') == true) {
			return $this->getURL($rt, $params);
		}
		//#PR Generate SEO URL based on standard URL
		$this->loadModel('tool/seo_url');
		return $this->url_encode($this->model_tool_seo_url->rewrite($this->getURL($rt, $params)), $encode);
	}
	/**
	 * Build secure SEO URL
	 * @param string $rt
	 * @param string $params
	 * @param string $encode
	 * @return string
	 */
	public function getSecureSEOURL($rt, $params = '', $encode = '') {
		//add token for embed mode with forbidden 3dparty cookies
		if($_SESSION['session_mode'] == 'embed_token'){
			$params .= '&'.EMBED_TOKEN_NAME.'='.session_id();
		}
		//#PR Generate SEO URL based on standard URL
		$this->loadModel('tool/seo_url');
		return $this->url_encode($this->model_tool_seo_url->rewrite($this->getSecureURL($rt, $params)), $encode);
	}

	/**This builds URL to the catalog to be used in admin
	 * @param string $rt
	 * @param string $params
	 * @param string $encode
	 * @param bool $ssl
	 * @return string
	 */
	public function getCatalogURL($rt, $params = '', $encode = '', $ssl = false) {
		//add token for embed mode with forbidden 3dparty cookies
		if($_SESSION['session_mode'] == 'embed_token'){
			$params .= '&'.EMBED_TOKEN_NAME.'='.session_id();
		}
		$suburl = '?' . ($rt ? 'rt=' . $rt : '') . $params;
		
		$http = $ssl ? HTTPS_SERVER : HTTP_SERVER;

		$url = $http . INDEX_FILE . $this->url_encode($suburl, $encode);
		return $url;
	}

	/**
	 * encode URLfor & to be &amp
	 * @param string $url
	 * @param bool $encode
	 * @return string
	 */
	public function url_encode($url, $encode = false) {
		if ($encode) {
			return str_replace('&', '&amp;', $url);
		} else {
			return $url;
		}
	}

	/**
	 * Current URL built based on get params with ability to exclude params
	 *
	 * @param $filter_params array - array of vars to filter
	 * @return string - url without unwanted filter parameters
	 */
	public function currentURL($filter_params = array()) {	
		$params_arr = $this->request->get;
		//detect if there is RT in the params. 
		$rt = 'index/home';
		if ( has_value($params_arr['rt']) ) {
			$rt = $params_arr['rt'];
			$filter_params[] = 'rt';	
		}
		if ( has_value($params_arr['s']) ) {
			$filter_params[] = 's';	
		}
		$URI = '&' . $this->buildURI($params_arr, $filter_params);
		return $this->getURL($rt, $URI);
	}

	/**
	 * URI entrypt parameteres in URI
	 *
	 * @param $uri
	 * @internal param array $filter_params - array of vars to filter
	 * @return string - url without unwanted filter parameters
	 */
	public function encryptURI($uri) {		
		$encrypted = base64_encode( $uri );
		if ( strlen( $encrypted ) <= 250 ) {
			return '__e='.$encrypted;
		} else {
			return $uri;
		}		
	}

	/**
	 * Build URI from array provided
	 *
	 * @param $params_arr array - data array to process
	 * @param $filter_params array - array of vars to filter
	 * @return string - url without unwanted filter parameters
	 */
	public function buildURI($params_arr, $filter_params = array()) {		

		foreach ($filter_params as $rv) {
			unset($params_arr[ $rv ]);		
		}

		return urldecode(http_build_query($params_arr, '', '&'));
	}

	/**
	 * Filter query parameters from url.
	 *
	 * @param $url string - url to process
	 * @param $filter_params string|array - single var or array of vars
	 * @return string - url without unwanted filter query parameters
	 */
	public function filterQueryParams($url, $filter_params = array()) {
		list($url_part, $q_part) = explode('?', $url);
		parse_str($q_part, $q_vars);
		//build array if passed as string
		if (!is_array($filter_params)) {
			$filter_params = array( $filter_params );
		}
		foreach ($filter_params as $rv) {
			unset($q_vars[ $rv ]);		
		}
		foreach ($q_vars as $key => $val) {
			$q_vars[$key] = $this->request->clean($val);
		}

		$new_qs = urldecode(http_build_query($q_vars, '', '&'));
		return $url_part . '?' . $new_qs;
	}

	/**
	 * remove get parameters from url.
	 * @deprecated since 1.1.4! Use filterQueryParams() instead
	 * @param $url - url to process
	 * @param $remove_vars
	 * @internal param array|string $vars - single var or array of vars
	 * @return string - url without unwanted get parameters
	 */
	public function removeQueryVar($url, $remove_vars) {
		return $this->filterQueryParams($url, $remove_vars);
	}

	/**
	 * function returns text error or empty
	 * @param string $query
	 * @param string $keyword
	 * @return string
	 */
	public function isSEOkeywordExists($query,$keyword=''){
		if(!$keyword){
			return '';
		}
		$seo_key = SEOEncode($keyword);

		$db = $this->registry->get('db');
		$sql = "SELECT * FROM ".DB_PREFIX."url_aliases WHERE query<>'".$db->escape($query)."' AND keyword='".$db->escape($seo_key)."'";
		$result = $db->query($sql);
		if($result->num_rows){
			$url = HTTP_CATALOG.$seo_key;
			return	sprintf($this->registry->get('language')->get('error_seo_keyword'),$url,$seo_key);
		}

		return '';
	}

	/**
	 * create html code based on passed data
	 * @param  $data - array with element data
	 *  sample
	 *  $data = array(
	 *   'type' => 'input' //(hidden, textarea, selectbox, file...)
	 *   'name' => 'input name'
	 *   'value' => 'input value' // could be array for select
	 *   'style' => 'my-form'
	 *   'form' => 'form id' // needed for unique element ID     *
	 *  );
	 * @return object
	 */
	public function buildElement($data) {
		return HtmlElementFactory::create($data);
	}

	/**
	 * @param  $data - array with element data
	 * same format as for buildElement, except unnecessarily 'type'
	 * @return string - html code
	 */
	public function buildHidden($data) {
		$item = new HiddenHtmlElement($data);
		return $item->getHtml();
	}

	/**
	 * @param  $data - array with element data
	 * same format as for buildElement, except unnecessarily 'type'
	 * @return string - html code
	 */
	public function buildSubmit($data) {
		$item = new SubmitHtmlElement($data);
		return $item->getHtml();
	}

	/**
	 * @param  $data - array with element data
	 * same format as for buildElement, except unnecessarily 'type'
	 * @return string - html code
	 */
	public function buildInput($data) {
		$item = new InputHtmlElement($data);
		return $item->getHtml();
	}

	/**
	 * @param  $data - array with element data
	 * same format as for buildElement, except unnecessarily 'type'
	 * @return string - html code
	 */
	public function buildPassword($data) {
		$item = new PasswordHtmlElement($data);
		return $item->getHtml();
	}

	/**
	 * @param  $data - array with element data
	 * same format as for buildElement, except unnecessarily 'type'
	 * @return string - html code
	 */
	public function buildTextarea($data) {
		$item = new TextareaHtmlElement($data);
		return $item->getHtml();
	}

	/**
	 * @param  $data - array with element data
	 * same format as for buildElement, except unnecessarily 'type'
	 * @return string - html code
	 */
	public function buildSelectbox($data) {
		$item = new SelectboxHtmlElement($data);
		return $item->getHtml();
	}

	/**
	 * @param  $data - array with element data
	 * same format as for buildElement, except unnecessarily 'type'
	 * @return string - html code
	 */
	public function buildMultiselectbox($data) {
		$item = new MultiSelectboxHtmlElement($data);
		return $item->getHtml();
	}


	/**
	 * @param  $data - array with element data
	 * same format as for buildElement, except unnecessarily 'type'
	 * @return string - html code
	 */
	public function buildCheckbox($data) {
		$item = new CheckboxHtmlElement($data);
		return $item->getHtml();
	}

	/**
	 * @param  $data - array with element data
	 * same format as for buildElement, except unnecessarily 'type'
	 * @return string - html code
	 */
	public function buildCheckboxGroup($data) {
		$item = new CheckboxGroupHtmlElement($data);
		return $item->getHtml();
	}

	/**
	 * @param  $data - array with element data
	 * same format as for buildElement, except unnecessarily 'type'
	 * @return string - html code
	 */
	public function buildFile($data) {
		$item = new FileHtmlElement($data);
		return $item->getHtml();
	}

	/**
	 * @param  $data - array with element data
	 * same format as for buildElement, except unnecessarily 'type'
	 * @return string - html code
	 */
	public function buildRadio($data) {
		$item = new RadioHtmlElement($data);
		return $item->getHtml();
	}

	/**
	 * @param  $data - array with element data
	 * same format as for buildElement, except unnecessarily 'type'
	 * @return string - html code
	 */
	public function buildButton($data) {
		$item = new ButtonHtmlElement($data);
		return $item->getHtml();
	}

	/**
	 * @param  $data - array with element data
	 * same format as for buildElement, except unnecessarily 'type'
	 * @return string - html code
	 */
	public function buildForm($data) {
		$item = new FormHtmlElement($data);
		return $item->getHtml();
	}

	/**
	 * @param  $data - array with element data
	 * same format as for buildElement, except unnecessarily 'type'
	 * @return string - html code
	 */
	public function buildRating($data) {
		$item = new RatingHtmlElement($data);
		return $item->getHtml();
	}

	/**
	 * @param  $data - array with element data
	 * same format as for buildElement, except unnecessarily 'type'
	 * @return string - html code
	 */
	public function buildCaptcha($data) {
		$item = new CaptchaHtmlElement($data);
		return $item->getHtml();
	}

	/**
	 * @param  $data - array with element data
	 * same format as for buildElement, except unnecessarily 'type'
	 * @return string - html code
	 */
	public function buildReCaptcha($data) {
		$item = new ReCaptchaHtmlElement($data);
		return $item->getHtml();
	}

	/**
	 * @param  $data - array with element data
	 * same format as for buildElement, except unnecessarily 'type'
	 * @return string - html code
	 */
	public function buildPasswordset($data) {
		$item = new PasswordsetHtmlElement($data);
		return $item->getHtml();
	}

	/**
	 * @param  $data - array with element data
	 * id, name, title, value(array with id of rows(or elements) which will be selected after popup content load)
	 * content_url - url of popup content
	 * postvars - associative array with POST variables for ajax request from popup
	 *
	 * @return string - html code
	 */
	public function buildMultivalueList($data) {
		$item = new MultivalueListHtmlElement($data);
		return $item->getHtml();
	}

	/**
	 * @param array $data
	 * @return string
	 */
	public function buildMultivalue($data) {
		$item = new MultivalueHtmlElement($data);
		return $item->getHtml();
	}

	/**
	 * @param array $data
	 * @return string
	 */
	public function buildResourceImage($data) {
		$item = new ResourceImageHtmlElement($data);
		return $item->getHtml();
	}
	/**
	 * @param array $data
	 * @return string
	 */
	public function buildDate($data) {
		$item = new DateHtmlElement($data);
		return $item->getHtml();
	}
	/**
	 * @param array $data
	 * @return string
	 */
	public function buildEmail($data) {
		$item = new EmailHtmlElement($data);
		return $item->getHtml();
	}
	/**
	 * @param array $data
	 * @return string
	 */
	public function buildNumber($data) {
		$item = new NumberHtmlElement($data);
		return $item->getHtml();
	}
	/**
	 * @param array $data
	 * @return string
	 */
	public function buildPhone($data) {
		$item = new PhoneHtmlElement($data);
		return $item->getHtml();
	}
	/**
	 * @param array $data
	 * @return string
	 */
	public function buildIPaddress($data) {
		$item = new IPaddressHtmlElement($data);
		return $item->getHtml();
	}
	/**
	 * @param array $data
	 * @return string
	 */
	public function buildCountries($data) {
		$item = new CountriesHtmlElement($data);
		return $item->getHtml();
	}

	/**
	 * @param  $data - array with element data
	 * method to build pagination HTML element
	 * @return string - html code
	 */
	public function buildPagination($data) {
		$item = new PaginationHtmlElement($data);
		return $item->getHtml();
	}

	/**
	 * @return string
	 * @throws AException
	 */
	public function getStoreSwitcher() {
		$registry = Registry::getInstance();
		$view = new AView(Registry::getInstance(), 0);
		//check if store_id is passed or in the session 
		$store_id = $registry->get('config')->get('config_store_id');
		//set store selector
		$stores = array();
		$hidden = array();
		$stores[0] = array('name' => $registry->get('language')->get('text_default'));
		$registry->get('load')->model('setting/store');
		//if loaded not default store - hide store switcher
		$default_store_settings = $registry->get('model_setting_store')->getStore(0);
		if($this->registry->get('config')->get('config_url') != $default_store_settings['config_url']){
			return '';
		}
		$result_stores = $registry->get('model_setting_store')->getStores();
		if (sizeof($result_stores) > 0) {
			foreach ($result_stores as $rs) {
				$stores[$rs['store_id']] = array(
					'name' => $rs['alias'] ? $rs['alias']:$rs['name'],
					'store_id' => $rs['store_id']
				);
			}
			foreach ($registry->get('request')->get as $name => $value) {
				if ($name == 'store_id') continue;
				$hidden[ $name ] = $value;
			}
			$view->assign('all_stores', $stores);
			$view->assign('current_store', $stores[$store_id]['name']);
			$view->assign('hiddens', $hidden);
			$view->assign('text_select_store', $registry->get('language')->get('text_select_store'));
			return $view->fetch('form/store_switcher.tpl');
		} else {
			return '';
		} 
	}

	/**
	 * @return string
	 * @throws AException
	 */
	public function getContentLanguageSwitcher() {
		$registry = Registry::getInstance();
		$view = new AView(Registry::getInstance(), 0);
		$registry->get('load')->model('localisation/language');
		$results = $registry->get('model_localisation_language')->getLanguages();
		$template['languages'] = array();

		foreach ($results as $result) {
			if ($result['status']) {
				$template['languages'][ ] = array(
					'name' => $result['name'],
					'code' => $result['code'],
					'image' => $result['image']
				);
			}
		}
		if (sizeof($template['languages']) > 1) {
			$template['language_code'] = $registry->get('session')->data['content_language']; //selected in selectbox
			foreach ($registry->get('request')->get as $name => $value) {
				if ($name == 'content_language_code') continue;
				$template['hiddens'][ $name ] = $value;
			}
		} else {
			$template['languages'] = array();
		}
		$view->batchAssign($template);
		return $view->fetch('form/language_switcher.tpl');
	}

	/**
	 * @return string
	 * @throws AException
	 */
	public function getContentLanguageFlags() {
		$registry = Registry::getInstance();
		$view = new AView(Registry::getInstance(), 0);
		$registry->get('load')->model('localisation/language');
		$results = $registry->get('model_localisation_language')->getLanguages();
		$template['languages'] = array();

		foreach ($results as $result) {
			if ($result['status']) {
				$template['languages'][ ] = array(
					'name' => $result['name'],
					'code' => $result['code'],
					'image' => $result['image']
				);
			}
		}
		if (sizeof($template['languages']) > 1) {
			$template['language_code'] = $registry->get('session')->data['content_language']; //selected in selectbox
			foreach ($registry->get('request')->get as $name => $value) {
				if ($name == 'content_language_code') continue;
				$template['hiddens'][ $name ] = $value;
			}
		} else {
			$template['languages'] = array();
		}
		$view->batchAssign($template);
		return $view->fetch('form/language_flags.tpl');
	}


	/**
	 * @param $html - text that might contain internal links #admin# or #storefront#
	 *           $mode  - 'href' create complete a tag or default just replace URL
	 * @param string $type - can be 'message' to convert url into <a> tag or empty
	 * @param bool $for_admin - force mode for converting links to admin side from storefront scope (see AIM-class etc)
	 * @return string - html code with parsed internal URLs
	 */
	public function convertLinks($html, $type = '', $for_admin = false) {
		$is_admin = (IS_ADMIN===true || $for_admin) ? true : false;
		$route_sections = $is_admin ? array( "admin", "storefront" ) : array( "storefront" );
		foreach ($route_sections as $rt_type) {
			preg_match_all('/(#' . $rt_type . '#rt=){1}[a-z0-9\/_\-\?\&=\%#]{1,255}(\b|\")/', $html, $matches, PREG_OFFSET_CAPTURE);
			if ($matches) {
				foreach ($matches[ 0 ] as $match) {
					$href = str_replace('?', '&', $match[ 0 ]);

					if ($rt_type == 'admin') {
						if($for_admin && IS_ADMIN!==true){
							$href .= '&s='.ADMIN_PATH;
						}
						$new_href = str_replace('#admin#', $this->getSecureURL('') . '&', $href);
					} else {
						$new_href = str_replace('#storefront#', $this->getCatalogURL('') . '&', $href);
					}
					$new_href = str_replace(array('&amp;','&&','&?'), '&', $new_href);
					$new_href = str_replace('?&', '?', $new_href);
					$new_href = str_replace('&', '&amp;', $new_href);

					switch ($type) {
						case 'message':
							$new_href = '<a href="' . $new_href . '" target="_blank">#link-text#</a>';
							break;
						default:
							break;
					}

					$html = str_replace($match[ 0 ], $new_href, $html);
				}
			}
		}

		return $html;
	}

}

/**
 * Class HtmlElementFactory
 */
class HtmlElementFactory {
	static private $available_elements = array(
		'I' => array(
			'type' => 'input',
			'method' => 'buildInput',
			'class' => 'InputHtmlElement'
		),
		'T' => array(
			'type' => 'textarea',
			'method' => 'buildTextarea',
			'class' => 'TextareaHtmlElement'
		),
		'S' => array(
			'type' => 'selectbox',
			'method' => 'buildSelectbox',
			'class' => 'SelectboxHtmlElement'
		),
		'M' => array(
			'type' => 'multiselectbox',
			'method' => 'buildMultiselectbox',
			'class' => 'MultiSelectboxHtmlElement'
		),
		'R' => array(
			'type' => 'radio',
			'method' => 'buildRadio',
			'class' => 'RadioHtmlElement'
		),
		'C' => array(
			'type' => 'checkbox',
			'method' => 'buildCheckbox',
			'class' => 'CheckboxHtmlElement'
		),
		'G' => array(
			'type' => 'checkboxgroup',
			'method' => 'buildCheckboxgroup',
			'class' => 'CheckboxgroupHtmlElement'
		),
		'U' => array(
			'type' => 'file',
			'method' => 'buildFile',
			'class' => 'FileHtmlElement'
		),
		'K' => array(
			'type' => 'captcha',
			'method' => 'buildCaptcha',
			'class' => 'CaptchaHtmlElement',
		),
		'J' => array(
			'type' => 'recaptcha',
			'method' => 'buildReCaptcha',
			'class' => 'ReCaptchaHtmlElement',
		),
		'H' => array(
			'type' => 'hidden',
			'method' => 'buildHidden',
			'class' => 'HiddenHtmlElement'
		),
		'P' => array(
			'type' => 'multivalue',
			'method' => 'buildMultivalue',
			'class' => 'MultivalueHtmlElement'
		),
		'L' => array(
			'type' => 'multivaluelist',
			'method' => 'buildMultivalueList',
			'class' => 'MultivalueListHtmlElement'
		),
		'D' => array(
			'type' => 'date',
			'method' => 'buildDateInput',
			'class' => 'DateInputHtmlElement'
		),
		'E' => array(
			'type' => 'email',
			'method' => 'buildEmail',
			'class' => 'EmailHtmlElement'
		),
		'N' => array(
			'type' => 'number',
			'method' => 'buildNumber',
			'class' => 'NumberHtmlElement'
		),
		'F' => array(
			'type' => 'phone',
			'method' => 'buildPhone',
			'class' => 'PhoneHtmlElement'
		),
		'A' => array(
			'type' => 'IPaddress',
			'method' => 'buildIPaddress',
			'class' => 'IPaddressHtmlElement'
		),
		'O' => array(
			'type' => 'countries',
			'method' => 'buildCountries',
			'class' => 'CountriesHtmlElement'
		),
		'Z' => array(
			'type' => 'zones',
			'method' => 'buildZones',
			'class' => 'ZonesHtmlElement'
		),

	);

	static private $elements_with_options = array(
		'S', 'M', 'R', 'G', 'O', 'Z',
	);
	static private $multivalue_elements = array(
		'M', 'R', 'G',
	);
	static private $elements_with_placeholder = array(
		'S','I','M','O', 'Z', 'F','N','E','D','U','T'
	);

	/**
	 *  return array of HTML elements supported
	 *  array key - code of element
	 *  [
	 *   type - element type
	 *   method - method in html class to get element html
	 *   class - element class
	 *  ]
	 *
	 * @static
	 * @return array
	 */
	static function getAvailableElements() {
		return self::$available_elements;
	}

	/**
	 * return array of elements indexes for elements which has options
	 *
	 * @static
	 * @return array
	 */
	static function getElementsWithOptions() {
		return self::$elements_with_options;
	}
	/**
	 * return array of elements indexes for elements which has options
	 *
	 * @static
	 * @return array
	 */
	static function getElementsWithPlaceholder() {
		return self::$elements_with_placeholder;
	}

	/**
	 * return array of elements indexes for elements which has options
	 *
	 * @static
	 * @return array
	 */
	static function getMultivalueElements() {
		return self::$multivalue_elements;
	}

	/**
	 * return element type
	 *
	 * @static
	 * @param $code - element code ( from $available_elements )
	 * @return null | string
	 */
	static function getElementType($code) {
		if (!array_key_exists($code, self::$available_elements)) {
			return null;
		}
		return self::$available_elements[ $code ]['type'];
	}

	/**
	 * @param $data
	 * @return HiddenHtmlElement | MultivalueListHtmlElement | MultivalueHtmlElement | SubmitHtmlElement | InputHtmlElement | PasswordHtmlElement | PaginationHtmlElement | TextareaHtmlElement | SelectboxHtmlElement | MultiSelectboxHtmlElement | CheckboxHtmlElement | CheckboxGroupHtmlElement | FileHtmlElement | RadioHtmlElement | ButtonHtmlElement | FormHtmlElement | RatingHtmlElement | CaptchaHtmlElement | ReCaptchaHtmlElement | PasswordsetHtmlElement | ResourceHtmlElement | ResourceImageHtmlElement | DateHtmlElement | EmailHtmlElement | NumberHtmlElement | PhoneHtmlElement | IPaddressHtmlElement | CountriesHtmlElement | ZonesHtmlElement | ModalHtmlElement
	 * @throws AException
	 */
	static function create($data) {

		$class = ucfirst($data['type'] . 'HtmlElement');
		if (!class_exists($class)) {
			throw new AException(AC_ERR_LOAD, 'Error: Could not load HTML element ' . $data['type'] . '!');
		}
		return new $class($data);
	}
}

/**
 * @abstract
 * Class HtmlElement
 */
abstract class HtmlElement {

	protected $data = array();
	protected $view;
	public $element_id;

	/**
	 * @param array $data
	 */
	function __construct($data) {
		if (!isset($data['value'])) $data['value'] = '';
		if (isset($data['required']) && $data['required'] == 1) $data['required'] = 'Y';
		if (isset($data['attr'])) {
			$data['attr'] = ' ' . htmlspecialchars_decode($data['attr']) . ' ';
		}
		$data['registry'] = Registry::getInstance();
		$this->data = $data;

		$this->view = new AView($data['registry'], 0);
		$this->element_id = preformatTextID($data['name']);
		if (isset($data['form']))
			$this->element_id = $data['form'] . '_' . $this->element_id;
	}

	/**
	 * @param string $name
	 * @return null|string
	 */
	public function __get($name) {

		if (array_key_exists($name, $this->data)) {
			return $this->data[ $name ];
		}
		return null;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function __isset($name) {
		return isset($this->data[ $name ]);
	}

	/**
	 * @return string
	 */
	public function __toString() {
		$javascript = '';
		if ($this->data['javascript']) {
			$javascript = $this->data['javascript'];
		} 
		return $javascript . $this->getHtml();
	}

	/**
	 * @return null
	 */
	public function getHtml() {
		return null;
	}

}

/**
 * Class HiddenHtmlElement
 */
class HiddenHtmlElement extends HtmlElement {
	/**
	 * @return string
	 */
	public function getHtml() {
		$this->view->batchAssign(
			array(
				'id' => $this->element_id,
				'name' => $this->name,
				'value' => $this->value,
				'attr' => $this->attr,
			)
		);

		return $this->view->fetch('form/hidden.tpl');
	}
}

/**
 * Class MultivalueListHtmlElement
 */
class MultivalueListHtmlElement extends HtmlElement {
	/**
	 * @return string
	 */
	public function getHtml() {
		$data = array(
			'id' => $this->element_id,
			'name' => $this->name,
			'values' => $this->values,
			'content_url' => $this->content_url,
			'edit_url' => $this->edit_url,
			'postvars' => $this->postvars,
			'form_name' => $this->form,
			'multivalue_hidden_id' => $this->multivalue_hidden_id,
			'return_to' => ($this->return_to ? $this->return_to : $this->form . '_' . $this->multivalue_hidden_id . '_item_count'),
			'with_sorting' => $this->with_sorting
		);

		$data['text']['delete'] = $this->text['delete'] ? $this->text['delete'] : 'delete';
		$data['text']['delete_confirm'] = $this->text['delete_confirm'] ? $this->text['delete_confirm'] : 'Confirm to delete?';
		$data['text']['column_action'] = $this->data['registry']->get('language')->get('column_action');
		$data['text']['column_sort_order'] = $this->data['registry']->get('language')->get('text_sort_order');
		$this->view->batchAssign($data);

		return $this->view->fetch('form/multivalue_list.tpl');
	}
}

/**
 * Class MultivalueHtmlElement
 */
class MultivalueHtmlElement extends HtmlElement {
	/**
	 * @return string
	 */
	public function getHtml() {
		$data = array(
			'id' => $this->element_id,
			'name' => $this->name,
			'selected_name' => ($this->selected_name ? $this->selected_name : 'selected[]'),
			'title' => $this->title,
			'selected' => $this->selected,
			'content_url' => $this->content_url,
			'postvars' => ($this->postvars ? json_encode($this->postvars) : ''),
			'form_name' => $this->form,
			'return_to' => ($this->return_to ? $this->return_to : $this->element_id . '_item_count'),
			'no_save' => (isset($this->no_save) ? (bool)$this->no_save : false),
			'popup_height' => ((int)$this->popup_height ? (int)$this->popup_height : 620),
			'popup_width' => ((int)$this->popup_width ? (int)$this->popup_width : 800),
			'js' => array( // custom triggers for dialog events (custom fucntions calls)
				'apply' => $this->js['apply'],
				'cancel' => $this->js['cancel'],
			) );

		$data['text_selected'] = $this->text['selected'];
		$data['text_edit'] = $this->text['edit'] ? $this->text['edit'] : 'Add / Edit';
		$data['text_apply'] = $this->text['apply'] ? $this->text['apply'] : 'apply';
		$data['text_save'] = $this->text['save'] ? $this->text['save'] : 'save';
		$data['text_reset'] = $this->text['reset'] ? $this->text['reset'] : 'reset';

		$this->view->batchAssign($data);

		return $this->view->fetch('form/multivalue_hidden.tpl');
	}
}

/**
 * Class SubmitHtmlElement
 */
class SubmitHtmlElement extends HtmlElement {
	/**
	 * @return string
	 */
	public function getHtml() {
		$this->view->batchAssign(
			array(
				'form' => $this->form,
				'name' => $this->name,
				'value' => $this->value,
				'attr' => $this->attr,
				'style' => $this->style,
				'icon' => $this->icon,
			)
		);

		return $this->view->fetch('form/submit.tpl');
	}
}

/**
 * Class InputHtmlElement
 */
class InputHtmlElement extends HtmlElement {
	/**
	 * @return string
	 */
	public function getHtml() {

		if (!isset($this->default)) $this->default = '';
		if ($this->value == '' && !empty($this->default)) $this->value = $this->default;

		$this->view->batchAssign(
			array(
				'name' => $this->name,
				'id' => $this->element_id,
				'type' => 'text',
				'value' => str_replace('"', '&quot;', $this->value),
				'default' => $this->default,
				'attr' => $this->attr,
				'required' => $this->required,
				'style' => $this->style,
				'placeholder' => $this->placeholder,
				'regexp_pattern' => trim($this->regexp_pattern,'/'),
				'error_text' => $this->error_text,
			)
		);
		if( is_object($this->data['registry']->get('language')) 
			&& count($this->data['registry']->get('language')->getActiveLanguages()) > 1 ) {
			$this->view->assign('multilingual', $this->multilingual);		
		}
		if (!empty($this->help_url)) {
			$this->view->assign('help_url', $this->help_url);
		}
				
		return $this->view->fetch('form/input.tpl');
	}
}

/**
 * Class PasswordHtmlElement
 */
class PasswordHtmlElement extends HtmlElement {
	/**
	 * @return string
	 */
	public function getHtml() {

		if (!isset($this->default)) $this->default = '';
		if ($this->value == '' && !empty($this->default)) $this->value = $this->default;

		$this->view->batchAssign(
			array(
				'name' => $this->name,
				'id' => $this->element_id,
				'type' => 'password',
				'has_value' => ($this->value) ? 'Y' : 'N',
				'attr' => $this->attr,
				'required' => $this->required,
				'style' => $this->style,
				'placeholder' => $this->placeholder,
				'regexp_pattern' => trim($this->regexp_pattern,'/'),
				'error_text' => $this->error_text,
			)
		);

		return $this->view->fetch('form/input.tpl');
	}
}

/**
 * Class TextareaHtmlElement
 */
class TextareaHtmlElement extends HtmlElement {
	/**
	 * @return string
	 */
	public function getHtml() {
		$this->view->batchAssign(
			array(
				'name' => $this->name,
				'id' => $this->element_id,
				'value' => $this->value,
				'ovalue' => htmlentities($this->value, ENT_QUOTES, 'UTF-8'),
				'attr' => $this->attr,
				'required' => $this->required,
				'style' => $this->style,
				'placeholder' => $this->placeholder,
				'label_text' => $this->label_text
			)
		);
		if( is_object($this->data['registry']->get('language'))
		 && count($this->data['registry']->get('language')->getActiveLanguages()) > 1 ) {
			$this->view->assign('multilingual', $this->multilingual);		
		}
		if (!empty($this->help_url)) {
			$this->view->assign('help_url', $this->help_url);
		}

		return $this->view->fetch('form/textarea.tpl');
	}
}

/**
 * Class TextEditorHtmlElement
 */
class TextEditorHtmlElement extends HtmlElement {
	/**
	 * @return string
	 */
	public function getHtml() {
		$registry = $this->data['registry'];
		$this->view->batchAssign(
			array(
				'name' => $this->name,
				'id' => $this->element_id,
				'value' => $this->value,
				'ovalue' => htmlentities($this->value, ENT_QUOTES, 'UTF-8'),
				'attr' => $this->attr,
				'required' => $this->required,
				'style' => $this->style,
				'placeholder' => $this->placeholder
			)
		);
		if( is_object($this->data['registry']->get('language'))){
			$language = $this->data['registry']->get('language');
			if (count($language->getActiveLanguages()) > 1){
				$this->view->assign('multilingual', $this->multilingual);
			}
			$text = array();
			$text['language_code'] = $registry->get('session')->data['content_language'];
			$text['tab_text'] = $language->get('tab_text');
			$text['tab_visual'] = $language->get('tab_visual');
			$text['button_add_media'] = $language->get('button_add_media');

			$this->view->batchAssign($text);
		}

		return $this->view->fetch('form/text_editor.tpl');
	}
}

/**
 * Class SelectboxHtmlElement
 */
class SelectboxHtmlElement extends HtmlElement {
	/**
	 * @return string
	 */
	public function getHtml() {

		if (!is_array($this->value)) $this->value = array( $this->value => (string)$this->value );

		$this->options = !$this->options ? array() : (array)$this->options;
		foreach ($this->options as &$opt) {
			$opt = (string)$opt;
		}
		unset($opt);

		$registry = $this->data['registry'];
		$text_continue_typing = $text_looking_for = '';
		if(is_object($registry->get('language'))){
			$text_continue_typing = $registry->get('language')->get('text_continue_typing','',true);
			$text_looking_for = $registry->get('language')->get('text_looking_for','',true);
		}

		$text_continue_typing = !$text_continue_typing || $text_continue_typing=='text_continue_typing' ? 'Continue typing ...' : $text_continue_typing;
		$text_looking_for = !$text_looking_for || $text_looking_for=='text_looking_for' ? 'Looking for' : $text_looking_for;


		$this->view->batchAssign(
			array(
				'name' => $this->name,
				'id' => $this->element_id,
				'value' => $this->value,
				'options' => $this->options,
				'attr' => $this->attr,
				'required' => $this->required,
				'style' => $this->style,
				'placeholder' => $this->placeholder,
				'ajax_url' => $this->ajax_url, //if mode of data load is ajax based 
				'search_mode' => $this->search_mode,
				'text_continue_typing' => $text_continue_typing,
				'text_looking_for' => $text_looking_for,
			)
		);
		if (!empty($this->help_url)) {
			$this->view->assign('help_url', $this->help_url);
		}
		if( strpos($this->style,'chosen') !== false ) {
			$this->view->batchAssign(
				array(
				'ajax_url' => $this->ajax_url, //if mode of data load is ajax based 
				'text_continue_typing' => $text_continue_typing,
				'text_looking_for' => $text_looking_for,
				)
			);
			$return = $this->view->fetch('form/chosen_select.tpl');
		} else {
			$return = $this->view->fetch('form/selectbox.tpl');
		}
		return $return;
	}
}

/**
 * Class MultiSelectboxHtmlElement
 */
class MultiSelectboxHtmlElement extends HtmlElement {
	/**
	 * @return string
	 */
	public function getHtml() {

		if (!is_array($this->value)) $this->value = array( $this->value => $this->value );

		$this->view->batchAssign(
			array(
				'name' => $this->name,
				'id' => $this->element_id,
				'value' => $this->value,
				'options' => $this->options,
                'disabled' => $this->disabled,
				'attr' => $this->attr . ' multiple="multiple" ',
				'required' => $this->required,
				'style' => $this->style,
				'placeholder' => $this->placeholder,
			)
		);
		if (!empty($this->help_url)) {
			$this->view->assign('help_url', $this->help_url);
		}

		if( strpos($this->style,'chosen') !== false ) {
			$registry = $this->data['registry'];
			$option_attr = $this->option_attr && !is_array($this->option_attr) ? array($this->option_attr) : $this->option_attr;
			$option_attr = !$option_attr ? array() : $option_attr;
			$this->view->batchAssign(
				array(
				'ajax_url' => $this->ajax_url, //if mode of data load is ajax based 
				'option_attr' => $option_attr, //list of custom html5 attributes for options of selectbox
				'text_continue_typing' => $registry->get('language')->get('text_continue_typing','',true),
				'text_looking_for' => $registry->get('language')->get('text_looking_for','',true),				
				)
			);
			$return = $this->view->fetch('form/chosen_select.tpl');
		} else {
			$return = $this->view->fetch('form/selectbox.tpl');
		}
		return $return;
	}
}

/**
 * Class CheckboxHtmlElement
 */
class CheckboxHtmlElement extends HtmlElement {
	/**
	 * @return string
	 */
	public function getHtml() {

		if( strpos($this->style,'btn_switch') !== false ) { //for switch button NOTE: value is binary (1 or 0)!!!
			$checked = is_null($this->checked) && $this->value ? true : (bool)$this->checked;
			if ( $checked ) {
				$this->value = 1;
			} else {
				$this->value = 0;
			}

			$tpl = 'form/switch.tpl';
		} else {//for generic checkbox NOTE: in this case value must be any and goes to tpl as-is
			$checked = !is_null($this->checked) ? $this->checked : false;
			$tpl = 'form/checkbox.tpl';
		}

		$registry = $this->data['registry'];
		$text_on = $text_off = '';
		if(is_object($registry->get('language'))){
			$text_on = $registry->get('language')->get('text_on','',true);
			$text_off = $registry->get('language')->get('text_off','',true);
		}

		$text_on = !$text_on || $text_on=='text_on' ? 'ON' : $text_on;
		$text_off = !$text_off || $text_off=='text_off' ? 'OFF' : $text_off;

		$this->view->batchAssign(
			array(
				'name' => $this->name,
				'id' => $this->element_id,
				'value' => $this->value,
				'attr' => $this->attr,
				'required' => $this->required,
				'label_text' => $this->label_text,
				'checked' => $checked,
				'style' => $this->style,
				'text_on'=> $text_on,
				'text_off'=> $text_off,
			));
		if (!empty($this->help_url)) {
			$this->view->assign('help_url', $this->help_url);
		}
		
		return $this->view->fetch($tpl);
	}
}

class CheckboxGroupHtmlElement extends HtmlElement {

	public function getHtml() {
		$this->value = !is_array($this->value) ? array( $this->value => $this->value ) : $this->value;
		$this->view->batchAssign(
			array(
				'name' => $this->name,
				'id' => $this->element_id,
				'value' => $this->value,
				'options' => $this->options,
				'attr' => $this->attr . ' multiple="multiple" ',
				'required' => $this->required,
				'scrollbox' => $this->scrollbox,
				'style' => $this->style,
				'placeholder' => $this->placeholder,
			)
		);
		if (!empty($this->help_url)) {
			$this->view->assign('help_url', $this->help_url);
		}

		if( strpos($this->style,'chosen') !== false ) {
			$return = $this->view->fetch('form/chosen_select.tpl');
		} else {
			$return = $this->view->fetch('form/checkboxgroup.tpl');
		}

		return $return;
	}
}

/**
 * Class FileHtmlElement
 */
class FileHtmlElement extends HtmlElement {
	/**
	 * @return string
	 */
	public function getHtml() {
        /**
         * @var $registry Registry
         */
        $registry = $this->data['registry'];
	
		$this->view->batchAssign(
			array(
				'name' => $this->name,
				'id' => $this->element_id,
				'attr' => $this->attr,
				'required' => $this->required,
				'style' => $this->style,
				'default_text' => $registry->get('language')->get('text_click_browse_file'),
				'text_browse' => $registry->get('language')->get('text_browse'),
				'placeholder' => $this->placeholder,
			)
		);

		if (!empty($this->help_url)) {
			$this->view->assign('help_url', $this->help_url);
		}

		return $this->view->fetch('form/file.tpl');
	}
}

class RadioHtmlElement extends HtmlElement {

	public function getHtml() {
		$this->view->batchAssign(
			array(
				'name' => $this->name,
				'id' => $this->element_id,
				'value' => $this->value,
				'options' => $this->options,
				'attr' => $this->attr,
				'required' => $this->required,
				'style' => $this->style,
			)
		);
		if (!empty($this->help_url)) {
			$this->view->assign('help_url', $this->help_url);
		}

		return $this->view->fetch('form/radio.tpl');
	}
}

class ButtonHtmlElement extends HtmlElement {

	public function getHtml() {
		$this->view->batchAssign(
			array( 
				'text' => $this->text,
				'title' => $this->title,
				'id' => $this->element_id,
				'attr' => $this->attr,
				'style' => $this->style,
				'href' => $this->href,
				'href_class' => $this->href_class,
				'icon' => $this->icon,
				'target' => $this->target
			)
		);

		return $this->view->fetch('form/button.tpl');
	}
}

class FormHtmlElement extends HtmlElement {

	public function getHtml() {
		$this->method = empty($this->method) ? 'post' : $this->method;
		$this->view->batchAssign(
			array(
				'id' => $this->name,
				'name' => $this->name,
				'action' => $this->action,
				'method' => $this->method,
				'attr' => $this->attr,
				'style' => $this->style,
			)
		);

		return $this->view->fetch('form/form_open.tpl');
	}
}

class RatingHtmlElement extends HtmlElement {

	function __construct($data) {
		parent::__construct($data);
		if (!$this->data['registry']->has('star-rating')) {
			/**
			 * @var $doc ADocument
			 */
			$doc = $this->data['registry']->get('document');
			$doc->addScript($this->view->templateResource('/javascript/jquery/star-rating/jquery.MetaData.js'));
			$doc->addScript($this->view->templateResource('/javascript/jquery/star-rating/jquery.rating.pack.js'));

			$doc->addStyle(array(
				'href' => $this->view->templateResource('/javascript/jquery/star-rating/jquery.rating.css'),
				'rel' => 'stylesheet',
				'media' => 'screen',
			));

			$this->data['registry']->set('star-rating', 1);
		}
	}

	public function getHtml() {
		$this->view->batchAssign(
			array(
				'name' => $this->name,
				'id' => $this->element_id,
				'value' => $this->value,
				'options' => $this->options,
				'style' => 'star',
				'required' => $this->required,
			)
		);

		return $this->view->fetch('form/rating.tpl');
	}
}


class CaptchaHtmlElement extends HtmlElement {

	public function getHtml() {
		$this->view->batchAssign(
			array(
				'name' => $this->name,
				'id' => $this->element_id,
				//TODO: remove deprecated attribute aform_field_type
				'attr' => 'aform_field_type="captcha" '.$this->attr.' data-aform-field-type="captcha"',
				'style' => $this->style,
				'required' => $this->required,
				'captcha_url' => $this->data['registry']->get('html')->getURL('common/captcha'),
				'placeholder' => $this->placeholder
			)
		);
		return $this->view->fetch('form/captcha.tpl');
	}
}

class ReCaptchaHtmlElement extends HtmlElement {

	public function getHtml() {
		$this->view->batchAssign(
			array(
				'name' => $this->name,
				'id' => $this->element_id,
				'attr' => $this->attr.' data-aform-field-type="captcha"',
				'language_code' => $this->language_code,
				'recaptcha_site_key' => $this->recaptcha_site_key
			)
		);
		return $this->view->fetch('form/recaptcha.tpl');
	}
}

class PasswordsetHtmlElement extends HtmlElement {

	public function getHtml() {
		$this->view->batchAssign(
			array(
				'name' => $this->name,
				'id' => $this->element_id,
				'value' => $this->value,
				'attr' => $this->attr,
				'style' => $this->style,
				'required' => $this->required,
				'text_confirm_password' => $this->data['registry']->get('language')->get('text_confirm_password'),
				'placeholder' => $this->placeholder,
			)
		);
		return $this->view->fetch('form/passwordset.tpl');
	}
}

class ResourceHtmlElement extends HtmlElement {

	function __construct($data) {
		parent::__construct($data);
	}

	public function getHtml() {
		if(empty($this->rl_type)){
			throw new AException(AC_ERR_LOAD, 'Error: Could not load HTML element of resource library. Resource type not given!');
		}
		$data = array(
			'id' => $this->element_id,
			'wrapper_id' => $this->element_id.'_wrapper',
			'name' => $this->name,
			'resource_path' => $this->resource_path, //path
			'resource_id'=>$this->resource_id, //resource_id
			'object_name'=> $this->object_name,
			'object_id'=> $this->object_id,
			'rl_type'=> $this->rl_type, // image or audio or pdf etc
			'hide'=> ($this->hide ? true : false) // hide image preview
		);
		if(!$data['resource_id'] && $data['resource_path']){
			$path = ltrim($data['resource_path'], $data['rl_type'].'/');
			$r = new AResource($data['rl_type']);
			$data['resource_id'] = $r->getIdFromHexPath( $path );
		}
		if($data['resource_id'] && !$data['resource_path']){
			$r = new AResource($data['rl_type']);
			$info = $r->getResource($data['resource_id']);
			if($info['resource_path']){
				$data['resource_path'] = $data['rl_type'] . '/' . $info['resource_path'];
			}else{
				//for code-resources
				$data['resource_path'] = $data['resource_id'];
			}
		}

		$this->view->batchAssign($data);

		return $this->view->fetch('form/resource.tpl');
	}
}

class ResourceImageHtmlElement extends HtmlElement {

	function __construct($data) {
		parent::__construct($data);
	}

	public function getHtml() {

		$this->view->batchAssign(array( 'url' => $this->url,
			'width' => $this->width,
			'height' => $this->height,
			'attr' => $this->attr,
		));

		return $this->view->fetch('common/resource_image.tpl');
	}

}

class DateHtmlElement extends HtmlElement {

	function __construct($data) {
		parent::__construct($data);
		if (!$this->data['registry']->has('date-field')) {

			$doc = $this->data['registry']->get('document');
			$doc->addScript($this->view->templateResource('/javascript/jquery-ui/js/jquery-ui-1.10.4.custom.min.js'));
			$doc->addScript($this->view->templateResource('/javascript/jquery-ui/js/jquery.ui.datepicker.js'));
			if(IS_ADMIN===true){
				$doc->addStyle(array(
					'href' => $this->view->templateResource('/javascript/jquery-ui/js/css/ui-lightness/ui.all.css'),
					'rel' => 'stylesheet',
					'media' => 'screen',
				));
			}else{
				$doc->addStyle(array(
						'href'  => $this->view->templateResource('/javascript/jquery-ui/css/ui-lightness/jquery-ui-1.10.4.custom.min.css'),
						'rel'   => 'stylesheet',
						'media' => 'screen',
				));
			}

			$this->data['registry']->set('date-field', 1);
		}
	}

	/**
	 * @return string
	 */
	public function getHtml() {

		if (!isset($this->default)) $this->default = '';
		if ($this->value == '' && !empty($this->default)) $this->value = $this->default;

		$this->element_id = preg_replace('/[\[+\]+]/', '_', $this->element_id);

		$this->view->batchAssign(
			array(
				'name' => $this->name,
				'id' => $this->element_id,
				'type' => 'text',
				'value' => str_replace('"', '&quot;', $this->value),
				'default' => $this->default,
				//TODO: remove deprecated attribute aform_field_type
				'attr' => 'aform_field_type="date" ' . $this->attr.' data-aform-field-type="captcha"',
				'required' => $this->required,
				'style' => $this->style,
				'dateformat' => $this->dateformat ? $this->dateformat : format4Datepicker($this->data['registry']->get('language')->get('date_format_short')),
				'highlight' => $this->highlight
			)
		);
		if (!empty($this->help_url)) {
			$this->view->assign('help_url', $this->help_url);
		}

		return $this->view->fetch('form/date.tpl');
	}
}

/**
 * Class EmailHtmlElement
 */
class EmailHtmlElement extends HtmlElement {
	/**
	 * @return string
	 */
	public function getHtml() {

		if (!isset($this->default)) $this->default = '';
		if ($this->value == '' && !empty($this->default)) $this->value = $this->default;
		$this->view->batchAssign(
			array(
				'name' => $this->name,
				'id' => $this->element_id,
				'type' => 'text',
				'value' => str_replace('"', '&quot;', $this->value),
				'default' => $this->default,
				//TODO: remove deprecated attribute aform_field_type
				'attr' => 'aform_field_type="email" ' . $this->attr.' data-aform-field-type="captcha"',
				'required' => $this->required,
				'style' => $this->style,
				'placeholder' => $this->placeholder,
				'regexp_pattern' => trim($this->regexp_pattern,'/'),
				'error_text' => $this->error_text
			)
		);
		if (!empty($this->help_url)) {
			$this->view->assign('help_url', $this->help_url);
		}

		return $this->view->fetch('form/input.tpl');
	}
}

/**
 * Class NumberHtmlElement
 */
class NumberHtmlElement extends HtmlElement {
	/**
	 * @return string
	 */
	public function getHtml() {

		if (!isset($this->default)) $this->default = '';
		if ($this->value == '' && !empty($this->default)) $this->value = $this->default;
		$this->view->batchAssign(
			array(
				'name' => $this->name,
				'id' => $this->element_id,
				'type' => 'text',
				'value' => str_replace('"', '&quot;', $this->value),
				'default' => $this->default,
				//TODO: remove deprecated attribute aform_field_type
				'attr' => 'aform_field_type="number" ' . $this->attr.' data-aform-field-type="captcha"',
				'required' => $this->required,
				'style' => $this->style,
				'placeholder' => $this->placeholder,
				'regexp_pattern' => trim($this->regexp_pattern,'/'),
				'error_text' => $this->error_text
			)
		);
		if (!empty($this->help_url)) {
			$this->view->assign('help_url', $this->help_url);
		}

		return $this->view->fetch('form/input.tpl');
	}
}

/**
 * Class PhoneHtmlElement
 */
class PhoneHtmlElement extends HtmlElement {
	/**
	 * @return string
	 */
	public function getHtml() {

		if (!isset($this->default)){
			$this->default = '';
		}
		if ($this->value == '' && !empty($this->default)){
			$this->value = $this->default;
		}

		/**
		 * @var $doc ADocument
		 */
		$doc = $this->data['registry']->get('document');
		$doc->addScript($this->view->templateResource('/javascript/intl-tel-input/js/intlTelInput.min.js'));
		$doc->addStyle(array (
						'href' => RDIR_TEMPLATE . 'javascript/intl-tel-input/css/intlTelInput.css',
						'rel'  => 'stylesheet'
				));

		$this->view->batchAssign(
			array(
				'name' => $this->name,
				'id' => $this->element_id,
				'type' => 'tel',
				'value' => str_replace('"', '&quot;', $this->value),
				'default' => $this->default,
				//TODO: remove deprecated attribute aform_field_type
				'attr' => $this->attr,
				'required' => $this->required,
				'style' => $this->style,
				'placeholder' => $this->placeholder,
				'regexp_pattern' => trim($this->regexp_pattern,'/'),
				'error_text' => $this->error_text
			)
		);
		if (!empty($this->help_url)) {
			$this->view->assign('help_url', $this->help_url);
		}

		return $this->view->fetch('form/phone.tpl');
	}
}

class IPaddressHtmlElement extends HtmlElement {

	public function getHtml() {
		$this->view->batchAssign(
			array(
				'id' => $this->element_id,
				'name' => $this->name,
				'value' => $_SERVER['REMOTE_ADDR'],
				//TODO: remove deprecated attribute aform_field_type
				'attr' => 'aform_field_type="ipaddress" ' . $this->attr.' data-aform-field-type="captcha"',
			)
		);

		return $this->view->fetch('form/hidden.tpl');
	}
}

class CountriesHtmlElement extends HtmlElement {

	public function __construct($data) {
		parent::__construct($data);
		$this->data['registry']->get('load')->model('localisation/country');
		$results = $this->data['registry']->get('model_localisation_country')->getCountries();
		$this->options = array();
		foreach ($results as $c) {
			$this->options[ $c['name'] ] = $c['name'];
		}
	}

	public function getHtml() {

		if (!is_array($this->value)) $this->value = array( $this->value => (string)$this->value );

		$this->options = !$this->options ? array() : $this->options;

		$this->view->batchAssign(
			array(
				'name' => $this->name,
				'id' => $this->element_id,
				'value' => $this->value,
				'options' => $this->options,
				'attr' => $this->attr,
				'required' => $this->required,
				'style' => $this->style,
				'placeholder' => $this->placeholder
			)
		);
		if (!empty($this->help_url)) {
			$this->view->assign('help_url', $this->help_url);
		}

		return $this->view->fetch('form/selectbox.tpl');
	}

}

class ZonesHtmlElement extends HtmlElement {
	//private $default_zone_value, $default_value;
	public function __construct($data) {
		parent::__construct($data);
		$this->data['registry']->get('load')->model('localisation/country');
		$results = $this->data['registry']->get('model_localisation_country')->getCountries();
		$this->options = array();
		$this->zone_options = array();
		$this->default_zone_field_name = 'zone_id';
		$config_country_id = $this->data['registry']->get('config')->get('config_country_id');
		foreach ($results as $c) {
			if($c['country_id']== $config_country_id){
				$this->default_value = $this->submit_mode == 'id' ? array($config_country_id) : array($c['name']=>$c['name']);
			}
			if ($this->submit_mode == 'id') {
				$this->options[ $c['country_id'] ] = $c['name'];
			} else {
				$this->options[ $c['name'] ] = $c['name'];
			}
		}
	}

	public function getHtml() {
		if($this->value && !is_array($this->value)){
			$this->value = array( $this->value => (string)$this->value );
		}else{
			$this->value = array();
		}

		$this->zone_name = !$this->zone_name ? '' : urlencode($this->zone_name);
		$this->default_zone_value = array();
		$this->options = !$this->options ? array() : $this->options;
		$this->element_id = preg_replace('/[\[+\]+]/', '_', $this->element_id);

		$html = new AHtml($this->data['registry']);
		
		if ($this->submit_mode == 'id') {
			$url = $html->getSecureURL('common/zone');
		} else {
			$url = $html->getSecureURL('common/zone/names');
		}


		$this->data['registry']->get('load')->model('localisation/zone');

		$results = array();
		if($this->submit_mode=='id'){
			$id = $this->value ? key($this->value) : $this->data['registry']->get('config')->get('config_country_id');
			$results = $this->data['registry']->get('model_localisation_zone')->getZonesByCountryId($id);

		}else{
			if($this->value){
				$name = current($this->value);
			}else{
				$this->data['registry']->get('load')->model('localisation/country');
				$temp = $this->data['registry']->get('model_localisation_country')->getCountry($this->data['registry']->get('config')->get('config_country_id'));
				$name = $temp['name'];
			}
			$results = $this->data['registry']->get('model_localisation_zone')->getZonesByCountryName($name);
		}

		if (!is_array($this->zone_value)){
			$this->zone_value = $this->zone_value ? array( $this->zone_value => (string)$this->zone_value ) : array();
		}
		$config_zone_id = $this->data['registry']->get('config')->get('config_zone_id');
		foreach ($results as $result) {
			// default zone_id is zone of shop
			if($result['zone_id']== $config_zone_id){
				$this->default_zone_value = $this->submit_mode == 'id' ? array($config_zone_id) : array($result['name']=>$result['name']);
				$this->default_zone_name = $result['name'];
			}

			if ($this->submit_mode == 'id') {
				$this->zone_options[$result['zone_id']] = $result['name'];
			}else{
				$this->zone_options[$result['name']] = $result['name'];
			}
		}

		$this->view->batchAssign(
			array(
				'name' => $this->name,
				'id' => $this->element_id,
				'value' => $this->value ? $this->value : $this->default_value,
				'options' => $this->options,
				'attr' => $this->attr,
				'required' => $this->required,
				'style' => $this->style,
				'url' => $url,
				'zone_field_name' => $this->zone_field_name ? $this->zone_field_name : $this->default_zone_field_name,
				'zone_name' => $this->zone_name ? $this->zone_name : $this->default_zone_name,
				'zone_value' => (array)($this->zone_value ? $this->zone_value : $this->default_zone_value),
				'zone_options' => $this->zone_options,
				'submit_mode' => $this->submit_mode,
				'placeholder' => $this->placeholder
			)
		);
		if (!empty($this->help_url)) {
			$this->view->assign('help_url', $this->help_url);
		}

		return $this->view->fetch('form/countries_zones.tpl');
	}

}

/*
* Build pagination HTML element based on the template. 
* Supported v 1.1.5+
*/

class PaginationHtmlElement extends HtmlElement {
	public $sts = array();

	/**
	 * @param array $data
	 */
	public function __construct($data) {
		parent::__construct($data);
		//default settings
		$this->sts['total'] = 0;
		$this->sts['page'] = 1;
		$this->sts['limit'] = 20;
		$this->sts['split'] = 10;
		$this->sts['limits'] = array();
		//max pages to show in pagination
		$this->sts['num_links'] = 10;
		$this->sts['url'] = '';
		$this->sts['text'] = 'Showing {start} to {end} of {total} ({pages} Pages)';
		$this->sts['text_limit'] = 'Per Page';
		$this->sts['text_first'] = '&lt;&lt;';
		$this->sts['text_last'] = '&gt;&gt;';
		$this->sts['text_next'] = '&gt;';
		$this->sts['text_prev'] = '&lt;';
		$this->sts['style_links'] = 'links';
		$this->sts['style_results'] = 'results';
		$this->sts['style_limits'] = 'limits';
		//override default
		foreach ($this->data as $key => $val) {
			if ( isset( $val ) ) {
				$this->sts[$key]= $val;
			}
		}
	}

	/**
	 * @return string
	 */
	public function getHtml() {
		//Build pagination data and dysplay
		/**
		 * @var $registry Registry
		 */
		$registry = $this->data['registry'];
		$html = new AHtml($registry);
		$s = $this->sts;
		//some more defaults		
		if ($s['page'] < 1 || !is_numeric($s['page'])) {
			$s['page'] = 1;
		}
		if (!$s['limit'] || !is_numeric($s['limit'])) {
			$s['limit'] = 10;
		}
		
		//count limits if needed
		if(!$s['no_perpage'] && !$s['limits']){
			$s['limits'][0] = $x = ( $s['split'] ? $s['split'] : $registry->get('config')->get('config_catalog_limit') );
			while( $x <= 50 ){
				$s['limits'][] = $x;
				$x += $s['limits'][0];
			}
		}
		
		
		$s['url'] = str_replace('{limit}', $s['limit'], $s['url']);
		$s['total_pages'] = ceil($s['total'] / $s['limit']);	

		if ($s['page'] > 1) {
			//not first page
			$this->view->assign('first_url', str_replace('{page}', 1, $s['url']));
			$this->view->assign('prev_url', str_replace('{page}', $s['page'] - 1, $s['url']));
		}
		
		if ($s['total_pages'] > 1) {
			if ($s['total_pages'] <= $s['num_links']) {
				$s['start'] = 1;
				$s['end'] = $s['total_pages'];
			} else {
				$s['start'] = $s['page'] - floor($s['num_links'] / 2);
				$s['end'] = $s['page'] + floor($s['num_links'] / 2);
			
				if ($s['start'] < 1) {
					$s['end'] += abs($s['start']) + 1;
					$s['start'] = 1;
				}
				if ($s['end'] > $s['total_pages']) {
					$s['start'] -= ($s['end'] - $s['total_pages']);
					$s['end'] = $s['total_pages'];
				}
			}
		} else {
			$s['start'] = $s['end'] = 1;
		}
		
   		if ($s['page'] < $s['total_pages']) {
			$this->view->assign('next_url', str_replace('{page}', $s['page'] + 1, $s['url']));
			$this->view->assign('last_url', str_replace('{page}', $s['total_pages'], $s['url']));
		}
		
		
		$replace = array(
			($s['total']) ? (($s['page'] - 1) * $s['limit']) + 1 : 0,
			((($s['page'] - 1) * $s['limit']) > ($s['total'] - $s['limit'])) ? $s['total'] : ((($s['page'] - 1) * $s['limit']) + $s['limit']),
			$s['total'], 
			$s['total_pages']
		);

		if(!$s['no_perpage']) {
			if ( !in_array($s['limit'], $s['limits']) ) {
				$s['limits'][] = $s['limit'];
				sort($s['limits']);
			}
			$options = array();
			foreach($s['limits'] as $item){
				$options[$item] = $item;
			}
	
			$limit_select = $html->buildSelectbox( array(
				                                'name' => 'limit',
				                                'value'=> $s['limit'],
				                                'options' => $options,
				                                'style' => 'input-mini',
				                                'attr' => ' onchange="location=\'' . str_replace('{page}', 1, $s['url']) . '&limit=\'+this.value;"',
	                    						)
			);
	
			$limit_select = str_replace('&', '&amp;', $limit_select);
			$this->view->assign('limit_select',$limit_select);
		}
			
		$find = array(
			'{start}',
			'{end}',
			'{total}',
			'{pages}',
			'{limit}'
		);		
		$s['text'] = str_replace($find, $replace, $s['text']);

		$this->view->batchAssign( $s );
		
		$return = $this->view->fetch('form/pagination.tpl');
		return $return;
	}

}

/**
 * Class ModalHtmlElement
 */
class ModalHtmlElement extends HtmlElement {
	/**
	 * @param array $data
	 */
	public function __construct($data) {
		parent::__construct($data);

	}

	/**
	 * @return string
	 */
	public function getHtml() {

		$modal_type = $this->modal_type ? $this->modal_type : 'lg';

		$this->view->batchAssign(
			array(
				'id' => $this->id,
				'title' => $this->title,
				'content' => $this->content,
				'footer' => $this->footer,
				'modal_type' => $modal_type,
				// if 'ajax' we clean up modal content after it close
				'data_source' => (string)$this->data_source,
				// js-triggers for modal events
				'js_onshow' => (string)$this->js_onshow,
				'js_onload' => ($this->data_source =='ajax' ? (string)$this->js_onload : ';'),  //if content
				'js_onclose' => (string)$this->js_onclose,
			)
		);

		$tpl = 'form/modal.tpl';

		$return = $this->view->fetch($tpl);
		return $return;
	}

}