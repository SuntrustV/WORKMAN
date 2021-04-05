<?php


namespace db;

use PDO;

include_once (__DIR__.'/dbInterface.php');

class pgSql implements dbInterface
{
    /**
     * @var PDO|null
     */
    private static $_connect = null;

    /**
     * @param array $config
     * @return dbInterface
     * @throws \Exception
     */
    public static function connect($config) {
        if(is_null(self::$_connect)){
            if(isset($config['host'],$config['dbname'],$config['user'],$config['password'])){
                $connect_string = "pgsql:host={$config['host']};dbname={$config['dbname']};user={$config['user']};password={$config['password']}";
                if(isset($config['port']) && !empty($config['port'])){
                    $connect_string .= ";port={$config['port']}";
                }
                self::$_connect = new PDO($connect_string);
            }else{
                throw new \Exception('Error config db!');
            }
        }

        return new self();
    }

    public function beginTransaction(){
        if(is_a(self::$_connect,PDO::class)){
            self::$_connect->beginTransaction();;
        }else{
            throw new \Exception('No active connection');
        }
    }

    public function commitTransaction(){
        if(is_a(self::$_connect,PDO::class)){
            self::$_connect->commit();

        }else{
            throw new \Exception('No active connection');
        }
    }

    public function rollbackTransaction(){
        if(is_a(self::$_connect,PDO::class)){
            self::$_connect->rollBack();
        }else{
            throw new \Exception('No active connection');
        }
    }

    /**
     * @param string $query
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public function query($query, $params = [], $style = PDO::FETCH_ASSOC) {
        if(is_a(self::$_connect,PDO::class)){
            $sth = self::$_connect->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            if($sth->execute($params)){
                return $sth->fetchAll($style);
            }else{
                throw new \Exception($sth->errorInfo()[2]);
            }

        }else{
            throw new \Exception('No active connection');
        }
    }

    public function select($query, $params) {
        // TODO: Implement select() method.
    }

    public function fetch($query, $params = [], $style = PDO::FETCH_ASSOC){
        if(is_a(self::$_connect,PDO::class)){
            $sth = self::$_connect->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            if($sth->execute($params)){
                return $sth->fetch($style);
            }else{
                throw new \Exception($sth->errorInfo()[2]);
            }

        }else{
            throw new \Exception('No active connection');
        }
    }

    public function insert($table,array $values, $keys = false){
        if(is_a(self::$_connect,PDO::class)){
            $sql = 'INSERT INTO '.$table;
            if(!empty($keys)){
                $sql .= ' ('.implode(', ', $keys).')';
            }

            $params_key = array_map(function ($item){ return ":{$item}";}, array_keys($values));
            $sql .= ' VALUES('.implode(',', $params_key).')';

            
            $sth = self::$_connect->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            if($sth->execute($values)){
                return self::$_connect->lastInsertId();
            }else{
                throw new \Exception($sth->errorInfo()[2]);
            }

        }else{
            throw new \Exception('No active connection');
        }
    }

    public function update($table,array $values, $where = false){
        if(is_a(self::$_connect,PDO::class)){
            $sql = 'UPDATE '.$table. ' SET ';

            $fields = [];
            foreach ($values as $key => $value){
                if(in_array($key, ['id'])){ continue; }
                if(!is_null($value)){
                    $fields[] = "$key = '$value'";
                }
            }

            $sql .= implode(', ', $fields);

            if(!empty($where)){
                $sql .= " WHERE $where";
            }elseif(isset($values['id'])){
                $sql .= " WHERE id = {$values['id']}";
            }

            $sth = self::$_connect->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            if($sth->execute()){
                return true;
            }else{
                throw new \Exception($sth->errorInfo()[2]);
            }

        }else{
            throw new \Exception('No active connection');
        }
    }

    public function getModelByQuery($select, $class){
        if(is_a(self::$_connect,PDO::class)){
            $sth = self::$_connect->prepare($select, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            if($sth->execute()){
                return $sth->fetchObject($class);
            }else{
                throw new \Exception($sth->errorInfo()[2]);
            }

        }else{
            throw new \Exception('No active connection');
        }
    }
}