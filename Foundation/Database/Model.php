<?php
namespace Foundation\Database;

use Container;

abstract class Model
{
    /**
     * @var Db
     */
    protected $db;

    private $sql;
    private $select= "SELECT ";
    private $from;
    private $where;
    private $join='';
    private $group;
    private $order;
    private $having;
    private $limit="";
    private $tuplas = array();
    private $distinct = '';

    public function __construct()
    {
        $this->db = Container::get('base.database.db');
    }

    protected abstract function getTableName();

    public function getById($id, array $fields = ['*'])
    {
        $fields = $this->parseFields($fields);

        $query = "SELECT $fields FROM {$this->getTableName()} WHERE myId = :myId";

        $where = [
            'myId' => $id
        ];

        return $this->db->select($query, $where, false);
    }

    public function getAll(array $fields = ['*'])
    {
        $fields = $this->parseFields($fields);

        $query = "SELECT $fields FROM {$this->getTableName()}";

        return $this->db->select($query, [], true);
    }

    public function insert(array $data = [])
    {
        return $this->db->insert($this->getTableName(), $data);
    }

    public function updateById($id, array $data = [])
    {
        $where = sprintf('myId = %s', (int) $id);

        return $this->db->update($this->getTableName(), $data, $where);
    }

    public function deleteById($id)
    {
        $where = sprintf('myId = %s', (int) $id);

        return $this->db->delete($this->getTableName(), $where);
    }

    protected function parseFields(array $fields = [])
    {
        return implode(',', $fields);
    }

    function __toString()
    {
        return $this->getSql();
    }

    public function select()
    {
        $this->tuplas = array();
        $this->join='';
        $this->select= "SELECT ";

        return $this;
    }

    public function limit($total, $offset=false)
    {
        $this->limit = " LIMIT $total";

        if($offset && $total)
            $this->limit .= ",$offset";

        return $this;
    }

    public function from($arr, $campos=false)
    {
        $alias = key($arr);
        $this->from = " from {$arr[$alias]} as {$alias} ";

        if($campos){
            if(is_array($campos)){
                foreach($campos as $nmtupla){
                    //$this->tuplas .= "{$nmtupla}, ";
                    $this->setTupla($nmtupla);
                }
            }else{
                //$this->tuplas .= $campos;
                $this->setTupla($campos);
            }
        }
        return $this;
    }

    public function joinInner($tabela,$on,$campos=false)
    {
        $alias = key($tabela);

        if($campos){
            if(is_array($campos)){
                foreach($campos as $nmtupla){
                    //$this->tuplas .= "{$nmtupla}, ";
                    $this->setTupla($nmtupla);
                }
            }else{
                //$this->tuplas .= $campos;
                $this->setTupla($campos);
            }
        }
        $this->join .=  " inner join   $tabela[$alias]  as {$alias} on {$on}";
        return $this;
    }

    public function joinLeft($tabela,$on,$campos=false)
    {
        $alias = key($tabela);

        if($campos){
            if(is_array($campos)){
                foreach($campos as $nmtupla){
                    //$this->tuplas .= "{$nmtupla}, ";
                    $this->setTupla($nmtupla);
                }
            }else{
                //$this->tuplas .= $campos;
                $this->setTupla($campos);
            }
        }
        $this->join .=  " left join   $tabela[$alias]  as {$alias} on {$on}";
        return $this;
    }

    public function joinRight($tabela,$on,$campos=false)
    {
        $alias = key($tabela);

        if($campos){
            if(is_array($campos)){
                foreach($campos as $nmtupla){
                    //$this->tuplas .= "{$nmtupla}, ";
                    $this->setTupla($nmtupla);
                }
            }else{
                //$this->tuplas .= $campos;
                $this->setTupla($campos);
            }
        }
        $this->join .=  " right join   $tabela[$alias]  as {$alias} on {$on}";
        return $this;
    }


    public function where($where)
    {
        if($where)
            $this->where =  " where $where";
        return $this;
    }

    public function having($having)
    {
        if($having)
            $this->having =  " having $having";
        return $this;
    }

    public function order($campos=false)
    {
        if($campos){
            $this->order = " order by ";
            if(is_array($campos)){
                foreach($campos as $nmtupla){
                    $this->order .= " {$nmtupla}, ";
                }
            }else{
                $this->order .= $campos;
            }
        }
        return $this;
    }

    public function group($campos=false)
    {
        if($campos){
            $this->group = " group by ";
            if(is_array($campos)){
                foreach($campos as $nmtupla){
                    $this->group .= "{$nmtupla}, ";
                }
                $this->group = substr($this->group,0, strlen($this->group)-2);
            }else{
                $this->group .= $campos;
            }
        }
        return $this;
    }

    private function setTupla($tupla)
    {
        if(!in_array($tupla, $this->tuplas)){
            array_push($this->tuplas, $tupla);
        }
    }

    private function getTuplas(){
        $saida = "";
        for($i=0; $i< count($this->tuplas); $i++){

            if($i == count($this->tuplas) - 1)
                $saida .= $this->tuplas[$i];
            else
                $saida .= $this->tuplas[$i].", ";
        }

        if(count($this->tuplas) == 0)
            return " * ";
        else
            return $saida;
    }

    public function distinct()
    {
        $this->distinct = " DISTINCT ";
        return $this;
    }

    public function getSql(){
        $sql  = $this->select;
        $sql .= $this->distinct;
        $sql .= $this->getTuplas();
        $sql .= $this->from;
        $sql .= $this->join;
        $sql .= $this->where;
        $sql .= $this->having;
        $sql .= $this->group;
        $sql .= $this->order;
        $sql .= $this->limit;

        $this->limit = null;
        $this->select = null;
        $this->from = null;
        $this->join = null;
        $this->where = null;
        $this->having = null;
        $this->group = null;
        $this->order = null;

        if(strpos($sql,"subQuery") !== false){
            $sql = str_replace("subQuery","",$sql);
        }
        return $sql;
    }

    protected function subQuery($sql){
        return "subQuery($sql)";
    }
}
