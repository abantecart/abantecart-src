<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/

/**
 * Class ADispatcher
 *
 * @property ARequest $request
 * @property AResponse $response
 * @property AView $view
 * @property ExtensionsApi $extensions
 */
final class ADispatcher
{
    /** @var Registry */
    protected $registry;
    /** @var string */
    protected $file;
    /** @var string */
    protected $class;
    /** @var string */
    protected $method;
    /** @var string */
    protected $controller;
    /** @var string */
    protected $controller_type;
    /** @var array */
    protected $args = [];

    /**
     * @param string $rt
     * @param array $args
     */
    public function __construct($rt, $args = [])
    {
        $this->registry = Registry::getInstance();
        $rt = str_replace('../', '', $rt);
        if (!empty($args)) {
            $this->args = $args;
        }

        ADebug::checkpoint('ADispatch: '.$rt.' construct start');
        // We always get full RT (route) to dispatcher. Needs to have pages/ or responses/
        if (!$this->processPath($rt)) {
            $warning_txt = 'ADispatch: '.$rt.' construct FAILED.'
                .' Missing or incorrect controller route path. '
                .'Possibly, layout block is enabled for disabled or missing extension! '
                .genExecTrace('full');
            $warning = new AWarning($warning_txt);
            $warning->toDebug();
        }
        ADebug::checkpoint('ADispatch: '.$rt.' construct end. file: class: '.$this->class.'; method: '.$this->method);
    }

    public function __destruct()
    {
        $this->clear();
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
     * @param string $rt
     *
     * @return bool
     */
    protected function processPath($rt)
    {
        $path_build = '';
        $pathfound = false;
        // Build the path based on the route, example, rt=information/contact/success
        $path_nodes = explode('/', $rt);
        $this->controller_type = $path_nodes[0];

        //looking for controller in admin/storefront section
        $dir_app = DIR_APP_SECTION.'controller/';
        foreach ($path_nodes as $path_node) {
            $path_build .= $path_node;

            if (is_dir($dir_app.$path_build)) {
                $path_build .= '/';
                array_shift($path_nodes);
                continue;
            }

            if (is_file($dir_app.$path_build.'.php')) {
                //Set pure controller route
                $this->controller = $path_build;
                //Set full file path to controller
                $this->file = $dir_app.$path_build.'.php';
                //Build Controller class name
                $this->class = 'Controller'.preg_replace('/[^a-zA-Z0-9]/', '', $path_build);
                array_shift($path_nodes);
                $pathfound = true;
                break;
            }
        }

        //Last part is the method of function to call
        $method_to_call = array_shift($path_nodes);
        if ($method_to_call) {
            $this->method = $method_to_call;
        } else {
            //Set default method
            $this->method = 'main';
        }

        // Already found the path, so return.
        // This will optimize performance, and will not allow override core controllers.
        if ($pathfound == true) {
            return $pathfound;
        }

        // looking for controller in extensions section
        $result = $this->registry->get('extensions')->isExtensionController($rt);
        if ($result) {
            $this->controller = $result['route'];
            $this->file = $result['file'];
            $this->class = $result['class'];
            $this->method = $result['method'];
            // if controller was found in admin/storefront section && in extensions section
            // warning will be added to log about controller override
            if ($pathfound) {
                $warning = new AWarning("Extension <b>{$result['extension']}</b> override controller <b>$rt</b>");
                $warning->toDebug();
            }
            $pathfound = true;
        }

        return $pathfound;
    }

    // Clear function is public in case controller needs to be cleaned explicitly
    public function clear()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $val) {
            $this->$key = null;
        }
    }

    /**
     * @param string $route
     *
     * @return string
     */
    protected function dispatchPrePost($route)
    {
        $result = '';
        if ($this->extensions->isExtensionController($route)) {
            //save output
            $output = $this->response->getOutput();
            //reset to save controller output
            $this->response->setOutput('');

            $dispatch_pre = new ADispatcher($route, ["instance_id" => '']);
            $dispatch_pre->dispatch();
            $result = $this->response->getOutput();

            //restore output
            $this->response->setOutput($output);
        }

        return $result;
    }

    /**
     * This function to dispatch the controller and get and destroy it's output
     *
     * @param string $controller
     *
     * @return string
     * @throws AException
     * @throws ReflectionException
     */
    public function dispatchGetOutput($controller = '')
    {
        $this->dispatch($controller);
        $output = $this->response->getOutput();
        $this->response->setOutput('');
        return $output;
    }

    /**
     * @param string $parent_controller
     *
     * @return null|string
     * @throws AException
     * @throws ReflectionException
     */
    public function dispatch($parent_controller = '')
    {
        ADebug::checkpoint($this->class.'/'.$this->method.' dispatch START');

        //Process the controller, layout and children

        //check if we have missing class or everything
        if (empty($this->class) && has_value($this->file)) {
            #Build back trace of calling functions to provide more details
            $backtrace = debug_backtrace();
            $function_stack = '';
            if (is_object($parent_controller) && strlen($parent_controller->rt()) > 1) {
                $function_stack = 'Parent Controller: '.$parent_controller->rt().' | ';
            }

            for ($i = 1; $i < sizeof($backtrace); $i++) {
                $function_stack .= ' < '.$backtrace[$i]['function'];
            }
            $url = $this->request->server['REQUEST_URI'];
            $error = new AError(
                'Error: URL: '.$url
                .' Could not load controller '.$this->controller.'! Call stack: '
                .$function_stack,
                AC_ERR_CLASS_CLASS_NOT_EXIST
            );
            $error->toLog()->toDebug();
            return null;
        } elseif (
            empty($this->file)
            && empty($this->class)
            || empty($this->method)
        ) {
            $warning_txt = 'ADispatch: skipping unavailable controller …';
            $warning = new AWarning($warning_txt);
            $warning->toDebug();
            return null;
        }

        //check for controller.pre
        $output_pre = $this->dispatchPrePost($this->controller.POSTFIX_PRE);

        /** @noinspection PhpIncludeInspection */
        require_once($this->file);
        /**
         * @var $controller AController
         */
        $controller = null;
        if (class_exists($this->class)) {
            $controller = new $this->class(
                $this->registry,
                $this->args["instance_id"] ?? null,
                $this->controller,
                $parent_controller
            );
            $controller->dispatcher = $this;
        } else {
            $error = new AError('Error: controller class not exist '.$this->class.'!', AC_ERR_CLASS_CLASS_NOT_EXIST);
            $error->toLog()->toDebug();
        }
        try {
            if (is_callable([$controller, $this->method])) {
                /**
                 * @var $dispatch ADispatcher
                 */
                $r = new ReflectionMethod($controller, $this->method);
                $params = $r->getParameters();
                if (!$params) {
                    $args = [];
                } else {
                    $args = $this->args;
                }

                $rfl = new ReflectionClass($controller);
                $method = $rfl->getMethod($this->method);
                if($method) {
                    $allParameters = $method->getParameters();
                    if($allParameters) {
                        $methodParams = [];
                        foreach ($allParameters as $p) {
                            if (isset($args[$p->name])) {
                                $methodParams[$p->name] = $args[$p->name];
                            }
                        }
                        if($methodParams && isAssocArray($args)) {
                            $args = $methodParams;
                        }elseif(!$methodParams && isAssocArray($args)){
                            $args = [];
                        }
                    }
                }

                $dispatch = call_user_func_array([$controller, $this->method], $args);
                //Check if return is a dispatch and need to call new page
                if ($dispatch && is_object($dispatch)) {
                    if ($this->args["instance_id"] == 0) {
                        //If main controller come back for new dispatch
                        return $dispatch->getController().'/'.$dispatch->getMethod();
                    } else {
                        // Call new dispatch for new controller and exit
                        $dispatch->dispatch();
                        return null;
                    }
                } else {
                    if ($dispatch == 'completed') {
                        //Check if we have message completed in controller response.
                        //If completed. stop further execution.
                        return 'completed';
                    }
                }
                /**
                 * Load layout and process children controllers
                 * @method AController getChildren()
                 */
                $children = $controller->getChildren();

                ADebug::variable('Processing children of '.$this->controller, $children);

                //Process each child controller
                foreach ($children as $child) {
                    //Add highest Debug level here with backtrace to review this
                    ADebug::checkpoint(
                        $child['controller'].' ( child of '.$this->controller
                        .', instance_id: '.$child['instance_id'].' ) dispatch START'
                    );
                    //Process each child and create dispatch to call recursive
                    $dispatch = new ADispatcher($child['controller'], ["instance_id" => $child['instance_id']]);
                    $dispatch->dispatch($controller);
                    // Append output of child controller to current controller
                    if (isset($child['position'])
                        && $child['position']) { // made for recognizing few custom_blocks in the same placeholder
                        $controller->view->assign(
                            $child['block_txt_id'].'_'.$child['instance_id'],
                            $this->response->getOutput()
                        );
                    } else {
                        $controller->view->assign($child['block_txt_id'], $this->response->getOutput());
                    }
                    //clean up and remove output
                    $this->response->setOutput('');
                    ADebug::checkpoint($child['controller'].' ( child of '.$this->controller.' ) dispatch END');
                }
                //Request controller to generate output
                $controller->finalize();

                //check for controller.pre
                $output_post = $this->dispatchPrePost($this->controller.POSTFIX_POST);
                //add pre and post controllers output
                $this->response->setOutput($output_pre.$this->response->getOutput().$output_post);

                //clean up and destroy the object
                unset($controller, $dispatch);
            } else {
                $err = new AError(
                    'Error: controller method not exist '.$this->class.'::'.$this->method.'!',
                    AC_ERR_CLASS_METHOD_NOT_EXIST
                );
                $err->toLog()->toDebug();
                if (in_array($this->controller_type, ['responses', 'api', 'task'])) {
                    $dd = new ADispatcher('responses/error/ajaxerror/not_found');
                    $dd->dispatch();
                }
            }
        } //catching output of around hook (it can be only one)
        catch (AException $e) {
            if ($e->getCode() != AC_HOOK_OVERRIDE) {
                throw $e;
            }
        }
        ADebug::checkpoint(''.$this->class.'/'.$this->method.' dispatch END');
        return null;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->controller_type;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }
}
