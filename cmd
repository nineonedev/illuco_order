#!/usr/bin/env php
<?php

use Framework\Core\Application;
use Framework\Console\Input;

/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

$app->handleCommand(Input::capture());