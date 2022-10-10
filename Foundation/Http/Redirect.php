<?php
namespace Foundation\Http;

class Redirect
{
    public function route($route, array $params = [])
    {
        $rota = \Foundation\Request::dataRouter();
        $module = strtolower($rota['module']);
        $moduleRoute = "/{$module}/$route";

        if(empty($params))
            return $this->to($moduleRoute);

        return $this->to(DIR_BASE .'/'. url()->route($route, $params));
    }

    public function routeRoot($route, array $params = [])
    {
        if(empty($params))
            return $this->to($route);

        return $this->to(url()->route($route, $params));
    }

    public function to($url)
    {
        return header(sprintf('Location: %s', $url));
    }
}
