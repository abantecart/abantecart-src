<?php
use Aws\Sdk;

class ControllerPagesAwsTest extends AController
{
    public function main()
    {
        $sharedConfig = [
            'profile' => 'default',
            'region' => 'eu-west-2',
            'version' => 'latest'
        ];

        try {
            $sdk = new Sdk($sharedConfig);
            $s3Client = $sdk->createS3();

            $result = $s3Client->putObject([
                'Bucket' => 'text-ai-documents',
                'Key'    => '000test.php',
                'Body'   => 'this is the body!'
            ]);

            echo $result['Body'];
        } catch (Exception $exception) {
            $this->log->write('AWS_SDK: '.$exception->getMessage());
            print_r($exception->getMessage());
        }
        echo "<br/><br/><br/><br/><br/>";
    }
}
