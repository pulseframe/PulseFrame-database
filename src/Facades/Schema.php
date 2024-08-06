<?php

namespace PulseFrame\Facades;

use PulseFrame\Database\Blueprint;
use PulseFrame\Facades\Database;

class Schema
{
  protected static $connection = 'default';

  public static function connection($connectionName)
  {
    self::$connection = $connectionName;
    return new static;
  }

  public static function createTable($tableName, callable $callback)
  {
    $blueprint = new Blueprint($tableName);
    $callback($blueprint);

    $connection = self::$connection;
    $columns = $blueprint->getColumns();
    $columnsSql = [];

    $dbType = Database::getDatabaseType($connection);

    foreach ($columns as $name => $attributes) {
      $type = $attributes['type'];
      $sql = "\"$name\" $type";

      if (isset($attributes['auto_increment']) && $attributes['auto_increment']) {
        if ($dbType === 'mysql') {
          $sql .= " AUTO_INCREMENT";
        } elseif ($dbType === 'pgsql') {
          $sql .= " SERIAL";
        }
      }

      if (isset($attributes['nullable']) && $attributes['nullable']) {
        $sql .= " NULL";
      } else {
        $sql .= " NOT NULL";
      }

      if (isset($attributes['primary']) && $attributes['primary']) {
        $sql .= " PRIMARY KEY";
      }

      if (isset($attributes['unique']) && $attributes['unique']) {
        $sql .= " UNIQUE";
      }

      $columnsSql[] = $sql;
    }

    $columnsString = implode(", ", $columnsSql);
    $sql = "CREATE TABLE IF NOT EXISTS \"{$tableName}\" ({$columnsString})";

    Database::Execute($connection, $sql);
  }

  public static function dropTable($tableName)
  {
    $sql = "DROP TABLE IF EXISTS \"{$tableName}\"";
    Database::Execute(self::$connection, $sql);
  }

  public static function insert($tableName, array $data)
  {
    if (empty($data)) {
      throw new \InvalidArgumentException('Data array cannot be empty.');
    }

    $columns = array_keys($data);

    $placeholders = array_map(function ($col) {
      return ':' . $col;
    }, $columns);

    $sql = "INSERT INTO \"{$tableName}\" (" . implode(", ", array_map(function ($col) {
      return "\"$col\"";
    }, $columns)) . ") VALUES (" . implode(", ", $placeholders) . ")";

    $params = [];
    foreach ($data as $key => $value) {
      $params[':' . $key] = $value;
    }

    try {
      $result = Database::Query(self::$connection, $sql, $params);
    } catch (\Exception $e) {
      throw new \InvalidArgumentException("Failed to insert record: " . $e->getMessage());
    }

    return $result;
  }
}
