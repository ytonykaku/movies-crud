#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

$container = require_once __DIR__ . '/app/Config/bootstrap.php';
$entityManager = $container['entityManager'];

ConsoleRunner::run(
    new SingleManagerProvider($entityManager)
);
