<?php
namespace App\Controllers;

use Foundation\Controller;
use App\Models\User;

class LoginController extends Controller
{
    protected $login;
    private $login_user;
    private $login_pass;
    private $login_pass_sha;
    
    public function __construct() {
        $this->login = new User;
    }
    
    public function validar() {

        $this->login_user = input()->get('login_user');
        $this->login_pass = input()->get('login_pass');

        $this->login_pass_sha = hash('sha256', $this->login_user.$this->login_pass);
        
        $dados_login = $this->login->getUserAtivo($this->login_user, $this->login_pass_sha);
        
        if ($dados_login){
            session()->put('_user_nome', $dados_login[0]->name);
            session()->put('_user_id', $dados_login[0]->id);
            session()->put('_user_status', true);
            session()->put('_erro_login', false);

            return redirect()->route('index');

        } else {
            session()->put('_user_status', false);
            session()->put('_erro_login', true);
            return redirect()->route('login.index');            
        }
    }
    
    public function logout(){
        session()->forget('_user_nome');
        session()->forget('_user_id');
        session()->forget('_user_id_unidade');
        session()->forget('_user_status');

        session()->put('_status_logout', true);

        return redirect()->route('login.index');
    }

    public function index(){        
        return $this->render('login/index');
    }
}