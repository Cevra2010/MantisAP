<?php
require_once __DIR__ . '/../vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', '1');


use MantisAP\MantisAP;
use MantisAP\Objects\MantisIssue;

$mantisAP = new MantisAP('URL','TOKEN');

$issues = MantisIssue::find(6);
$issues->delete();

var_dump($issues);