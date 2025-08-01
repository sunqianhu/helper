<?php

namespace Sunqianhu\Helper;

use PDO;
use Exception;
use PDOStatement;

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
     * @param array $config
     * @throws Exception
     */
    public function __construct($config = [])
    {
        if (empty($config)) {
            $config = Config::get('databases');
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
     * @return PDO
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
     * @param string $sql sql
     * @param array $data 数据
     * @return PDOStatement PDOStatement对象
     * @throws Exception
     */
    public function execute($sql, $data = [])
    {
        if (empty($sql)) {
            throw new Exception('sql不能为空');
        }

        $pdo = $this->getPdo();
        $pdoStatement = $pdo->prepare($sql);
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
        $pdoStatement->execute();
        return $pdoStatement;
    }

    /**
     * 得到查询条件的全部数据
     * @access public
     * @param PDOStatement $pdoStatement 结果集对象
     * @param integer $type 返回内容格式
     * @return array
     */
    public function fetchAll($pdoStatement, $type = PDO::FETCH_ASSOC)
    {
        return $pdoStatement->fetchAll($type);
    }

    /**
     * 提取列名数组
     * @access public
     * @param PDOStatement $pdoStatement 结果集对象
     * @return array
     * @throws Exception
     */
    public function fetch($pdoStatement)
    {
        $row = $pdoStatement->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            throw new Exception('没有找到记录');
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
        $field = $pdoStatement->fetchColumn($columnNumber);
        if($field === false){
            throw new Exception('没有找到记录');
        }
        return $field;
    }
}