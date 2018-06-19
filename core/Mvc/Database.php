<?php

namespace Simple\Mvc;

use Simple\DI\InjectionInterface;
use Simple\Exception;

/**
 * Class Database
 * @package Simple\Mvc
 */
class Database implements InjectionInterface
{
    protected $di;

    protected $query;

    /**
     * @var \mysqli
     */
    protected $connect = null;

    /**
     * @param \Simple\DI\DefaultFactory $di
     * @return Database
     */
    public function setDi($di)
    {
        $this->di = $di;

        return $this->connect();
    }

    /**
     * @return \Simple\DI\DefaultFactory
     */
    public function getDi()
    {
        return $this->di;
    }

    /**
     * Creating a connection
     *
     * @return $this
     */
    public function connect()
    {
        $config = $this->getDi()->getService('config');

        $this->connect = new \mysqli(
            $config->database->host,
            $config->database->username,
            $config->database->password,
            $config->database->dbname
        );
        if ($this->connect->connect_errno) {
            throw new Exception('Couldn\'t connect. Number error: ' . $this->connect->connect_errno . '. Error: ' . $this->connect->connect_error);
        }
        $this->connect->set_charset($config->database->charset);

        return $this;
    }

    /**
     * Return query
     *
     * @param $sql
     * @return bool|\mysqli_result
     */
    public function query($sql)
    {
        $this->query = $this->connect->query($sql);
        if (!$this->query) {
            throw new Exception('Error in query. Request: ' . $sql . '. Number error: ' . $this->connect->errno . '. Error: ' . $this->connect->error);
        }

        return $this->query;
    }

    /**
     * Get count rows
     *
     * @param $query
     * @return mixed
     */
    public function count($query)
    {
        return $query->num_rows;
    }

    /**
     * Get all rows
     *
     * @param $query
     * @param $classModel
     * @return array|bool
     */
    public function getAll($query, $classModel)
    {
        $s_model = get_class($classModel);
        $arrayRows = [];

        while ($rows = $query->fetch_object()) {
            $model = new $s_model();
            foreach ($rows as $key => $item) {
                $model->{$key} = $item;
            }
            $arrayRows[] = $model;
        }

        return (!empty($arrayRows)) ? $arrayRows : false;
    }

    /**
     * Get first row
     *
     * @param $query
     * @param $classModel
     * @return array|bool
     */
    public function getFirst($query, $classModel)
    {
        $result = $query->fetch_object();
        if (!$result) {
            return false;
        }

        $s_model = get_class($classModel);
        $model = new $s_model();

        foreach ($result as $key => $item) {
            $model->{$key} = $item;
        }

        return $model;
    }

    /**
     * Get last insert id
     *
     * @return mixed
     */
    public function getInsertId()
    {
        return $this->connect->insert_id;
    }
}