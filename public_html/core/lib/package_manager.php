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

/**
 * @property  AExtensionManager $extension_manager
 * @property  AMessage $messages
 * @property  ALoader $load
 * @property  ASession $session
 * @property  ExtensionsApi $extensions
 * @property  AUser $user
 * @property  ALanguageManager $language
 * @property  ALog $log
 * @property  ACache $cache
 * @property  ADB $db
 */
class APackageManager
{
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

    const DS = DIRECTORY_SEPARATOR;

    public function __construct()
    {
        if (!IS_ADMIN) { // forbid for non admin calls
            throw new AException (AC_ERR_LOAD, 'Error: permission denied to access package manager');
        }
        /**
         * @var Registry
         */
        $this->registry = Registry::getInstance();
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
     * @param string $url
     * @param boolean $save
     * @param string $new_file_name
     *
     * @return boolean|array
     * @throws AException
     */
    public function getRemoteFile($url, $save = true, $new_file_name = '')
    {
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
     * @param false $secure
     *
     * @return array|bool|int
     * @throws AException
     */
    public function getRemoteFileHeaders($url, $secure = false)
    {
        if (!$url) {
            return false;
        }
        $file = new AConnect();
        $file->connect_method = 'curl';
        $url = $url.(!is_int(strpos($url, '?')) ? '?file_size=1' : '&file_size=1');
        $result = $file->getDataHeaders($url, ($secure ? 443 : null));
        if (!$result) {
            $this->error = $file->error;
            return false;
        }
        return $result;
    }

    /**
     * @param string $tar_filename
     * @param string $dst_dir
     *
     * @return boolean
     * @throws AException
     */
    public function unpack($tar_filename, $dst_dir)
    {
        if (!file_exists($tar_filename)) {
            $this->error = 'Error: Cannot unpack file "'.$tar_filename.'" because it does not exists.';
            $error = new AError ($this->error);
            $error->toLog()->toDebug();
            return false;
        }
        if (!file_exists($dst_dir) || !is_dir($dst_dir)) {
            $this->error = 'Error: Cannot unpack file "'
                .$tar_filename.'" because destination directory "'
                .$dst_dir.'" does not exists.';
            $error = new AError ($this->error);
            $error->toLog()->toDebug();
            return false;
        }
        if (!is_writable($dst_dir)) {
            $this->error = 'Error: Cannot unpack file "'
                .$tar_filename.'" because destination directory "'
                .$dst_dir.'" have no write permission.';
            $error = new AError ($this->error);
            $error->toLog()->toDebug();
            return false;
        }
        $exit_code = 0;
        if (class_exists('PharData')) {
            //remove destination folder first
            //run pathinfo twice for tar.gz. files
            $this->removeDir($dst_dir.pathinfo(pathinfo($tar_filename, PATHINFO_FILENAME), PATHINFO_FILENAME));
            try {
                if (!ini_get('sys_temp_dir')) {
                    ini_set('sys_temp_dir', sys_get_temp_dir());
                }
                $phar = new PharData($tar_filename);
                $phar->extractTo($dst_dir, null, true);
            } catch (Exception $e) {
                $error = new AError($e->getMessage());
                $error->toLog()->toDebug();
                $this->error = 'Error: Cannot unpack file "'.$tar_filename.'". Please, see error log for details. ';
                return false;
            }
        } else {
            $exit_code = 1;
        }

        if ($exit_code) {
            $this->load->library('targz');
            $targz = new Atargz();
            $targz->extractTar($tar_filename, $dst_dir);
        }

        $this->chmod_R($dst_dir.$this->session->data['package_info']['tmp_dir'], 0777, 0777);
        return true;
    }

    /**
     * Function make backup and move it into admin/system/backup/directory
     *
     * @param string $extension_id
     *
     * @return bool
     * @throws AException
     */
    public function backupPrevious($extension_id = '')
    {
        $old_path = !$extension_id ? DIR_ROOT.self::DS.$this->session->data['package_info']['dst_dir'] : DIR_EXT;
        $package_id = !$extension_id ? $this->session->data['package_info']['package_id'] : $extension_id;
        if (!$package_id) {
            return false;
        }
        if (file_exists($old_path.$package_id)) {
            $backup = new ABackup($extension_id.'_'.date('Y-m-d-H-i-s'));
            if ($backup->error) {
                $this->error = implode("\n", $backup->error);
                return false;
            }
            $backup_dirname = $backup->getBackupName();
            if ($backup_dirname) {
                if (!$backup->backupDirectory($old_path.$package_id, true)) {
                    $this->error = implode("\n", $backup->error);
                    return false;
                }

                if (!$backup->dumpDatabase()) {
                    return false;
                }
                if (!$backup->archive(DIR_BACKUP.$backup_dirname.'.tar.gz', DIR_BACKUP, $backup_dirname)) {
                    return false;
                }
            } else {
                return false;
            }

            $info = $this->extensions->getExtensionInfo($package_id);

            $install_upgrade_history = new ADataset('install_upgrade_history', 'admin');
            $install_upgrade_history->addRows(
                [
                    'date_added'  => date("Y-m-d H:i:s", time()),
                    'name'        => $package_id,
                    'version'     => $info['version'],
                    'backup_file' => $backup_dirname.'.tar.gz',
                    'backup_date' => date("Y-m-d H:i:s", time()),
                    'type'        => 'backup',
                    'user'        => $this->user->getUsername(),
                ]
            );

            //delete previous version
            $this->removeDir($old_path.$package_id);
        }
        return true;
    }

    public function replaceCoreFiles()
    {
        $coreFiles = $this->session->data['package_info']['package_content']['core'];
        if ($this->session->data['package_info']['ftp']) {
            $ftp_user = $this->session->data['package_info']['ftp_user'];
            $ftp_password = $this->session->data['package_info']['ftp_password'];
            $ftp_port = $this->session->data['package_info']['ftp_port'];
            $ftp_host = $this->session->data['package_info']['ftp_host'];

            $fconnect = ftp_connect($ftp_host, $ftp_port);
            ftp_login($fconnect, $ftp_user, $ftp_password);
            ftp_pasv($fconnect, true);

            foreach ($coreFiles as $core_filename) {
                $remote_file = pathinfo(
                    $this->session->data['package_info']['ftp_path'].$core_filename,
                    PATHINFO_BASENAME
                );

                $remote_dir = pathinfo(
                        $this->session->data['package_info']['ftp_path'].$core_filename,
                        PATHINFO_DIRNAME
                    ).self::DS;

                $src_dir = $this->session->data['package_info']['tmp_dir']
                    .$this->session->data['package_info']['package_dir']
                    .self::DS.'code'
                    .self::DS.$core_filename;

                $result = $this->ftp_move($fconnect, $src_dir, $remote_file, $remote_dir);
                if ($result) {
                    $install_upgrade_history = new ADataset('install_upgrade_history', 'admin');
                    $install_upgrade_history->addRows(
                        [
                            'date_added'  => date("Y-m-d H:i:s", time()),
                            'name'        => 'Upgrade core file: '.$remote_file,
                            'version'     => $this->session->data['package_info']['package_version'],
                            'backup_file' => '',
                            'backup_date' => '',
                            'type'        => 'upgrade',
                            'user'        => $this->user->getUsername(),
                        ]
                    );
                } else {
                    $this->error .= " Error: Cannot upgrade file : '".$core_filename."\n";
                    $error = new AError ($this->error);
                    $error->toLog()->toDebug();
                }
            }// end of loop
            ftp_close($fconnect);
        } else {
            foreach ($coreFiles as $core_filename) {
                if (is_file(DIR_ROOT.self::DS.$core_filename)) {
                    unlink(DIR_ROOT.self::DS.$core_filename);
                }
                //check is target directory exists before copying
                $dir = pathinfo(DIR_ROOT.self::DS.$core_filename, PATHINFO_DIRNAME);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }

                if (!is_dir($dir) || !is_writable($dir)) {
                    $this->error .= " Error: Cannot upgrade file : '"
                        .$core_filename."\n Destination folder "
                        .$dir." is not writable or does not exists";
                    $this->messages->saveNotice('Error', $this->error);
                    $error = new AError ($this->error);
                    $error->toLog()->toDebug();
                    continue;
                }

                $result = rename(
                    $this->session->data['package_info']['tmp_dir']
                    .$this->session->data['package_info']['package_dir']
                    .self::DS.'code'
                    .self::DS.$core_filename,
                    DIR_ROOT.self::DS.$core_filename
                );
                if ($result) {
                    // for index.php do not set 777 permissions because hosting providers will ban it
                    if (pathinfo($core_filename, PATHINFO_BASENAME) == 'index.php') {
                        chmod(DIR_ROOT.self::DS.$core_filename, 0755);
                    } else {
                        chmod(DIR_ROOT.self::DS.$core_filename, 0777);
                    }

                    $install_upgrade_history = new ADataset('install_upgrade_history', 'admin');
                    $install_upgrade_history->addRows(
                        [
                            'date_added'  => date("Y-m-d H:i:s", time()),
                            'name'        => 'Upgrade core file: '.$core_filename,
                            'version'     => $this->session->data['package_info']['package_version'],
                            'backup_file' => '',
                            'backup_date' => '',
                            'type'        => 'upgrade',
                            'user'        => $this->user->getUsername(),
                        ]
                    );
                } else {
                    $this->error .= " Error: Cannot upgrade file : '".$core_filename."\n";
                    $error = new AError ($this->error);
                    $error->toLog()->toDebug();
                }
            }
        }
    }

    /**
     * method removes non-empty directory (use it carefully)
     *
     * @param string $dir
     *
     * @return boolean
     * @throws AException
     */
    public function removeDir($dir = '')
    {
        if (!$this->session->data['package_info']['ftp']) { // if not ftp
            if (is_dir($dir)) {
                $objects = scandir($dir);
                foreach ($objects as $obj) {
                    if ($obj != "." && $obj != "..") {
                        chmod($dir.self::DS.$obj, 0777);
                        $err = is_dir($dir.self::DS.$obj) ? $this->removeDir($dir.self::DS.$obj) : unlink($dir.self::DS.$obj);
                        if (!$err) {
                            $this->error = "Error: Cannot delete file or directory: '".$dir.self::DS.$obj."'.";
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
            $dir = $this->session->data['package_info']['ftp_path']
                .$this->session->data['package_info']['dst_dir']
                .$dir;

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
     *
     * @return array|false
     */
    public function getDestinationDirectories()
    {
        $package_dirname = $this->session->data['package_info']['package_dir'];
        $output = [];
        if (!file_exists($this->session->data['package_info']['tmp_dir'].$package_dirname.self::DS."code")) {
            return false;
        } else {
            $dir = $this->session->data['package_info']['tmp_dir'].$package_dirname.self::DS."code";
            $d = [];
            while ($dirs = glob($dir.self::DS.'*', GLOB_ONLYDIR)) {
                $dir .= self::DS.'*';
                if (!$d) {
                    $d = $dirs;
                } else {
                    $d = array_merge($d, $dirs);
                }
            }
        }

        if ($d) {
            foreach ($d as $dir) {
                $dir = str_replace(
                    $this->session->data['package_info']['tmp_dir'].$package_dirname.self::DS."code".self::DS,
                    "",
                    $dir
                );
                $output[] = $dir;
            }
        }
        return $output;
    }

    /**
     * @param string $ftp_user
     * @param string $ftp_password
     * @param string $ftp_host
     * @param string $ftp_path
     * @param int $ftp_port
     *
     * @return bool
     * @throws AException
     */
    public function checkFTP($ftp_user, $ftp_password = '', $ftp_host = '', $ftp_path = '', $ftp_port = 21)
    {
        $this->load->language('tool/package_installer');
        if (!$ftp_host) {
            $ftp_host = 'localhost';
        } else {
            // looking for port number in the host
            $ftp_host = explode(':', $ftp_host);
            $ftp_port = (int) $ftp_host[1];
            $ftp_host = $ftp_host[0];
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
        if (!$fconnect && $ftp_host == 'localhost') {
            //check dns perversion :-)
            $ftp_host = '127.0.0.1';
            $fconnect = ftp_connect($ftp_host, $ftp_port);
        }

        if ($fconnect) {
            $login = ftp_login($fconnect, $ftp_user, $ftp_password);
            if (!$login) {
                $this->error = $this->language->get('error_ftp_login').$ftp_host.':'.$ftp_port;
                return false;
            }

            $ftp_path = !$ftp_path ? $this->ftpFindAppRoot($fconnect) : $ftp_path;
            // if all fine  - write ftp parameters into session
            $this->session->data['package_info']['ftp'] = true;
            $this->session->data['package_info']['ftp_user'] = $ftp_user;
            $this->session->data['package_info']['ftp_password'] = $ftp_password;
            $this->session->data['package_info']['ftp_host'] = $ftp_host;
            $this->session->data['package_info']['ftp_port'] = $ftp_port;
            $this->session->data['package_info']['ftp_path'] = $ftp_path;

            ftp_close($fconnect);
        } else {
            $this->error = $this->language->get('error_ftp_connect');
            return false;
        }

        return true;
    }

    /**
     * Try to guess an installation location in the server via FTP
     *
     * @param resource $fconnect
     *
     * @return string|bool
     */
    private function ftpFindAppRoot($fconnect)
    {
        if (!$fconnect) {
            return false;
        }

        // Turn passive mode on
        if (@ftp_pasv($fconnect, true) === false) {
            return false;
        }

        $abs_path = pathinfo($_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF'], PATHINFO_DIRNAME);

        $ftp_dir_list = [];

        // first fo all try to change directory to absolute server path
        //(for case when ftp-user does not locked in ftp root directory)
        if (@ftp_chdir($fconnect, $abs_path) === true) {
            return $abs_path.self::DS;
        } else {
            //for ftp chrooted users
            //get list of directories
            if ($files = @ftp_nlist($fconnect, '.')) {
                //get only directories
                foreach ($files as $file) {
                    if (ftp_size($fconnect, $file) == "-1") {
                        $ftp_dir_list[] = $file;
                    }
                }
                //find ftp-directory name inside absolute server path
                $target_dir = null;
                if ($ftp_dir_list) {
                    foreach ($ftp_dir_list as $dir) {
                        if (is_int($pos = strpos($abs_path, $dir))) {
                            $target_dir = substr($abs_path, $pos);
                            break;
                        }
                    }
                    if ($target_dir) {
                        return self::DS.trim($target_dir, self::DS).self::DS;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Function for moving directory or file via ftp-connection
     *
     * @param        $fconnect
     * @param string $local local path to file or directory
     * @param string $remote_file remote file  or directory name
     * @param string $remote_dir
     *
     * @return bool
     * @throws AException
     */
    public function ftp_move($fconnect, $local, $remote_file, $remote_dir)
    {
        $local = (string) $local;
        $remote_file = (string) $remote_file;
        $remote_dir = (string) $remote_dir;

        if (!$this->session->data['package_info']['ftp']) {
            return false;
        }

        // if destination folder does not exists - try to create
        if (@ftp_chdir($fconnect, $remote_dir) === false) {
            $basedir = $this->session->data['package_info']['ftp_path'];
            //relative subdirs
            $sub_dirs = str_replace($basedir, '', $remote_dir);
            if (substr($sub_dirs, 0, 1) == self::DS) {
                $sub_dirs = substr($sub_dirs, 1);
            }
            $result = $this->ftpMakeSubDirs($fconnect, $basedir, $sub_dirs);
            if (!$result) {
                $this->error .= "\nCannot create directory ".$remote_dir." via ftp. ";
                return false;
            }
            if (!ftp_chmod($fconnect, 0755, $remote_dir)) {
                $error = new AError('Cannot change mode for directory '.$remote_dir);
                $error->toLog()->toDebug();
            }
            //change current directory to newly created
            @ftp_chdir($fconnect, $remote_dir);
        }

        if (is_dir($local)) {
            $this->ftp_put_dir($fconnect, $local, $remote_dir);
        } else {
            if (!ftp_put($fconnect, $remote_file, $local, FTP_BINARY)) {
                $this->error .= "\nCannot put file ".$remote_file." via ftp.";
                return false;
            }
            $remote_file = $remote_dir.pathinfo($local, PATHINFO_BASENAME);
            $chmod_result = ftp_chmod($fconnect, 0755, $remote_file);
            if (!$chmod_result) {
                $error = new AError('Cannot change mode for file '.$remote_file);
                $error->toLog()->toDebug();
            }
        }
        return true;
    }

    /**
     * @param $fconnect
     * @param $ftpBaseDir
     * @param $ftpPath
     *
     * @return bool
     */
    private function ftpMakeSubDirs($fconnect, $ftpBaseDir, $ftpPath)
    {
        @ftp_chdir($fconnect, $ftpBaseDir); // /var/www/uploads
        $parts = explode(self::DS, $ftpPath); // 2013/06/11/username
        foreach ($parts as $part) {
            if (!@ftp_chdir($fconnect, $part)) {
                ftp_mkdir($fconnect, $part);
                $result = ftp_chdir($fconnect, $part);
                if (!$result) {
                    return false;
                }
                ftp_chmod($fconnect, 0755, $part);
            }
        }
        return true;
    }

    /**
     * method for moving directory via ftp connection
     *
     * @param resource $conn_id
     * @param string $src_dir
     * @param string $dst_dir
     */
    private function ftp_put_dir($conn_id, $src_dir, $dst_dir)
    {
        $d = dir($src_dir);
        // do this for each file in the directory
        while ($file = $d->read()) {
            // Stay only with in current directory
            if ($file != "." && $file != "..") {
                // do the following if it is a directory
                if (is_dir($src_dir.self::DS.$file)) {
                    if (!@ftp_chdir($conn_id, $dst_dir.self::DS.$file)) {
                        // create directories that do not yet exist
                        ftp_mkdir($conn_id, $dst_dir.self::DS.$file);
                        ftp_chmod($conn_id, 0755, $dst_dir.self::DS.$file);
                    }
                    // recursive part
                    $this->ftp_put_dir($conn_id, $src_dir.self::DS.$file, $dst_dir.self::DS.$file);
                } else {
                    // put the files
                    ftp_put($conn_id, $dst_dir.self::DS.$file, $src_dir.self::DS.$file, FTP_BINARY);
                    ftp_chmod($conn_id, 0755, $dst_dir.self::DS.$file);
                }
            }
        }
        $d->close();
    }

    /**
     * @param resource $conn
     * @param string $dir
     *
     * @return void
     */
    private function delete_ftp_dir($conn, $dir)
    {
        $files = ftp_nlist($conn, $dir);
        if (!$files) {
            ftp_rmdir($conn, $dir);
            return;
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
    }

    /**
     * @param string $extension_id
     * @param string $type
     * @param string $version
     * @param string $install_mode
     *
     * @return bool
     * @throws AException
     */
    public function installExtension($extension_id = '', $type = '', $version = '', $install_mode = 'install')
    {
        $type = $type ? : $this->session->data['package_info']['package_type'];
        $version = $version ? : $this->session->data['package_info']['package_version'];
        $extension_id = $extension_id ? : $this->session->data['package_info']['package_id'];
        $package_dirname = $this->session->data['package_info']['package_dir'];

        switch ($type) {
            case 'extension':
            case 'extensions':
            case 'template':
            case 'payment':
            case 'shipping':
            case 'language':
            case 'tax':
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

                    $result = $this->extension_manager->install(
                        $extension_id,
                        getExtensionConfigXml($extension_id)
                    );

                    if ($result === false) {
                        return false;
                    }
                } elseif ($install_mode == 'upgrade') {
                    $install_upgrade_history = new ADataset('install_upgrade_history', 'admin');
                    $install_upgrade_history->addRows(
                        [
                            'date_added'  => date("Y-m-d H:i:s", time()),
                            'name'        => $extension_id,
                            'version'     => $version,
                            'backup_file' => '',
                            'backup_date' => '',
                            'type'        => 'upgrade',
                            'user'        => $this->user->getUsername(),
                        ]
                    );

                    $config = null;
                    $ext_conf_filename = $this->session->data['package_info']['tmp_dir']
                        .$package_dirname.self::DS
                        .'code'.self::DS
                        .'extensions'.self::DS
                        .$extension_id.self::DS.'config.xml';
                    if (is_file($ext_conf_filename)) {
                        $config = simplexml_load_file($ext_conf_filename);
                    }
                    $config = !$config ? getExtensionConfigXml($extension_id) : $config;
                    // running sql upgrade script if it exists
                    if (isset($config->upgrade->sql)) {
                        $file = $this->session->data['package_info']['tmp_dir']
                            .$package_dirname.self::DS
                            .'code'.self::DS
                            .'extensions'.self::DS
                            .$extension_id.self::DS
                            .$config->upgrade->sql;
                        $file = !file_exists($file)
                            ? DIR_EXT.$extension_id.self::DS.$config->upgrade->sql
                            : $file;
                        if (file_exists($file)) {
                            if (!$this->db->performSql($file)) {
                                $this->error = 'SQL-ERROR: "'.$this->db->error.'"';
                                $err = new AError($this->error);
                                $err->toLog()->toDebug();
                                return false;
                            }
                        }
                    }
                    // running php install script if it exists
                    if (isset($config->upgrade->trigger)) {
                        $file = $this->session->data['package_info']['tmp_dir']
                            .$package_dirname.self::DS
                            .'code'.self::DS
                            .'extensions'.self::DS
                            .$extension_id.self::DS
                            .$config->upgrade->trigger;
                        $file = !file_exists($file) ? DIR_EXT.$extension_id.self::DS.$config->upgrade->trigger : $file;
                        if (file_exists($file)) {
                            /** @noinspection PhpIncludeInspection */
                            include($file);
                        }
                    }

                    $this->extension_manager->editSetting(
                        $extension_id, [
                                         'license_key' => $this->session->data['package_info']['extension_key'],
                                         'version'     => $version,
                                     ]
                    );
                }
                break;
            default:
                $this->error = 'Unknown extension type: "'.$type.'"';
                $err = new AError($this->error);
                $err->toLog()->toDebug();
                return false;
        }
        return true;
    }

    /**
     * @param SimpleXmlElement $config
     *
     * @throws AException
     */
    public function upgradeCore($config)
    {
        //clear all cache
        $this->cache->remove('*');

        $package_dirname = $this->session->data['package_info']['package_dir'];
        $package_tmpdir = $this->session->data['package_info']['tmp_dir'];
        // running sql upgrade script if it exists
        if (isset($config->upgrade->sql)) {
            $file = $package_tmpdir.$package_dirname.self::DS.$config->upgrade->sql;
            if (is_file($file)) {
                if (!$this->db->performSql($file)) {
                    $this->error = 'SQL-ERROR: "'.$this->db->error.'"';
                    throw new AException(0, $this->error);
                }
            }
        }
        // running php upgrade script if it exists
        if (isset($config->upgrade->trigger)) {
            $file = $package_tmpdir.$package_dirname.self::DS.$config->upgrade->trigger;
            if (is_file($file)) {
                /** @noinspection PhpIncludeInspection */
                include($file);
            }
        }

        // write to history
        $install_upgrade_history = new ADataset('install_upgrade_history', 'admin');
        $install_upgrade_history->addRows(
            [
                'date_added'  => date("Y-m-d H:i:s", time()),
                'name'        => 'Core upgrade',
                'version'     => $this->session->data['package_info']['package_version'],
                'backup_file' => '',
                'backup_date' => '',
                'type'        => 'upgrade',
                'user'        => $this->user->getUsername(),
            ]
        );
    }

    /**
     * @param string $new_version
     *
     * @return bool
     * @throws AException
     */
    public function updateCoreVersion($new_version)
    {
        if (!$new_version) {
            return false;
        }

        $new_version = preg_replace('/[^0-9\.]/', '', $new_version);
        list($master, $minor, $built) = explode(".", $new_version);
        $content = "<?php\n";
        $content .= "define('MASTER_VERSION', '".$master."');\n";
        $content .= "define('MINOR_VERSION', '".$minor."');\n";
        $content .= "define('VERSION_BUILT', '".$built."');\n";

        if (!$this->session->data['package_info']['ftp']) {
            file_put_contents(DIR_CORE.'version.php', $content);
        } else {
            file_put_contents($this->session->data['package_info']['tmp_dir'].'version.php', $content);
            $ftp_user = $this->session->data['package_info']['ftp_user'];
            $ftp_password = $this->session->data['package_info']['ftp_password'];
            $ftp_port = $this->session->data['package_info']['ftp_port'];
            $ftp_host = $this->session->data['package_info']['ftp_host'];

            $fconnect = ftp_connect($ftp_host, $ftp_port);
            ftp_login($fconnect, $ftp_user, $ftp_password);
            ftp_pasv($fconnect, true);

            $this->ftp_move(
                $fconnect,
                $this->session->data['package_info']['tmp_dir'].'version.php',
                'version.php',
                $this->session->data['package_info']['ftp_path'].'core'.self::DS
            );
            ftp_close($fconnect);
        }
        return true;
    }

    /**
     * Method change access mode recursively
     *
     * @param string $path path to directory or file
     * @param string $filemode
     * @param string $dirmode
     */
    public function chmod_R($path, $filemode, $dirmode)
    {
        $path = (string) $path;
        if (is_dir($path)) {
            if (!chmod($path, $dirmode)) {
                $dirmode_str = decoct($dirmode);
                $error_text = "Notice: Failed applying filemode '".$dirmode_str."' on directory '".$path."'.\n";
                $error_text .= "  `-> the directory '".$path."' will be skipped from recursive chmod.\n";
                $this->log->write($error_text);
                return null;
            }
            $dh = opendir($path);
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..') { // skip self and parent pointing directories
                    $fullPath = $path.self::DS.$file;
                    $this->chmod_R($fullPath, $filemode, $dirmode);
                }
            }
            closedir($dh);
        } else {
            //skip if does not exists
            if (!file_exists($path)) {
                return null;
            }

            if (is_link($path)) {
                $this->log->write('Notice: Recursive chmod. Symlink '.$path.' is skipped.');
                return null;
            }
            // for index.php do not set 777 permissions because hosting providers will ban it
            if (pathinfo($path, PATHINFO_BASENAME) == 'index.php' && $filemode == 777) {
                $filemode = 644;
            }
            if (!chmod($path, $filemode)) {
                $filemode_str = decoct($filemode);
                $this->log->write("Notice: Failed applying filemode ".$filemode_str." on file ".$path."\n");
                return null;
            }
        }
    }

    /**
     * Method of checks before installation process
     */
    public function validate()
    {
        $this->error = '';
        //1.check is extension directory writable
        if (!is_writable(DIR_EXT)) {
            $this->error .= 'Directory '.DIR_EXT.' is not writable. Please change permissions for it.'."\n";
        }
        //2. check temporary directory. just call method
        $this->getTempDir();

        //3. run validation for backup-process before install
//        $bkp = new ABackup('', false);
//        if (!$bkp->validate()) {
//            $this->error .= implode("\n", $bkp->error);
//        }

        $this->extensions->hk_ValidateData($this);

        return !$this->error;
    }

    /**
     * Method returns absolute path to temporary directory for unpacking package
     * if system/temp is inaccessible - use php temp directory
     *
     * @return string
     */
    public function getTempDir()
    {
        $tmp_dir = DIR_APP_SECTION.'system/temp';
        $tmp_install_dir = $tmp_dir.'/install';
        //try to create tmp dir if not yet created and install.
        if (make_writable_dir($tmp_dir) && make_writable_dir($tmp_install_dir)) {
            $dir = $tmp_install_dir.self::DS;
        } else {
            if (!is_dir(sys_get_temp_dir().'/abantecart_install')) {
                mkdir(sys_get_temp_dir().'/abantecart_install/', 0777);
            }
            $dir = sys_get_temp_dir().'/abantecart_install/';

            if (!is_writable($dir)) {
                $error_text = 'Error: php tried to use directory '.DIR_APP_SECTION."system/temp/install"
                    .' but it is non-writable. Temporary php-directory '.$dir
                    .' is non-writable too! Please change permissions one of them.'."\n";
                $this->error .= $error_text;
                $this->log->write($error_text);
            }
        }
        return $dir;
    }
}