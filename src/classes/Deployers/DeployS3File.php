<?php

namespace App\Deployers;

use App\AppManager;
use App\Config;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use DI\Container;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

class DeployS3File implements DeployerInterface
{
    /** @var AppManager */
    protected $appManager;
    /** @var Config */
    protected $config;

    public function __construct(Container $container) {
        $this->appManager = $container->get("appManager");
        $this->config     = $container->get("config");
    }

    public function deploy($appId, $targetVersion, $targetPackages)
    {
        $app = $this->appManager->getApp($appId);

        $requiredKeys = ['s3_key', 's3_secret', 's3_version', 's3_region', 's3_bucket_name', 's3_endpoint'];
        foreach ($requiredKeys as $requiredKey) {
            if (!isset($app[$requiredKey])) throw new \Exception("S3 was not configured properly.");
        }

        $client = new S3Client([
            'credentials' => [
                'key'    => $app['s3_key'],
                'secret' => $app['s3_secret'],
            ],
            'region' => $app['s3_region'],
            'endpoint' => $app['s3_endpoint'],
            'version' => $app['s3_version'],
        ]);
        // Register the stream wrapper from an S3Client object
        $client->registerStreamWrapper();



        $tempFolder = $this->config->getTempPath();
        $archivePaths = array_map(function ($targetPackage) use ($tempFolder, $app, $client) {
            $archivePath = $tempFolder . '/' . time() . '_s3_object';
            $localFP = fopen($archivePath, 'w');
            try {
                // Open a stream in read-only mode
                if ($stream = fopen("s3://{$app['s3_bucket_name']}/{$targetPackage}", 'r')) {
                    // While the stream is still open
                    while (!feof($stream)) {
                        // Read 1,024 bytes from the stream
                        fwrite($localFP, fread($stream, 1024));
                    }
                    // Be sure to close the stream resource when you're done with it
                    fclose($stream);
                }
            } catch (S3Exception $e) {
                fclose($localFP);
                unlink($archivePath);
                var_dump($e->getResponse()->getHeaders());
                var_dump($e->getResponse()->getStatusCode());
                // better handle error
                die("S3 settings maybe wrong");
            }
            return $archivePath;
        }, $targetPackages);

        chdir($app['path']);
        mkdir($targetVersion);

        $zip = new \ZipArchive();
        foreach ($archivePaths as $archivePath) {
            $zip->open($archivePath);
            $zip->extractTo($targetVersion);
            $zip->close();
            unlink($archivePath);
        }
    }
}
