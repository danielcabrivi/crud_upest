<?php
namespace Foundation;

final class Request
{
    private static function url()
    {
        return isset($_REQUEST['url']) ? $_REQUEST['url'] : '/index';
    }
    public static function dispatch($page, $action, $module)
    {
        if( ! isset($page))
        {
            throw new \Exception('É preciso informar a página.');
        }

        $name = ucwords(strtolower($page)) . 'Controller';
        $class = "{$module}\\Controllers\\{$name}";

        $controller = new $class;
        $controller->$action();
    }


    public static function dataRouter()
    {
        $url = self::url(); //isset($_REQUEST['url']) ? $_REQUEST['url'] : '/index';
        $module = self::getModule();
        $routerKey = self::getRouterKey($url);

        $pathRouter = dirname(__FILE__)."/../{$module}/Routes.php";
        if(file_exists($pathRouter)) {
            $all_routers = require $pathRouter;
            if(self::router_exists($routerKey, $all_routers)){
                $rota =  $all_routers[$routerKey];
                return [
                    'module' => $module,
                    'controller' => $rota[0],
                    'action' => $rota[1]
                ];
            }
        }

        //Se nao houver rota cadastrada erro 404
        return [
          'module' => $module,
          'controller' => 'Error404',
          'action'     => 'index'
        ];

    }

    private static function isModule($module)
    {
        $modules = \Container::get('base.modules');
        return in_array(ucfirst($module), $modules);
    }

    public static function getModule()
    {
        $url = self::url();
        $arrUrl = explode('/', $url);
        $firstParam = ucfirst(array_shift($arrUrl));

        $moduleDefault = \Container::get('module.default');

        if(empty($url) || !self::isModule($firstParam)){
            return $moduleDefault;
        }else{
            return $firstParam;
        }
    }

    private static function getRouterKey()
    {
        $url = self::url();
        $arrUrl = explode('/', $url);
        if (count($arrUrl) > 1  &&  self::isModule($arrUrl[0])) {
            return $arrUrl[1];
        } else{
            if(!empty($arrUrl[0]))
                return $arrUrl[0];
            else
                return $arrUrl[1];
        }
    }

    private static function router_exists($keyRouter, $allRoutes)
    {
        if(!is_array($allRoutes))
            return false;
        return array_key_exists($keyRouter, $allRoutes);
    }

}
