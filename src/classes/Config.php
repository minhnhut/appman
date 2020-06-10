<?php

namespace App;

use Symfony\Component\Yaml\Yaml;

class Config
{
    const CURRENT_FOLDER_NAME = "current_folder_name";
    const COMMON_FOLDER_NAME = "common_folder_name";
    const META_FILE_NAME = "meta_file_name";
    const APPS_FOLDER = "apps_folder";

    protected $internalConfigStore;

    public function __construct()
    {
        $configRaw = file_get_contents(__DIR__. '/../../config.yaml');
        $config = Yaml::parse($configRaw);
        $this->internalConfigStore = $config;
    }

    public function getConfig($name, $default = null)
    {
        if (isset($this->internalConfigStore[$name])) {
            return $this->internalConfigStore[$name];
        }
        return $default;
    }

    public function getGlobalConfig($name, $default = null)
    {
        $globalConfig = $this->internalConfigStore['global'];
        if (isset($globalConfig[$name])) {
            return $globalConfig[$name];
        }
        return $default;
    }

    public function addApp($name, $path)
    {
        $this->internalConfigStore['apps'][] = [
            'name'   => $name,
            'path'   => $path,
            'meta'   => $this->getGlobalConfig(self::META_FILE_NAME),
            'common' => $this->getGlobalConfig(self::COMMON_FOLDER_NAME),
        ];
        $this->saveConfig();
    }

    protected function saveConfig()
    {
        file_put_contents(
            __DIR__. '/../../config.yaml',
            Yaml::dump($this->internalConfigStore, 2, 4, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK)
        );
    }

}
