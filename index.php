<?php

use Framework\Core\Application;
use Framework\Http\Request;
use Framework\Support\Facades\Name;

/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

dump_classes_in_html('framework/Support'); exit; 

$app->handleRequest(Request::capture());

