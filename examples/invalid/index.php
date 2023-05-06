<?php

require __DIR__."/../../vendor/autoload.php";
use Socarrat\Environment\EnvironmentManager;

EnvironmentManager::parseFS(rootDir: __DIR__, putenv: true);

$env = EnvironmentManager::getParsedEnv();
print_r($env);
echo PHP_EOL;
