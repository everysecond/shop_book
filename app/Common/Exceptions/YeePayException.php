<?php
namespace App\Common\Exceptions;

use Exception;

class YeePayException extends Exception {

    public function __construct($message, $code = 90001) {
        parent::__construct($message, $code, null);
    }
}