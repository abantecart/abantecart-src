<?php
require 'vendor/autoload.php';
require_once 'AwsS3Service.php';

use AwsS3Service;

class ExtensionAwsCdn extends Extension
{
    const RESOURCES = 'resources/';
    private $awsS3Service;
    public $registry;
    public $config;
    public $extensionEnabled;

    public function __construct()
    {
        $this->registry = Registry::getInstance();
        $this->config = $this->registry->get('config');
        $this->awsS3Service = new AwsS3Service(
            $this->config->get('aws_cdn_bucket'),
            $this->config->get('aws_cdn_region'),
            $this->config->get('aws_cdn_aws_profile')
        );
        $this->extensionEnabled = $this->config->get('aws_cdn_status');
    }

    public function onControllerResponsesCommonResourceLibrary_UpdateData()
    {
        if (!$this->extensionEnabled) {
            return;
        }

        switch ($this->baseObject_method) {
            case 'upload':
                $this->uploadResource();
                break;
            case 'replace':
                $this->replaceResource();
                break;
            case 'delete':
                $this->deleteResource();
                break;
        }
    }

    public function onAImage_UpdateData()
    {

        if (!$this->extensionEnabled) {
            return;
        }

        if ($this->baseObject_method !== 'resizeAndSave') {
            return;
        }

        $that = $this->baseObject;
        if ($that->data['result']) {
            $fullFilePath = $that->data['filename'];
            $relativeFilePath = str_replace(DIR_ROOT.'/', "", $fullFilePath);
            try {
                $this->awsS3Service->upload($relativeFilePath, $fullFilePath);
            } catch (Exception $exception) {
                $registry = Registry::getInstance();
                $registry->get('log')->write(AWS_CDN_TAG.' Error: '.$exception->getMessage());
            }
        }
    }

    private function uploadResource(): void
    {
        $that = $this->baseObject;
        foreach ($that->data['result'] as $file) {
            if ($file->error_text) {
                continue;
            }
            try {
                $this->awsS3Service->upload(self::RESOURCES.$file->typeDir.$file->resource_path, DIR_RESOURCE.$file->typeDir.$file->resource_path);
            } catch (Exception $exception) {
                $that->log->write(AWS_CDN_TAG.' Error: '.$exception->getMessage());
            }
        }
    }

    private function replaceResource(): void
    {
        $that = $this->baseObject;
        foreach ($that->data['result'] as $file) {
            if ($file->error_text) {
                continue;
            }
            try {
                $this->awsS3Service->delete(self::RESOURCES.$file->typeDir.$file->oldResourcePath);
                $this->awsS3Service->upload(self::RESOURCES.$file->typeDir.$file->newResourcePath, DIR_RESOURCE.$file->typeDir.$file->newResourcePath);
            } catch (Exception $exception) {
                $that->log->write(AWS_CDN_TAG.' Error: '.$exception->getMessage());
            }
        }
    }

    private function deleteResource(): void
    {
        $that = $this->baseObject;
        if ($that->data['result']['delete_result']) {
            $file = $that->data['result']['resource'];
            try {
                $this->awsS3Service->delete(self::RESOURCES.$file['type_dir'].$file['resource_path']);
            } catch (Exception $exception) {
                $that->log->write(AWS_CDN_TAG.' Error: '.$exception->getMessage());
            }
        }
    }

    private function getBaseResourseUrl()
    {
        return 'https://'.$this->config->get('aws_cdn_bucket').'.s3.'.$this->config->get('aws_cdn_region').'.amazonaws.com/';
    }

    public function beforeAResource_processData()
    {
        if (!$this->extensionEnabled) {
            return;
        }

        $that = $this->baseObject;
        $cdn_url = $this->getBaseResourseUrl();
        if (in_array($this->baseObject_method, array('buildResourceURL', 'getResourceAllObjects'))) {
            $cdn_url .= 'resources/';
        } elseif (in_array($this->baseObject_method, array('getResourceThumb', 'getResizedImageURL'))) {
            $cdn_url .= 'image/';
        }
        $that->data['http_dir'] = $cdn_url;
    }

    public function beforeAView_processData()
    {

        if (!$this->extensionEnabled || IS_ADMIN === true) {
            return;
        }

        $that = $this->baseObject;
        $cdn_url = $this->getBaseResourseUrl();
        if ($this->baseObject_method == 'getResourceThumb') {
            $cdn_url .= 'image/';
        }
        $that->data['http_dir'] = $cdn_url;
    }

    public function onControllerCommonHead_updateData()
    {
        if ($this->extensionEnabled && IS_ADMIN !== true) {
            $this->baseObject->view->assign('base', $this->getBaseResourseUrl());
        }
    }

}
