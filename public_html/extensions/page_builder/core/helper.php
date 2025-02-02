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

if(!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

function checkPBDirs($templateTxtId)
{
    if( !defined('DIR_PB_TEMPLATES')
        || !is_writable_dir(DIR_PB_TEMPLATES.'savepoints')
        || !is_writable_dir(DIR_PB_TEMPLATES.'public')
        || !is_writable_dir(DIR_PB_TEMPLATES.'presets')
    ){
        throw new AException(
            AC_ERR_USER_ERROR,
            'Error! Please check permissions of directory '
            . DIR_SYSTEM . 'page_builder and it\'s subdirectories'
        );
    }

    foreach(['savepoints','presets','public'] as $subDir){
        $dir = DIR_PB_TEMPLATES.$subDir.DS.$templateTxtId;
        if(!is_dir($dir)){
            mkdir($dir,0775);
        }
        if(!is_writable_dir($dir) || !is_readable($dir)){
            throw new AException(
                AC_ERR_USER_ERROR,
                'Error! Please check permissions of directory '. $dir . ' .'
            );
        }
    }
}

/**
 * @param string $data
 * @param string $mode
 * @param array $indexes
 * @return false|mixed|string|null
 */
function preparePageBuilderPreset($data, $mode, $indexes){
    if (!$data) {
        return null;
    }
    $output = null;
    if ($mode == 'html') {
        $doc = new DOMDocument();
        $doc->loadHTML($data);

        $xpath = new DOMXpath($doc);
        $elements = $xpath->query("//*[@data-gjs-custom_block_id]");
        foreach ($elements as $item) {
            /** @var DOMElement $item */
            $item->removeAttribute('data-gjs-layout_id');
            $item->removeAttribute('data-gjs-page_id');
            $customBlockId = $item->getAttribute('data-gjs-custom_block_id');
            if($customBlockId){
                $customBlockName = str_replace('ABC-BS5','BS5',$item->getAttribute('data-gjs-custom-name'));
                if($indexes[$customBlockName]){
                    $item->setAttribute('data-gjs-custom_block_id', $indexes[$customBlockName]);
                }else{
                    Registry::getInstance()->get('log')->write($customBlockName .' not found in the default preset');
                }
            }
        }
        $output = $doc->saveHTML($doc->getElementsByTagName('html')->item(0));
    }elseif ($mode == 'components'){
        $data = json_decode($data, true, JSON_PRETTY_PRINT);
        $output = processPageBuilderComponent($data, $indexes);
    }

    return $output;
}

/**
 * @param array $data
 * @param array $indexes
 * @return array
 */
function processPageBuilderComponent($data, $indexes){
    foreach($data as &$item){
        unset(
            $item['attributes']['data-gjs-layout_id'],
            $item['attributes']['data-gjs-page_id']
        );
        $customBlockId = (int)$item['attributes']['data-gjs-custom_block_id'];
        if($customBlockId){
            $customBlockName = str_replace('ABC-BS5','BS5',$item['attributes']['data-gjs-custom-name']);
            if($indexes[$customBlockName]){
                $item['attributes']['data-gjs-custom_block_id'] = $indexes[$customBlockName];
                if($item['custom_block_id']){
                    $item['custom_block_id'] = $indexes[$customBlockName];
                }
            }else{
                Registry::getInstance()->get('log')->write($customBlockName .' not found in the default preset');
            }
        }

        if($item['components']){
            $item['components'] = processPageBuilderComponent($item['components'], $indexes);
        }
    }

    return $data;
}

function recurseCopy( string $sourceDirectory, string $destinationDirectory, string $childFolder = ''): void
{
    $directory = opendir($sourceDirectory);

    if (is_dir($destinationDirectory) === false) {
        mkdir($destinationDirectory);
    }

    if ($childFolder !== '') {
        if (is_dir($destinationDirectory.DS.$childFolder) === false) {
            mkdir($destinationDirectory.DS.$childFolder);
        }

        while (($file = readdir($directory)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            if (is_dir($sourceDirectory.DS.$file) === true) {
                recurseCopy($sourceDirectory.DS.$file, $destinationDirectory.DS.$childFolder.DS.$file);
            } else {
                copy($sourceDirectory.DS.$file, $destinationDirectory.DS.$childFolder.DS.$file);
            }
        }

        closedir($directory);

        return;
    }

    while (($file = readdir($directory)) !== false) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        if (is_dir($sourceDirectory.DS.$file) === true) {
            recurseCopy($sourceDirectory.DS.$file, $destinationDirectory.DS.$file);
        }
        else {
            copy($sourceDirectory.DS.$file, $destinationDirectory.DS.$file);
        }
    }

    closedir($directory);
}