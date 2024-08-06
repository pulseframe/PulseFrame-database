<?php

namespace PulseFrame\Methods\Static\Database;

use PulseFrame\Facades\Database;

class Find
{
  protected static $instance;
  protected static $isLike = false;
  protected $willReturn;
  protected static $model;
  protected static $fields = null;
  protected $isAll = false;
  protected $attributes = [];

  public function __construct($model, $fields, $willReturn = true)
  {
    self::$instance = Database::getModelInstance($model);
    $this->willReturn = $willReturn;
    self::$model = $model;
    self::$fields = $fields;
  }

  public function Execute() {
    return $this->handle();
  }

  public function All() {
    $this->isAll = true;
    return $this;
  }

  public function Like()
  {
    $this->willReturn = true;
    self::$isLike = true;
    return $this;
  }

  public function handle()
  {
    if (is_array(self::$fields)) {
      $conditions = [];
      foreach (self::$fields as $key => $value) {
        if (self::$isLike) {
          $conditions[] = "$key::text LIKE :$key";
          $this->attributes[":$key"] = '%' . $value . '%';
        } else {
          $conditions[] = "$key::text = :$key";
          $this->attributes[":$key"] = $value;
        }
      }
      $sql = "SELECT * FROM " . self::$instance->table . " WHERE " . implode(" AND ", $conditions);
    } else {
      if (self::$isLike) {
        $sql = "SELECT * FROM " . self::$instance->table . " WHERE " . self::$instance->primaryKey . "::text" . " LIKE :id";
        $this->attributes = [":id" => '%' . self::$fields . '%'];
      } else {
        $sql = "SELECT * FROM " . self::$instance->table . " WHERE " . self::$instance->primaryKey . "::text" . " = :id";
        $this->attributes = [":id" => self::$fields];
      }
    }

    try {
      if ($this->willReturn) {
        return Database::Query(self::$instance, $sql, $this->attributes, $this->isAll);
      } else {
        Database::Query(self::$instance, $sql, $this->attributes, $this->isAll);
        return new self(self::$model, self::$fields, $this->willReturn);
      }
    } catch (\Exception $e) {
      throw new \Exception($e);
    }
  }

  public function __toString() {
    return $this->string;
  }
}
