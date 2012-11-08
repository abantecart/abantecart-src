<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

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
	protected $registry;	
	protected $id;
	protected $template;
	protected $controller_name;
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
			return;
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
			return;
		}
        if ( !is_null($value) ) {
		    $this->data[$template_variable] = $this->data[$template_variable] . $value;
        } else {
            $this->data[$template_variable] = $this->data[$template_variable] . $default_value;
        }
	}

	public function batchAssign($assign_arr){
		if (empty($assign_arr)){
			return;
		}

		foreach($assign_arr as $key => $value) {
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
    public function fetch($filename) {

		//#PR Build the path to the template file
		if (!defined('INSTALL')) {

			$tmpl_id = IS_ADMIN ? $this->config->get('admin_template') : $this->config->get('config_storefront_template');

            $path = DIR_TEMPLATE;
            if (is_file($path . $tmpl_id . '/template/'.  $filename)) {
                $file = $tmpl_id . '/template/'.  $filename;
            } else {
                $file = 'default/template/'.  $filename;
            }
            
        } else {
            $path = DIR_TEMPLATE;
            $file = $filename;
        }

		$file = $path . $file;

        if ( $this->registry->has('extensions') && $result = $this->extensions->isExtensionResource('T', $filename) ) {
            if ( is_file($file) ) {
                $warning = new AWarning("Extension <b>{$result['extension']}</b> override template <b>$filename</b>" );
                $warning->toDebug();
            }
            $file = $result['file'];
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
	}

    public function templateResource( $filename ) {
        $template = IS_ADMIN ? $this->config->get('admin_template') : $this->config->get('config_storefront_template');
        $extensions = $this->extensions->getEnabledExtensions();

        $file = (IS_ADMIN ? DIR_EXT_ADMIN : DIR_EXT_STORE) . DIR_EXT_TEMPLATE . $template . $filename;
        $file_default = (IS_ADMIN ? DIR_EXT_ADMIN : DIR_EXT_STORE) . DIR_EXT_TEMPLATE . 'default' . $filename;

	    foreach ( $extensions as $ext ) {
            if ( is_file(DIR_EXT . $ext . $file) ) {
				return DIR_EXTENSIONS . $ext . $file;
			}
            //check default template
            if ( $template != 'default' && is_file(DIR_EXT . $ext . $file_default) ) {
				return DIR_EXTENSIONS . $ext . $file_default;
			}
        }
		//TODO : need to check how it will work with admin templates. i suspect not work.
        if (is_file( DIR_TEMPLATE . $template . $filename)) {
            return 'storefront/view/' . $template . $filename;
        } else {
            return 'storefront/view/default' . $filename;
        }
    }
    public function isTemplateExists( $filename ) {
        $template = IS_ADMIN ? $this->config->get('admin_template') : $this->config->get('config_storefront_template');
        $extensions = $this->extensions->getEnabledExtensions();

        $file = (IS_ADMIN ? DIR_EXT_ADMIN : DIR_EXT_STORE) . DIR_EXT_TEMPLATE . $template .'/template/'. $filename;
		$file_default = (IS_ADMIN ? DIR_EXT_ADMIN : DIR_EXT_STORE) . DIR_EXT_TEMPLATE . 'default/template/' . $filename;

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

        if ( !file_exists($file) ) return ;

        ADebug::checkpoint('fetch '.$file.' start');
        extract($this->data);

        ob_start();
        require($file);
        $content = ob_get_contents();
        ob_end_clean();

        ADebug::checkpoint('fetch '.$file.' end');
        return $content;
    }

}