<?php
require_once __DIR__ . '/vendor/autoload.php';


use King\Messages\Consumer;

$consumer = new Consumer();
$consumer->consumeMessages();