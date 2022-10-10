<?php

require '../bootstrap.php';

$rota = Foundation\Request::dataRouter();

$controller = $rota['controller'];
$action = $rota['action'];
$module = $rota['module'];


//$page = isset($_GET['page']) ? $_GET['page'] : 'index';
//$action = isset($_GET['action']) ? $_GET['action'] : 'index';
//Foundation\Request::dispatch($page, $action);

Foundation\Request::dispatch($controller, $action, $module);
