<?php
require_once __DIR__ . '/../vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', '1');


use MantisAP\MantisAP;

$mantisAP = new MantisAP('http://mantis.ff-dotzheim.de','9x8JgEI1xwnFlnrA9P6F8dvLMDKfNtKV');

$issues = \MantisAP\Objects\MantisIssue::find(6);
$issues->delete();

var_dump($issues);