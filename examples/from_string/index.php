<?php

require __DIR__."/../../vendor/autoload.php";
use Socarrat\Environment\EnvironmentManager;

$file = '

HELLO="World"

MULTILINE="Test
on multiple

lines………"

';

EnvironmentManager::parseString($file, putenv: true);

$env = EnvironmentManager::getParsedEnv();
print_r($env);
echo PHP_EOL;

$double = getenv("HELLO");
var_dump($double);
echo PHP_EOL;
