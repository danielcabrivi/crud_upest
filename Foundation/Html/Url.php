<?php
namespace Foundation\Html;

use Container;

class Url
{
    public function route($route, array $params = [])
    {
        $rota = \Foundation\Request::dataRouter();
        $module = strtolower($rota['module']);
        $moduleRoute = "/{$module}/$route";

        if(empty($params))
            return $moduleRoute;

        $data = getRoute($route, $module);
        $extraParams = '';
        foreach($params as $key => $value) {
            $extraParams .= sprintf('/%s/%s', $key, $value);
        }

        return sprintf('%s/%s.%s%s',
            //Container::get('app.url'),
            $module,
            $data[0],
            $data[1],
            $extraParams
        );
    }

    public function getParams()
    {
        $url = isset($_GET['url']) ? $url= $_GET['url'] : $url='';
        $url = explode('/',$url);
        $parametros=array();
        //VERIFICA SE EXISTE 3 OU MAIS PARAMENTROS NA URL POR ANTERIORES SÃO [MODULO,CONTROLLER, ACTION]
        if(count($url) >= 3){
            //VERIFICA SE A QUANTIDADE DE PARAMETROS É PAR CASO NÃO SETA O ULTIMO AUTOMATICO
//            if( (count($url)-1) % 2 == 1){
            if( (count($url)) % 2 == 1){
                $tamanho = count($url)-1;
                $parametros['ultimo'] = $url[count($url)-1];
            }else{
                $tamanho = count($url);
            }
            //TESTE SE É UM METODO DO COTROLLER ATIVO
            $metodo = $url[2]."Action";
            if(method_exists($this, $metodo)){
                $inicio = 4;
            }else
                $inicio = 3;
            //PERCORRE A URL E MONTA UM ARRAY COM OS PARAMETROS
            for($i=$inicio; $i< $tamanho; $i++){
                $parametros[$url[$i-1] ] = $url[$i];
                $i++;
            }
        }
        $parametros = array_merge($parametros, $_REQUEST);
        $parametros = array_merge($parametros, $_FILES);
        return $parametros;
    }

    public  function getParam($key){
        $params = $this->getParams();
        if(isset($params[$key])){
            return strip_tags($params[$key],'<script');
        }
    }
}
