<?php

namespace App;

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
        $versions = array_filter($versions, function($fname) {
            return !in_array($fname, ['.', '..', 'current', 'common', '.DS_Store']);
        });
        return $versions;
    }

    /**
     * @param $app
     *
     * @return mixed
     */
    protected function resolveAppInfo($app)
    {
        $config      = $this->config;
        $currentName = $config->getGlobalConfig(Config::CURRENT_FOLDER_NAME);
        $path        = implode(DIRECTORY_SEPARATOR, [$app['path'], $currentName]);
        $current     = readlink($path);

        // resolve version
        if ($current) {
            $app['version'] = $current;
        } else {
            $app['version'] = "Unknown";
        }

        return $app;
    }
}
