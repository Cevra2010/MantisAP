<?php
namespace MantisAP\Objects;

use MantisAP\MantisObject;

class MantisProject extends MantisObject {

    protected $objectName = "projects";
    protected $required = [
        'name',
    ];
}
