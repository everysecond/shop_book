<?php
namespace App\Common\Exceptions;

use Exception;

class ApiException extends Exception {

    public function __construct($message, $code = 90001) {
        parent::__construct($message, $code, null);
    }
}