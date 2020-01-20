<?php
/**
 * Created by PhpStorm.
 * User: aima
 * Date: 2019/12/11
 * Time: 12:06
 */

namespace Vastchain\VctcPhpSdk;
use \Exception;

class VctcException extends \Exception
{
    public $rawResponse;
    public $errorCode;

    public function __construct($message, $code, Exception $previous = NULL)
    {
        parent::__construct($message, $code, $previous);

    }

    public function setRaw($raw)
    {
        $this->rawResponse = $raw;
        $this->errorCode = $raw['code'];
    }

    // 自定义字符串输出的样式
    public function __toString()
    {
        return __CLASS__ . ": [{$this->errorCode}] {$this->message}\n";
    }
}