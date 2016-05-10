<?php
/*
------------------------------------------------------------------------------
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
------------------------------------------------------------------------------  
*/
/**\
 * Class ControllerPagesInstall
 * @property ModelInstall $model_install
 */
class ControllerPagesInstall extends AController {
	private $error = array();
	public $data = array();

	public function main() {

		$this->data = array();
		$run_level = $this->request->get['runlevel'];

		if (isset($run_level)) {
			if (!in_array((int)$run_level, array( 1, 2, 3, 4, 5))) {
				$this->redirect(HTTP_SERVER . 'index.php?rt=activation' . '&admin_path=' . $this->request->post['admin_path']);
			}

			if (!$this->session->data['install_step_data'] && (int)$run_level == 1) {
				if (filesize(DIR_ABANTECART . 'system/config.php')) {
					$this->redirect(HTTP_SERVER . 'index.php?rt=activation');
				} else {
					$this->redirect(HTTP_SERVER . 'index.php?rt=license');
				}
			}

			echo $this->runlevel((int)$run_level);
			return null;
		}

		if ( $this->request->is_POST() && ($this->_validate())) {

			$this->session->data['install_step_data'] = $this->request->post;
			$this->redirect(HTTP_SERVER . 'index.php?rt=install&runlevel=1');
		}


		$this->data[ 'error' ] = $this->error;
		$this->data[ 'action' ] = HTTP_SERVER . 'index.php?rt=install';

		$fields = array( 'db_driver', 'db_host', 'db_user', 'db_password', 'db_name', 'db_prefix', 'username', 'password',
			'password_confirm', 'email', 'admin_path',
		);
		$defaults = array( '', 'localhost', '', '', '', 'abc_', 'admin', '', '', '', '' );
		$place_holder = array( 'Select Database Driver', 
								'Enter Database Hostname', 
								'Enter Database Username', 
								'Enter Password, if any', 
								'Enter Database Name', 
								'Add prefix to database tables',
								'Enter new admin username', 
								'Enter Secret Admin Password', 
								'Repeat the password', 
								'Provide valid email of administrator', 
								'Enter your secret admin key' 
								);

		foreach ($fields as $k => $field) {
			if (isset($this->request->post[ $field ])) {
				$this->data[ $field ] = $this->request->post[ $field ];
			} else {
				$this->data[ $field ] = $defaults[ $k ];
			}
		}

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

		foreach ($fields as $k => $field) {
			if($field != 'db_driver'){
				$this->data[ 'form' ][ $field ] = $form->getFieldHtml(array(
					'type' => (in_array($field, array( 'password', 'password_confirm' )) ? 'password' : 'input'),
					'name' => $field,
					'value' => $this->data[ $field ],
					'placeholder' => $place_holder[$k],
					'required' => in_array($field, array( 'db_host', 'db_user', 'db_name', 'username', 'password', 'password_confirm', 'email', 'admin_path' )),
				));
			} else {
				$options = array();

				if(extension_loaded('mysqli')){
					$options['amysqli'] = 'MySQLi';
				}

				if(extension_loaded('pdo_mysql')){
					$options['apdomysql'] = 'PDO MySQL';
				}

				//regular mysql is not supported on PHP 5.5.+
				if(extension_loaded('mysql') && version_compare(phpversion(), '5.5.0', '<') == TRUE ){
					$options['mysql'] = 'MySQL';
				}
				if($options){
					$this->data['form'][$field] = $form->getFieldHtml(array (
							'type'     => 'selectbox',
							'name'     => $field,
							'value'    => $this->data[$field],
							'options'  => $options,
							'required' => true
					));
				}else{
					$this->data['form'][$field] = '';
					$this->data['error'][$field] = 'No database support. Please install AMySQL or PDO_MySQL php extension.';
				}

			}
		}
		
		$this->view->assign('back', HTTP_SERVER . 'index.php?rt=settings');

		$this->addChild('common/header', 'header', 'common/header.tpl');
		$this->addChild('common/footer', 'footer', 'common/footer.tpl');

		$this->view->batchAssign($this->data);
		$this->processTemplate('pages/install.tpl');
	}

	private function _validate() {
		if (!$this->request->post[ 'admin_path' ]) {
			$this->error[ 'admin_path' ] = 'Admin unique name is required!';
		} else if (preg_match('/[^A-Za-z0-9_]/', $this->request->post[ 'admin_path' ])) {
			$this->error[ 'admin_path' ] = 'Admin unique name contains non-alphanumeric characters!';
		}

		if (!$this->request->post[ 'db_driver' ]) {
			$this->error[ 'db_driver' ] = 'Driver required!';
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

		if (!empty($this->request->post[ 'db_prefix' ]) && preg_match('/[^A-Za-z0-9_]/', $this->request->post[ 'db_prefix' ])) {
			$this->error[ 'db_prefix' ] = 'DB prefix contains non-alphanumeric characters!';
		}

		if ($this->request->post[ 'db_driver' ]
			&& $this->request->post[ 'db_host' ]
			&& $this->request->post[ 'db_user' ]
			&& $this->request->post[ 'db_password' ]
			&& $this->request->post[ 'db_name' ]
		) {
			try{
			$db = new ADB($this->request->post[ 'db_driver' ],
						  $this->request->post[ 'db_host' ],
				          $this->request->post[ 'db_user' ],
						  $this->request->post[ 'db_password' ],
						  $this->request->post[ 'db_name' ]);
			}catch(AException $exception){
				$this->error[ 'warning' ] = $exception->getMessage();
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
		$this->load->library('json');
		if ($step == 2) {
			$this->_install_SQL();
			$this->response->addJSONHeader();
			return AJson::encode(array( 'ret_code' => 50 ));
		} elseif ($step == 3) {
			//NOTE: Create config as late as possible. This will prevent triggering finished installation 
			$this->_configure();
			//wait for end of writing of file on disk (for slow hdd)
			sleep(3);
			$this->session->data['finish'] = 'false';
			$this->response->addJSONHeader();
			return AJson::encode(array( 'ret_code' => 100 ));
		} elseif ($step == 4) {
			// Load demo data
			if($this->session->data['install_step_data']['load_demo_data'] == 'on') {
				$this->_load_demo_data();
			}	
			//Clean session for configurations. We do not need them any more
			unset($this->session->data['install_step_data'], $this->session->data['SALT']);		
			$this->session->data['finish'] = 'false';
			$this->response->addJSONHeader();
			return AJson::encode(array( 'ret_code' => 150 ));
		} elseif ($step == 5) {
			//install is completed but we are not yet finished
			$this->session->data['finish'] = 'false';
			// Load languages with asynchronous approach
			$this->response->addJSONHeader();
			return AJson::encode(array( 'ret_code' => 200 ));
		}		

		$this->view->assign('url', HTTP_SERVER . 'index.php?rt=install');
		$this->view->assign('redirect', HTTP_SERVER . 'index.php?rt=finish');
		$temp = $this->dispatch('pages/install/progressbar_scripts', array( 'url' => HTTP_SERVER . 'index.php?rt=install/progressbar' ));
		$this->view->assign('progressbar_scripts', $temp->dispatchGetOutput());

		$this->addChild('common/header', 'header', 'common/header.tpl');
		$this->addChild('common/footer', 'footer', 'common/footer.tpl');
		$this->processTemplate('pages/install_progress.tpl');
		return null;
	}


	private function _install_SQL() {
		$this->load->model('install');
		$this->model_install->RunSQL($this->session->data['install_step_data']);

	}

	private function _configure() {
		define('DB_PREFIX', $this->session->data['install_step_data'][ 'db_prefix' ]);

		$content = "<?php\n";
		$content .= "/**\n";
		$content .= "	AbanteCart, Ideal OpenSource Ecommerce Solution\n";
		$content .= "	http://www.AbanteCart.com\n";
		$content .= "	Copyright © 2011-".date('Y')." Belavier Commerce LLC\n\n";
		$content .= "	Released under the Open Software License (OSL 3.0)\n";
		$content .= "*/\n";
		$content .= "// Admin Section Configuration. You can change this value to any name. Will use ?s=name to access the admin\n";
		$content .= "define('ADMIN_PATH', '" . $this->session->data['install_step_data'][ 'admin_path' ] . "');\n\n";
		$content .= "// Database Configuration\n";
		$content .= "define('DB_DRIVER', '".$this->session->data['install_step_data']['db_driver']."');\n";
		$content .= "define('DB_HOSTNAME', '" . $this->session->data['install_step_data']['db_host'] . "');\n";
		$content .= "define('DB_USERNAME', '" . $this->session->data['install_step_data']['db_user'] . "');\n";
		$content .= "define('DB_PASSWORD', '" . $this->session->data['install_step_data']['db_password'] . "');\n";
		$content .= "define('DB_DATABASE', '" . $this->session->data['install_step_data']['db_name'] . "');\n";
		$content .= "define('DB_PREFIX', '" . DB_PREFIX . "');\n";		
		$content .= "\n";		
		$content .= "define('CACHE_DRIVER', 'file');\n";
		$content .= "// Unique AbanteCart store ID\n";
		$content .= "define('UNIQUE_ID', '" . md5(time()) . "');\n";
		$content .= "// Salt key for oneway encryption of passwords. NOTE: Change of SALT key will cause a loss of all existing users' and customers' passwords!\n";
		$content .= "define('SALT', '" . SALT . "');\n";
		$content .= "// Encryption key for protecting sensitive information. NOTE: Change of this key will cause a loss of all existing encrypted information!\n";
		$content .= "define('ENCRYPTION_KEY', '" . randomWord(6) . "');\n";

		$file = fopen(DIR_ABANTECART . 'system/config.php', 'w');
		fwrite($file, $content);
		fclose($file);
		return null;
	}

	private function _prepare_registry() {
		$registry = Registry::getInstance();
		//This is ran after config is saved and we ahve database connection now				
		$db = new ADB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		$registry->set('db', $db);
		define('DIR_LANGUAGE', DIR_ABANTECART . 'admin/language/');

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


	public function _load_demo_data() {
		$reg = $this->_prepare_registry();
		$db = $reg->get('db');	
		$db->query("SET NAMES 'utf8'");
		$db->query("SET CHARACTER SET utf8");
		
		$file = DIR_APP_SECTION . 'abantecart_sample_data.sql';
	
		if ($sql = file($file)) {
			$query = '';

			foreach($sql as $line) {
				$tsl = trim($line);

				if (($sql != '') && (substr($tsl, 0, 2) != "--") && (substr($tsl, 0, 1) != '#')) {
					$query .= $line;
  
					if (preg_match('/;\s*$/', $line)) {
						$query = str_replace("DROP TABLE IF EXISTS `ac_", "DROP TABLE IF EXISTS `" . DB_PREFIX, $query);
						$query = str_replace("CREATE TABLE `ac_", "CREATE TABLE `" . DB_PREFIX, $query);
						$query = str_replace("INSERT INTO `ac_", "INSERT INTO `" . DB_PREFIX, $query);
						
						$result = $db->query($query);
  
						if (!$result || $db->error) {
							die($db->error . '<br>'. $query);
						}
	
						$query = '';
					}
				}
			}
			$db->query("SET CHARACTER SET utf8");
			$db->query("SET @@session.sql_mode = 'MYSQL40'");
		}
		//clear earlier created cache by AConfig and ALanguage classes in previous step
		$cache = new ACache();
        $cache->remove('*');
		return null;
	}	

	public function progressbar() {
		session_write_close(); // unlock session !important!
		$dbprg = new progressbar($this->_prepare_registry(), $this);
		$this->response->addJSONHeader();
		switch ($this->request->get[ "work" ]) {
			case "max":
				echo AJson::encode(array( 'total' => $dbprg->get_max() ));
				break;
			case "do":
				$result = $dbprg->do_work();
				if (!$result) {
					$result = array( 'status' => 406,
									 'errorText' => $result );
				} else {
					$result = array( 'status' => 100 );
				}
				echo AJson::encode($result);
				break;
			case "progress":
				echo AJson::encode(array( 'prc' => (int)$dbprg->get_progress() ));
				break;
		}
	}

	public function progressbar_scripts($url) {
		$this->view->assign('url', $url);
		$this->processTemplate('pages/progressbar.tpl');
	}
}

/** @noinspection PhpIncludeInspection */
require_once(DIR_CORE . "lib/progressbar.php");
/*
 * Interface for progressbar
 * */
class progressbar implements AProgressBar {
	/**
	 * @var Registry
	 */
	private $registry;

	function __construct($registry) {
		$this->registry = $registry;
	}

	function get_max() {
		define('IS_ADMIN', true);
		$language = new ALanguageManager($this->registry, 'en');
		$language_blocks = $language->getAllLanguageBlocks('english');
		$language_blocks[ 'admin' ] = array_merge($language_blocks[ 'admin' ], $language_blocks[ 'extensions' ][ 'admin' ]);
		$language_blocks[ 'storefront' ] = array_merge($language_blocks[ 'storefront' ], $language_blocks[ 'extensions' ][ 'storefront' ]);
		return sizeof($language_blocks[ 'admin' ]) + sizeof($language_blocks[ 'storefront' ]);
	}

	function get_progress() {
		$cnt = 0;
		$res = $this->registry->get('db')->query('SELECT section, COUNT(DISTINCT `block`) as cnt
													FROM ' . DB_PREFIX . 'language_definitions
													GROUP by section');
		foreach ($res->rows as $row) {
			$cnt += $row[ 'cnt' ];
		}
		return $cnt;
	}

	function do_work() {
		define('IS_ADMIN', true);
		$language = new ALanguageManager($this->registry, 'en');
		//Load default language (1) English on install only.
		return $language->definitionAutoLoad(1, 'all', 'all');
	}
}