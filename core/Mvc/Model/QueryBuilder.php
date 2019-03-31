<?php

namespace Simple\Mvc\Model;

use Simple\Di;
use Simple\Exception;

/**
 * Class QueryBuilder
 * @package Simple\Mvc\Model
 */
class QueryBuilder
{
    protected $from;

    protected $limit;

    protected $offset;

    protected $operator;

    protected $columns;

    protected $where;

    protected $values;

    protected $new_values;

    protected $order;

    protected $query;

    /**
     * @param $columns
     * @return $this
     */
    public function select($columns)
    {
        $this->columns = $columns;
        $this->operator = 'SELECT';

        return $this;
    }

    /**
     * @param $values
     * @return $this
     */
    public function insert($values)
    {
        $this->operator = 'INSERT';
        $this->values = $values;

        return $this;
    }

    /**
     * @param $values
     * @return $this
     */
    public function update($values)
    {
        $this->operator = 'UPDATE';
        $this->new_values = $values;

        return $this;
    }

    /**
     * @return $this
     */
    public function delete()
    {
        $this->operator = 'DELETE';

        return $this;
    }

    /**
     * Set table name
     *
     * @param $from
     * @return $this
     */
    public function from($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @param $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param $offset
     * @return $this
     */
    public function offset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @param $where
     * @param $values
     * @return $this
     */
    public function where($where, $values)
    {
        $this->where = $where;
        $this->values = $values;

        return $this;
    }

    /**
     * @param $order
     * @return $this
     */
    public function order($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        if (empty($this->operator)) {
            throw new Exception('Not selected operator (select, insert, update, delete)');
        }
        if (empty($this->from)) {
            throw new Exception('Undefined table name');
        }

        $where = $this->where;
        if ($this->operator == 'SELECT' || $this->operator == 'DELETE' || $this->operator == 'UPDATE') {
            if (!empty($this->values)) {
                foreach ($this->values as $key_agr => $argument) {
                    $where = str_replace(':' . $key_agr . ':', '"' . htmlspecialchars($argument) . '"', $where);
                }
                $where = 'WHERE ' . $where . ' ';
            }
        }
        $this->where = $where;

        $query_params = '';
        if ($this->operator == 'INSERT') {
            if (empty($this->values)) {
                throw new Exception('Not selected values');
            }
            $attr = [];
            $values= [];
            foreach ($this->values as $argument => $value) {
                $attr[] = $argument;
                $values[] = '"' . htmlspecialchars($value) . '"';
            }

            if (!empty($this->values)) {
                $query_params = '(' . implode(',', $attr) . ') VALUES (' . implode(',', $values) . ') ';
            }
        }

        $this->query = '';

        switch ($this->operator) {
            case 'SELECT':
                $this->query = $this->operator . ' ';
                $this->query .= (!empty($this->columns)) ? $this->columns . ' ' : '* ';
                $this->query .= 'FROM ' . $this->from . ' ';
                $this->query .= $where;
                $this->query .= (!empty($this->order)) ? 'ORDER BY ' . $this->order . ' ' : '';
                $this->query .= (!empty($this->limit)) ? 'LIMIT ' . $this->limit . ' ' : '';
                $this->query .= (!empty($this->offset)) ? 'OFFSET ' . $this->offset . ' ' : '';
                break;

            case 'INSERT':
                $this->query = $this->operator . ' INTO ';
                $this->query .= $this->from . ' ';
                $this->query .= $query_params . ' ';

                break;

            case 'UPDATE':
                if (empty($this->new_values)) {
                    throw new Exception('Undefined new values');
                }
                $this->query = $this->operator . ' ';
                $this->query .= $this->from . ' ';

                $new_values = [];
                foreach ($this->new_values as $key => $item) {
                    $new_values[] = $key . '="' . htmlspecialchars($item) . '"';
                }
                $this->query .= 'SET ' . implode(', ', $new_values) . ' ';
                $this->query .= $where;
                break;

            case 'DELETE':
                $this->query = $this->operator . ' ';
                $this->query .= 'FROM ' . $this->from . ' ';
                $this->query .= $where;
                break;
        }

        return $this->query;
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $di = Di::getDefault();
        $query = $di->getService('db')->query($this->query);

        return $query;
    }
}