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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class AView
 *
 * @property AConfig       $config
 * @property ExtensionsAPI $extensions
 * @property AResponse     $response
 * @property ACache        $cache
 *
 */
class AView
{
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
     * @var string
     */
    protected $html_cache_key;

    /**
     * @param Registry $registry
     * @param int      $instance_id
     */
    public function __construct($registry, $instance_id)
    {
        $this->registry = $registry;
        $this->has_extensions = $this->registry->has('extensions');
        if ($this->registry->get('config')) {
            $this->default_template = IS_ADMIN ? $this->registry->get('config')->get('admin_template') : $this->registry->get('config')->get('config_storefront_template');
        }
        $this->data['template_dir'] = RDIR_TEMPLATE;
        $this->data['tpl_common_dir'] = RDIR_TEMPLATE.'/template/common/';
        $this->instance_id = $instance_id;

    }

    public function __get($key)
    {
        return $this->registry->get($key);
    }

    public function __set($key, $value)
    {
        $this->registry->set($key, $value);
    }

    /**
     * @deprecated since v1.2.9
     *
     * @param string $url
     */
    protected function redirect($url)
    {
        redirect($url);
    }

    /**
     * @void
     */
    public function enableOutput()
    {
        $this->enableOutput = true;
    }

    /**
     * @void
     */
    public function disableOutput()
    {
        $this->enableOutput = false;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Return array with awailable variables and types in the view
     *
     * @param string $key - optional parameter to spcify variable type of array.
     *
     * @return array | mixed
     */
    public function getVariables($key = '')
    {
        $variables = array();
        $scope = array();
        if ($key) {
            $scope = $this->data[$key];
        } else {
            $scope = $this->data;
        }
        if (is_array($scope)) {
            foreach (array_keys($scope) as $var) {
                $variables[$var] = gettype($scope[$var]);
            }
        }
        return $variables;
    }

    /**
     * @param string $key - optional parameter for better access from hook that called by "_UpdateData".
     *
     * @return array | mixed - reference to $this->data
     */
    public function &getData($key = '')
    {
        if ($key) {
            return $this->data[$key];
        } else {
            return $this->data;
        }
    }

    /**
     * @param string $template_variable
     * @param string $value
     * @param string $default_value
     *
     * @return null
     */
    public function assign($template_variable, $value = '', $default_value = '')
    {
        if (empty($template_variable)) {
            return null;
        }
        if (!is_null($value)) {
            $this->data[$template_variable] = $value;
        } else {
            $this->data[$template_variable] = $default_value;
        }
    }

    /**
     * Call append if you need to add values to earlier assigned value
     *
     * @param string $template_variable
     * @param string $value
     * @param string $default_value
     *
     * @return null
     */
    public function append($template_variable, $value = '', $default_value = '')
    {
        if (empty($template_variable)) {
            return null;
        }
        if (!is_null($value)) {
            $this->data[$template_variable] = $this->data[$template_variable].$value;
        } else {
            $this->data[$template_variable] = $this->data[$template_variable].$default_value;
        }
    }

    /**
     * @param array $assign_arr - associative array
     *
     * @return null
     */
    public function batchAssign($assign_arr)
    {
        if (empty($assign_arr) || !is_array($assign_arr)) {
            return null;
        }

        foreach ($assign_arr as $key => $value) {
            //when key already defined and type of old and new values are different send warning in debug-mode
            if (isset($this->data[$key]) && is_object($this->data[$key])) {
                $warning_text = 'Warning! Variable "'.$key.'" in template "'.$this->template.'" overriding value and data type "object." ';
                $warning_text .= 'Possibly need to review your code! (also check that extensions do not load language definitions in UpdateData hook).';
                $warning = new AWarning($warning_text);
                $warning->toDebug();
                continue; // prevent overriding.
            } elseif (isset($this->data[$key]) && gettype($this->data[$key]) != gettype($value)) {
                $warning_text = 'Warning! Variable "'.$key.'" in template "'.$this->template.'" overriding value and data type "'.gettype($this->data[$key]).'" ';
                $warning_text .= 'Forcing new data type '.gettype($value).'. Possibly need to review your code!';
                $warning = new AWarning($warning_text);
                $warning->toDebug();
            }
            $this->data[$key] = $value;
        }
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function addHookVar($name, $value)
    {
        if (!empty($name)) {
            $this->hook_vars[$name] .= $value;
        }
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getHookVar($name)
    {
        if (isset($this->hook_vars[$name])) {
            return $this->hook_vars[$name];
        }
        return '';
    }

    // Render html output
    public function render()
    {
        // If no template return empty. We might have controller that has no templates
        if (!empty($this->template) && $this->enableOutput) {
            $compression = '';
            if ($this->config) {
                $compression = $this->config->get('config_compression');
            }
            if (!empty($this->output)) {
                $this->response->setOutput($this->output, $compression);
            } else {
                $this->response->setOutput($this->fetch($this->template), $compression);
            }
        }
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        return (!empty($this->output) ? $this->output : !empty($this->template))
                ? $this->fetch($this->template)
                : '';
    }

    /**
     * @param string $output
     *
     * @void
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * Process the template
     *
     * @param $filename
     *
     * @return string
     */
    public function fetch($filename)
    {
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

            if ($this->has_extensions && $result = $this->extensions->isExtensionResource('T', $filename)) {
                if (is_file($file)) {
                    $warning = new AWarning("Extension <b>".$result['extension']."</b> overrides core template with <b>".$filename."</b>");
                    $warning->toDebug();
                }
                $file = $result['file'];
            }
        }

        if (empty($file)) {
            $error = new AError('Error: Unable to identify file path to template '.$filename.'! Check blocks in the layout or enable debug mode to get more details. '.AC_ERR_LOAD);
            $error->toDebug()->toLog();
            return '';
        }

        if (is_file($file)) {
            $content = '';
            $file_pre = str_replace('.tpl', POSTFIX_PRE.'.tpl', $filename);
            if ($result = $this->extensions->getAllPrePostTemplates($file_pre)) {
                foreach ($result as $item) {
                    $content .= $this->_fetch($item['file']);
                }
            }

            $content .= $this->_fetch($file);

            $file_post = str_replace('.tpl', POSTFIX_POST.'.tpl', $filename);
            if ($result = $this->extensions->getAllPrePostTemplates($file_post)) {
                foreach ($result as $item) {
                    $content .= $this->_fetch($item['file']);
                }
            }
            ADebug::checkpoint('fetch '.$filename.' end');

            //Write HTML Cache if we need and can write
            if ($this->config && $this->config->get('config_html_cache') && $this->html_cache_key) {
                if ($this->cache->save_html_cache($this->html_cache_key, $content) === false) {
                    $error = new AError('Error: Cannot create HTML cache for file '.$this->html_cache_key.'! Directory to write cache is not writable', AC_ERR_LOAD);
                    $error->toDebug()->toLog();
                }
            }

            return $content;
        } else {
            $error = new AError('Error: Cannot load template '.$filename.'! File '.$file.' is missing or incorrect. Check blocks in the layout or enable debug mode to get more details. ', AC_ERR_LOAD);
            $error->toDebug()->toLog();
        }

        return '';
    }

    /**
     * Storefront function to return path to the resource
     *
     * @param string $filename
     * @param string $mode Mode to return format: http | file
     *
     * @return string with relative path
     */
    public function templateResource($filename, $mode = 'http')
    {
        if (!$filename) {
            return null;
        }
        $http_path = '';
        $res_arr = $this->_extensions_resource_map($filename);
        //get first exact template extension resource or default template resource otherwise.
        if (isset($res_arr['original'][0])) {
            $output = $res_arr['original'][0];
        } else {
            if (isset($res_arr['default'][0])) {
                $output = $res_arr['default'][0];
            } else {
                //no extension found, use resource from core templates
                $output = $this->_get_template_path(DIR_TEMPLATE, $filename, 'relative');
            }
        }

        if (!in_array(pathinfo($filename, PATHINFO_EXTENSION), array('tpl', 'php'))) {
            $this->extensions->hk_ProcessData($this, __FUNCTION__);
            $http_path = $this->data['http_dir'];
        }

        if ($mode == 'http') {
            return $http_path.$output;
        } else {
            if ($mode == 'file') {
                return DIR_ROOT."/".$output;
            } else {
                return '';
            }
        }
    }

    /**
     * @param string $filename
     *
     * @return bool
     */
    public function isTemplateExists($filename)
    {
        if (!$filename) {
            return false;
        }

        //check if this template file in extensions or in core
        if ($this->templateResource('/template/'.$filename)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if HTML Cache file present
     *
     * @param string $key
     *
     * @return bool
     */
    public function setCacheKey($key)
    {
        $this->html_cache_key = $key;
    }

    /**
     * Check if HTML Cache file present
     *
     * @param string $key
     *
     * @return bool
     */
    public function checkHTMLCache($key)
    {
        if (!$key) {
            return false;
        }
        $this->html_cache_key = $key;
        $html_cache = $this->cache->get_html_cache($key);
        if ($html_cache) {
            $compression = '';
            if ($this->config) {
                $compression = $this->config->get('config_compression');
            }
            $this->response->setOutput($html_cache, $compression);
            return true;
        }
        return false;
    }

    /**
     * Beta!
     * Build or load minified CSS and return an output.
     *
     * @param string $css_file css file with relative name
     * @param string $group    CSS group name for caching
     *
     * @return string
     */
    public function LoadMinifyCSS($css_file, $group = 'css')
    {
        if (empty($css_file)) {
            return '';
        }
        //build hash key
        $key = '';
        //get file time stamp
        $key .= $css_file."-".filemtime($this->templateResource($css_file, 'file'));
        $key = $group.".".md5($group.'-'.$key);
        //check if hash is created and load 
        $css_data = $this->cache->pull($key);
        if ($css_data === false) {
            require_once(DIR_CORE.'helper/html-css-js-minifier.php');
            //build minified css and save
            $path = dirname($this->templateResource($css_file, 'http'));
            $new_content = file_get_contents($this->templateResource($css_file, 'file'));
            //replace relative directories with full path
            $css_data = preg_replace('/\.\.\//', $path.'/../', $new_content);
            $css_data = minify_css($css_data);
            $this->cache->push($key, $css_data);
        }
        return $css_data;
    }

    /**
     * Beta!
     * Preload JavaScript and return an output.
     *
     * @param        string /array $js_file file(s) with relative name
     * @param string $group JS group name for caching
     *
     * @return string
     */
    public function PreloadJS($js_file, $group = 'js')
    {
        if (empty($js_file)) {
            return '';
        }
        //build hash key
        $key = '';
        //get file time stamp
        if (is_array($js_file)) {
            foreach ($js_file as $js) {
                //get file time stamp
                $key .= $js."-".filemtime($this->templateResource($js, 'file'));
            }
        } else {
            $key .= $js_file."-".filemtime($this->templateResource($js_file, 'file'));
        }

        $key = $group.".".md5($group.'-'.$key);
        //check if hash is created and load 
        $js_data = $this->cache->pull($key);
        if ($js_data === false) {
            //load js and save to cache
            //TODO: Add stable minify method. minify_js in html-css-js-minifier.php is not stable  
            $js_data = '';
            if (is_array($js_file)) {
                foreach ($js_file as $file) {
                    $js_data .= file_get_contents($this->templateResource($file, 'file'))."\n";
                }
            } else {
                $js_data .= file_get_contents($this->templateResource($js_file, 'file'));
            }
            //$js_data = minify_js($js_data);
            $this->cache->push($key, $js_data);
        }
        return $js_data;
    }

    /**
     * full directory path
     *
     * @param string $extension_name
     *
     * @return string
     */
    private function _extension_view_dir($extension_name)
    {
        return $this->_extension_section_dir($extension_name).DIR_EXT_TEMPLATE;
    }

    /**
     * full directory path
     *
     * @param string $extension_name
     *
     * @return string
     */
    private function _extension_section_dir($extension_name)
    {
        $rel_view_path = (IS_ADMIN ? DIR_EXT_ADMIN : DIR_EXT_STORE);
        return DIR_EXT.$extension_name.$rel_view_path;
    }

    /**
     * Build template source map for enabled extensions
     *
     * @param string $filename
     *
     * @return array
     */
    private function _extensions_resource_map($filename)
    {
        if (empty($filename)) {
            return array();
        }
        $ret_arr = array();
        $extensions = $this->extensions->getEnabledExtensions();
        //loop through each extension and locate resource to use 
        //Note: first extension with exact resource or default resource will be used 
        foreach ($extensions as $ext) {
            $res_arr = $this->_test_template_paths($this->_extension_view_dir($ext), $filename, 'relative');
            if ($res_arr) {
                $ret_arr[$res_arr['match']][] = DIR_EXTENSIONS.$ext.'/'.$res_arr['path'];
            }
        }

        return $ret_arr;
    }

    /**
     * return path to the template resource
     *
     * @param string $path
     * @param string $filename
     * @param string $mode
     *
     * @return mixed
     */
    private function _get_template_path($path, $filename, $mode)
    {
        //look into extensions first
        $res_arr = $this->_extensions_resource_map($filename);
        //get first exact template extension resource or default template resource otherwise.
        if (isset($res_arr['original'][0])) {
            return $res_arr['original'][0];
        } else {
            if (isset($res_arr['default'][0])) {
                return $res_arr['default'][0];
            }
        }

        $template_path_arr = $this->_test_template_paths($path, $filename, $mode);
        return $template_path_arr['path'];
    }

    /**
     * Function to test file paths and location of original or default file
     *
     * @param string $path
     * @param string $filename
     * @param string $mode
     *
     * @return array|null
     */
    private function _test_template_paths($path, $filename, $mode = 'relative')
    {
        $ret_path = '';
        $template = $this->default_template;
        $match = 'original';

        if (IS_ADMIN) {
            if (is_file($path.$template.$filename)) {
                $ret_path = $path.$template.$filename;
                if ($mode == 'relative') {
                    $ret_path = 'admin/view/'.$template.$filename;
                }
            } else {
                if (is_file($path.'default'.$filename)) {
                    $ret_path = $path.'default'.$filename;
                    if ($mode == 'relative') {
                        $ret_path = 'admin/view/default'.$filename;
                        $match = 'default';
                    }
                }
            }
        } else {
            if (is_file($path.$template.$filename)) {
                $ret_path = $path.$template.$filename;
                if ($mode == 'relative') {
                    $ret_path = 'storefront/view/'.$template.$filename;
                }
            } else {
                if (is_file($path.'default'.$filename)) {
                    $ret_path = $path.'default'.$filename;
                    if ($mode == 'relative') {
                        $ret_path = 'storefront/view/default'.$filename;
                        $match = 'default';
                    }
                }
            }
        }
        //return path. Empty path indicates, nothing found
        if ($ret_path) {
            return array('match' => $match, 'path' => $ret_path);
        } else {
            return null;
        }
    }

    /**
     * @param $file string - full path of file
     *
     * @return string
     */
    public function _fetch($file)
    {

        if (!file_exists($file)) {
            return '';
        }

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