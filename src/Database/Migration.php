<?php

namespace PulseFrame\database;

abstract class Migration
{
  abstract public function up();
  abstract public function down();
}
