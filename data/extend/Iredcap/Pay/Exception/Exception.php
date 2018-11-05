<?php

namespace Iredcap\Pay\Exception;


class Exception extends \Exception
{
    /**
     * BaseException constructor.
     * @param $message
     */
    public function __construct($message)
    {
        parent::__construct($message);

    }
}
