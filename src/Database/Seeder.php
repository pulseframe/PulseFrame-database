<?php

namespace PulseFrame\Database;

use Symfony\Component\Console\Output\OutputInterface;

abstract class Seeder
{
  abstract public function run(OutputInterface $output);
}
