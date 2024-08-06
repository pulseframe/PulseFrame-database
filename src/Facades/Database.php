<?php

namespace PulseFrame\Facades;

use PDO;

class Database
{
  protected static $conn = null;

  public static function getConnection($model)
  {
    $connectionName = self::resolveConnectionName($model);
    $databaseConfig = Config::get('database');

    if (!isset(self::$conn[$connectionName]) || self::$conn[$connectionName]->getAttribute(PDO::ATTR_DRIVER_NAME) !== $databaseConfig[$connectionName]['driver']) {
      $host = $databaseConfig[$connectionName]['host'];
      $username = $databaseConfig[$connectionName]['username'];
      $password = $databaseConfig[$connectionName]['password'];
      $database = $databaseConfig[$connectionName]['database'];
      $port = $databaseConfig[$connectionName]['port'];
      $driver = $databaseConfig[$connectionName]['driver'];

      try {
        self::$conn[$connectionName] = new PDO("$driver:host=$host;dbname=$database;port=$port", $username, $password);
        self::$conn[$connectionName]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch (\PDOException $e) {
        throw new \Exception("Connection failed: " . $e->getMessage());
      }
    }

    return self::$conn[$connectionName];
  }

  public static function getDatabaseType($connection = 'default')
  {
    $conn = self::getConnection($connection);
    return $conn->getAttribute(PDO::ATTR_DRIVER_NAME);
  }

  public static function getModelInstance($model)
  {
    if (is_string($model) && class_exists($model)) {
      $className = $model;
    } elseif (is_string($model)) {
      $className = "\\App\\Models\\" . $model;
    } elseif (is_object($model) && method_exists($model, 'class')) {
      $className = get_class($model);
    }

    if (!class_exists($className)) {
      throw new \Exception("Model not found: {$className}");
    }

    return new $className();
  }

  public static function __callStatic($method, $args)
  {
    $methodClass = "\\PulseFrame\\Methods\\Static\\Database\\" . ucfirst($method);
    if (class_exists($methodClass) && method_exists($methodClass, 'handle')) {
      $instance = new $methodClass(...$args);
      if (method_exists($instance, $method)) {
        return $instance->$method(...$args);
      } elseif (method_exists($instance, 'handle')) {
        return $instance->handle(...$args);
      } else {
        throw new \BadMethodCallException("Method {$method} does not exist in {$methodClass}.");
      }
    } else {
      throw new \BadMethodCallException("Method {$method} does not exist.");
    }
  }

  public function __call($method, $args)
  {
    $methodClass = "\\PulseFrame\\Methods\\Static\\Database\\" . ucfirst($method);
    if (class_exists($methodClass)) {
      $instance = new $methodClass($this->instance);
      if (method_exists($instance, $method)) {
        return $instance->$method(...$args);
      } elseif (method_exists($instance, 'handle')) {
        return $instance->handle($instance, ...$args);
      } else {
        throw new \BadMethodCallException("Method {$method} does not exist in {$methodClass}.");
      }
    } else {
      throw new \BadMethodCallException("Class {$methodClass} does not exist.");
    }
  }

  protected static function resolveConnectionName($model)
  {
    if (is_string($model)) {
      $connectionName = $model;
    } elseif (is_object($model) && property_exists($model, 'connection')) {
      $connectionName = $model->connection;
    } else {
      throw new \InvalidArgumentException("Invalid model type. Must be an object with a 'connection' property or a string.");
    }
    return $connectionName;
  }
}
