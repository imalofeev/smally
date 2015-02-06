<?php
namespace Core\DB;
use Core\HTTP;

/**
 * Abstract class connecting to PostgreSQL
 */
abstract class DBPg
{
    protected static $connection;

    protected static $host;
    protected static $dbname;
    protected static $user;
    protected static $password;

    /**
     * Initialize connection
     */
    protected static function InitConnection()
    {
        $connectionString = 'host='     . static::$host .
                            ' dbname='   . static::$dbname .
                            ' user='     . static::$user .
                            ' password=' . static::$password;

        $connection = pg_connect($connectionString, PGSQL_CONNECT_FORCE_NEW);

        if (false === $connection) {
            static::MaintenanceBanner();
        } else {
            static::$connection = $connection;

            // if you need to set any connection params (time zone, client_encoding...), set them here

        }
    }

    /**
     * Render mintenance banner
     */
    public static function MaintenanceBanner()
    {
        HTTP::ServiceUnavailable503();
        exit();
    }

    /**
     * Execute query
     *
     * @param string $query
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public static function Execute($query)
    {
        global $debug;
         
        if (!trim($query)) {
            throw new \Exception('SQL-query is not defined');
        } else {
            if (!isset(static::$connection)) {
                static::InitConnection();
            }
            	
            if ($debug) {
                $GLOBALS['debug_db_queries_count']++;
                $debugQueryStart = microtime(true);
            }

            $result = pg_query(static::$connection, $query);
            	
            if ($debug) {
                $debugQueryTime = microtime(true) - $debugQueryStart;
                if ($debugQueryTime > $GLOBALS['debug_heaviest_query_time']) {
                    $GLOBALS['debug_heaviest_query_time'] = $debugQueryTime;
                    $GLOBALS['debug_heaviest_query'] = $query;
                }
            }
            
            if ($result === false) {
                throw new \Exception('PostgreSQL error: ' . pg_last_error(static::$connection));
            } else {
                return true;
            }
        }
    }

    /**
     * Insert into DB
     *
     * @param string $tableName
     * @param array  $data array(field => value)
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public static function Insert($tableName, $data = array())
    {
        if (!trim($tableName)) {
            throw new \Exception('Table name is not defined');
        } else {
            try {
                $sql = static::buildInsertQuery($tableName, $data);
                $result = static::Execute($sql);  // do not pg_insert, it's not return error
                return $result;
            } catch (\Exception $e) {
                throw new \Exception('PostgreSQL error: ' . pg_last_error(static::$connection));
            }
        }
    }

    /**
     * Update DB
     *
     * @param string $tableName
     * @param array  $data array(field => value)
     * @param array  $condition  array(field => value)
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public static function Update($tableName, $data, $condition = array())
    {
        if (!trim($tableName)) {
            throw new \Exception('Table name is not defined');
        } else {
            if (is_array($condition)) {
                try {
                    $sql = static::buildUpdateQuery($tableName, $data, $condition);
                    $result = static::Execute($sql);
                    return $result;
                } catch (\Exception $e) {
                    throw new \Exception('PostgreSQL error: ' . pg_last_error(static::$connection));
                }
            } else {
                throw new \Exception('Condition is not correct');
            }
        }
    }

    /**
     * Delete from DB
     *
     * @param string $tableName
     * @param array $condition array(field => value)
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public static function Delete($tableName, $condition)
    {
        if (!trim($tableName)) {
            throw new \Exception('Table name is not defined');
        } else {
            if (is_array($condition)) {
                try {
                    $sql = static::buildDeleteQuery($tableName, $condition);
                    $result = static::Execute($sql);
                    return $result;
                } catch (\Exception $e) {
                    throw new \Exception('PostgreSQL error: ' . pg_last_error(static::$connection));
                }
            } else {
                throw new \Exception('Condition is not correct');
            }
        }
    }

    /**
     * Return SQL-query for insert into DB
     *
     * @param string $tableName
     * @param array  $data array(field => value)
     *
     * @return mixed
     */
    public static function buildInsertQuery($tableName, $data)
    {
        foreach ($data as $fieldName => $fieldValue) {
            $fieldNames[]  = static::formatRegclass($fieldName);
            $fieldValues[] = static::formatValue($fieldValue);
        }

        if ($fieldNames) {
            $queryNames =  '(' . implode(', ', $fieldNames) . ')';
            $queryValues = '(' . implode(', ', $fieldValues) . ')';
            $sql = "INSERT INTO " . static::formatRegclassTablename($tableName) . $queryNames . "\n VALUES " . $queryValues;
        } else {
            $sql = "INSERT INTO " . static::formatRegclassTablename($tableName) . " DEFAULT VALUES";
        }

        return $sql;
    }

    /**
     * Return SQL-query for update DB
     *
     * @param string $tableName
     * @param array $data array(field => value)
     * @param array $condition array(field => value)
     *
     * @return mixed
     */
    public static function buildUpdateQuery($tableName, $data, $condition = array())
    {
        if (count($data)) {
            foreach ($data as $fieldName => $fieldValue) {
                $querySets[] = static::formatRegclass($fieldName) . " = " . static::formatValue($fieldValue);
            }

            $sql = "UPDATE " . static::formatRegclassTablename($tableName) . "\n SET " . implode(', ', $querySets);

            if (count($condition)) {
                foreach ($condition as $fieldName => $fieldValue) {
                    $queryConditions[] = static::formatRegclass($fieldName) . " = " . static::formatValue($fieldValue);
                }
                $sql .= "\n WHERE " . implode("\n  AND ", $queryConditions);
            }
        }

        return $sql;
    }

    /**
     * Return SQL-query for delete from DB
     *
     * @param string $tableName
     * @param array $condition array(field => value)
     *
     * @return mixed
     */
    public static function buildDeleteQuery($tableName, $condition)
    {
        if (count($condition)) {
            foreach ($condition as $fieldName => $fieldValue) {
                $queryConditions[] = static::formatRegclass($fieldName) . " = " . static::formatValue($fieldValue);
            }

            $sql = 'DELETE FROM ' . static::formatRegclassTablename($tableName) . "\n WHERE " . implode("\n  AND ", $queryConditions);
        }

        return $sql;
    }

    /**
     * Frames Regclass
     *
     * @param string $name
     *
     * @return string
     */
    public static function formatRegclass($name)
    {
        return '"' . addcslashes($name, '"') . '"';
    }

    /**
     * Frames RegclassTablename
     *
     * @param string $name
     *
     * @return string
     */
    public static function formatRegclassTablename($name)
    {
        if (strpos($name, '.')) {
            list($scheme, $tableName) = explode('.', $name, 2);
            return static::formatRegclass($scheme) . '.' . static::formatRegclass($tableName); // "scheme"."table_name"
        } else {
            return static::formatRegclass($name); // "table_name"
        }
    }

    /**
     * Prepare value for DB
     *
     * @param mixed $value PHP value
     *
     * @return string
     */
    public static function formatValue($value)
    {
        if (is_bool($value)) {
            if (true === $value) {
                return 'true';
            } else {
                return 'false';
            }
            	
        } elseif (is_null($value)) {
            return 'null';
            	
        } else {
            $value = str_replace("'", "''", $value);
            return "'" . $value . "'";
        }
    }

    /**
     * Return sql-query result in rows
     *
     * @param string $query SQL-query
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function Rows($query)
    {
        global $debug;

        if (!isset(static::$connection)) {
            static::InitConnection();
        }

        if ($debug) {
            $GLOBALS['debug_db_queries_count']++;
            $debugQueryStart = microtime(true);
        }
        
        $result = pg_query(static::$connection, $query);

        if ($debug) {
            $debugQueryTime = microtime(true) - $debugQueryStart;
            if ($debugQueryTime > $GLOBALS['debug_heaviest_query_time']) {
                $GLOBALS['debug_heaviest_query_time'] = $debugQueryTime;
                $GLOBALS['debug_heaviest_query'] = $query;
            }
        }

        if (false === $result) {
            throw new \Exception(pg_last_error(static::$connection));
        } else {
            return pg_fetch_all($result);
        }
    }
    
    /**
     * Return sql-query result in row
     *
     * @param string $query SQL-query
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function Row($query)
    {
        global $debug;
         
        if (!isset(static::$connection)) {
            static::InitConnection();
        }

        if ($debug) {
            $GLOBALS['debug_db_queries_count']++;
            $debugQueryStart = microtime(true);
        }

        $result = pg_query(static::$connection, $query);

        if ($debug) {
            $debugQueryTime = microtime(true) - $debugQueryStart;
            if ($debugQueryTime > $GLOBALS['debug_heaviest_query_time']) {
                $GLOBALS['debug_heaviest_query_time'] = $debugQueryTime;
                $GLOBALS['debug_heaviest_query'] = $query;
            }
        }

        if (false === $result) {
            throw new \Exception(pg_last_error(static::$connection));
        } else {
            return pg_fetch_assoc($result);
        }
    }


    /**
     * Return sql-query result in column
     *
     * @param string $query SQL-query
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function Column($query)
    {
        global $debug;

        if(!isset(static::$connection)) {
            static::InitConnection();
        }

        if ($debug) {
            $GLOBALS['debug_db_queries_count']++;
            $debugQueryStart = microtime(true);
        }

        $result = pg_query(static::$connection, $query);

        if ($debug) {
            $debugQueryTime = microtime(true) - $debugQueryStart;
            if ($debugQueryTime > $GLOBALS['debug_heaviest_query_time']) {
                $GLOBALS['debug_heaviest_query_time'] = $debugQueryTime;
                $GLOBALS['debug_heaviest_query'] = $query;
            }
        }

        if (false === $result) {
            throw new \Exception(pg_last_error(static::$connection));
        } else {
            return pg_fetch_all_columns($result, 0);
        }
    }

    /**
     * Return sql-query result in value
     *
     * @param string $query SQL-query
     *
     * @return string
     *
     * @throws \Exception
     */
    public static function Value($query)
    {
        global $debug;

        if(!isset(static::$connection)) {
            static::InitConnection();
        }

        if ($debug) {
            $GLOBALS['debug_db_queries_count']++;
            $debugQueryStart = microtime(true);
        }

        $result = pg_query(static::$connection, $query);

        if ($debug) {
            $debugQueryTime = microtime(true) - $debugQueryStart;
            if ($debugQueryTime > $GLOBALS['debug_heaviest_query_time']) {
                $GLOBALS['debug_heaviest_query_time'] = $debugQueryTime;
                $GLOBALS['debug_heaviest_query'] = $query;
            }
        }

        if (false === $result) {
            throw new \Exception(pg_last_error(static::$connection));
        } else {
            if (pg_num_rows($result)) {
                $result = pg_fetch_result($result, 0, 0);
                return $result;
            } else {
                return false;
            }
        }
    }
}

