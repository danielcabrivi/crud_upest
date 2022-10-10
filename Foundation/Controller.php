<?php

namespace Foundation;

use Container;

abstract class Controller {

    protected function render($view, array $data = []) {
        extract($data);

        $dadosRota = Request::dataRouter();
        $file =   dirname(__FILE__)."/../{$dadosRota['module']}/Views/{$view}.phtml";

        //$file = Container::get('app.view.path') . $view . '.phtml';

        if (!file_exists($file)) {
            throw new \Exception('O arquivo de visão não foi localizado.');
        }

        return require $file;
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

    public function getParamsText()
    {
        $url = isset($_GET['url']) ? $url= $_GET['url'] : $url='';
        $url = explode('/',$url);

        $parametros=array();
        $parametros['id'] = isset($url[1]) ? $url[1] : NULL;
        $parametros['titulo'] = isset($url[2]) ? $url[2] : NULL;

        $parametros = array_merge($parametros, $_REQUEST);
        $parametros = array_merge($parametros, $_FILES);
        return $parametros;
    }

    protected  function getParamText($key){
        $params = $this->getParamsText();
        if(isset($params[$key])){
            return strip_tags($params[$key],'<script');
        }
    }

    protected  function getParamUnscape($key){
        $params = $this->getParams();
        if(isset($params[$key]))
            return  $params[$key];
    }


}
