<?php
namespace Foundation\Http;

class Session
{
    public function __construct()
    {
        session_start();
    }

    public function put($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key, $default = null)
    {
        return $this->has($key) ? $_SESSION[$key] : $default;
    }

    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public function forget($key)
    {
        if($this->has($key)) {
            unset($_SESSION[$key]);
        }
    }

    public function getAndRemove($key)
    {
        $value = $this->get($key, null);

        if($value) {
            $this->forget($key);
        }

        return $value;
    }
}
