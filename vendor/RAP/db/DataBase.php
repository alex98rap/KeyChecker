<?php
/**
 * Created by PhpStorm.
 * User: alex1rap
 * Date: 03.06.2017
 * Time: 15:38
 */

namespace RAP\db;

class DataBase
{
    protected $host;
    protected $user;
    protected $pass;
    protected $database;

    protected $MySQLi;

    protected $fields;
    protected $table;
    protected $conditions;
    protected $order;
    protected $limit;

    /**
     * DataBase constructor.
     * @param array $params
     */
    public function __construct($params = [])
    {
        if (empty($params)) {
            $params = include __DIR__ . '/../../../config/db.php';
        }
        foreach ($params as $param => $value) {
            if (property_exists($this, $param)) {
                $this->$param = $value;
            }
        }
        $this->MySQLi = new \MySQLi($this->host, $this->user, $this->pass, $this->database);
        return $this->MySQLi;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function select($fields = [])
    {
        $fields_string = !empty($fields) ? '' : '*';
        foreach ($fields as $field) {
            $fields_string .= !empty($fields_string) ? ',' . $field : $field;
        }
        $this->fields = $fields_string;
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function changeTable($name)
    {
        $this->table = $name;
        return $this;
    }

    /**
     * @param string $conditions
     * @return $this
     */
    public function whereString($conditions = '')
    {
        $this->conditions = $conditions;
        return $this;
    }

    /**
     * @param array $conditions
     * @return $this
     */
    public function andWhere($conditions = [])
    {
        $conditionString = '';
        foreach ($conditions as $condition => $value) {
            $conditionString .= !empty($conditionString) ? ' AND ' . "`{$condition}`='{$value}'" : "`{$condition}`='{$value}'";
        }
        $this->conditions .= $conditionString;
        return $this;
    }

    /**
     * @param $conditions
     * @return $this
     */
    public function orWhere($conditions)
    {
        $conditionString = '';
        foreach ($conditions as $condition => $value) {
            $conditionString .= !empty($conditionString) ? ' OR ' . "`{$condition}`='{$value}'" : "`{$condition}`='{$value}'";
        }
        $this->conditions .= $conditionString;
        return $this;
    }

    /**
     * @param $start
     * @param $end
     * @return $this
     */
    public function limit($start, $end)
    {
        $this->limit = "{$start}, {$end}";
        return $this;
    }

    /**
     * @param $order
     * @param int $sort
     * @return $this
     */
    public function orderBy($order, $sort = 1)
    {
        $sort = ['ASC', 'DESC'][$sort];
        $this->order = "{$order}, {$sort}";
        return $this;
    }

    /**
     * @return mixed
     */
    public function one()
    {
        $where = !empty($this->conditions) ? $this->conditions : 1;
        $order = !empty($this->order) ? "ORDER BY {$this->order}" : '';
        $limit = !empty($this->limit) ? "LIMIT {$this->limit}" : '';
        $query = "SELECT {$this->fields} FROM {$this->table} WHERE {$where} {$order} {$limit}";
        $res = $this->MySQLi->query($query);
        return !empty($res) ? $res->fetch_assoc() : false;
    }

    /**
     * @return array
     */
    public function all()
    {
        $where = !empty($this->conditions) ? $this->conditions : 1;
        $order = !empty($this->order) ? "ORDER BY {$this->order}" : '';
        $limit = !empty($this->limit) ? "LIMIT {$this->limit}" : '';
        $query = "SELECT {$this->fields} FROM {$this->table} WHERE {$where} {$order} {$limit}";
        $result = [];
        $sql = $this->MySQLi->query($query);
        while ($res = $sql->fetch_assoc()) {
            $result[] = $res;
        }
        return $result;
    }

    /**
     * @param array $fields
     * @param array $values
     * @return mixed
     */
    public function add($fields = [], $values = [])
    {
        $fieldString = '';
        foreach ($fields as $field) {
            $fieldString .= !empty($fieldString) ? ",`{$field}`" : "`{$field}`";
        }
        $valueString = '';
        foreach ($values as $value) {
            $valueString .= !empty($valueString) ? ",`{$value}`" : "`{$value}`";
        }
        return $this->MySQLi->query("INSERT INTO ({$fieldString}) VALUES ({$valueString})");
    }

    /**
     * @return mixed
     */
    public function delete()
    {
        $where = !empty($this->conditions) ? $this->conditions : 1;
        return $this->MySQLi->query("DELETE FROM `{$this->table}` WHERE {$where}");
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function update($params = [])
    {
        $sets = '';
        foreach ($params as $param => $value) {
            $sets = !empty($sets) ? ",`{$param}`='{$value}'" : "`{$param}`='{}$value'";
        }
        $where = !empty($this->conditions) ? $this->conditions : 1;
        return $this->MySQLi->query("UPDATE {$this->table} SET {$sets} WHERE {$where}");
    }
}
