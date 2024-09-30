<?php
if(!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

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