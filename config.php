<?php
define('NOME_SITE', 'Processo seletivo UP ESTATE - Daniel Viana');
define('DIR_BASE', 'http://daniel.provisorio.local');
define('URL_IMG_PUBLIC', DIR_BASE.'/public/app/assets/img/conteudo/');
define('PATH_IMG_PUBLIC', $_SERVER['DOCUMENT_ROOT'].'/public/app/assets/img/conteudo/');
define('PATH_PDF_PUBLIC', $_SERVER['DOCUMENT_ROOT'].'/public/app/assets/pdf/');

// Caminho dos arquivos de visão
Container::set('app.view.path', __DIR__ . '/App/Views/');

// URL da aplicação
Container::set('app.url', 'http://daniel.provisorio.local/public');

Container::set('app.db.config', [
    'sgdb' => 'mysql',
    'host' => 'db_daniel_up.mysql.dbaas.com.br',
    'database' => 'db_daniel_up',
    'user' => 'db_daniel_up',
    'password' => 'daniel.UP@2022'
]);

// Rotas
Container::set('app.routes', require __DIR__ . '/App/Routes.php');

// Classes
Container::set('base.database.db', function() {
    $data = array_values(Container::get('app.db.config'));
    return new Foundation\Database\Db(...$data);
});

Container::set('base.modules', ['App']);
Container::set('module.default', 'App');

Container::set('base.http.session', new Foundation\Http\Session());
Container::set('base.http.input', new Foundation\Http\Input());
Container::set('base.http.redirect', new Foundation\Http\Redirect());
Container::set('base.html.assets', new Foundation\Html\Assets());
Container::set('base.html.url', new Foundation\Html\Url());
Container::set('base.html.date', new Foundation\Html\Date());