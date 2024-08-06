<?php

namespace PulseFrame\Database;

class Blueprint
{
  protected $table;
  protected $columns = [];

  public function __construct($tableName)
  {
    $this->table = $tableName;
  }

  public function id($type = 'int')
  {
    $this->addColumn('id', $type, ['primary' => true]);
    return $this;
  }

  public function text($name)
  {
    $this->addColumn($name, 'text');
    return $this;
  }

  public function timestamp($name = 'timestamp')
  {
    $this->addColumn($name, 'timestamp');
    return $this;
  }

  public function nullable()
  {
    $this->setAttribute('nullable', true);
    return $this;
  }

  public function primary()
  {
    $this->setAttribute('primary', true);
    return $this;
  }

  public function unique()
  {
    $this->setAttribute('unique', true);
    return $this;
  }

  protected function addColumn($name, $type, $attributes = [])
  {
    $this->columns[$name] = array_merge(['type' => $type], $attributes);
  }

  protected function setAttribute($attribute, $value)
  {
    $lastColumn = array_key_last($this->columns);
    if ($lastColumn !== null) {
      $this->columns[$lastColumn][$attribute] = $value;
    }
  }

  public function getColumns()
  {
    return $this->columns;
  }

  public function getTable()
  {
    return $this->table;
  }
}
