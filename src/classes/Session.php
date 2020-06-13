<?php

namespace App;

class Session
{
    public function get($key, $default = "")
    {
        return $_SESSION[$key] ?? $default;
    }

    public function set($key, $value)
    {

    }
}
