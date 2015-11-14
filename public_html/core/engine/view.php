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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
/**
 * Class AView
 * @property AConfig $config
 * @property ExtensionsAPI $extensions
 * @property AResponse $response
 *
 */
class AView {
	/**
	 * @var $registry Registry
	 */
	protected $registry;
	/**
	 * @var
	 */
	protected $id;
	/**
	 * @var string
	 */
	protected $template = '';
	/**
	 * @var string
	 */
	protected $default_template;
	/**
	 * @var int
	 */
	protected $instance_id;
	/**
	 * @var bool
	 */
	protected $enableOutput = false;
	/**
	 * @var string
	 */
	protected $output = '';
	/**
	 * @var array
	 */
	protected $hook_vars = array();
	/**
	 * @var array
	 */
	public $data = array();

	protected $render;
	/**
	 * @var bool
	 */
	protected $has_extensions;
	/**
	 * @param Registry $registry
	 * @param int $instance_id
	 */
	public function __construct($registry, $instance_id) {
		$this->registry = $registry;
		$this->has_extensions = $this->registry->has('extensions');
		if ( $this->registry->get('config') ) {
			$this->default_template = IS_ADMIN ? $this->registry->get('config')->get('admin_template') : $this->registry->get('config')->get('config_storefront_template');
		}
		$this->data['template_dir'] = RDIR_TEMPLATE;
		$this->data['tpl_common_dir'] = RDIR_TEMPLATE . '/template/common/';
		$this->instance_id = $instance_id;
	}

	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	/**
	 * @param string $url
	 */
	protected function redirect($url) {
		header('Location: ' . str_replace('&amp;', '&', $url));
		die();
	}

	/**
	 * @void
	 */
	public function enableOutput() {
        $this->enableOutput = true;
    }

	/**
	 * @void
	 */
	public function disableOutput() {
        $this->enableOutput = false;
    }

	/**
	 * @param string $template
	 */
	public function setTemplate($template){
		$this->template = $template;
	}

	/**
	 * @return string
	 */
	public function getTemplate(){
		return $this->template;
	}

	/**
	 * @param string $key - optional parameter for better access from hook that called by "_UpdateData".
	 * @return array | mixed
	 */
	public function getData($key='') {
		if($key){
			return $this->data[$key];
		}else{
        	return $this->data;
		}
    }

	/**
	 * @param string $template_variable
	 * @param string $value
	 * @param string $default_value
	 * @return null
	 */
	public function assign($template_variable, $value = '', $default_value = ''){
		if (empty($template_variable)){
			return null;
		}
        if ( !is_null($value) ) {
		    $this->data[$template_variable] = $value;
        } else {
            $this->data[$template_variable] = $default_value;
        }
	}
	
	/**
	 * Call append if you need to add values to earlier assigned value
	 * @param string $template_variable
	 * @param string $value
	 * @param string $default_value
	 * @return null
	 */
	public function append($template_variable, $value = '', $default_value = ''){
		if (empty($template_variable)){
			return null;
		}
        if ( !is_null($value) ) {
		    $this->data[$template_variable] = $this->data[$template_variable] . $value;
        } else {
            $this->data[$template_variable] = $this->data[$template_variable] . $default_value;
        }
	}

	/**
	 * @param array $assign_arr - associative array
	 * @return null
	 */
	public function batchAssign($assign_arr){
		if (empty($assign_arr) || !is_array($assign_arr)){
			return null;
		}

		foreach($assign_arr as $key => $value) {
			//when key already defined and type of old and new values are different send warning in debug-mode
			if(isset($this->data[$key]) && is_object($this->data[$key])){
				$warning_text = 'Warning! Variable "'.$key.'" in template "'.$this->template.'" overriding value and data type "object." ';
				$warning_text .= 'Possibly need to review your code! (also check that extensions do not load language definitions in UpdateData hook).';
				$warning = new AWarning($warning_text);
				$warning->toDebug();
				continue; // prevent overriding.
			}elseif( isset($this->data[$key]) && gettype($this->data[$key]) != gettype($value) ){
				$warning_text = 'Warning! Variable "'.$key.'" in template "'.$this->template.'" overriding value and data type "'.gettype($this->data[$key]).'" ';
				$warning_text .= 'Forcing new data type '.gettype($value).'. Possibly need to review your code!';
				$warning = new AWarning($warning_text);
				$warning->toDebug();
			}
			$this->data[$key] =  $value;
		}
	}

	/**
	 * @param string $name
	 * @param string $value
	 */
	public function addHookVar($name, $value) {
        if (!empty($name)){
            $this->hook_vars[$name] .= $value;
        }
    }

	/**
	 * @param string $name
	 * @return string
	 */
	public function getHookVar($name) {
        if (isset($this->hook_vars[$name])) {
            return $this->hook_vars[$name];
        }
        return '';
    }
	
     // Render html output
     public function render(){
    	// If no template return empty. We might have controller that has no templates
     	if ( !empty($this->template) && $this->enableOutput ) {
     		$compression = '';
     		if ($this->config) { 
     			$compression = $this->config->get('config_compression');
     		}
            if ( !empty( $this->output ) ) {
        	    $this->response->setOutput($this->output, $compression);
            } else {
                $this->response->setOutput($this->fetch($this->template), $compression);
            }
     	}
     }

	/**
	 * @return string
	 */
	public function getOutput() {
        return !empty( $this->output ) ? $this->output : !empty($this->template) ? $this->fetch($this->template) : '';
    }

	/**
	 * @param string $output
	 * @void
	 */
	public function setOutput( $output ) {
        $this->output = $output;
    }

	/**
	 * Process the template
	 * @param $filename
	 * @return string
	 */
	public function fetch($filename) {
		ADebug::checkpoint('fetch '.$filename.' start');
		//#PR First see if we have full path to template file. Nothing to do. Higher precedence!
		if (is_file($filename)) {
			//#PR set full path
			$file = $filename;
		} else {
			//#PR Build the path to the template file
			$path = DIR_TEMPLATE;
			if (!defined('INSTALL')) {
		        $file = $this->_get_template_path($path, '/template/'.$filename, 'full');
		    } else {
		        $file = $path.$filename;
		    }
	
	        if ( $this->has_extensions && $result = $this->extensions->isExtensionResource('T', $filename) ) {
	            if ( is_file($file) ) {
	                $warning = new AWarning("Extension <b>".$result['extension']."</b> overrides core template with <b>".$filename."</b>" );
	                $warning->toDebug();
	            }
	            $file = $result['file'];
	        }
		}
	    
	    if (empty($file)) {
			$error = new AError('Error: Unable to identify file path to template ' . $filename . '! Check blocks in the layout or enable debug mode to get more details. ' . AC_ERR_LOAD);
			$error->toDebug()->toLog();
			return '';
	    }
	    
		if (is_file($file)) {
            $content = '';
            $file_pre = str_replace('.tpl', POSTFIX_PRE.'.tpl', $filename );
            if ( $result = $this->extensions->getAllPrePostTemplates($file_pre) ) {
	            foreach($result as $item){
                    $content .= $this->_fetch($item['file']);
                }
            }
			
      		$content .= $this->_fetch($file);

            $file_post = str_replace('.tpl', POSTFIX_POST.'.tpl', $filename );
            if ( $result = $this->extensions->getAllPrePostTemplates( $file_post) ) {
	            foreach($result as $item){
		            $content .= $this->_fetch($item['file']);
	            }
            }
			ADebug::checkpoint('fetch '.$filename.' end');
      		return $content;
    	} else {
			$error = new AError('Error: Could not load template ' . $filename . '! File '.$file.' is missing or incorrect. Check blocks in the layout or enable debug mode to get more details. ', AC_ERR_LOAD);
			$error->toDebug()->toLog();
    	}

		return '';
	}

	/**
	 * Storefront function to return path to the resource
	 * @param $filename
	 * @return string with relative path
	 */
    public function templateResource( $filename) {
    	if ( !$filename ) {
    		return null;    	
    	}
	    $output = '';
		$res_arr = $this->_extensions_resource_map($filename);
		//get first exact template extension resource or default template resource othewise.
		if ( count($res_arr['original'])) {
			$output = $res_arr['original'][0];
		} else if(count($res_arr['default'])) {
			$output = $res_arr['default'][0];
		}else{
			//no extension found, use resource from core templates
			$output = $this->_get_template_path(DIR_TEMPLATE, $filename, 'relative');
		}

	    if(!in_array(pathinfo($filename,PATHINFO_EXTENSION),array('tpl', 'php'))){
		    $this->extensions->hk_ProcessData($this, __FUNCTION__);
		    $http_path = $this->data['http_dir'];
	    }

	    return $http_path.$output;
    }

	/**
	 * @param string $filename
	 * @return bool
	 */
	public function isTemplateExists( $filename ) {
    	if ( !$filename ) {
    		return false;    	
    	} 
       
    	//check if this template file in extensions or in core
    	if ( $this->templateResource('/template/'. $filename) ) {
    		return true;
    	} else {
    		return false;
    	}
    }

	/**
	 * full directory path
	 * @param string $extension_name
	 * @return string
	 */
	private function _extension_view_dir( $extension_name ) {
		return  $this->_extension_section_dir( $extension_name ) . DIR_EXT_TEMPLATE;
	}

	/**
	 * full directory path
	 * @param string $extension_name
	 * @return string
	 */
	private function _extension_section_dir( $extension_name ) {
		$rel_view_path = (IS_ADMIN ? DIR_EXT_ADMIN : DIR_EXT_STORE);
		return  DIR_EXT . $extension_name . $rel_view_path;
	}


	/**
	 * Build template source map for enabled extensions
	 * @param string $filename
	 * @return array
	 */
	private function _extensions_resource_map($filename) {
 		if (empty($filename)) {
 			return array();
 		}
 		$ret_arr = array();
        $extensions = $this->extensions->getEnabledExtensions();
		//loop through each extension and locate resource to use 
		//Note: first extension with exact resource or default resource will be used 
	    foreach ( $extensions as $ext ) {
	    	$res_arr = $this->_test_template_paths( $this->_extension_view_dir($ext), $filename, 'relative' );
	    	if ( $res_arr ) {
	    		$ret_arr[$res_arr['match']][] = DIR_EXTENSIONS . $ext .'/'. $res_arr['path'];
	    	} 	    
        }

		return $ret_arr;
	}

	/**
	 * return path to the template resource
	 * @param string $path
	 * @param string $filename
	 * @param string $mode
	 * @return mixed
	 */
	private function _get_template_path($path, $filename, $mode) {
		//look into extensions first
		$res_arr = $this->_extensions_resource_map($filename);
		//get first exact template extension resource or default template resource othewise.
		if ( count($res_arr['original'])) {
			return $res_arr['original'][0];
		} else if(count($res_arr['default'])) {
			return $res_arr['default'][0];
		}

		$template_path_arr = $this->_test_template_paths($path, $filename, $mode);
		return $template_path_arr['path'];
	}

	/**
	 * Function to test file paths and location of original or default file
	 * @param string $path
	 * @param string $filename
	 * @param string $mode
	 * @return array|null
	 */
	private function _test_template_paths($path, $filename, $mode = 'relative') {
    	$ret_path = '';
        $template = $this->default_template;
		$match = 'original';

		if (IS_ADMIN) {
	        if (is_file( $path . $template . $filename)) {
	        	$ret_path = $path . $template . $filename;
	        	if ($mode == 'relative') {
	            	$ret_path = 'admin/view/' . $template . $filename;
	        	}
	        } else if (is_file( $path . 'default' . $filename)) {
	        	$ret_path = $path . 'default' . $filename;
	        	if ($mode == 'relative') {
	            	$ret_path = 'admin/view/default' . $filename;
	            	$match = 'default';
	        	}
	        }
		} else {
			if (is_file( $path . $template . $filename)) {
	        	$ret_path = $path . $template . $filename;
	        	if ($mode == 'relative') {
	            	$ret_path = 'storefront/view/' . $template . $filename;
	        	}
	        } else if (is_file( $path . 'default' . $filename)) {
	        	$ret_path = $path . 'default' . $filename;
	        	if ($mode == 'relative') {
	            	$ret_path = 'storefront/view/default' . $filename;
	            	$match = 'default';
	        	}
	        }
		}
		//return path. Empty path indicates, nothing found
		if ( $ret_path ) {
			return array( 'match' => $match,  'path' => $ret_path );
		} else {
			return null;
		}
	}

	/**
	 * @param $file string - full path of file
	 * @return string
	 */
	public function _fetch( $file ) {

        if ( !file_exists($file) ) return '';

        ADebug::checkpoint('_fetch '.$file.' start');
        extract($this->data);

        ob_start();
		/** @noinspection PhpIncludeInspection */
		require($file);
        $content = ob_get_contents();
        ob_end_clean();

        ADebug::checkpoint('_fetch '.$file.' end');
        return $content;
    }
}