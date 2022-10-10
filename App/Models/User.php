<?php
namespace App\Models;

use Foundation\Database\Model;

class User extends Model
{
    public function getUserAtivo($login, $senha){
        $sql = "SELECT * FROM tb_user WHERE  user = :login_user and password = :senha_user and ativo = 1 LIMIT 1";
        $where = ['login_user' => $login, 'senha_user' => $senha];
        return $this->db->select($sql, $where);
    }

    protected function getTableName() {
        return 'tb_user';
    }
}