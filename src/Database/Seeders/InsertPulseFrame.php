<?php

namespace PulseFrame\Database\Seeders;

use PulseFrame\Database\Seeder;
use PulseFrame\Facades\Env;
use PulseFrame\Facades\Config;
use PulseFrame\Facades\Database;
use PulseFrame\Facades\Schema;
use PulseFrame\Database\Models\PulseFrameModel;

class InsertPulseFrame extends Seeder
{
  public function run($output)
  {
    if (empty(Env::get('app.key'))) {
      echo "It looks like you forgot to generate the app key!";
      exit;
    }

    $ifExist = Database::find(PulseFrameModel::class, "328ef6b3-68d0-4f47-9ffe-7529d5d392b3");

    if (!$ifExist) {
      Schema::insert('pulseframe', [
        'id' => '328ef6b3-68d0-4f47-9ffe-7529d5d392b3',
        'data' => json_encode([
          'name' => Env::get('app.name'),
          'version' => Config::get('app', 'version'),
          'stage' => Config::get('app', 'stage'),
          'key' => Env::get('app.key')
        ]),
        'timestamp' => date('Y-m-d H:i:s')
      ]);
    } else {
      exit;
    }
  }
}
