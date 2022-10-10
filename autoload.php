<?php

// Autoload
spl_autoload_register(function($class) {

    $ds = DIRECTORY_SEPARATOR;

    $file = __DIR__ . $ds . str_replace('\\', $ds, $class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Carrega o container.
require __DIR__ . '/Foundation/Container.php';

// Carrega os helpers
require __DIR__ . '/Foundation/Helpers.php';

