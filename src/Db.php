<?php

namespace Sunqianhu\Helper;

use PDO;
use Exception;
use PDOStatement;

class Db
{
    static public $pdos = []; //连接池，本进程有效
    public $connectKey = '';
    public $config = [];

    /**
     * 构造函数
     * @param array $config
     * @throws Exception
     */
    public function __construct($config = [])
    {
        if (empty($config)) {
            $config = new Config();
            $config = $config->get('database');
        }
        if(empty($config)){
            throw new Exception('数据库配置不存在');
        }

        $this->config = $config;
        $this->connectKey = md5(json_encode($this->config));
        if (isset(self::$pdos[$this->connectKey])) {
            return;
        }
        $this->connect();
    }

    /**
     * 得到DSN
     * @return string
     * @throws Exception
     */
    public function getDsn()
    {
        $type = strtolower($this->config['type']);
        switch ($type) {
            case 'mysql':
                $dsn = "mysql:host={$this->config['host']};port={$this->config['port']};dbname={$this->config['dbname']};charset={$this->config['charset']}";
                break;
            case 'pgsql':
                $dsn = "pgsql:host={$this->config['host']};port={$this->config['port']};dbname={$this->config['dbname']};charset={$this->config['charset']}";
                break;
            case 'sqlsrv':
                $dsn = "sqlsrv:Server={$this->config['host']},{$this->config['port']};Database={$this->config['dbname']}";
                break;
            case 'sqlite':
                $dsn = "sqlite:{$this->config['dbname']}"; // dbname 是文件路径
                break;
            default:
                throw new Exception("Unsupported database type: {$type}");
        }
        return $dsn;
    }

    /**
     * 检测连接断开
     * @param $errorInfo
     * @return bool
     */
    private function checkConnectionGone($errorInfo)
    {
        $type = strtolower($this->config['type'] ?? 'mysql');
        switch ($type) {
            case 'mysql':
                return isset($errorInfo[1]) && in_array($errorInfo[1], [2006, 2013, 1053]);
            case 'pgsql':
                return isset($errorInfo[0]) && is_string($errorInfo[0]) && strpos($errorInfo[0], '08') === 0;
            case 'sqlsrv':
                return isset($errorInfo[1]) && in_array($errorInfo[1], [233, 10054, 10060, 4060]);
            case 'sqlite':
                return false;
            default:
                return false;
        }
    }

    /**
     * 连接
     * @return void
     * @throws Exception
     */
    public function connect()
    {
        $dsn = $this->getDsn();
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ];
        $pdo = new PDO($dsn, $this->config['username'], $this->config['password'], $options);
        self::$pdos[$this->connectKey] = $pdo;
    }

    /**
     * 重新连接
     */
    public function reconnect()
    {
        unset(self::$pdos[$this->connectKey]);
        $this->connect();
    }

    /**
     * 得到pdo对象
     * @throws Exception
     * @return PDO
     */
    public function getPdo()
    {
        return self::$pdos[$this->connectKey];
    }

    /**
     * 执行sql语句
     * @access public
     * @param string $sql sql
     * @param array $params 数据
     * @return PDOStatement PDOStatement对象
     * @throws Exception
     */
    public function execute($sql, $params = [])
    {
        $pdo = $this->getPdo();
        $pdoStatement = $pdo->prepare($sql);
        $this->bindParams($pdoStatement, $params);

        try {
            $result = $pdoStatement->execute();
            if ($result === false) {
                throw new Exception($this->getPdoStatementErrorInfo($pdoStatement));
            }
            return $pdoStatement;
        } catch (Exception $exception) {
            $errorInfo = $pdoStatement->errorInfo();
            $isGone = $this->checkConnectionGone($errorInfo);

            if ($this->config['break_reconnect'] && $isGone) {
                $this->reconnect();
                $pdo = $this->getPdo();
                $pdoStatement = $pdo->prepare($sql);
                $this->bindParams($pdoStatement, $params);

                $result = $pdoStatement->execute();
                if ($result === false) {
                    throw new Exception($this->getPdoStatementErrorInfo($pdoStatement));
                }
                return $pdoStatement;
            }

            throw $exception;
        }
    }

    /**
     * 绑定参数
     * @param $pdoStatement
     * @param $params
     * @return void
     */
    protected function bindParams($pdoStatement, $params = [])
    {
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $paramType = $value[1];
                if(!$paramType){
                    $paramType = $this->getParamType($value[0]);
                }
                $pdoStatement->bindValue($key, $value[0], $paramType);
            } else {
                $pdoStatement->bindValue($key, $value, $this->getParamType($value));
            }
        }
    }

    /**
     * 得到参数类型
     * @param $value
     * @return int
     */
    public function getParamType($value)
    {
        if(is_int($value)){
            return PDO::PARAM_INT;
        }
        if(is_bool($value)){
            return PDO::PARAM_BOOL;
        }
        if(is_null($value)){
            return PDO::PARAM_NULL;
        }
        return PDO::PARAM_STR;
    }

    /**
     * 得到查询条件的全部数据
     * @access public
     * @param PDOStatement $pdoStatement 结果集对象
     * @param integer $mode 返回内容格式
     * @return array
     */
    public function fetchAll($pdoStatement, $mode = PDO::FETCH_ASSOC)
    {
        return $pdoStatement->fetchAll($mode);
    }

    /**
     * 提取列名
     * @param $pdoStatement
     * @param $mode
     * @return mixed|null
     */
    public function fetch($pdoStatement, $mode = PDO::FETCH_ASSOC)
    {
        $row = $pdoStatement->fetch($mode);
        if ($row === false) {
            return null;
        }
        return $row;
    }

    /**
     * 从结果集中的下一行返回单独的一列
     * @access public
     * @param PDOStatement $pdoStatement 结果集对象
     * @param int $columnNumber
     * @return mixed
     * @throws Exception
     */
    public function fetchColumn($pdoStatement, $columnNumber = 0)
    {
        return $pdoStatement->fetchColumn($columnNumber);
    }

    /**
     * 查询一行
     * @param $sql
     * @param $params
     * @return mixed
     * @throws Exception
     */
    public function queryRow($sql, $params = [])
    {
        $pdoStatement = $this->execute($sql, $params);
        return $this->fetch($pdoStatement);
    }

    /**
     * 查询全部
     * @param $sql
     * @param $params
     * @return array
     * @throws Exception
     */
    public function queryAll($sql, $params = [])
    {
        $pdoStatement = $this->execute($sql, $params);
        return $this->fetchAll($pdoStatement);
    }

    /**
     * 查询一列
     * @param $sql
     * @param $params
     * @return mixed
     * @throws Exception
     */
    public function queryColumn($sql, $params = [])
    {
        $pdoStatement = $this->execute($sql, $params);
        return $this->fetchColumn($pdoStatement);
    }
    /**
     * 开启事务
     * @throws Exception
     */
    public function beginTransaction()
    {
        return $this->getPdo()->beginTransaction();
    }

    /**
     * 提交事务
     * @throws Exception
     */
    public function commit()
    {
        return $this->getPdo()->commit();
    }

    /**
     * 回滚事务
     * @throws Exception
     */
    public function rollBack()
    {
        return $this->getPdo()->rollBack();
    }

    /**
     * 返回影响的行数
     * @param $pdoStatement
     * @return mixed
     */
    public function getRowCount($pdoStatement)
    {
        return $pdoStatement->rowCount();
    }

    /**
     * 得到最后插入ID
     * @return int|null
     * @throws Exception
     */
    public function getLastInsertId($fieldName = null)
    {
        $pdo = $this->getPdo();
        $id = $pdo->lastInsertId($fieldName);
        if($id === '' || $id === false){
            return null;
        }
        return (int)$id;
    }

    /**
     * 获取PDOStatement错误信息
     * @param PDOStatement $pdoStatement
     * @return string
     */
    public function getPdoStatementErrorInfo(PDOStatement $pdoStatement)
    {
        $errorInfos = $pdoStatement->errorInfo();
        $convertedErrorInfos = [];
        $convertedErrorInfos[] = 'SQLSTATE 错误码：'.$errorInfos[0];
        $convertedErrorInfos[] = '驱动错误码：'.$errorInfos[1];
        $convertedErrorInfos[] = '驱动错误信息：'.$errorInfos[2];
        return implode('，', $convertedErrorInfos);
    }
}