<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2013 Belavier Commerce LLC

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

class AView {
	/**
	 * @var $registry Registry
	 */
	protected $registry;	
	protected $id;
	protected $template;
	protected $instance_id;
    protected $enableOutput = false;
    protected $output = '';
    protected $hook_vars = array();

	public $data = array();

	protected $render;
	
	public function __construct($registry, $instance_id) {
		$this->registry = $registry;	
		$this->data['template_dir'] = RDIR_TEMPLATE;
		$this->instance_id = $instance_id;
	}
	
	public function __get($key) {
		return $this->registry->get($key);
	}
	
	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

    protected function redirect($url) {
		header('Location: ' . str_replace('&amp;', '&', $url));
		die();
	}

    public function enableOutput() {
        $this->enableOutput = true;
    }

    public function disableOutput() {
        $this->enableOutput = false;
    }
	
	public function setTemplate($template){
		$this->template = $template;
	}
	
	public function getTemplate(){
		return $this->template;
	}

    public function getData() {
        return $this->data;
    }
	
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
	
	//Call append if you need to add values to earlier assigned value
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

	public function batchAssign($assign_arr){
		if (empty($assign_arr)){
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

    public function addHookVar($name, $value) {
        if (!empty($name)){
            $this->hook_vars[$name] .= $value;
        }
    }

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

    public function getOutput() { 
        return !empty( $this->output ) ? $this->output : !empty($this->template) ? $this->fetch($this->template) : '';
    }

    public function setOutput( $output ) {
        $this->output = $output;
    }
	
	// Process the template
	/**
	 * @param $filename
	 * @return string
	 */
	public function fetch($filename) {

		//#PR First see if we have full path to template file. Nothing to do. Higher precedence! 
		if (is_file($filename)) {
			//#PR set full path
			$file = $filename;
		} else {
			//#PR Build the path to the template file
			$path = DIR_TEMPLATE;
			if (!defined('INSTALL')) {	
			    $tmpl_id = IS_ADMIN ? $this->config->get('admin_template') : $this->config->get('config_storefront_template');
		
		        if (is_file($path . $tmpl_id . '/template/'.  $filename)) {
		            $file = $path . $tmpl_id . '/template/'.  $filename;
		        } else {
		            $file = $path . 'default_html5/template/'.  $filename;
					if (!is_file($file)) {
						$file = $path . 'default/template/'.  $filename;
					}
		        }
		        
		    } else {
		        $file = $path.$filename;
		    }
	
	        if ( $this->registry->has('extensions') && $result = $this->extensions->isExtensionResource('T', $filename) ) {
	            if ( is_file($file) ) {
	                $warning = new AWarning("Extension <b>{$result['extension']}</b> overrides core template with <b>$filename</b>" );
	                $warning->toDebug();
	            }
	            $file = $result['file'];
	        }
		}
	    
		if (is_file($file)) {

            $content = '';

            $file_pre = str_replace('.tpl', POSTFIX_PRE.'.tpl', $filename );
            if ( $result = $this->extensions->isExtensionResource('T', $file_pre) ) {
                $content .= $this->_fetch($result['file']);
            }
			
      		$content .= $this->_fetch($file);

            $file_post = str_replace('.tpl', POSTFIX_POST.'.tpl', $filename );
            if ( $result = $this->extensions->isExtensionResource('T', $file_post) ) {
                $content .= $this->_fetch($result['file']);
            }

      		return $content;
    	} else {
			$error = new AError('Error: Could not load template ' . $file . '!' , AC_ERR_LOAD);
			$error->toDebug()->toLog();
    	}
		return '';
	}

    public function templateResource( $filename ) {
        $template = IS_ADMIN ? $this->config->get('admin_template') : $this->config->get('config_storefront_template');
        $extensions = $this->extensions->getEnabledExtensions();

        $file = (IS_ADMIN ? DIR_EXT_ADMIN : DIR_EXT_STORE) . DIR_EXT_TEMPLATE . $template . $filename;
        $file_default = (IS_ADMIN ? DIR_EXT_ADMIN : DIR_EXT_STORE) . DIR_EXT_TEMPLATE . 'default_html5' . $filename;
		if(!is_file($file_default)){
			$file_default = (IS_ADMIN ? DIR_EXT_ADMIN : DIR_EXT_STORE) . DIR_EXT_TEMPLATE . 'default' . $filename;
		}

	    foreach ( $extensions as $ext ) {
            if ( is_file(DIR_EXT . $ext . $file) ) {
				return DIR_EXTENSIONS . $ext . $file;
			}
            //check default template
            if ( $template != 'default' && $template != 'default_html5' && is_file(DIR_EXT . $ext . $file_default) ) {
				return DIR_EXTENSIONS . $ext . $file_default;
			}
        }
		//TODO : need to check how it will work with admin templates. i suspect not work.
		if (is_file( DIR_TEMPLATE . $template . $filename)) {
			return 'storefront/view/' . $template . $filename;
		} else {
			if(is_file(DIR_ROOT.'/storefront/view/default' . $filename)){
				return 'storefront/view/default' . $filename;
			}else{
				return 'storefront/view/default_html5' . $filename;
			}

		}
    }
    public function isTemplateExists( $filename ) {
        $template = IS_ADMIN ? $this->config->get('admin_template') : $this->config->get('config_storefront_template');
        $extensions = $this->extensions->getEnabledExtensions();

        $file = (IS_ADMIN ? DIR_EXT_ADMIN : DIR_EXT_STORE) . DIR_EXT_TEMPLATE . $template .'/template/'. $filename;
		$file_default = (IS_ADMIN ? DIR_EXT_ADMIN : DIR_EXT_STORE) . DIR_EXT_TEMPLATE . 'default_html5/template/' . $filename;
		if(!is_file($file_default)){
			$file_default = (IS_ADMIN ? DIR_EXT_ADMIN : DIR_EXT_STORE) . DIR_EXT_TEMPLATE . 'default/template/' . $filename;
		}

	    foreach ( $extensions as $ext ) {
            if ( is_file(DIR_EXT . $ext . $file) ) {
				return true;
			}
            //check default template
            if ( $template != 'default' && is_file(DIR_EXT . $ext . $file_default) ) {
				return true;
			}
        }

        if (is_file( DIR_TEMPLATE . $template .'/template/'. $filename)) {
            return true;
        } else {
            return false;
        }
    }

    public function _fetch( $file ) {

        if ( !file_exists($file) ) return null;

        ADebug::checkpoint('fetch '.$file.' start');
        extract($this->data);

        ob_start();
		/** @noinspection PhpIncludeInspection */
		require($file);
        $content = ob_get_contents();
        ob_end_clean();

        ADebug::checkpoint('fetch '.$file.' end');
        return $content;
    }

}