<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");

// Carrega o autoloader
require __DIR__ . '/autoload.php';

// Carrega o arquivo de configuração.
require __DIR__ . '/config.php';
