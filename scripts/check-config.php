<?php
require __DIR__ . '/../vendor/autoload.php';
$c = require __DIR__ . '/../config/config.php';
echo "PASS:[" . (isset($c['mail']['smtp']['password']) && $c['mail']['smtp']['password'] !== '' ? $c['mail']['smtp']['password'] : '<empty>') . "]\n";
