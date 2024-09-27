<?php
require_once __DIR__ . '/vendor/autoload.php';


use King\Messages\Producer;

$producer = new Producer();
$producer->sendMessage();