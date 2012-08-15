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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

if (defined('IS_DEMO') && IS_DEMO) {
    header('Location: static_pages/demo_mode.php');
}

class ControllerPagesToolPackageInstaller extends AController {
    private $data;

    public function main() {

        $extension_key = !$this->request->get['extension_key'] ? '' : $this->request->get['extension_key'];
        $extension_key = !$this->request->post['extension_key'] ? $extension_key : $this->request->post['extension_key'];
        $extension_key = $this->session->data['package_info']['extension_key'] ? $this->session->data['package_info']['extension_key'] : $extension_key;

        if (!$extension_key) {
            unset($this->session->data['package_info']);
        }
        $this->document->initBreadcrumb(array(
            'href' => $this->html->getSecureURL('index/home'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE));
        $this->document->addBreadcrumb(array(
            'href' => $this->html->getSecureURL('tool/package_installer'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '));


        $form = new AForm('ST');
        $form->setForm(
            array('form_name' => 'installFrm')
        );
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
            'type' => 'form',
            'name' => 'installFrm',
            'action' => $this->html->getSecureURL('tool/package_installer/download')));

        $this->data['form']['input'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'extension_key',
            'value' => (!$extension_key ? $this->language->get('text_key_hint') : $extension_key),
            'attr' => 'autocomplete="off" onfocus = "if(this.value==\'' . $this->language->get('text_key_hint') . '\'){
													                                             this.value = \'\';}"',
            'help_url' => $this->gen_help_url('extension_key'),));


        $this->data['form']['submit'] = $form->getFieldHtml(array('type' => 'button',
            'name' => 'submit',
            'text' => $this->language->get('text_continue'),
            'style' => 'button1'));
        if (isset($this->session->data['error'])) {
            $this->view->assign('error', $this->session->data['error']);
            unset($this->session->data['package_info']['package_dir']);
        }
        unset($this->session->data['error']);
        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->view->assign('help_url', $this->gen_help_url(''));
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/tool/package_installer.tpl');
    }

    public function download() {

        $extension_key = ($this->request->post['extension_key']) ? $this->request->post['extension_key'] : $this->request->get['extension_key'];
        $disclaimer = ($this->request->post['disclaimer']) ? $this->request->post['disclaimer'] : $this->request->get['disclaimer'];

        if (substr($extension_key, 0, 11) == 'abantecart_') {
            $disclaimer = true;
        }

        if (!$extension_key) {
            $this->redirect($this->html->getSecureURL('tool/package_installer'));
        }

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post['disagree'] == 1) { // if does not agree  with agreement of filesize
            $this->_removeTempFiles();
            unset($this->session->data['package_info']);
            $this->redirect($this->html->getSecureURL('extension/extensions/extensions'));
        }
        if (!$disclaimer) {
            $this->view->assign('heading_title', $this->language->get('text_disclaimer_heading'));
            $this->view->assign('text_disclaimer', $this->language->get('text_disclaimer'));

            $form = new AForm('ST');
            $form->setForm(array('form_name' => 'disclaimerFrm'));
            $this->data['form']['form_open'] = $form->getFieldHtml(array('type' => 'form',
                'name' => 'disclaimerFrm',
                'action' => $this->html->getSecureURL('tool/package_installer/download')));

            $this->data['form']['extension_key'] = $form->getFieldHtml(array(
                'id' => 'extension_key',
                'type' => 'hidden',
                'name' => 'extension_key',
                'value' => $extension_key));

            $this->data['form']['disagree_button'] = $form->getFieldHtml(array('type' => 'button',
                'text' => $this->language->get('text_disagree'),
                'style' => 'button'));

            $this->data['form']['submit'] = $form->getFieldHtml(array('type' => 'button',
                'text' => $this->language->get('text_agree'),
                'style' => 'button1'));
            $this->view->batchAssign($this->data);
            $this->processTemplate('pages/tool/package_installer_disclaimer.tpl');
            return;
        }


        $form = new AForm('ST');
        $form->setForm(array('form_name' => 'retryFrm'));
        $this->data['form']['form_open'] = $form->getFieldHtml(array('type' => 'form',
            'name' => 'retryFrm',
            'action' => $this->html->getSecureURL('tool/package_installer/download')));
        $this->data['form']['hidden'][] = $form->getFieldHtml(array('id' => 'extension_key',
            'type' => 'hidden',
            'name' => 'extension_key',
            'value' => $extension_key));
        $this->data['form']['hidden'][] = $form->getFieldHtml(array('id' => 'disclaimer',
            'type' => 'hidden',
            'name' => 'disclaimer',
            'value' => '1'));

        $this->data['form']['cancel'] = $form->getFieldHtml(array('type' => 'button',
            'text' => $this->language->get('button_cancel'),
            'style' => 'button'));

        $this->data['form']['retry'] = $form->getFieldHtml(array('type' => 'button',
            'text' => $this->language->get('text_retry'),
            'style' => 'button1'));

        $this->view->assign('text_download_error', $this->language->get('text_download_error'));

        $this->session->data['package_info']['extension_key'] = $extension_key;
        if (is_writable(DIR_APP_SECTION . "system/temp/")) {
            $this->session->data['package_info']['tmp_dir'] = DIR_APP_SECTION . "system/temp/";
        } else if (is_writable(DIR_DOWNLOAD . "temp/")) {
            $this->session->data['package_info']['tmp_dir'] = DIR_DOWNLOAD . "temp/";
        } else {
            $this->session->data['package_info']['tmp_dir'] = sys_get_temp_dir() . '/';
        }

        if (!is_writable($this->session->data['package_info']['tmp_dir'])) {
            $this->session->data['error'] = $this->language->get('error_dir_permission') . ' ' . DIR_APP_SECTION . "system/temp/";
            unset($this->session->data['package_info']);
            $this->redirect($this->html->getSecureURL('tool/package_installer'));
        }

        $url = "/?option=com_abantecartrepository&format=raw";
        $url .= "&store_id=" . UNIQUE_ID;
        $url .= "&store_ip=" . $_SERVER ['SERVER_ADDR'];
        $url .= "&store_url=" . HTTP_SERVER;
        $url .= "&store_version=" . VERSION;
        $url .= "&extension_key=" . $extension_key;

        $pmanager = new APackageManager();
        $headers = $pmanager->getRemoteFileHeaders($url);

        if (!$headers) {
            $this->session->data['error'] = $pmanager->error;
            $this->redirect($this->html->getSecureURL('tool/package_installer'));
        }

        if ($headers['Content-Type'] == 'application/json') {
            $error = $pmanager->getRemoteFile($url, false);
            $this->session->data['error'] = $error['error'];
            $this->redirect($this->html->getSecureURL('tool/package_installer'));
        } else {
            $package_name = str_replace("attachment; filename=", "", $headers['Content-Disposition']);
            $package_name = str_replace(array('"', ';'), '', $package_name);

            if (!$package_name) {
                $this->session->data['error'] = $this->language->get('error_repository');
                $this->redirect($this->html->getSecureURL('tool/package_installer'));
            }
        }

        $this->session->data['package_info']['package_url'] = $url;
        $this->session->data['package_info']['package_name'] = $package_name;
        $this->session->data['package_info']['package_size'] = $headers['Content-Length'];

        // if file already downloaded - check size.
        if (file_exists($this->session->data['package_info']['tmp_dir'] . $package_name)) {
            $filesize = filesize($this->session->data['package_info']['tmp_dir'] . $package_name);
            if ($filesize != $this->session->data['package_info']['package_size']) {
                @unlink($this->session->data['package_info']['tmp_dir'] . $package_name);
            } else {
                if ($this->request->get['agree'] == '1') {
                    $this->redirect($this->html->getSecureURL('tool/package_installer/agreement'));
                } else {
                    $already_downloaded = true;
                    $this->redirect($this->html->getSecureURL('tool/package_installer/agreement'));
                }
            }
        }


        $this->data['url'] = $this->html->getSecureURL('tool/package_download');
        $this->data['redirect'] = $this->html->getSecureURL('tool/package_installer/agreement');

        $this->document->initBreadcrumb(array(
            'href' => $this->html->getSecureURL('index/home'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE));
        $this->document->addBreadcrumb(array(
            'href' => $this->html->getSecureURL('tool/package_installer'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '));
        $this->data['heading_title'] = $this->language->get('heading_title_download');


        // if we got package file name
        // #1 check existing extension based on package file name
        $package_version = strrchr($package_name, '_');
        $package_version = str_replace(array('.tar.gz', '_'), '', $package_version);
        $package_id = str_replace(array('.tar.gz', '_' . $package_version, '_install', '_upgrade'), '', $package_name);

        $this->data['pack_info'] = '';
        $existing_ext = $this->extensions->getExtensionsList(array('search' => $package_id));
        if ($existing_ext->row) { // if that extension is already exist
            foreach ($existing_ext->rows as $ext) {
                if ($ext['key'] != $package_id) {
                    continue;
                }
                if (version_compare($ext['version'], $package_version, "<")) {
                    $this->data['pack_info'] = str_replace('%EXTENSION%', $package_id, $this->language->get('warning_already_installed'));
                    //$confirm_upgrade = true;
                    $this->session->data['package_info']['install_mode'] = 'upgrade';
                    break;
                } else {
                    $this->session->data['error'] = str_replace('%EXTENSION%', $package_id, $this->language->get('warning_already_installed_uninstall'));
                    $this->redirect($this->html->getSecureURL('tool/package_installer'));
                }
            }
        }
        $this->data['loading'] = sprintf($this->language->get('text_loading'), (round($this->session->data['package_info']['package_size'] / 1024, 1)) . 'kb');

        $this->session->data['package_info']['install_mode'] = !$this->session->data['package_info']['install_mode']
            ? 'install' : $this->session->data['package_info']['install_mode'];

        if (!$already_downloaded) {
            $this->data['pack_info'] .= str_replace('%file%', $package_name . ' (' . (round($this->session->data['package_info']['package_size'] / 1024, 1)) . 'kb)', $this->language->get('text_preloading'));
        }


        $this->view->batchAssign($this->data);

        $this->processTemplate('pages/tool/package_installer_download.tpl');
    }

    public function agreement() {

        // if we got decision
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post['disagree'] == 1) { // if does not agree  with agreement of filesize
            $this->_removeTempFiles();
            unset($this->session->data['package_info']);
            $this->redirect($this->html->getSecureURL('extension/extensions/extensions'));
        } elseif ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post['agree']) { // if agree
            $this->redirect($this->html->getSecureURL('tool/package_installer/install'));
        } elseif ($this->request->server['REQUEST_METHOD'] == 'POST' && !$this->request->post['agree'] && !isset($this->request->post['ftp_user'])) {
            $this->_removeTempFiles('dir');
            $this->redirect($this->html->getSecureURL('tool/package_installer'));
        }

        // if file already downloaded - check size.
        $package_name = $this->session->data['package_info']['package_name'];
        if (file_exists($this->session->data['package_info']['tmp_dir'] . $package_name)) {
            $pmanager = new APackageManager();
            $headers = $pmanager->getRemoteFileHeaders($this->session->data['package_info']['package_url']);

            if ($headers['Content-Length'] != $this->session->data['package_info']['package_size']) {
                @unlink($this->session->data['package_info']['tmp_dir'] . $package_name);
                $this->_removeTempFiles();
                $extension_key = $this->session->data['package_info']['extension_key'];
                unset($this->session->data['package_info']);
                $this->redirect($this->html->getSecureURL('tool/package_installer/download', '&disclaimer=1&extension_key=' . $extension_key));
            }
        }


        $this->loadLanguage('tool/package_installer');
        $package_name = $this->session->data['package_info']['package_name'];
        if (!$package_name) { // if direct link - redirect to the begining
            $this->redirect('tool/package_installer');
        }
        $package_dirname = str_replace(".tar.gz", "", $package_name);
        $this->session->data['package_info']['package_dir'] = $package_dirname;

        $pmanager = new APackageManager();
        //unpack package
        if (file_exists($this->session->data['package_info']['tmp_dir'] . $package_name)) {
            //remove the same directory before unpack
            if (file_exists($this->session->data['package_info']['tmp_dir'] . $package_dirname)) {
                $this->_removeTempFiles('dir');
            }
            // if package not unpack - redirect to the begin and show error message
            if (!$pmanager->unpack($this->session->data['package_info']['tmp_dir'] . $package_name, $this->session->data['package_info']['tmp_dir'])) {
                $this->session->data['error'] = str_replace('%PACKAGE%', $package_name, $this->language->get('error_unpack'));
                $error = new AError ($pmanager->error);
                $error->toLog()->toDebug();
                $this->redirect($this->html->getSecureURL('tool/package_installer'));
            }
        }

        if (!file_exists($this->session->data['package_info']['tmp_dir'] . $package_dirname)) {
            $this->session->data['error'] = str_replace('%PACKAGE%', $this->session->data['package_info']['tmp_dir'] . $package_dirname, $this->language->get('error_pack_not_found'));
            $this->redirect($this->html->getSecureURL('tool/package_installer'));
        }

        // so.. we need to know about install mode of this package
        $config = simplexml_load_string(file_get_contents($this->session->data['package_info']['tmp_dir'] . $package_dirname . '/package.xml'));
        if (!$config) {
            $this->session->data['error'] = $this->language->get('error_package_config');
            $this->_removeTempFiles();
            $this->redirect($this->html->getSecureURL('tool/package_installer'));
        }

        $this->session->data['package_info']['package_id'] = (string)$config->id;
        $this->session->data['package_info']['package_type'] = (string)$config->type;
        $this->session->data['package_info']['package_priority'] = (string)$config->priority;
        $this->session->data['package_info']['package_version'] = (string)$config->version;
        $this->session->data['package_info']['package_content'] = '';
        if ((string)$config->package_content->extensions) {
            foreach ($config->package_content->extensions->extension as $item) {
                if ((string)$item) {
                    $this->session->data['package_info']['package_content']['extensions'][] = (string)$item;
                }
            }
            $this->session->data['package_info']['package_content']['total'] = sizeof($this->session->data['package_info']['package_content']['extensions']);
        }

        if ((string)$config->package_content->core) {
            foreach ($config->package_content->core->files->file as $item) {
                if ((string)$item) {
                    $this->session->data['package_info']['package_content']['core'][] = (string)$item;
                }
            }
        }


        if (!$this->session->data['package_info']['package_content']
            || ($this->session->data['package_info']['package_content']['core'] && $this->session->data['package_info']['package_content']['extensions'])
        ) {
            $this->session->data['error'] = $this->language->get('error_package_structure');
            $this->_removeTempFiles();
            $this->redirect($this->html->getSecureURL('tool/package_installer'));

        }

        //check cart version compability
        $coreversion = MASTER_VERSION . '.' . MINOR_VERSION;
        foreach ($config->cartversions->item as $item) {
            $version = (string)$item;
            $versions[] = $version;
            // compare $v to current version
            // if one of versions equal to current, all is ok
            // otherwise add error
            $result = version_compare($version, $coreversion, '==');
            if ($result) {
                break;
            }
        }
        if (!$result) {
            $this->session->data['error'] = str_replace('%CURRENT_VERSION%', $coreversion, $this->language->get('error_version_compability'));
            $this->session->data['error'] = str_replace('%VERSIONS%', implode(',', $versions), $this->session->data['error']);
            $this->_removeTempFiles();
            $this->redirect($this->html->getSecureURL('tool/package_installer'));
        }


        // if we were redirected
        if ($this->request->server['REQUEST_METHOD'] == 'GET') {
            //check  write permissions
            // find directory from app_root_dir
            if ($this->session->data['package_info']['package_content']['extensions']) {
                $dst_dirs = $pmanager->getDestinationDirectories();
                $ftp = false;
                // if even one destination directory is not writable - use ftp mode
                if ($dst_dirs) {
                    foreach ($dst_dirs as $dir) {
                        if (file_exists(DIR_ROOT . '/' . $dir)) {
                            if (!is_writable(DIR_ROOT . '/' . $dir)) {
                                $ftp = true; // enable ftp-mode
                                $non_writables[] = DIR_ROOT . '/' . $dir;
                            }
                        }
                    }
                }
            } else {
                foreach ($this->session->data['package_info']['package_content']['core'] as $corefile) {
                    if (file_exists(DIR_ROOT . '/' . $corefile)) {
                        if (!is_writable(DIR_ROOT . '/' . $corefile)) {
                            $ftp = true; // enable ftp-mode
                            $non_writables[] = DIR_ROOT . '/' . $corefile;
                        }
                    }
                }
            }
        }


        // if ftp mode and user give ftp parameters
        if (isset($this->request->post['ftp_user']) && $this->request->server['REQUEST_METHOD'] == 'POST') {
            $ftp_user = $this->request->post['ftp_user'];
            $ftp_password = $this->request->post['ftp_password'];
            $ftp_host = $this->request->post['ftp_host'];
            $ftp_path = $this->request->post['ftp_path'];

            //let's try to connect
            if (!$pmanager->checkFTP($ftp_user, $ftp_password, $ftp_host, $ftp_path)) {
                $this->session->data['error'] = $pmanager->error;
                $this->redirect($this->html->getSecureURL('tool/package_installer/agreement'));
            }
            $ftp = false; // for form fields hiding
            $this->redirect($this->html->getSecureURL('tool/package_installer/install'));
        } else {
            if (is_writable(DIR_DOWNLOAD . "temp/")) {
                $this->session->data['package_info']['tmp_dir'] = DIR_DOWNLOAD . "temp/";
            } else {
                $this->session->data['package_info']['tmp_dir'] = sys_get_temp_dir() . '/';
            }
        }
        // if all fine show license agreement
        if (!file_exists($this->session->data['package_info']['tmp_dir'] . $package_dirname . "/license.txt") && !$ftp) {
            $this->redirect($this->html->getSecureURL('tool/package_installer/install'));
        }

        $this->data['license_text'] = file_get_contents($this->session->data['package_info']['tmp_dir'] . $package_dirname . "/license.txt");
        $this->data['license_text'] = htmlentities($this->data['license_text'], ENT_QUOTES, 'UTF-8');
        $this->data['license_text'] = nl2br($this->data['license_text']);


        $this->document->initBreadcrumb(array(
            'href' => $this->html->getSecureURL('index/home'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE));
        $this->document->addBreadcrumb(array(
            'href' => $this->html->getSecureURL('tool/package_installer'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '));

        if (isset($this->session->data['error'])) {
            $this->view->assign('error', $this->session->data['error']);
            unset($this->session->data['error']);
        }

        $form = new AForm('ST');
        $form->setForm(array('form_name' => 'ftpFrm'));
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
            'type' => 'form',
            'name' => 'ftpFrm',
            'action' => $this->html->getSecureURL('tool/package_installer/agreement')));

        if ($ftp) {
            $ftp_user = $this->session->data['package_info']['ftp_user']
                ? $this->session->data['package_info']['ftp_user'] : '';
            $ftp_password = $this->session->data['package_info']['ftp_password']
                ? $this->session->data['package_info']['ftp_password'] : '';
            $ftp_host = $this->session->data['package_info']['ftp_host']
                ? $this->session->data['package_info']['ftp_host'] : '';
            $ftp_path = $this->session->data['package_info']['ftp_path']
                ? $this->session->data['package_info']['ftp_path'] : '';


            $this->data['form']['fuser'] = $form->getFieldHtml(array(
                'type' => 'input',
                'name' => 'ftp_user',
                'value' => $ftp_user,
                'require' => true,
                'help_url' => $this->gen_help_url('ftp_user'),));
            $this->data['form']['fpass'] = $form->getFieldHtml(array(
                'type' => 'password',
                'name' => 'ftp_password',
                'require' => true,
                'value' => $ftp_password,));
            $this->data['form']['fhost'] = $form->getFieldHtml(array(
                'type' => 'input',
                'name' => 'ftp_host',
                'value' => $ftp_host,
                'help_url' => $this->gen_help_url('ftp_host'),));
            $this->data['form']['fpath'] = $form->getFieldHtml(array(
                'type' => 'input',
                'name' => 'ftp_path',
                'value' => $ftp_path,
                'help_url' => $this->gen_help_url('ftp_path'),));

            $this->data['form']['submit'] = $form->getFieldHtml(array('type' => 'button',
                'text' => $this->language->get('button_go'),
                'style' => 'button1'
            ));

            $this->data['fuser'] = $this->language->get('text_ftp_user');
            $this->data['fpassword'] = $this->language->get('text_ftp_password');
            $this->data['fhost'] = $this->language->get('text_ftp_host');
            $this->data['fpath'] = $this->language->get('text_ftp_path');
            $this->data['heading_title'] = $this->language->get('heading_title_ftp');
            $this->data['warning_ftp'] = $this->language->get('warning_ftp');
            $this->data['warning_ftp'] .= '<br>Need write permission for:<br>' . implode('<br>', $non_writables);

        } else {

            $this->data['form']['checkbox'] = $form->getFieldHtml(array(
                'id' => 'agree',
                'type' => 'checkbox',
                'name' => 'agree',
                'value' => '0',
                'checked' => 'false',
                'attr' => ' onclick="if(this.checked){
			                                                                                        $(\'#agree_button\').show();
			                                                                                        $(\'#disagree_button\').hide();
			                                                                                    }else{
			                                                                                        $(\'#agree_button\').hide();
			                                                                                        $(\'#disagree_button\').show();
			                                                                                        }"'));
            $this->data['text_agree'] = $this->language->get('text_i_agree');
            $this->data['form']['disagree_button'] = $form->getFieldHtml(array('type' => 'button',
                'text' => $this->language->get('text_disagree'),
                'style' => 'button'));
            $this->data['heading_title'] = $this->language->get('heading_title_license');
            $this->data['form']['submit'] = $form->getFieldHtml(array('type' => 'button',
                'text' => $this->language->get('text_agree'),
                'style' => 'button1'
            ));
        }


        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/tool/package_installer_agreement.tpl');
    }

    public function install() {

        $package_id = $this->session->data['package_info']['package_id'];
        $package_dirname = $this->session->data['package_info']['package_dir'];
        $temp_dirname = $this->session->data['package_info']['tmp_dir'];

        // if we got decision
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post['disagree'] == 1) { // if does not agree  with agreement of filesize
            $this->_removeTempFiles();
            unset($this->session->data['package_info']);
            $this->redirect($this->html->getSecureURL('extension/extensions/extensions'));
        }

        if (!$package_id || !file_exists($temp_dirname . $package_dirname . "/code")) { // if error
            $this->session->data['error'] = $this->language->get('error_package_structure');
            $this->redirect($this->html->getSecureURL('tool/package_installer'));
        }

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $upgrade_confirmed = $this->request->post['agree'] == 2 ? true : false;
            $license_agree = $this->request->post['agree'] == 1 ? true : false;
            unset($this->request->post['agree']);
        }

        //check for previous version of package and backup it
        if ($this->session->data['package_info']['package_content']['extensions']) { // for multipackage
            foreach ($this->session->data['package_info']['package_content']['extensions'] as $k => $ext) {
                $result = $this->_installExtension($ext, $upgrade_confirmed, $license_agree);
                unset($license_agree);
                if ($result !== true) {
                    if (isset($result['license'])) {
                        $this->data['license_text'] = file_get_contents($temp_dirname . $package_dirname . "/code/extensions/" . $ext . "/license.txt");
                        $this->data['license_text'] = htmlentities($this->data['license_text'], ENT_QUOTES, 'UTF-8');
                        $this->data['license_text'] = nl2br($this->data['license_text']);
                    } else {
                        $this->data['license_text'] = '<h2>Extension "' . $ext . '" will be upgrade from version ' . $result['upgrade'] . '</h2>';
                    }
                    break;
                } else {
                    unset($this->session->data['package_info']['package_content']['extensions'][$k]);
                }
                $extension_id = $ext;
            }
        }

        if ($this->session->data['package_info']['package_content']['core']) { // for cart upgrade)
            $result = $this->_upgradeCore();
        }

        if ($result === true) { // if all  was installed
            // clean and redirect after install
            $this->_removeTempFiles();
            unset($this->session->data['package_info']);
            $this->session->data['success'] = $this->language->get('success');
            if ($extension_id) {
                $this->redirect($this->html->getSecureURL('extension/extensions/edit', '&extension=' . $extension_id));
            } else {
                $this->redirect($this->html->getSecureURL('tool/install_upgrade_history'));
            }
        }

        $form = new AForm('ST');
        $form->setForm(array('form_name' => 'preinstallFrm'));
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
            'type' => 'form',
            'name' => 'preinstallFrm',
            'action' => $this->html->getSecureURL('tool/package_installer/install')));
        if (isset($result['license'])) {
            $this->data['form']['checkbox'] = $form->getFieldHtml(array(
                'id' => 'agree',
                'type' => 'checkbox',
                'name' => 'agree',
                'value' => 'true',
                'attr' => ' onclick="this.checked ? $(\'#agree_button\').show(): $(\'#agree_button\').hide()"'
            ));
            $this->data['text_agree'] = $this->language->get('text_i_agree');
        } else {
            $this->data['form']['checkbox'] = $form->getFieldHtml(array(
                'id' => 'agree',
                'type' => 'hidden',
                'name' => 'agree',
                'value' => 2
            ));
        }


        $this->data['form']['disagree_button'] = $form->getFieldHtml(array('type' => 'button',
            'text' => $this->language->get('text_disagree'),
            'style' => 'button'));
        $this->data['heading_title'] = $this->language->get('heading_title_license') . '. Extension: ' . $ext;

        $this->data['form']['submit'] = $form->getFieldHtml(array('type' => 'button',
            'text' => $this->language->get('text_agree'),
            'style' => 'button1'
        ));

        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/tool/package_installer_install.tpl');
    }

    /**
     * method of installation of extension from package
     * @param string $extension_id
     * @return void
     */
    private function _installExtension($extension_id = '', $confirmed = false, $agree = 0) {

        $package_dirname = $this->session->data['package_info']['package_dir'];
        $temp_dirname = $this->session->data['package_info']['tmp_dir'];

        $config = simplexml_load_string(file_get_contents($temp_dirname . $package_dirname . "/code/extensions/" . $extension_id . '/config.xml'));
        $version = (string)$config->version;
        $type = (string)$config->type;
        $type = !$type && $this->session->data['package_info']['package_type']
            ? $this->session->data['package_info']['package_type'] : $type;
        $type = !$type ? 'extension' : $type;


        // #1. check installed version
        $all_installed = $this->extensions->getInstalled('exts');
        if (in_array($extension_id, $all_installed)) {
            $already_installed = true;
            $installed_info = $this->extensions->getExtensionInfo($extension_id);
            $installed_version = $installed_info['version'];

            if (version_compare($version, $installed_version, '<=')) {
                // if installed version the same or higher - do nothing
                return true;
            } else {
                if (!$confirmed && !$agree) {
                    return array('upgrade' => $installed_version . ' >> ' . $version);
                }
            }
        }

        $pmanager = new APackageManager();
        // #2. backup previous version
        if ($already_installed || file_exists(DIR_EXT . $extension_id)) {
            if (!$pmanager->backupPrevious($extension_id)) {
                $this->session->data['error'] = $pmanager->error;

                $this->redirect($this->html->getSecureURL('tool/package_installer'));
            }

        }

        // #3. if all fine - copy code of package
        if ($this->session->data['package_info']['ftp']) { // if ftp-access
            $result = $pmanager->ftp_move($temp_dirname . $package_dirname . "/code/extensions/" . $extension_id,
                $extension_id,
                $this->session->data['package_info']['ftp_path'] . $package_dirname . 'code/extensions/' . $extension_id);
        } else {
            $result = rename($temp_dirname . $package_dirname . "/code/extensions/" . $extension_id,
                DIR_EXT . $extension_id);
        }
        // #4. if copied successully - install(upgrade)
        if ($result) {
            $install_mode = $already_installed ? 'upgrade' : 'install';
            if (!$pmanager->installExtension($extension_id, $type, $version, $install_mode)) {
                $this->session->data['error'] .= '<br>' . $this->language->get('error_install');
                $this->_removeTempFiles('dir');
                $this->redirect($this->html->getSecureURL('tool/package_installer'));
            }
        } else {
            $this->session->data['error'] = $this->language->get('error_move') . DIR_EXT . 'extensions/' . $extension_id;
            $this->_removeTempFiles('dir');
            $this->redirect($this->html->getSecureURL('tool/package_installer'));
        }

        return true;
    }


    /**
     * method installs upgrade for abantecart core
     * @param $core_file
     * @return void
     */
    private function _upgradeCore() {

        if (version_compare(VERSION, $this->session->data['package_info']['package_version'], ">=")) {

            $this->session->data['error'] = str_replace('%VERSION%', VERSION, $this->language->get('error_core_version')) . $this->session->data['package_info']['package_version'] . '!';
            unset($this->session->data['package_info']);
            $this->redirect($this->html->getSecureURL('tool/package_installer'));
        }

        $corefiles = $this->session->data['package_info']['package_content']['core'];
        $pmanager = new APackageManager();
        //#1 backup files
        $backup = new ABackup('abantecart_' . VERSION);
        foreach ($corefiles as $core_file) {
            if (file_exists(DIR_ROOT . '/' . $core_file)) {
                if (!$backup->backupFile(DIR_ROOT . '/' . $core_file, false)) {
                    break;
                    return false;
                }
            }
        }
        //#2 backup database
        if ($backup->dumpDatabase()) {
            $backup_dirname = $backup->getBackupName();
            if ($backup_dirname) {
                if (!$backup->dumpDatabase()) {
                    return false;
                }
                if (!$backup->archive(DIR_BACKUP . $backup_dirname . '.tar.gz', DIR_BACKUP, $backup_dirname)) {
                    return false;
                }
            } else {
                return false;
            }

            $install_upgrade_history = new ADataset('install_upgrade_history', 'admin');
            $install_upgrade_history->addRows(array('date_added' => date("Y-m-d H:i:s", time()),
                'name' => 'Backup before core upgrade. Core version: ' . VERSION,
                'version' => VERSION,
                'backup_file' => $backup_dirname . '.tar.gz',
                'backup_date' => date("Y-m-d H:i:s", time()),
                'type' => 'backup',
                'user' => $this->user->getUsername()));
        } else {
            return false;
        }

        //#3 replace files
        $pmanager->replaceCoreFiles();
        //#4 run sql and php upgare procedure files
        $package_dirname = $this->session->data['package_info']['tmp_dir'] . $this->session->data['package_info']['package_dir'];
        $config = simplexml_load_string(file_get_contents($package_dirname . '/package.xml'));
        $pmanager->upgradeCore($config);

        $pmanager->updateCoreVersion((string)$config->core->version);

        return true;
    }

    private function _removeTempFiles($target = 'both') {
        if (!in_array($target, array('both', 'pack', 'dir'))
            || !$this->session->data['package_info']['package_dir']
        ) {
            return false;
        }
        $pmanager = new APackageManager();
        switch ($target) {
            case 'both':
                $result = $pmanager->removeDir($this->session->data['package_info']['tmp_dir'] . $this->session->data['package_info']['package_dir']);
                @unlink($this->session->data['package_info']['tmp_dir'] . $this->session->data['package_info']['package_name']);
                break;
            case 'pack':
                $result = @unlink($this->session->data['package_info']['tmp_dir'] . $this->session->data['package_info']['package_name']);
                break;
            case 'dir':
                $result = $pmanager->removeDir($this->session->data['package_info']['tmp_dir'] . $this->session->data['package_info']['package_dir']);
                break;
        }
        if (!$result) {
            $this->session->data['error'] = $pmanager->error;
            return false;
        }
        return true;
    }

}