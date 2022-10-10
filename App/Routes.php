<?php 
return [
    /*
     * Aqui pode-se documentar as páginas do sistema, para facilitar
     * a escrita dos redirecionamentos. Exemplo:
     *
     * return Redirect::route('index');
     *
     * Sintaxe:
     *
     * 'nome' => ['controlador', 'método']
     */

    'index' => ['client', 'index'],
    'client_form' => ['client', 'clientForm'],
    'client_save' => ['client', 'clientSave'],
    'deleteClient' => ['client', 'deleteClient'],

    'login.index' => ['login', 'index'],
    'login.validar' => ['login', 'validar'],
    'login.logout' => ['login', 'logout']
];
