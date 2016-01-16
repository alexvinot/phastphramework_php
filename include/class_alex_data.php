<?php
class alex_data {
    public 
        $host = 'localhost',
        $user = 'user',
        $pass = 'pass',
        $db,
        $currdb = 'sd',
        $dbs  = array(
            'db1' => 'db1_mysql',
            'db2' => 'db2_mysql'
        ),
        $last_sql = '',
        $field_names = array(),
        $num_fields  = 0,
        $last_insert_id = 0
    ;

    public function __construct() 
    {
        $this->currdb = $this->dbs['db1'];
    }
    
    public function db_connect() 
    {
        try {
            $this->db = mysqli_connect ($this->host, $this->user, $this->pass, $this->currdb);
            mysqli_select_db ($this->db, $this->currdb);
        }
        catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }
    
    private function check_sql($db) 
    {
        if ($db && isset($this->dbs[$db]) && $this->currdb != $this->dbs[$db]) {
            $this->currdb = $this->dbs[$db];
            mysqli_select_db ($this->db, $this->currdb);
        }
    }
    
    public function select($sql = '', $binds = array(), $db = '', $key = '', $one_dimension = '') 
    {
        $result = array();
        $this->check_sql($db);
        if (!empty($binds)) { // pdo will replace this
            foreach ($binds as $k => $v) {
                $sql = str_replace(':'.$k, $v, $sql);
            }
        }
        $sql = trim($sql);
        if ($sql) {
            $this->last_sql = $sql;
            try {
                $rows = mysqli_query($this->db, $sql);
                if ($rows && !empty($key)) {
                    $valid_row = mysqli_fetch_assoc($rows);
                    if (isset($valid_row[$key])) {
                        if (!empty($one_dimension)) {
                            $result[$valid_row[$key]] = $valid_row[$one_dimension];
                            while ($valid_row = mysqli_fetch_assoc($rows)) {
                                $result[$valid_row[$key]] = $valid_row[$one_dimension];
                            }
                        } else {
                            $result[$valid_row[$key]] = $valid_row;
                            while ($valid_row = mysqli_fetch_assoc($rows)) {
                                $result[$valid_row[$key]] = $valid_row;
                            }
                        }
                    }
                    else { // sorry no go on the key supplied
                        $result[] = $valid_row;
                        $key = '';
                    }
                }
                
                if ($rows && empty($key)) {
                    while ($valid_row = mysqli_fetch_assoc($rows)) {
                        $result[] = $valid_row;
                    }
                }
                if (!empty($result) && !empty($result[0]) ) {
                    $this->field_names = array_keys(current($result));
                    $this->num_fields  = count($this->field_names);
                } else {
                    $this->field_names = array('dummy');
                    $this->num_fields = 0;
                }
            }
            catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
        }
        return $result;
    }
    
    public function selectone($sql = '', $binds = array(), $db = '', $onlyfield = 0, $default_val = false) 
    {
        $result = $default_val;
        $this->check_sql($db);
        if (!empty($binds)) { // pdo will replace this
            foreach ($binds as $k => $v) {
                $sql = str_replace(':'.$k, $v, $sql);
            }
        }
        $sql = trim(trim($sql), ';') . ' LIMIT 1;';

        $this->last_sql = $sql;
        try {
            $rows = mysqli_query($this->db, $sql);
            if ($rows) {
                $result = mysqli_fetch_assoc($rows);
                if (empty($onlyfield) && !empty($result)){
                    $this->field_names = array_keys(current($result));
                    $this->num_fields  = count($this->field_names);
                } else if (isset($result[$onlyfield]) && !empty($result[$onlyfield])) {
                    $result = $result[$onlyfield];
                } else {
                    $result = $default_val;
                }
            }
        }
        catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        return $result;
    }
    
    public function upsert($sql = '', $binds = array(), $db = '') 
    {
        $result = 0;
        $this->check_sql($db); 
        if (!empty($binds)) { // pdo will replace this
            foreach ($binds as $k => $v) {
                $sql = str_replace(':'.$k, $v, $sql);
            }
        }
        $sql = trim($sql);
        if ($sql) {
            $this->last_sql = $sql;
            try {
                if (mysqli_query($this->db, $sql)) {
                    $this->last_insert_id = mysqli_insert_id ( $this->db );
                    $result = $this->last_insert_id;
                    /*
                    will only get id if it's an insert, not for updates, for these integrate this :
                    $sqlQuery = "UPDATE 
                        update_table 
                    SET 
                        set_name = 'value' 
                    WHERE 
                        where_name = 'name'
                    LIMIT 1;";
                    function updateAndGetId($sqlQuery)
                    {
                        mysql_query(str_replace("SET", "SET id = LAST_INSERT_ID(id),", $sqlQuery));
                        return mysql_insert_id();
                    }
                    */
                }
            }
            catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
        }
        return $result;
    }
    
    public function query($sql = '', $binds = array(), $db = '') 
    {
        $result = 0;
        $this->check_sql($db); 
        if (!empty($binds)) { // pdo will replace this
            foreach ($binds as $k => $v) {
                $sql = str_replace(':'.$k, $v, $sql);
            }
        }
        $sql = trim($sql);
        if ($sql) {
            $this->last_sql = $sql;
            try {
                $result = mysqli_query($this->db, $sql);
            }
            catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
        }
        return $result;
    }
    
    public function sql_insert($params) 
    {
        $result = 0;
        if (is_array($params) && isset($params['db']) && isset($params['table']) && is_array($params['cols'])) {
            $sql = "INSERT INTO {$params['table']} SET ";
            $binds = array();
            $sql_tmp = array();
            foreach ($params['cols'] as $k => $v) {
                $uk = strtoupper($k);
                $sql_tmp[]  = "{$k} =':{$uk}'";
                $binds[$uk] = $v;
            }
            $sql .= implode(',', $sql_tmp);
            $result = $this->upsert($sql, $binds, $params['db']);
        }
        return $result;
    }
    
}  

