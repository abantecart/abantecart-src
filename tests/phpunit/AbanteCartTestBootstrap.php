<?php

/**
 * Class AbanteCartTest
 *
 * @property ACustomer $customer
 */
class AbanteCartTest extends PHPUnit\Framework\TestCase
{
    protected $registry;

    public function __construct()
    {
        parent::__construct();
        $GLOBALS['error_descriptions'] = 'Abantecart PhpUnit test';

        $dirname = dirname(__FILE__);
        $dirname = dirname($dirname);

        $dirname = dirname($dirname).'/public_html';
        define('ABC_TEST_ROOT_PATH', $dirname);
        define('ABC_TEST_HTTP_HOST', 'travis-ci.org');
        define('ABC_TEST_PHP_SELF', 'abantecart/abantecart-src/public_html/index.php');

        $_SERVER['HTTP_HOST'] = ABC_TEST_HTTP_HOST;
        $_SERVER['PHP_SELF'] = ABC_TEST_PHP_SELF;

        // Required PHP Version
        define('MIN_PHP_VERSION', '7.4.0');
        if (version_compare(phpversion(), MIN_PHP_VERSION, '<') == true) {
            throw new Exception(
                MIN_PHP_VERSION
                .'+ Required for AbanteCart to work properly! Please contact your system administrator or host service provider.'
            );
        }

        // Load Configuration

        // Real path (operating system web root) to the directory where abantecart is installed
        $root_path = ABC_TEST_ROOT_PATH;

        // Windows IIS Compatibility
        if (stristr(PHP_OS, 'WIN')) {
            define('IS_WINDOWS', true);
            $root_path = str_replace('\\', '/', $root_path);
        }
        define('DIR_ROOT', $root_path);
        define('DIR_CORE', DIR_ROOT.'/core/');

        $this->loadConfiguration(DIR_ROOT.'/system/config.php');

        //set server name for correct email sending
        if (defined('SERVER_NAME') && SERVER_NAME != '') {
            putenv("SERVER_NAME=".SERVER_NAME);
        }

        //purge _GET
        $get = ['mode' => isset($_GET['mode']) ? $_GET['mode'] : ''];
        if (!in_array($get['mode'], ['run', 'query'])) { // can be 'query' or 'run'
            $get['mode'] = 'run';
        }
        // if task details needed for ajax step-by-step run
        if ($get['mode'] == 'query') {
            $get['task_name'] = $_GET['task_name'];
        }
        $_GET = $get;
        unset($get);

        $_GET['s'] = ADMIN_PATH; // sign of admin side for controllers run from dispatcher
        // Load all initial set up
        require_once(DIR_ROOT.'/core/init.php');
        unset($_GET['s']);// not needed anymore

        // Registry
        $this->registry = Registry::getInstance();
        //add admin in scope
        $this->registry->get('session')->data['user_id'] = 1;
        $this->registry->set('user', new AUser($this->registry));
    }

    public function __get($key)
    {
        return $this->registry->get($key);
    }

    public function __set($key, $value)
    {
        $this->registry->set($key, $value);
    }

    public function loadConfiguration($path)
    {
        // Configuration
        if (file_exists($path) && filesize($path)) {
            require_once($path);
        } else {
            throw new Exception('AbanteCart has to be installed first!');
        }

        // New Installation
        if (!defined('DB_DATABASE')) {
            throw new Exception('AbanteCart has to be installed first!');
        }
    }

    public function customerLogin($user, $password)
    {
        $logged = $this->customer->login($user, $password);
        if (!$logged) {
            throw new Exception('Could not login customer');
        }
    }

    public function customerLogout()
    {
        if ($this->customer->isLogged()) {
            $this->customer->logout();
        }
    }

    public function getOutput()
    {
        $class = new ReflectionClass("Response");
        $property = $class->getProperty("output");
        $property->setAccessible(true);
        return $property->getValue($this->response);
    }
}