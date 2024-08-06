<?php

namespace PulseFrame\Database\Migrations;

use PulseFrame\Database\Migration;
use PulseFrame\Facades\Schema;
use PulseFrame\Database\Blueprint;

class CreatePulseFrame extends Migration
{
  public function up()
  {
    Schema::createTable('pulseframe', function (Blueprint $table) {
      $table->id('uuid');
      $table->text('data')->nullable();
      $table->timestamp()->nullable();
    });
  }

  public function down()
  {
    Schema::dropTable('pulseframe');
  }
}
