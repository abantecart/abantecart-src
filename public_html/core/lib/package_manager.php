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
if (!defined('DIR_CORE')) {
	header('Location: static_pages/');
}
/**
 * @property  AExtensionManager $extension_manager
 * @property  AMessage $messages
 * @property  ALoader $load
 * @property  ExtensionsApi $extensions
 * @property  AUser $user
 * @property  ALanguageManager $language
 * @property  ALog $log
 * @property  ACache $cache
 * @property  ADB $db
 */
class APackageManager {
	/**
	 * @var Registry
	 */
	protected $registry;
	public $error = '';
	/**
	 * size of data in bytes
	 *
	 * @var int
	 */
	public $dataSize = 0;

	public function __construct() {
		if (!IS_ADMIN) { // forbid for non admin calls
			throw new AException (AC_ERR_LOAD, 'Error: permission denied to access package manager');
		}
		/**
		 * @var Registry
		 */
		$this->registry = Registry::getInstance();
	}

	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	/**
	 * @param string $url
	 * @param boolean $save
	 * @return boolean|array
	 */
	public function getRemoteFile($url, $save = true, $new_file_name = '') {
		if (!$url) {
			return false;
		}
		$file = new AConnect();
		if ($save) {
			$result = $file->getFile($url, $new_file_name); //download
		} else {
			$result = $file->getResponse($url); // just get data
		}
		if (!$result) {
			$this->error = $file->error;
			return false;
		}
		return $result;
	}

	/**
	 * @param string $url
	 * @return bool|string
	 */
	public function getRemoteFileHeaders($url) {
		if (!$url) {
			return false;
		}
		$file = new AConnect();
		$file->connect_method = 'socket'; //use this method because curl returns no header 'Content-Disposition' with file name
		$url = $url . (!is_int(strpos($url, '?')) ? '?file_size=1' : '&file_size=1');
		$result = $file->getDataHeaders($url);
		if (!$result) {
			$this->error = $file->error;
			return false;
		}
		return $result;
	}

	/**
	 * @param string $tar_filename
	 * @param string $dst_dir
	 * @return boolean
	 */
	public function unpack($tar_filename, $dst_dir) {
		if (!file_exists($tar_filename)) {
			$this->error = 'Error: Can\'t unpack file "' . $tar_filename . '" because it does not exists.';
			$error = new AError ($this->error);
			$error->toLog()->toDebug();
			return false;
		}
		if (!file_exists($dst_dir) || !is_dir($dst_dir)) {
			$this->error = 'Error: Can\'t unpack file "' . $tar_filename . '" because destination directory "' . $dst_dir . '" does not exists.';
			$error = new AError ($this->error);
			$error->toLog()->toDebug();
			return false;
		}
		if (!is_writable($dst_dir)) {
			$this->error = 'Error: Can\'t unpack file "' . $tar_filename . '" because destination directory "' . $dst_dir . '" have no write permission.';
			$error = new AError ($this->error);
			$error->toLog()->toDebug();
			return false;
		}


		$command = 'tar -C ' . $dst_dir . ' -xzvf ' . $tar_filename . ' > /dev/null';
		if (isFunctionAvailable('system')) {
			system($command, $exit_code);
		} else {
			$exit_code = 1;
		}

		if ($exit_code) {
			$this->load->library('targz');
			$targz = new Atargz();
			$targz->extractTar($tar_filename, $dst_dir);
		}

		$this->chmod_R($dst_dir . $this->session->data['package_info']['tmp_dir'], 0777, 0777);
		return true;
	}

	/**
	 * Function make backup and move it into admin/system/backup/directory
	 * @param string $extension_id
	 * @return bool
	 */
	public function backupPrevious($extension_id = '') {

		$old_path = !$extension_id ? DIR_ROOT . '/' . $this->session->data['package_info']['dst_dir'] : DIR_EXT;
		$package_id = !$extension_id ? $this->session->data['package_info']['package_id'] : $extension_id;

		if (file_exists($old_path . $package_id)) {
			$backup = new ABackup($extension_id);
			$backup_dirname = $backup->getBackupName();
			if ($backup_dirname) {

				if (!$backup->backupDirectory($old_path . $package_id)) {
					$this->error = $backup->error;
					return false;
				}

				if (!$backup->dumpDatabase()) {
					return false;
				}
				if (!$backup->archive(DIR_BACKUP . $backup_dirname . '.tar.gz', DIR_BACKUP, $backup_dirname)) {
					return false;
				}
			} else {
				return false;
			}

			$info = $this->extensions->getExtensionInfo($package_id);

			$install_upgrade_history = new ADataset('install_upgrade_history', 'admin');
			$install_upgrade_history->addRows(array('date_added' => date("Y-m-d H:i:s", time()),
				'name' => $package_id,
				'version' => $info['version'],
				'backup_file' => $backup_dirname . '.tar.gz',
				'backup_date' => date("Y-m-d H:i:s", time()),
				'type' => 'backup',
				'user' => $this->user->getUsername()));

			//delete previous version
			$this->removeDir($old_path . $package_id);
		}

		return true;
	}

	public function replaceCoreFiles() {
		$corefiles = $this->session->data['package_info']['package_content']['core'];
		if ($this->session->data['package_info']['ftp']) {
			foreach ($corefiles as $core_filename) {
				$remote_file = pathinfo($this->session->data['package_info']['ftp_path'] . $core_filename, PATHINFO_BASENAME);
				$remote_dir = pathinfo($this->session->data['package_info']['ftp_path'] . $core_filename, PATHINFO_DIRNAME);
				$src_dir = (string)$this->session->data['package_info']['tmp_dir'] . $this->session->data['package_info']['package_dir'] . '/code/' . $core_filename;
				$result = $this->ftp_move($src_dir, $remote_file, $remote_dir);
				if ($result) {
					$install_upgrade_history = new ADataset('install_upgrade_history', 'admin');
					$install_upgrade_history->addRows(array('date_added' => date("Y-m-d H:i:s", time()),
						'name' => 'Upgrade core file: ' . $remote_file,
						'version' => $this->session->data['package_info']['package_version'],
						'backup_file' => '',
						'backup_date' => '',
						'type' => 'upgrade',
						'user' => $this->user->getUsername()));
				} else {
					$this->error = "Error: Can't upgrade file : '" . $core_filename;
					$this->messages->saveNotice('Error', $this->error);
					$error = new AError ($this->error);
					$error->toLog()->toDebug();
				}
			}
		} else {
			foreach ($corefiles as $core_filename) {
				if (file_exists(DIR_ROOT . '/' . $core_filename)) {
					unlink(DIR_ROOT . '/' . $core_filename);
				}
				//check is target directory exists before copying
				$dir = pathinfo(DIR_ROOT . '/' . $core_filename, PATHINFO_DIRNAME);
				if (!file_exists($dir)) {
					mkdir($dir, 0777, true);
				}

				$result = rename($this->session->data['package_info']['tmp_dir'] . $this->session->data['package_info']['package_dir'] . '/code/' . $core_filename, DIR_ROOT . '/' . $core_filename);
				if ($result) {
					chmod(DIR_ROOT . '/' . $core_filename,0777);
					$install_upgrade_history = new ADataset('install_upgrade_history', 'admin');
					$install_upgrade_history->addRows(array('date_added' => date("Y-m-d H:i:s", time()),
						'name' => 'Upgrade core file: ' . $core_filename,
						'version' => $this->session->data['package_info']['package_version'],
						'backup_file' => '',
						'backup_date' => '',
						'type' => 'upgrade',
						'user' => $this->user->getUsername()));
				} else {
					$this->error = "Error: Can't upgrade file : '" . $core_filename;
					$this->messages->saveNotice('Error', $this->error);
					$error = new AError ($this->error);
					$error->toLog()->toDebug();
				}
			}
		}
	}

	/**
	 * method removes non-empty directory (use it carefully)
	 *
	 * @param srting $dir
	 * @return boolean
	 */
	public function removeDir($dir = '') {
		if (!$this->session->data['package_info']['ftp']) { // if not ftp
			if (is_dir($dir)) {
				$objects = scandir($dir);
				foreach ($objects as $obj) {
					if ($obj != "." && $obj != "..") {
						chmod($dir . "/" . $obj, 0777);
						$err = is_dir($dir . "/" . $obj) ? $this->removeDir($dir . "/" . $obj) : unlink($dir . "/" . $obj);
						if (!$err) {
							$this->error = "Error: Can't to delete file or directory: '" . $dir . "/" . $obj . "'.";
							$this->messages->saveNotice('Error', $this->error);
							$error = new AError ($this->error);
							$error->toLog()->toDebug();
							return false;
						}
					}
				}
				reset($objects);
				rmdir($dir);
				return true;
			} else {
				return $dir;
			}

		} else {

			$ftp_user = $this->session->data['package_info']['ftp_user'];
			$ftp_password = $this->session->data['package_info']['ftp_password'];
			$ftp_port = $this->session->data['package_info']['ftp_port'];
			$ftp_host = $this->session->data['package_info']['ftp_host'];
			$dir = $this->session->data['package_info']['ftp_path'] . $this->session->data['package_info']['dst_dir'] . $dir;

			$fconnect = ftp_connect($ftp_host, $ftp_port);
			ftp_login($fconnect, $ftp_user, $ftp_password);
			ftp_pasv($fconnect, true);

			$this->delete_ftp_dir($fconnect, $dir);
			ftp_close($fconnect);
		}

		return true;
	}

	/**
	 * function returns destination directory of extension or some else.
	 * It looking for package_id in code directory of package
	 * @return bool|mixed
	 */
	public function getDestinationDirectories() {
		$package_id = $this->session->data['package_info']['package_id'];
		$package_dirname = $this->session->data['package_info']['package_dir'];
		$output = array();
		if (!file_exists($this->session->data['package_info']['tmp_dir'] . $package_dirname . "/code")) {
			return false;
		} else {
			$dir = $this->session->data['package_info']['tmp_dir'] . $package_dirname . "/code";
			$d = array();
			while ($dirs = glob($dir . '/*', GLOB_ONLYDIR)) {
				$dir .= '/*';
				if (!$d) {
					$d = $dirs;
				} else {
					$d = array_merge($d, $dirs);
				}
			}
		}

		if ($d) {
			foreach ($d as $dir) {
				$dir = str_replace($this->session->data['package_info']['tmp_dir'] . $package_dirname . "/code/", "", $dir);
				$output[] = str_replace($package_id, '', $dir);
			}
		}
		return $output;
	}

	/**
	 * @param string $ftp_user
	 * @param string $ftp_password
	 * @param string $ftp_host
	 * @param string $ftp_path
	 * @return bool
	 */
	public function checkFTP($ftp_user, $ftp_password = '', $ftp_host = '', $ftp_path = '') {
		$this->load->language('tool/package_installer');
		if (!$ftp_host) {
			$ftp_host = 'localhost';
		} else { // looking for port number
			$start = strrchr($ftp_host, ':');
			if ($start !== FALSE) {
				$ftp_port = substr($ftp_host, $start + 1);
				if ((int)$ftp_port == $ftp_port) {
					$ftp_host = substr($ftp_host, 0, $start);
				}
			}
		}

		$ftp_port = !$ftp_port ? 21 : $ftp_port;

		if (!$ftp_user) {
			$this->error = $this->language->get('error_ftp_user');
			return false;
		}
		if (!$ftp_password) {
			$this->error = $this->language->get('error_ftp_password');
			return false;
		}


		$fconnect = ftp_connect($ftp_host, $ftp_port);
		if ($fconnect) {
			$login = ftp_login($fconnect, $ftp_user, $ftp_password);
			if (!$login) {
				$this->error = $this->language->get('error_ftp_login') . $ftp_host . ':' . $ftp_port;
				return false;
			}

			$ftp_path = !$ftp_path ? $this->_ftp_find_app_root($fconnect, $ftp_user) : $ftp_path;

			if (is_array($ftp_path) && $ftp_path) {
				$temp = array();
				foreach ($ftp_path as $ftp_base_path) {
					if (file_exists($ftp_base_path . 'system/config.php')) {
						$config_content = file_get_contents($ftp_base_path . 'system/config.php');
						if (strpos($config_content, UNIQUE_ID) !== FALSE) {
							$temp[] = $ftp_base_path;
							break;
						}
					}
				}
				$ftp_path = $temp;
				unset($temp);
			}
			// it made for recognizing a few copy of cart with same unique id in config
			$ftp_path = is_array($ftp_path) && sizeof($ftp_path) == 1 ? $ftp_path[0] : $ftp_path;
			if ($ftp_path) {
				if (!ftp_chdir($fconnect, $ftp_path)) {
					if (is_array($ftp_path)) {
						$this->error = $this->language->get('error_ftp_path_array');
						$this->session->data['package_info']['ftp_path'] = '';
						// show path suggestions
						foreach ($ftp_path as $suggest) {
							$this->error .= '<br>' . $suggest;
						}
					} else {
						$this->session->data['package_info']['ftp_path'] = $ftp_path; // for form default value
						$this->error = $this->language->get('error_ftp_path');
					}
					ftp_close($fconnect);
					return false;
				}
			}
			// if all fine  - write ftp parameters into session
			$this->session->data['package_info']['ftp'] = true;
			$this->session->data['package_info']['ftp_user'] = $ftp_user;
			$this->session->data['package_info']['ftp_password'] = $ftp_password;
			$this->session->data['package_info']['ftp_host'] = $ftp_host;
			$this->session->data['package_info']['ftp_port'] = $ftp_port;
			$this->session->data['package_info']['ftp_path'] = $ftp_path;
			//$this->session->data['package_info']['tmp_dir'] = sys_get_temp_dir ().'/';
			ftp_close($fconnect);
		} else {
			$this->error = $this->language->get('error_ftp_connect');
			return false;
		}

		return true;
	}

	/**
	 * @param resource $fconnect
	 * @param string $ftp_user
	 * @param string $needle
	 * @return array|bool
	 */
	private function _ftp_find_app_root($fconnect, $ftp_user = '', $needle = 'extensions') {
		if (!$fconnect) {
			return false;
		}
		$prefix = ftp_pwd($fconnect);
		$top_dirs = array('home', 'htdocs', $ftp_user, $_SERVER['HTTP_HOST'], 'www', 'public_html');
		foreach ($top_dirs as $dir) {

			$contents = ftp_nlist($fconnect, $dir);
			if (!$contents) continue;

			if (in_array($dir . '/' . $needle, $contents)) {
				$ftp_base_path[] = $prefix . '/' . $dir . '/';
			}

			foreach ($contents as $dir2) {
				$contents2 = ftp_nlist($fconnect, $dir2);
				if (in_array($dir2 . '/' . $needle, $contents2)) {
					$ftp_base_path[] = $prefix . '/' . $dir2 . '/';
				}
				foreach ($contents2 as $dir3) {
					$contents3 = ftp_nlist($fconnect, $dir3);
					if (in_array($dir3 . '/' . $needle, $contents3)) {
						$ftp_base_path[] = $prefix . '/' . $dir3 . '/';
					}
				}
			}
		}
		return $ftp_base_path;
	}


	/**
	 * Function for moving directory or file via ftp-connection
	 *
	 * @param string $local local path to file or directory
	 * @param string $remote remote file  or directory name
	 * @param string $remote_dir
	 * @return bool
	 */
	public function ftp_move($local, $remote, $remote_dir) {
		$local = (string)$local;
		$remote = (string)$remote;
		$remote_dir = (string)$remote_dir;

		if (!$this->session->data['package_info']['ftp']) {
			return false;
		}

		$ftp_user = $this->session->data['package_info']['ftp_user'];
		$ftp_password = $this->session->data['package_info']['ftp_password'];
		$ftp_port = $this->session->data['package_info']['ftp_port'];
		$ftp_host = $this->session->data['package_info']['ftp_host'];

		$fconnect = ftp_connect($ftp_host, $ftp_port);
		ftp_login($fconnect, $ftp_user, $ftp_password);
		ftp_pasv($fconnect, true);

		// if destination folder does not exists - try to create
		if (!ftp_chdir($fconnect, $remote_dir)) {
			$result = ftp_mkdir($fconnect, $remote_dir);
			if (!$result) {
				@fclose($fconnect);
				return false;
			}
			@ftp_chmod($fconnect, 0777, $remote_dir);
		}
		ftp_chdir($fconnect, $remote_dir);


		if (is_dir($local)) {
			$this->ftp_put_dir($fconnect, $local, $remote_dir);

		} else {
			if (!ftp_put($fconnect, $remote, $local, FTP_BINARY)) {
				fclose($fconnect);
				return false;
			}
			@ftp_chmod($fconnect, 0777, $remote_dir . pathinfo($local, PATHINFO_FILENAME));
		}


		fclose($fconnect);
		return true;
	}

	/**
	 * method for moving directory via ftp connection
	 * @param resource $conn_id
	 * @param string $src_dir
	 * @param string $dst_dir
	 */
	private function ftp_put_dir($conn_id, $src_dir, $dst_dir) {
		$d = dir($src_dir);
		while ($file = $d->read()) { // do this for each file in the directory
			if ($file != "." && $file != "..") { // to prevent an infinite loop
				if (is_dir($src_dir . "/" . $file)) { // do the following if it is a directory
					if (!@ftp_chdir($conn_id, $dst_dir . "/" . $file)) {
						ftp_mkdir($conn_id, $dst_dir . "/" . $file); // create directories that do not yet exist
						ftp_chmod($conn_id, 0777, $dst_dir . "/" . $file);
					}
					$this->ftp_put_dir($conn_id, $src_dir . "/" . $file, $dst_dir . "/" . $file); // recursive part
				} else {
					ftp_put($conn_id, $dst_dir . "/" . $file, $src_dir . "/" . $file, FTP_BINARY); // put the files
					ftp_chmod($conn_id, 0777, $dst_dir . "/" . $file);
				}
			}
		}
		$d->close();
	}

	/**
	 * @param resource $conn
	 * @param string $dir
	 * @return bool
	 */
	private function delete_ftp_dir($conn, $dir) {
		$files = ftp_nlist($conn, $dir);
		if (!$files) {
			return ftp_rmdir($conn, $dir);
		}
		foreach ($files as $file) {
			$is_dir = ftp_chdir($conn, $file);
			if ($is_dir) {
				$this->delete_ftp_dir($conn, $file);
			} else {
				ftp_delete($conn, $file);
			}
		}
		ftp_rmdir($conn, $dir);
		return true;
	}

	/**
	 * @param string $extension_id
	 * @param string $type
	 * @param string $version
	 * @param string $install_mode
	 * @return bool
	 */
	public function installExtension($extension_id = '', $type = '', $version = '', $install_mode = 'install') {
		$type = !$type ? $this->session->data['package_info']['package_type'] : $type;
		$version = !$version ? $this->session->data['package_info']['package_version'] : $version;
		$extension_id = !$extension_id ? $this->session->data['package_info']['package_id'] : $extension_id;
		$package_dirname = $this->session->data['package_info']['package_dir'];

		switch ($type) {
			case 'extension':
			case 'extensions':
			case 'template':
			case 'payment':
			case 'shipping':
			case 'language':
				// if extensions is not installed yet - install it
				if ($install_mode == 'install') {
					$validate = $this->extension_manager->validate($extension_id);
					$validateErrors = $this->extension_manager->errors;
					if (!$validate) {
						$this->error = implode('<br>', $validateErrors);
						$err = new AError($this->error);
						$err->toLog()->toDebug();
						return false;
					}

					$result = $this->extension_manager->install($extension_id, getExtensionConfigXml($extension_id));
					if ($result === false) {
						return false;
					}

				} elseif ($install_mode == 'upgrade') {
					$install_upgrade_history = new ADataset('install_upgrade_history', 'admin');
					$install_upgrade_history->addRows(array('date_added' => date("Y-m-d H:i:s", time()),
						'name' => $extension_id,
						'version' => $version,
						'backup_file' => '',
						'backup_date' => '',
						'type' => 'upgrade',
						'user' => $this->user->getUsername()));


					$config = simplexml_load_string(file_get_contents($this->session->data['package_info']['tmp_dir'] . $package_dirname . '/code/extensions/' . $extension_id . '/config.xml'));
					$config = !$config ? getExtensionConfigXml($extension_id) : $config;
					// running sql upgrade script if it exists
					if (isset($config->upgrade->sql)) {
						$file = $this->session->data['package_info']['tmp_dir'] . $package_dirname . '/code/extensions/' . $extension_id . '/' . (string)$config->upgrade->sql;
						$file = !file_exists($file) ? DIR_EXT . $extension_id . '/' . (string)$config->upgrade->sql : $file;
						if (file_exists($file)) {
							$this->db->performSql($file);
						}
					}
					// running php install script if it exists
					if (isset($config->upgrade->trigger)) {
						$file = $this->session->data['package_info']['tmp_dir'] . $package_dirname . '/code/extensions/' . $extension_id . '/' . (string)$config->upgrade->trigger;
						$file = !file_exists($file) ? DIR_EXT . $extension_id . '/' . (string)$config->upgrade->sql : $file;
						if (file_exists($file)) {
							include($file);
						}
					}

					$this->extension_manager->editSetting($extension_id, array('license_key' => $this->session->data['package_info']['installation_key'],
						'version' => $version));
				}
				break;
			default:
				$this->error = 'Unknown extension type: "' . $type . '"';
				$err = new AError($this->error);
				$err->toLog()->toDebug();
				return false;
				break;
		}
		return true;
	}

	/**
	 * @param SimpleXmlElement $config
	 */
	public function upgradeCore($config) {
		//clear all cache
		$this->cache->delete('*');

		$package_dirname = $this->session->data['package_info']['package_dir'];
		$package_tmpdir = $this->session->data['package_info']['tmp_dir'];
		// running sql upgrade script if it exists
		if (isset($config->upgrade->sql)) {
			$file = $package_tmpdir . $package_dirname . '/' . (string)$config->upgrade->sql;
			if (is_file($file)) {
				$this->db->performSql($file);
			}
		}
		// running php upgrade script if it exists
		if (isset($config->upgrade->trigger)) {
			$file = $package_tmpdir . $package_dirname . '/' . (string)$config->upgrade->trigger;
			if (is_file($file)) {
				include($file);
			}
		}


		// write to history
		$install_upgrade_history = new ADataset('install_upgrade_history', 'admin');
		$install_upgrade_history->addRows(array('date_added' => date("Y-m-d H:i:s", time()),
			'name' => 'Core upgrade',
			'version' => $this->session->data['package_info']['package_version'],
			'backup_file' => '',
			'backup_date' => '',
			'type' => 'upgrade',
			'user' => $this->user->getUsername()));
	}

	/**
	 * @param string $new_version
	 * @return bool
	 */
	public function updateCoreVersion($new_version) {
		if (!$new_version) {
			return false;
		}

		$new_version = preg_replace('/[^0-9\.]/', '', $new_version);
		list($master, $minor, $built) = explode(".", $new_version);
		$content = "<?php\n";
		$content .= "define('MASTER_VERSION', '" . $master . "');\n";
		$content .= "define('MINOR_VERSION', '" . $minor . "');\n";
		$content .= "define('VERSION_BUILT', '" . $built . "');\n";

		if (!$this->session->data['package_info']['ftp']) {
			file_put_contents(DIR_CORE . 'version.php', $content);
		} else {
			file_put_contents($this->session->data['package_info']['tmp_dir'] . 'version.php', $content);
			$this->ftp_move($this->session->data['package_info']['tmp_dir'] . 'version.php',
				'version.php',
					$this->session->data['package_info']['ftp_path'] . 'core');
		}
	}

	/**
	 * Method change access mode recursively
	 * @param string $path path to directory or file
	 * @param string $filemode
	 * @param string $dirmode
	 * @return
	 */
	public function chmod_R($path, $filemode, $dirmode) {
		$path = (string)$path;
		if (is_dir($path)) {
			if (!chmod($path, $dirmode)) {
				$dirmode_str = decoct($dirmode);
				// print "Failed applying filemode '$dirmode_str' on directory '$path'\n";
				//  print "  `-> the directory '$path' will be skipped from recursive chmod\n";
				return;
			}
			$dh = opendir($path);
			while (($file = readdir($dh)) !== false) {
				if ($file != '.' && $file != '..') { // skip self and parent pointing directories
					$fullpath = $path . '/' . $file;
					$this->chmod_R($fullpath, $filemode, $dirmode);
				}
			}
			closedir($dh);
		} else {
			//skip if does not exists
			if (!file_exists($path)) {
				return;
			}

			if (is_link($path)) {
				// print "link '$path' is skipped\n";
				return;
			}
			if (!chmod($path, $filemode)) {
				$filemode_str = decoct($filemode);
				//print "Failed applying filemode '$filemode_str' on file '$path'\n";
				return;
			}
		}
	}
}