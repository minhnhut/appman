<?php

namespace App;

use Symfony\Component\Yaml\Yaml;

class Config
{
    const CURRENT_FOLDER_NAME = "current_folder_name";
    const COMMON_FOLDER_NAME = "common_folder_name";
    const META_FILE_NAME = "meta_file_name";
    const APPS_FOLDER = "apps_folder";
    const TEMP_UPLOAD_FOLDER = "tmp_upload_folder";

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

    public function getBasePath()
    {
        return realpath(__DIR__ . '/../..');
    }

    public function getTempPath()
    {
        return $this->getBasePath() . $this->getConfig(self::TEMP_UPLOAD_FOLDER);
    }

    public function addApp($name, $path, $otherConfig)
    {
        $app = [
            'name'   => $name,
            'path'   => $path,
            'meta'   => $this->getGlobalConfig(self::META_FILE_NAME),
            'common' => $this->getGlobalConfig(self::COMMON_FOLDER_NAME),
        ];
        unset($otherConfig['path'], $otherConfig['meta'], $otherConfig['common']);
        $this->internalConfigStore['apps'][] = array_merge(
            $app,
            $otherConfig
        );
        $this->internalConfigStore['apps'][] = $app;
        $this->saveConfig();
    }

    public function updateApp($name, $newConfig)
    {
        $apps = $this->internalConfigStore['apps'];
        if (isset($apps[$name])) {
            unset($newConfig['path'], $newConfig['meta'], $newConfig['common']);
            if (isset($newConfig['extra_links']) && !is_array($newConfig['extra_links'])) {
                $newConfig['extra_links'] = [$newConfig['extra_links']];
            }
            $this->internalConfigStore['apps'][$name] = array_merge(
                $apps[$name],
                $newConfig
            );
            $this->saveConfig();
        }
    }

    public function deregisterApp($name)
    {
        if (isset($this->internalConfigStore['apps'][$name])) {
            unset($this->internalConfigStore['apps'][$name]);
            $this->saveConfig();
        }
    }

    protected function saveConfig()
    {
        file_put_contents(
            __DIR__. '/../../config.yaml',
            Yaml::dump($this->internalConfigStore, 4, 4, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK)
        );
    }

}
