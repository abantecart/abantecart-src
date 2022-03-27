<?php

/**
 * Interface for external media storages (AWS S3, google cloud, etc.)
 */
interface ExternalMediaStorageService
{
    /**
     * @param string $fileName
     * @param string $filePath
     */
    public function upload(string $fileName, string $filePath): void;

    /**
     * @param string $fileName
     */
    public function download(string $fileName): void;

    /**
     * @param string $fileName
     */
    public function delete(string $fileName): void;
}
