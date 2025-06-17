<?php

use Framework\Core\Application;
use Framework\Http\Request;

/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

// dump_classes_in('database/migrations'); exit; 

$app->handleRequest(Request::capture());

