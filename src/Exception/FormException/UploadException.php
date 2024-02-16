<?php
namespace App\Exception\FormException;

use Exception;

class UploadException extends Exception
{
    public $field;
    
    public function __construct($message, $field)
    {
        $this->field = $field;
        parent::__construct($message);
    }
}