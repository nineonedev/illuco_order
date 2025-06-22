<?php

use Framework\Core\Application;
use Framework\Http\Request;

/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

// dump_classes_in('framework/Database/ORM/Relations'); exit; 
// dump_classes_in('framework/Routing'); exit; 

$app->handleRequest(Request::capture());

