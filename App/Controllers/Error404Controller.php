<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Controllers;
use Foundation\Controller;

/**
 * Description of 404Controller
 *
 * @author cfinelli
 */
class Error404Controller extends Controller {
    
    public function index() {
        return $this->render("404/index");
    }
}
