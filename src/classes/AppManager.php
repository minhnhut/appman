<?php

namespace App;

use Symfony\Component\Filesystem\Filesystem;

class AppManager
{
    protected $config;

    public function __construct(Config $config) {
        $this->config = $config;
    }

    /**
     * @param $name
     *
     * @return mixed|null
     */
    public function getApp($name)
    {
        $config      = $this->config;
        $apps        = $config->getConfig("apps");
        $appsPath    = $config->getGlobalConfig(Config::APPS_FOLDER);
        $currentName = $config->getGlobalConfig(Config::CURRENT_FOLDER_NAME);

        if (!isset($apps[$name])) {
            return null;
        }
        return $this->resolveAppInfo($apps[$name]);
    }

    /**
     * @return array
     */
    public function getApps()
    {
        $config      = $this->config;
        $apps        = $config->getConfig("apps");

        return $apps ? array_map([$this, 'resolveAppInfo'], $apps) : [];
    }

    /**
     * @param $name
     */
    public function getAppVersions($name)
    {
        $app = $this->getApp($name);
        if (!$app || !$app['path']) {
            return [];
        }
        $versions = scandir($app['path'], SCANDIR_SORT_DESCENDING);
        $ignoreFiles = array_merge(
            ['.', '..', '.DS_Store'],
            [$app['current_folder']],
            $app['ignore_files'] ?? []);
        $versions = array_filter($versions, function($fname) use ($ignoreFiles) {
            return !in_array($fname, $ignoreFiles);
        });
        return $versions;
    }

    public function switchVersion($name, $version)
    {
        $app               = $this->getApp($name);
        $config            = $this->config;
        $currentFolderName = $app['current_folder'] ?? $config->getGlobalConfig(Config::CURRENT_FOLDER_NAME);
        $filesystem        = new Filesystem();
        $isWindows         = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if (chdir ($app['path'])) {
            $execSupport = false;
            if(exec('echo EXEC') == 'EXEC' && !$isWindows){
                // exec is available, lets use it
                // link by ln is atomic action
                exec     ("ln -sfn $version $currentFolderName");
                $execSupport = true;
            } else {
                // exec is not available, so we need to unlink first
                // then link again, there is a chance of very short down time
                @unlink  ($currentFolderName);
                $filesystem->symlink($version, $currentFolderName, true);
            }

            if (chdir($currentFolderName)) {
                if (isset($app['extra_links'])) {
                    $extraLinks = !is_array($app['extra_links']) ? [$app['extra_links']] : $app['extra_links'];
                    foreach ($extraLinks as $extraLink) {
                        $parts = explode(':', $extraLink);
                        if (count($parts) !== 2) continue;
                        if ($filesystem->exists($parts[1])) {
                            $backupName = $parts[1] . ".appman.bak";
                            if ($filesystem->exists($backupName)) {
                                $filesystem->remove($backupName);
                            }
                            $filesystem->rename($parts[1], $backupName);
                        }
                        if ($execSupport) {
                            exec ("ln -sfn {$parts[0]} {$parts[1]}");
                        } else {
                            $filesystem->symlink($parts[0], $parts[1], true);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $app
     *
     * @return mixed
     */
    protected function resolveAppInfo($app)
    {
        $config      = $this->config;
        $currentName = $app['current_folder'] ?? $config->getGlobalConfig(Config::CURRENT_FOLDER_NAME);
        $path        = implode(DIRECTORY_SEPARATOR, [$app['path'], $currentName]);
        $current     = readlink($path);

        // resolve version
        if ($current) {
            $app['version'] = $current;
        } else {
            $app['version'] = "Unknown";
        }

        $app['current_folder'] = $currentName;

        return $app;
    }
}
