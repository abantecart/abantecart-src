<?php
require_once 'ExternalMediaStorageService.php';

use Aws\Sdk;

/**
 * External storage AWS S3 implementation
 */
class AwsS3Service implements ExternalMediaStorageService
{
    /**
     * @var \Aws\S3\S3Client
     */
    private $s3Client;
    /**
     * @var string
     */
    private $bucket;
    /**
     * @var string
     */
    private $fileAcl;

    /**
     * @var string
     */
    private $s3path;

    /**
     * @param string $bucket
     * @param string $region
     * @param string $profile
     * @param string $s3path
     * @param string $version
     * @param string $fileAcl
     */
    public function __construct(
        string $bucket,
        string $region,
        string $profile = 'default',
        string $s3path = '',
        string $version = 'latest',
        string $fileAcl = 'public-read'
    ) {
        $sharedConfig = [
            'profile' => $profile,
            'region'  => $region,
            'version' => $version,
        ];

        $sdk = new Sdk($sharedConfig);
        $this->s3Client = $sdk->createS3();
        $this->bucket = $bucket;
        $this->fileAcl = $fileAcl;
        $this->s3path = $s3path;
    }

    /**
     * @param string $fileName
     * @param string $filePath
     */
    public function upload(string $fileName, string $filePath): void
    {
        $fileName = ($this->s3path) ? $this->s3path . "/" . $fileName : $fileName;
        $this->s3Client->putObject([
            'Bucket'     => $this->bucket,
            'Key'        => $fileName,
            'SourceFile' => $filePath,
            'ACL'        => $this->fileAcl,
        ]);
    }

    /**
     * @param string $fileName
     */
    public function download(string $fileName): void
    {
        //TODO: Finish method implementation. Possible this method not needed in future
        $fileName = ($this->s3path) ? $this->s3path . "/" . $fileName : $fileName;
        $this->s3Client->getObject([
            'Bucket' => $this->bucket,
            'Key'    => $fileName,
        ]);
    }

    /**
     * @param string $fileName
     */
    public function delete(string $fileName): void
    {
        $fileName = ($this->s3path) ? $this->s3path . "/" . $fileName : $fileName;
        $this->s3Client->deleteObject([
            'Bucket' => $this->bucket,
            'Key'    => $fileName,
        ]);
    }
}
