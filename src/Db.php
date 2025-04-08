<?php

namespace Sunqianhu\Helper;

use PDO;
use Exception;

/**
 * 数据库助手类
 */
class Db
{
    static public $pdos = [];
    public $id = '';
    public $type = '';
    public $host = '';
    public $port = '';
    public $dbname = '';
    public $username = '';
    public $password = '';
    public $charset = '';

    /**
     * 构造函数
     * @param $config
     */
    public function __construct($config = [])
    {
        if (empty($config)) {
            $config = new Config();
            $config = $config->get('databases');
        }
        $this->id = md5(implode('|', $config));

        $this->type = $config['type'];
        $this->host = $config['host'];
        $this->port = $config['port'];
        $this->dbname = $config['dbname'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->charset = $config['charset'];

        $this->connect();
    }

    /**
     * 连接
     * @return void
     */
    public function connect()
    {
        $dsn = $this->type .
            ':host=' . $this->host .
            ';port=' . $this->port .
            ';dbname=' . $this->dbname .
            ';charset=' . $this->charset;
        $pdo = new PDO($dsn, $this->username, $this->password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false, //关闭模拟预处理
            PDO::ATTR_STRINGIFY_FETCHES => false //禁止将数值转换为字符串
        ]);
        self::$pdos[$this->id] = $pdo;
    }

    /**
     * 得到pdo对象
     * @throws Exception
     */
    public function getPdo()
    {
        if (!isset(self::$pdos[$this->id])) {
            throw new Exception('pdo对象不存在');
        }
        return self::$pdos[$this->id];
    }

    /**
     * 执行sql语句
     * @access public
     * @param PDO $pdo pdo对象
     * @param string $sql sql
     * @param array $data 数据
     * @return PDOStatement PDOStatement对象
     * @throws Exception
     */
    public function execute($sql, $data = array())
    {
        if (empty($sql)) {
            throw new Exception('sql不能为空');
        }

        $pdo = $this->getPdo();
        $pdoStatement = $pdo->prepare($sql);
        if ($pdoStatement === false) {
            $error = $this->getPdoError();
            throw new Exception($error);
        }
        foreach ($data as $param => $value) {
            if (is_array($value)) {
                if (count($value) == 1) {
                    $pdoStatement->bindValue($param, $value[0]);
                }else{
                    $pdoStatement->bindValue($param, $value[0], $value[1]);
                }
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
    public function fetchAll($pdoStatement, $type = PDO::FETCH_ASSOC)
    {
        $list = $pdoStatement->fetchAll($type);
        if (empty($list)) {
            return [];
        }
        return $list;
    }

    /**
     * 从结果集中获取下一行
     * @access public
     * @param PDO $pdoStatement 结果集对象
     * @param integer $type 返回内容格式
     * @return array
     */
    public function fetch($pdoStatement, $type = PDO::FETCH_ASSOC)
    {
        $row = $pdoStatement->fetch($type);
        if (empty($row)) {
            return [];
        }
        return $row;
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
        $pdo = $this->getPdo();
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