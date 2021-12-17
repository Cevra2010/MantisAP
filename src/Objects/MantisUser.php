<?php
namespace MantisAP\Objects;

use MantisAP\MantisObject;

class MantisUser extends MantisObject {
    protected $objectName = "users";
    protected $required = [
        'username',
    ];
}