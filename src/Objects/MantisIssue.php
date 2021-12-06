<?php
namespace MantisAP\Objects;

use MantisAP\MantisObject;

class MantisIssue extends MantisObject {
    protected $objectName = "issues";
    protected $required = [
        'name',
    ];
}