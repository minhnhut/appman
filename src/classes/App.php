<?php

namespace App;

class App
{
    protected $path;
    protected $name;
    protected $currentVersion;
    protected $availableVersions = [];

    /**
     * App constructor.
     *
     * @param $name
     * @param $path
     * @param $version
     */
    public function __construct($name, $path) {
        $this->name = $name;
        $this->path = $path;
    }
}
