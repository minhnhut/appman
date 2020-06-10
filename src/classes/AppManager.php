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

        return array_map([$this, 'resolveAppInfo'], $apps);
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
