<?php


namespace db;


interface dbInterface
{
    public static function connect($config);

    public function select($query, $params);

    public function beginTransaction();
    public function commitTransaction();
    public function rollbackTransaction();

    public function insert($table,array $values, $keys = false);

    public function query($query, $params = []);

    public function fetch($query);

    //public function update($table,array $values, $where);

}