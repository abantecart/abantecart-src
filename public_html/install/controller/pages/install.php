<?php
/*
------------------------------------------------------------------------------
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
------------------------------------------------------------------------------  
*/

/**\
 * Class ControllerPagesInstall
 *
 * @property ModelInstall $model_install
 */
class ControllerPagesInstall extends AController
{
    private $error = [];

    public function main()
    {

        $run_level = $this->request->get['runlevel'];

        if (isset($run_level)) {
            if (!in_array((int)$run_level, [1, 2, 3, 4, 5])) {
                redirect(HTTP_SERVER.'index.php?rt=activation'.'&admin_path='.$this->request->post['admin_path']);
            }

            if (isset($this->session->data['install_step_data'])
                    && !$this->session->data['install_step_data']
                    && (int)$run_level == 1
            ) {
                if (filesize(DIR_ABANTECART.'system/config.php')) {
                    redirect(HTTP_SERVER.'index.php?rt=activation');
                } else {
                    redirect(HTTP_SERVER.'index.php?rt=license');
                }
            }

            echo $this->runlevel((int)$run_level);
            return null;
        }

        if ($this->request->is_POST()){
            //this data becomes escaped. We need to write it into file as constant. Revert back into as-is view
            $this->request->post['db_password'] = html_entity_decode($this->request->post['db_password']);
            if($this->_validate()) {
                $this->session->data['install_step_data'] = $this->request->post;
                redirect(HTTP_SERVER.'index.php?rt=install&runlevel=1');
            }
        }

        $this->data['error'] = $this->error;
        $this->data['action'] = HTTP_SERVER.'index.php?rt=install';

        $fields = [
            'db_driver',
            'db_host',
            'db_user',
            'db_password',
            'db_name',
            'db_prefix',
            'username',
            'password',
            'password_confirm',
            'email',
            'admin_path',
        ];
        $defaults = ['', 'localhost', '', '', '', 'abc_', 'admin', '', '', '', ''];
        $place_holder = [
            'Select Database Driver',
            'Enter Database Hostname',
            'Enter Database Username',
            'Enter Password, if any',
            'Enter Database Name',
            'Add prefix to database tables',
            'Enter new admin username',
            'Enter Secret Admin Password',
            'Repeat the password',
            'Provide valid email of administrator',
            'Enter your secret admin key',
        ];

        foreach ($fields as $k => $field) {
            if (isset($this->request->post[$field])) {
                $this->data[$field] = $this->request->post[$field];
            } else {
                $this->data[$field] = $defaults[$k];
            }
        }

        $form = new AForm('ST');
        $form->setForm(
            [
            'form_name' => 'form',
            'update'    => '',
            ]
        );

        $this->data['form']['id'] = 'form';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
            'type'   => 'form',
            'name'   => 'editFrm',
            'action' => $this->data['action'],
            ]
        );

        foreach ($fields as $k => $field) {
            if ($field != 'db_driver') {
                $this->data['form'][$field] = $form->getFieldHtml(
                    [
                        'type'        => (in_array($field, ['password', 'password_confirm']) ? 'password' : 'input'),
                        'name'        => $field,
                        'value'       => $this->data[$field],
                        'placeholder' => $place_holder[$k],
                        'required'    => in_array($field, ['db_host', 'db_user', 'db_name', 'username', 'password', 'password_confirm', 'email', 'admin_path']
                        ),
                    ]
                );
            } else {
                $options = [];

                if (extension_loaded('mysqli')) {
                    $options['amysqli'] = 'MySQLi';
                }

                if (extension_loaded('pdo_mysql')) {
                    $options['apdomysql'] = 'PDO MySQL';
                }

                //regular mysql is not supported on PHP 5.5.+
                if (extension_loaded('mysql') && version_compare(phpversion(), '5.5.0', '<') == true) {
                    $options['mysql'] = 'MySQL';
                }
                if ($options) {
                    $this->data['form'][$field] = $form->getFieldHtml(
                        [
                        'type'     => 'selectbox',
                        'name'     => $field,
                        'value'    => $this->data[$field],
                        'options'  => $options,
                        'required' => true,
                        ]
                    );
                } else {
                    $this->data['form'][$field] = '';
                    $this->data['error'][$field] = 'No database support. Please install AMySQL or PDO_MySQL php extension.';
                }

            }
        }

        $this->view->assign('back', HTTP_SERVER.'index.php?rt=settings');

        $this->addChild('common/header', 'header', 'common/header.tpl');
        $this->addChild('common/footer', 'footer', 'common/footer.tpl');

        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/install.tpl');
    }

    private function _validate()
    {

        $this->load->model('install');
        $result = $this->model_install->validateSettings($this->request->post);

        if (!$result) {
            $this->error = $this->model_install->errors;
        }

        return $result;
    }

    public function runlevel($step)
    {
        $this->load->library('json');
        if ($step == 2) {
            $this->_install_SQL();
            $this->response->addJSONHeader();
            return AJson::encode(['ret_code' => 50]);
        } elseif ($step == 3) {
            //NOTE: Create config as late as possible. This will prevent triggering finished installation 
            $this->_configure();
            //wait for end of writing of file on disk (for slow hdd)
            sleep(3);
            $this->session->data['finish'] = 'false';
            $this->response->addJSONHeader();
            return AJson::encode(['ret_code' => 100]);
        } elseif ($step == 4) {
            // Load demo data
            if (($this->session->data['install_step_data']['load_demo_data'] ?? '') == 'on') {
                $this->_load_demo_data();
            }
            //Clean session for configurations. We do not need them any more
            unset($this->session->data['install_step_data']);
            $this->session->data['finish'] = 'false';
            $this->response->addJSONHeader();
            return AJson::encode(['ret_code' => 150]);
        } elseif ($step == 5) {
            //install is completed but we are not yet finished
            $this->session->data['finish'] = 'false';
            // Load languages with asynchronous approach
            $this->response->addJSONHeader();
            return AJson::encode(['ret_code' => 200]);
        }

        $this->view->assign('url', HTTP_SERVER.'index.php?rt=install');
        $this->view->assign('redirect', HTTP_SERVER.'index.php?rt=finish');
        $temp = $this->dispatch('pages/install/progressbar_scripts', ['url' => HTTP_SERVER.'index.php?rt=install/progressbar']
        );
        $this->view->assign('progressbar_scripts', $temp->dispatchGetOutput());

        $this->addChild('common/header', 'header', 'common/header.tpl');
        $this->addChild('common/footer', 'footer', 'common/footer.tpl');
        $this->processTemplate('pages/install_progress.tpl');
        return null;
    }

    private function _install_SQL()
    {
        $this->load->model('install');
        $this->model_install->RunSQL($this->session->data['install_step_data']);

    }

    private function _configure()
    {
        $this->load->model('install');
        $this->model_install->configure($this->session->data['install_step_data']);
    }

    private function _prepare_registry()
    {
        $registry = Registry::getInstance();
        //This is ran after config is saved and we have database connection now
        $db = new ADB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        $registry->set('db', $db);
        define('DIR_LANGUAGE', DIR_ABANTECART.'admin/language/');

        // Cache
        $cache = new ACache();
        $registry->set('cache', $cache);

        // Config
        $config = new AConfig($registry);
        $registry->set('config', $config);

        // Extensions api
        $extensions = new ExtensionsApi();
        $extensions->loadEnabledExtensions();
        $registry->set('extensions', $extensions);

        return $registry;
    }

    public function _load_demo_data()
    {
        $registry = $this->_prepare_registry();
        $this->load->model('install');
        $this->model_install->loadDemoData($registry);
        return null;
    }

    public function progressbar()
    {
        session_write_close(); // unlock session !important!
        $progress = new progressbar($this->_prepare_registry());
        $this->response->addJSONHeader();
        switch ($this->request->get["work"]) {
            case "max":
                echo AJson::encode(['total' => $progress->get_max()]);
                break;
            case "do":
                $result = $progress->do_work();
                if (!$result) {
                    $result = [
                        'status'    => 406,
                        'errorText' => $result,
                    ];
                } else {
                    $result = ['status' => 100];
                }
                echo AJson::encode($result);
                break;
            case "progress":
                echo AJson::encode(['prc' => (int)$progress->get_progress()]);
                break;
        }
    }

    public function progressbar_scripts($url)
    {
        $this->view->assign('url', $url);
        $this->processTemplate('pages/progressbar.tpl');
    }
}

/** @noinspection PhpIncludeInspection */
require_once(DIR_CORE."lib/progressbar.php");

/*
 * Interface for progressbar
 * */

class progressbar implements AProgressBar
{
    /**
     * @var Registry
     */
    private $registry;

    function __construct($registry)
    {
        $this->registry = $registry;
    }

    function get_max()
    {
        define('IS_ADMIN', true);
        $language = new ALanguageManager($this->registry, 'en');
        $language_blocks = $language->getAllLanguageBlocks('english');
        $language_blocks['admin'] = array_merge($language_blocks['admin'], $language_blocks['extensions']['admin']);
        $language_blocks['storefront'] = array_merge($language_blocks['storefront'], $language_blocks['extensions']['storefront']);
        return sizeof($language_blocks['admin']) + sizeof($language_blocks['storefront']);
    }

    function get_progress()
    {
        $cnt = 0;
        $res = $this->registry->get('db')->query(
            'SELECT section, COUNT(DISTINCT `block`) as cnt
            FROM '.DB_PREFIX.'language_definitions
            GROUP by section'
        );
        foreach ($res->rows as $row) {
            $cnt += $row['cnt'];
        }
        return $cnt;
    }

    function do_work()
    {
        if(!defined('IS_ADMIN')) {
            define('IS_ADMIN', true);
        }
        $language = new ALanguageManager($this->registry, 'en');
        //Load default language (1) English on install only.
        return $language->definitionAutoLoad(1, 'all', 'all');
    }
}