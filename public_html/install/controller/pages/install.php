<?php
/*
------------------------------------------------------------------------------
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
------------------------------------------------------------------------------  
*/

class ControllerPagesInstall extends AController {
	private $error = array();
	public $data = array();

	public function main() {

		$this->data = array();

		if (isset($this->request->get[ 'runlevel' ])) {
			if (!in_array((int)$this->request->get[ 'runlevel' ], array( 1, 2, 3, 4 ))) {
				$this->redirect(HTTP_SERVER . 'index.php?rt=activation' . '&admin_path=' . $this->request->post[ 'admin_path' ]);
			}
			echo $this->runlevel((int)$this->request->get[ 'runlevel' ]);
			return;
		}


		if (($this->request->server[ 'REQUEST_METHOD' ] == 'POST') && ($this->_validate())) {

			$this->session->data[ 'install_step_data' ] = $this->request->post;
			$this->redirect(HTTP_SERVER . 'index.php?rt=install&runlevel=1');
		}

		$this->data[ 'error' ] = $this->error;
		$this->data[ 'action' ] = HTTP_SERVER . 'index.php?rt=install';

		$fields = array( 'db_host', 'db_user', 'db_password', 'db_name', 'db_prefix', 'username', 'password',
		                 'password_confirm', 'email', 'admin_path',
		);
		$defaults = array( 'localhost', '', '', '', '', 'admin', '', '', '', 'your_admin' );

		foreach ($fields as $k => $field) {
			if (isset($this->request->post[ $field ])) {
				$this->data[ $field ] = $this->request->post[ $field ];
			} else {
				$this->data[ $field ] = $defaults[ $k ];
			}
		}
		$this->data[ 'button_continue' ] = $this->html->buildButton(array(
		                                                                 'name' => 'continue',
		                                                                 'text' => 'Continue >>',
		                                                                 'style' => 'button1' )
		);

		$form = new AForm('ST');
		$form->setForm(array(
		                    'form_name' => 'form',
		                    'update' => '',
		               ));

		$this->data[ 'form' ][ 'id' ] = 'form';
		$this->data[ 'form' ][ 'form_open' ] = $form->getFieldHtml(array(
		                                                                'type' => 'form',
		                                                                'name' => 'editFrm',
		                                                                'action' => $this->data[ 'action' ],
		                                                           ));
		$this->data[ 'form' ][ 'submit' ] = $form->getFieldHtml(array(
		                                                             'type' => 'button',
		                                                             'name' => 'submit',
		                                                             'text' => 'Continue >>',
		                                                             'style' => 'button1',
		                                                        ));

		foreach ($fields as $field) {
			$this->data[ 'form' ][ $field ] = $form->getFieldHtml(array(
			                                                           'type' => (in_array($field, array( 'password', 'password_confirm' ))
					                                                           ? 'password' : 'input'),
			                                                           'name' => $field,
			                                                           'value' => $this->data[ $field ],
			                                                           'required' => in_array($field, array( 'db_host', 'db_user', 'db_name', 'username', 'password', 'password_confirm', 'email' )),
			                                                      ));
		}

		$this->addChild('common/header', 'header', 'common/header.tpl');
		$this->addChild('common/footer', 'footer', 'common/footer.tpl');

		$this->view->batchAssign($this->data);
		$this->processTemplate('pages/install.tpl');
	}

	private function _validate() {
		if (!$this->request->post[ 'admin_path' ]) {
			$this->error[ 'admin_path' ] = 'Admin unique name is required!';
		}
		else if (preg_match('/[^A-Za-z0-9_]/', $this->request->post[ 'admin_path' ])) {
			$this->error[ 'admin_path' ] = 'Admin unique name contains non-alphanumeric characters!';
		}

		if (!$this->request->post[ 'db_host' ]) {
			$this->error[ 'db_host' ] = 'Host required!';
		}

		if (!$this->request->post[ 'db_user' ]) {
			$this->error[ 'db_user' ] = 'User required!';
		}

		if (!$this->request->post[ 'db_name' ]) {
			$this->error[ 'db_name' ] = 'Database Name required!';
		}

		if (!$this->request->post[ 'username' ]) {
			$this->error[ 'username' ] = 'Username required!';
		}

		if (!$this->request->post[ 'password' ]) {
			$this->error[ 'password' ] = 'Password required!';
		}
		if ($this->request->post[ 'password' ] != $this->request->post[ 'password_confirm' ]) {
			$this->error[ 'password_confirm' ] = 'Password does not match the confirm password!';
		}

		$pattern = '/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i';

		if (!preg_match($pattern, $this->request->post[ 'email' ])) {
			$this->error[ 'email' ] = 'Invalid E-Mail!';
		}

        if (!empty($this->request->post['db_prefix']) && preg_match('/[^A-Za-z0-9_]/', $this->request->post['db_prefix'])) {
            $this->error[ 'db_prefix' ] = 'DB prefix contains non-alphanumeric characters!';
        }

		if ($this->request->post[ 'db_host' ] && $this->request->post[ 'db_user' ] && $this->request->post[ 'db_password' ]) {
			if (!$connection = @mysql_connect($this->request->post[ 'db_host' ], $this->request->post[ 'db_user' ], $this->request->post[ 'db_password' ])) {
				$this->error[ 'warning' ] = 'Error: Could not connect to the database please make sure the database server, username and password is correct!';
			} else {
				if (!@mysql_select_db($this->request->post[ 'db_name' ], $connection)) {
					$this->error[ 'warning' ] = 'Error: Database does not exist!';
				}

				mysql_close($connection);
			}
		}

		if (!is_writable(DIR_ABANTECART . 'system/config.php')) {
			$this->error[ 'warning' ] = 'Error: Could not write to config.php please check you have set the correct permissions on: ' . DIR_ABANTECART . 'system/config.php!';
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}


	public function runlevel($step) {

		if ($step == 2) {
			$this->installSQL();
			return 50;
		} elseif ($step == 3) {
			//$this->load_language($this);
			return 100;
		} elseif ($step == 4) {
			$this->configure();
			return 150;
		}

		// prevent rewriting database
		/*if(time() - filemtime(DIR_ABANTECART . 'system/config.php')>'180'  || $this->request->server['REQUEST_METHOD'] != 'GET'){
			$this->redirect(HTTP_SERVER . 'index.php?rt=activation' . '&admin_path=' . $this->request->post['admin_path']);
		}*/

		$this->view->assign('url', HTTP_SERVER . 'index.php?rt=install');
		$this->view->assign('state_url', HTTP_SERVER . 'index.php?rt=install/getstate');
		$this->view->assign('redirect', HTTP_SERVER . 'index.php?rt=activation&admin_path=' . $this->session->data[ 'install_step_data' ][ 'admin_path' ]);
		$this->view->assign('progressbar', HTTP_SERVER . '/view/image/progressbar.gif');

		$language_blocks = $this->_process_languages('blocks');

		$language_blocks['admin'] = array_merge($language_blocks['admin'],$language_blocks['extensions']['admin']);
		$language_blocks['storefront'] = array_merge($language_blocks['storefront'],$language_blocks['extensions']['storefront']);
		unset($language_blocks['extensions']);

		$this->load->library('json');
		$this->view->assign('language_blocks', AJson::encode($language_blocks));

		$this->addChild('common/header', 'header', 'common/header.tpl');
		$this->addChild('common/footer', 'footer', 'common/footer.tpl');
		$this->processTemplate('pages/install_progress.tpl');


	}
	//  language preloading time counter
	public function getState(){
		return $this->_process_languages('load',$this->request->post['section'],$this->request->post['language_block']);
	}

	private function installSQL() {

		$this->load->model('install');
		$this->model_install->mysql($this->session->data[ 'install_step_data' ]);
	}

	private function _process_languages($mode='load', $section='', $language_block='') {
		$registry = Registry::getInstance();
		$db = new ADB('mysql',
			$this->session->data[ 'install_step_data' ][ 'db_host' ],
			$this->session->data[ 'install_step_data' ][ 'db_user' ],
			$this->session->data[ 'install_step_data' ][ 'db_password' ],
			$this->session->data[ 'install_step_data' ][ 'db_name' ]);
		$registry->set('db', $db);
		define('DB_PREFIX', $this->session->data[ 'install_step_data' ][ 'db_prefix' ]);
		define('DIR_LANGUAGE', DIR_ABANTECART . 'admin/language/');

        // Cache
        $cache = new ACache();
        $registry->set('cache', $cache );

        // Config
        $config = new AConfig($registry);
        $registry->set('config', $config);

        // Extensions api
        $extensions = new ExtensionsApi();
        $extensions->loadEnabledExtensions();
        $registry->set('extensions', $extensions);

		// languages
		$language = new ALanguage($registry, 'en');
		if($mode=='blocks'){
			return $language->getAllLanguageBlocks();
		}
        $language->definitionAutoLoad(1,$section,$language_block,'update');
	}

	private function configure() {
		define('DB_PREFIX', $this->session->data[ 'install_step_data' ][ 'db_prefix' ]);

		$stdout = '<?php' . "\n";
		$stdout .= '/*' . "\n";
		$stdout .= '	AbanteCart, Ideal OpenSource Ecommerce Solution' . "\n";
		$stdout .= '	http://www.AbanteCart.com' . "\n";
		$stdout .= '	Copyright © 2011 Belavier Commerce LLC' . "\n\n";
		$stdout .= '	Released under the Open Software License (OSL 3.0)' . "\n";
		$stdout .= '*/' . "\n";
		$stdout .= '// Admin Section Configuration. You can change this value to any name. Will use ?s=name to access the admin' . "\n";
		$stdout .= 'define(\'ADMIN_PATH\', \'' . $this->session->data[ 'install_step_data' ][ 'admin_path' ] . '\');' . "\n\n";
		$stdout .= '// Database Configuration' . "\n";
		$stdout .= 'define(\'DB_DRIVER\', \'mysql\');' . "\n";
		$stdout .= 'define(\'DB_HOSTNAME\', \'' . $this->session->data[ 'install_step_data' ][ 'db_host' ] . '\');' . "\n";
		$stdout .= 'define(\'DB_USERNAME\', \'' . $this->session->data[ 'install_step_data' ][ 'db_user' ] . '\');' . "\n";
		$stdout .= 'define(\'DB_PASSWORD\', \'' . $this->session->data[ 'install_step_data' ][ 'db_password' ] . '\');' . "\n";
		$stdout .= 'define(\'DB_DATABASE\', \'' . $this->session->data[ 'install_step_data' ][ 'db_name' ] . '\');' . "\n";
		$stdout .= 'define(\'DB_PREFIX\', \'' . DB_PREFIX . '\');' . "\n";
		$stdout .= 'define(\'SALT\', \'' . SALT . '\');' . "\n";
		$stdout .= 'define(\'UNIQUE_ID\', \'' . md5(time()) . '\');' . "\n";
		$stdout .= '?>';

		$file = fopen(DIR_ABANTECART . 'system/config.php', 'w');
		fwrite($file, $stdout);
		fclose($file);
		unset($this->session->data[ 'install_step_data' ], $this->session->data[ 'SALT' ]);

	}
}