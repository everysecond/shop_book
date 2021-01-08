<?php namespace App\Common\Exceptions;

class AjaxException extends \Exception {
    public $field;

    public function __construct($message, $code = 400, $field = null, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->field = $field;
    }

    public function getField() {
        return $this->field;
    }
}