<?php
namespace MantisAP;

class MantisAPException extends \Exception {

    public function exit() {
        die("Something went wrong with MantisAP (".$this->getCode()."): ".$this->getMessage());
    }

    public function output() {
        echo "Something went wrong with MantisAP (".$this->getCode()."): ".$this->getMessage();
    }

}