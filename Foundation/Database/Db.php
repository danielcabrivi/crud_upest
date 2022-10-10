<?php
namespace Foundation\Database;

use PDO;

class Db extends PDO
{
    public function __construct($sgdb, $host, $db, $user, $password, $persistent = false)
    {
        $options = [
            PDO::ATTR_PERSISTENT => $persistent
        ];

        $dns = sprintf('%s:host=%s;dbname=%s;charset=utf8;', $sgdb, $host, $db);
        parent::__construct($dns, $user, $password, $options);
    }

    protected function bindValues(&$sth, $data = [])
    {
        // Define os dados do where, se existirem.
        foreach ($data as $key => $value) {
            // Se o tipo do dado for inteiro, usa PDO::PARAM_INT, caso contrário, PDO::PARAM_STR
            $tipo = ( is_int($value) ) ? PDO::PARAM_INT : PDO::PARAM_STR;
            // Define o dado
            $sth->bindValue(":$key", $value, $tipo);
        }
    }

    public function select($sql, array $where = [], $all = TRUE, $fetchMode = PDO::FETCH_OBJ)
    {
        // Prepara a Query
        $sth = $this->prepare($sql);

        // Define os dados do where, se existirem.
        $this->bindValues($sth, $where);

        // Executa
        $sth->execute();

        // Executar fetchAll() ou fetch()?
        if ($all) {
            // Retorna a coleção de dados (array multidimensional)
            return $sth->fetchAll($fetchMode);
        }

        // Retorna apenas um dado
        return $sth->fetch($fetchMode);
    }

    public function insert($table, $data)
    {
        // Ordena
        ksort($data);

        // Campos e valores
        $camposNomes = implode('`, `', array_keys($data));
        $camposValores = ':' . implode(', :', array_keys($data));

        // Prepara a Query
        $sql = sprintf('INSERT INTO %s (`%s`) VALUES (%s)', $table, $camposNomes, $camposValores);
        $sth = $this->prepare($sql);

        // Define os dados do where, se existirem.
        $this->bindValues($sth, $data);

        // Executa
        $sth->execute();

        // Retorna o id do item inserido.
        return $this->lastInsertId();
    }

    public function update($table, $data = [], $where = "")
    {
        // Ordena
        ksort($data);

        // Define os dados que serão atualizados
        $novosDados = NULL;

        foreach ($data as $key => $value) {
            $novosDados .= "`$key`= :$key,";
        }

        $novosDados = rtrim($novosDados, ',');

        // Prepara a Query
        $sql = sprintf('UPDATE %s SET %s WHERE %s', $table, $novosDados, $where);
        $sth = $this->prepare($sql);

        // Define os dados
        $this->bindValues($sth, $data);

        // Sucesso ou falha?
        return $sth->execute();
    }

    public function delete($table, $where, $limit = 1)
    {
        $sql = sprintf('DELETE FROM %s WHERE %s LIMIT %s', $table, $where, $limit);
        return $this->exec($sql);
    }
}
