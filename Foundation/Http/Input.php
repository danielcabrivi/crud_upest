<?php
namespace Foundation\Http;

class Input
{
    public function get($key, $default = null)
    {
        if ($this->has($key) && is_array($_REQUEST[$key])) {
            return $_REQUEST[$key];
        }

        return $this->has($key) ? htmlspecialchars($_REQUEST[$key], ENT_QUOTES, 'UTF-8') : $default;
    }

    public function has($key)
    {
        return isset($_REQUEST[$key]);
    }
}
