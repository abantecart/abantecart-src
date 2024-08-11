<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class ALoader
 *
 * @property AConfig       $config
 * @property ALanguage     $language
 * @property ExtensionsAPI $extensions
 */
final class ALoader
{
    /**
     * @var Registry
     */
    public $registry;

    /**
     * @param $registry Registry
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
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
     * @param string $library
     *
     * @throws AException
     */
    public function library($library)
    {
        $file = DIR_CORE.'lib/'.$library.'.php';

        if (file_exists($file)) {
            /** @noinspection PhpIncludeInspection */
            include_once($file);
        } else {
            throw new AException(AC_ERR_LOAD, 'Error: Could not load library '.$library.'!');
        }
    }

    /**
     * @param string $model - rt to model class
     * @param string $mode  - can be 'storefront','force'
     *
     * @return bool | object
     * @throws AException
     */
    public function model($model, $mode = '')
    {

        //force mode allows to load models for ALL extensions to bypass extension enabled only status
        //This might be helpful in storefront. In admin all installed extensions are available
        $force = '';
        if ($mode == 'force') {
            $force = 'all';
        }

        //mode to force load storefront model
        $section = defined('INSTALL') && $model!='install'
            ? dirname(DIR_ROOT).DS.'admin'.DS
            : DIR_APP_SECTION;
        if ($mode == 'storefront') {
            $section = defined('INSTALL')
                ? dirname(DIR_ROOT).DS.'storefront'.DS
                : DIR_ROOT.DS.'storefront'.DS;
        }

        $file = $section.'model'.DS.$model.'.php';
        if ($this->registry->has('extensions') && $result = $this->extensions->isExtensionResource('M', $model, $force, $mode)) {
            if (is_file($file)) {
                $warning = new AWarning("Extension <b>{$result['extension']}</b> override model <b>$model</b>");
                $warning->toDebug();
            }
            $file = $result['file'];
        }

        $class = 'Model'.preg_replace('/[^a-zA-Z0-9]/', '', $model);
        $obj_name = 'model_'.str_replace('/', '_', $model);

        //if model is loaded return it back
        if (is_object($this->registry->get($obj_name))) {
            return $this->registry->get($obj_name);
        } else {
            if (file_exists($file)) {
                include_once($file);
                $this->registry->set($obj_name, new $class($this->registry));
                return $this->registry->get($obj_name);
            } else if ($mode != 'silent') {
                $backtrace = debug_backtrace();
                $trace = '';
                foreach ($backtrace as $k=>$dbg){
                    $trace .= '#'.$k.' '.$dbg['file'].":".$dbg['line']."\n";
                }
                throw new AException(AC_ERR_LOAD, 'Error: Could not load model '.$model."\nTrace: \n".$trace);
            } else {
                return false;
            }
        }
    }

    /**
     * @param string $helper
     *
     * @throws AException
     */
    public function helper($helper)
    {
        $file = DIR_CORE.'helper/'.$helper.'.php';

        if (file_exists($file)) {
            include_once($file);
        } else {
            throw new AException(AC_ERR_LOAD, 'Error: Could not load helper '.$helper.'!');
        }
    }

    /**
     * @param string $config
     *
     * @throws AException
     */
    public function config($config)
    {
        $this->config->load($config);
    }

    /**
     * @param string $language
     * @param string $mode
     *
     * @return array|null
     * @throws AException
     */
    public function language($language, $mode = '')
    {
        return $this->language->load($language, $mode);
    }
}
