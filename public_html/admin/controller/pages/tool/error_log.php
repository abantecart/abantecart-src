<?php

/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2026 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerPagesToolErrorLog extends AController
{
    const VIEW_SIZE_LIMIT = 500000;

    public function main()
    {
        $this->data['log'] = [];
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('tool/error_log');

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        if (isset($this->session->data['error'])) {
            $this->data['error_warning'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $this->data['error'] = '';
        }

        $filename = $this->request->get['filename'] ? : $this->config->get('config_error_filename');
        //remove relative parents from the filename to avoid directory traversal
        $filename = str_replace('..' . DS, '', $filename);
        $file = DIR_LOGS . $filename;
        if (!is_file($file)) {
            redirect(
                $this->html->getSecureURL(
                    'tool/error_log',
                    '&' . http_build_query(['filename' => $this->config->get('config_error_filename')])
                )
            );
        }

        $this->data['main_url'] = $this->html->getSecureURL('tool/error_log');

        $all_logs = array_merge(
            glob(DIR_LOGS . '{*.log,*.txt,*.gz}', GLOB_BRACE),
            glob(DIR_LOGS . '*/{*.log,*.txt,*.gz}', GLOB_BRACE)
        );
        $logFiles = [];
        foreach ($all_logs as $f) {
            $subDir = str_replace(DIR_LOGS, '', dirname($f) . '/');
            $logFiles[$subDir][$subDir . basename($f)] = basename($f) . ' ' . human_filesize(filesize($f));
        }

        $this->data['logfile_list'] = $logFiles;
        $this->data['current_logfile'] = $filename;

        $heading_title = $this->language->get('heading_title');
        $this->document->setTitle($heading_title);
        $this->data['heading_title'] = $heading_title;
        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('tool/error_log', ($filename ? '&filename=' . $filename : '')),
                'text'      => $heading_title,
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        $isArchive = pathinfo($file, PATHINFO_EXTENSION) === 'gz';
        $filesize = filesize($file);
        if (file_exists($file) && $filesize && !$isArchive) {
            $fp = fopen($file, 'r');
            // check filesize
            if ($filesize > self::VIEW_SIZE_LIMIT) {
                $this->data['log'][''] =
                    PHP_EOL
                    . PHP_EOL
                    . PHP_EOL
                    . str_repeat('#', 100)
                    . PHP_EOL
                    . PHP_EOL
                    . strtoupper($this->language->get('text_file_tail')) . DIR_LOGS
                    . PHP_EOL
                    . str_repeat('#', 100)
                    . PHP_EOL
                    . PHP_EOL
                    . PHP_EOL;
                fseek($fp, -self::VIEW_SIZE_LIMIT, SEEK_END);
                fgets($fp);
            }
            $log = '';
            while (!feof($fp)) {
                $log .= fgets($fp);
            }
            fclose($fp);
        } elseif ($isArchive) {
            $log = ' File is GZIP archive. To view the content, please download the file and extract it.';
        } else {
            $log = '';
        }
        if ($log) {
            $this->data['download_btn'] = $this->html->buildElement(
                [
                    'type'  => 'button',
                    'name'  => 'download',
                    'title' => $this->language->get('button_download'),
                ]
            );
        }
        $this->data['clear_btn'] =
            $this->html->buildElement(
                [
                    'name' => 'clear',
                    'href' => $this->html->getSecureURL('tool/error_log/clearlog', '&filename=' . $filename),
                    'text' => $this->language->get('button_clear'),
                    'type' => 'button',
                ]
            );
        $log = htmlentities(str_replace(['<br/>', '<br />'], "\n", $log), ENT_QUOTES | ENT_IGNORE, 'UTF-8');
        //filter empty string
        $lines = array_filter(explode("\n", $log), 'strlen');
        unset($log);
        $k = 0;
        $data = [];
        foreach ($lines as $line) {
            if (preg_match('(^\d{4}-\d{2}-\d{2} \d{1,2}:\d{2}:\d{2})', $line, $match)) {
                $k++;
                $data[$k] = str_replace($match[0], '<b>' . $match[0] . '</b>', $line);
            } else {
                $data[$k] .= '<br>' . $line;
            }
        }

        $this->data['log'] += $data;
        $this->data['download_url'] = $this->html->getSecureURL('tool/error_log/download');

        $this->view->batchAssign($this->data);
        /** @see public_html/admin/view/default/template/pages/tool/error_log.tpl */
        $this->processTemplate('pages/tool/error_log.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function clearLog()
    {
        $this->loadLanguage('tool/error_log');
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $filename = (string) $this->request->get['filename'];
        if (!$filename) {
            redirect($this->html->getSecureURL('tool/error_log'));
        }
        $file = DIR_LOGS . $filename;

        $base = realpath(DIR_LOGS);
        $file = realpath($file);

        if ($file === false || !str_starts_with($file, $base . DS)) {
            redirect($this->html->getSecureURL('tool/error_log'));
        }

        if (is_writable($file)) {
            $handle = fopen($file, 'w+');
            fclose($handle);
            unlink($file);
            $this->session->data['success'] = $this->language->get('text_success');
        } else {
            $this->session->data['error'] = $this->language->get('text_file_error');
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        redirect($this->html->getSecureURL('tool/error_log', '&' . http_build_query(['filename' => $filename])));
    }

    public function download()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if (!$this->user->canAccess('tool/error_log')) {
            $this->dispatch('error/permission');
            return;
        }

        $this->loadLanguage('tool/error_log');

        $filename = (string) $this->request->get['filename'];
        $file = DIR_LOGS . $filename;

        $base = realpath(DIR_LOGS);
        $file = realpath($file);

        if ($file === false || !str_starts_with($file, $base . DS)) {
            redirect($this->html->getSecureURL('tool/error_log'));
        }

        if (!filesize($file)) {
            $this->session->data['error'] = 'File not found or zero-length.';
            redirect($this->html->getSecureURL('tool/error_log', '&' . http_build_query(['filename' => $filename])));
            return;
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename(str_replace(DS, '_', $filename)));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_end_clean();
        flush();
        readfile($file);
        exit;
    }
}
