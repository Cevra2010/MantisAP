<?php
require_once __DIR__ . '/../vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', '1');


use MantisAP\MantisAP;

$mantisAP = new MantisAP('http://mantis.ff-dotzheim.de','xUJhx16f368z4h1n7yC2NBCMMefECZiF');

$issues = \MantisAP\Objects\MantisIssue::find(6);
$issues->delete();

var_dump($issues);