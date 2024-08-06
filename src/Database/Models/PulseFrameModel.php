<?php
namespace PulseFrame\Database\Models;

use PulseFrame\Model;

class PulseFrameModel extends Model
{
  public $table = 'pulseframe';
  public $primaryKey = 'id';
  public $connection = 'default';
}