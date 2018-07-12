<?php

namespace Simple\Mvc;

use Simple\Di;
use Simple\DI\InjectionInterface;
use Simple\Mvc\Model\QueryBuilder;

class Model implements InjectionInterface
{
    private static $queryBuilder;

    /**
     * @var \Simple\DI\DefaultFactory
     */
    protected $di;

    /**
     * @var Database
     */
    protected $db;

    protected static $classModel;

    /**
     * Model constructor.
     */
    public function __construct()
    {
        $this->di = Di::getDefault();
        $this->db = $this->di->getService('db');
    }

    /**
     * @param \Simple\DI\DefaultFactory $di
     */
    public function setDi($di)
    {
        $this->di = $di;
    }

    /**
     * @return \Simple\DI\DefaultFactory
     */
    public function getDi()
    {
        return $this->di;
    }

    /**
     * Ger all results
     *
     * @param $parameters
     * @return Model
     */
    public static function find($parameters)
    {
        $class = get_called_class();
        self::$classModel = new $class();

        $queryBuilder = new QueryBuilder();
        $queryBuilder->select((!empty($parameters['columns'])) ? $parameters['columns'] : '*');
        $queryBuilder->from(self::$classModel->getSource());
        if (!empty($parameters['conditions'])) {
            $queryBuilder->where($parameters['conditions'], (!empty($parameters['bind'])) ? $parameters['bind'] : []);
        }
        if (!empty($parameters['limit'])) {
            $queryBuilder->limit($parameters['limit']);
        }
        if (!empty($parameters['offset'])) {
            $queryBuilder->offset($parameters['offset']);
        }
        if (!empty($parameters['order'])) {
            $queryBuilder->order($parameters['order']);
        }
        self::$queryBuilder = $queryBuilder;

        return self::$classModel;
    }

    /**
     * @param $parameters
     * @return mixed
     */
    public static function insert($parameters)
    {
        $class = get_called_class();
        $class = new $class();

        $queryBuilder = new QueryBuilder();
        $queryBuilder->insert($parameters);
        $queryBuilder->from($class->getSource());
        $queryBuilder->getQuery();

        self::$queryBuilder = $queryBuilder->execute();

        return $class::find([
            'conditions' => 'id = :id:',
            'bind' => [
                'id' => (!empty($parameters['id'])) ? $parameters['id'] : $queryBuilder->getInsertId()
            ]
        ])->getFirst();
    }

    /**
     * @param $parameters
     * @return string
     */
    public static function update($parameters)
    {
        $class = get_called_class();
        $class = new $class();

        $queryBuilder = new QueryBuilder();
        $queryBuilder->update($parameters['new_values']);
        $queryBuilder->from($class->getSource());
        if (!empty($parameters['conditions'])) {
            $queryBuilder->where($parameters['conditions'], (!empty($parameters['bind'])) ? $parameters['bind'] : []);
        }
        $queryBuilder->getQuery();

        self::$queryBuilder = $queryBuilder->execute();

        return $class;
    }

    /**
     * @param $parameters
     * @return string
     */
    public static function delete($parameters)
    {
        $class = get_called_class();
        $class = new $class();

        $queryBuilder = new QueryBuilder();
        $queryBuilder->delete((!empty($parameters['columns'])) ? $parameters['columns'] : '*');
        $queryBuilder->from($class->getSource());
        if (!empty($parameters['conditions'])) {
            $queryBuilder->where($parameters['conditions'], (!empty($parameters['bind'])) ? $parameters['bind'] : []);
        }
        $queryBuilder->getQuery();

        self::$queryBuilder = $queryBuilder->execute();

        return $class;
    }

    /**
     * Set limit
     *
     * @param $limit
     * @return mixed
     */
    public function limit($limit)
    {
        return self::$queryBuilder->limit($limit);
    }

    /**
     * Get count rows
     *
     * @return mixed
     */
    public function count()
    {
        self::$queryBuilder->getQuery();
        self::$queryBuilder = self::$queryBuilder->execute();
        return $this->db->count(self::$queryBuilder);
    }

    /**
     * Get query
     *
     * @return string
     */
    public function getQuery()
    {
        return self::$queryBuilder->getQuery();
    }

    /**
     * @return $this
     */
    public function execute()
    {
        return self::$queryBuilder->execute();
    }

    /**
     * Get results
     *
     * @return mixed
     */
    public function get()
    {
        self::$queryBuilder->getQuery();
        self::$queryBuilder = self::$queryBuilder->execute();

        return $this->db->getAll(self::$queryBuilder, self::$classModel);
    }

    /**
     * Get first result
     *
     * @return mixed
     */
    public function getFirst()
    {
        self::$queryBuilder->getQuery();
        self::$queryBuilder = self::$queryBuilder->execute();

        return $this->db->getFirst(self::$queryBuilder, self::$classModel);
    }
}