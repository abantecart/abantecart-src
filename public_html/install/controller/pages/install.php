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

		if (isset($this->request->get[ 'progress' ])) {
			if (!in_array((int)$this->request->get[ 'progress' ], array( 1, 2, 3 ))) {
				$this->redirect(HTTP_SERVER . 'index.php?rt=activation' . '&admin_path=' . $this->request->post[ 'admin_path' ]);
			}
			echo $this->progress((int)$this->request->get[ 'progress' ]);
			return;
		}


		if (($this->request->server[ 'REQUEST_METHOD' ] == 'POST') && ($this->_validate())) {

			$this->session->data[ 'install_step_data' ] = $this->request->post;
			$this->redirect(HTTP_SERVER . 'index.php?rt=install&progress=1');
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


	public function progress($step) {

		if ($step == 2) {
			$this->installSQL();
			return 50;
		} elseif ($step == 3) {
			$this->configure();
			return 100;
		}

		// prevent rewriting database
		/*if(time() - filemtime(DIR_ABANTECART . 'system/config.php')>'180'  || $this->request->server['REQUEST_METHOD'] != 'GET'){
			$this->redirect(HTTP_SERVER . 'index.php?rt=activation' . '&admin_path=' . $this->request->post['admin_path']);
		}*/

		$this->view->assign('url', HTTP_SERVER . 'index.php?rt=install');
		$this->view->assign('redirect', HTTP_SERVER . 'index.php?rt=activation&admin_path=' . $this->session->data[ 'install_step_data' ][ 'admin_path' ]);
		$this->view->assign('progressbar', HTTP_SERVER . '/view/image/progressbar.gif');

		$this->addChild('common/header', 'header', 'common/header.tpl');
		$this->addChild('common/footer', 'footer', 'common/footer.tpl');
		$this->processTemplate('pages/install_progress.tpl');


	}

	private function installSQL() {

		$this->load->model('install');
		$this->model_install->mysql($this->session->data[ 'install_step_data' ]);
	}

	private function configure() {
		$registry = Registry::getInstance();
		$db = new ADB('mysql',
			$this->session->data[ 'install_step_data' ][ 'db_host' ],
			$this->session->data[ 'install_step_data' ][ 'db_user' ],
			$this->session->data[ 'install_step_data' ][ 'db_password' ],
			$this->session->data[ 'install_step_data' ][ 'db_name' ]);
		$registry->set('db', $db);
		define('DB_PREFIX', $this->session->data[ 'install_step_data' ][ 'db_prefix' ]);
		define('DIR_LANGUAGE', DIR_ABANTECART . 'storefront/language/');
		// languages
		$language = new ALanguage($registry, 'en');
		$language->load('english', 'silent');
		$language->load('common/header', 'silent');
		$language->load('common/footer', 'silent');

		$language->load('product/category', 'silent');
		$language->load('product/manufacturer', 'silent');
		$language->load('product/product', 'silent');
		$language->load('product/search', 'silent');
		$language->load('product/special', 'silent');

		$language->load('blocks/bestseller', 'silent');
		$language->load('blocks/featured', 'silent');
		$language->load('blocks/cart', 'silent');
		$language->load('blocks/category', 'silent');
		$language->load('blocks/content', 'silent');
		$language->load('blocks/currency', 'silent');
		$language->load('blocks/language', 'silent');
		$language->load('blocks/latest', 'silent');
		$language->load('blocks/manufacturer', 'silent');
		$language->load('blocks/order_summary', 'silent');
		$language->load('blocks/special', 'silent');

		$language->load('index/home', 'silent');


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