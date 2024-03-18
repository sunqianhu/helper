<?php

namespace sunqianhu\helper;

class Db
{
    static public $pdo;
    public $type = 'mysql';
    public $host = '127.0.0.1';
    public $port = '3306';
    public $dbname = 'test';
    public $username = 'root';
    public $password = '';
    public $charset = 'utf8mb4';

    /**
     * 构造函数
     * @param $config
     */
    public function __construct($config = [])
    {
        if (isset($config['type'])) {
            $this->type = $config['type'];
        }
        if (isset($config['host'])) {
            $this->host = $config['host'];
        }
        if (isset($config['port'])) {
            $this->port = $config['port'];
        }
        if (isset($config['dbname'])) {
            $this->dbname = $config['dbname'];
        }
        if (isset($config['username'])) {
            $this->username = $config['username'];
        }
        if (isset($config['password'])) {
            $this->password = $config['password'];
        }
        if (isset($config['charset'])) {
            $this->charset = $config['charset'];
        }

        $this->connect();
    }

    /**
     * 连接
     * @return void
     */
    public function connect()
    {
        if (self::$pdo) {
            return;
        }

        $dsn = $this->type .
            ':host=' . $this->host .
            ';port=' . $this->port .
            ';dbname=' . $this->dbname .
            ';charset=' . $this->charset;
        $pdo = new \PDO($dsn, $this->username, $this->password);
        self::$pdo = $pdo;
    }

    /**
     * 执行sql语句
     * @access public
     * @param PDO $pdo pdo对象
     * @param string $sql sql
     * @param array $data 数据
     * @return PDOStatement PDOStatement对象
     */
    public function execute($sql, $data = array())
    {
        if (empty($sql)) {
            throw new Exception('sql不能为空');
        }

        $pdo = self::$pdo;
        $pdoStatement = $pdo->prepare($sql);
        if ($pdoStatement === false) {
            $error = $this->getPdoError();
            throw new Exception($error);
        }
        foreach ($data as $param => $value) {
            if (is_array($value) && count($value) > 1) {
                $pdoStatement->bindValue($param, $value[0], $value[1]);
            } else {
                $pdoStatement->bindValue($param, $value);
            }
        }
        if (!$pdoStatement->execute()) {
            $error = $this->getPodStatementError($pdoStatement);
            throw new Exception($error);
        }

        return $pdoStatement;
    }

    /**
     * 得到查询条件的全部数据
     * @access public
     * @param PDO $pdoStatement 结果集对象
     * @param integer $type 返回内容格式
     * @return array
     */
    public function fetchAll($pdoStatement, $type = \PDO::FETCH_ASSOC)
    {
        $datas = array();

        $datas = $pdoStatement->fetchAll($type);
        if (empty($datas)) {
            return array();
        }

        return $datas;
    }

    /**
     * 从结果集中获取下一行
     * @access public
     * @param PDO $pdoStatement 结果集对象
     * @param integer $type 返回内容格式
     * @return array
     */
    public function fetch($pdoStatement, $type = \PDO::FETCH_ASSOC)
    {
        $data = array();

        $data = $pdoStatement->fetch($type);
        if (empty($data)) {
            return array();
        }

        return $data;
    }

    /**
     * 从结果集中的下一行返回单独的一列
     * @access public
     * @param PDO $pdoStatement 结果集对象
     * @param array $data 数据
     * @return string
     */
    public function fetchColumn($pdoStatement, $columnNumber = 0)
    {
        $field = '';

        $field = $pdoStatement->fetchColumn($columnNumber);
        if ($field === false) {
            return '';
        }

        return $field;
    }

    /**
     * 得到pdo错误描述
     * @param PDO $pdo pdo对象
     * @return string 错误描述
     */
    public function getPdoError()
    {
        $pdo = self::$pdo;
        $errors = array();
        $error = '';

        $errors = $pdo->errorInfo();
        if (!empty($errors[0])) {
            $error .= 'SQLSTATE[' . $errors[0] . ']';
        }
        if (!empty($errors[1])) {
            $error .= '，驱动错误代码：' . $errors[1];
        }
        if (!empty($errors[2])) {
            $error .= '，驱动错误描述：' . $errors[2];
        }

        return $error;
    }

    /**
     * 得到预处理结果对象错误描述
     * @param PDOStatement $pdoStatement 结果集对象
     * @return string 错误描述
     */
    public function getPodStatementError($pdoStatement)
    {
        $errors = array();
        $error = '';

        if (!$pdoStatement) {
            $error = 'pdostatement对象为false';
            return $error;
        }

        $errors = $pdoStatement->errorInfo();
        if (!empty($errors[0])) {
            $error .= 'SQLSTATE[' . $errors[0] . ']';
        }
        if (!empty($errors[1])) {
            $error .= '，驱动错误代码：' . $errors[1];
        }
        if (!empty($errors[2])) {
            $error .= '，驱动错误描述：' . $errors[2];
        }

        return $error;
    }
}