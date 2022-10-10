<?php
namespace App\Models;

use Foundation\Database\Model;

class Client extends Model {

    protected function getTableName() {
        return 'tb_client';
    }
}