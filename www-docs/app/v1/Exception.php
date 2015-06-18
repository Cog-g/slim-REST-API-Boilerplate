<?php
namespace API\v1;

class Exception extends \Exception
{

    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        // Save the trace in the debug param
        if (Api::$isDebug) {

            Api::$debug[] = (object)[
                "Message" => $this->getMessage(),
                "File" => $this->getFile(),
                "Line" => $this->getLine()
            ];

        }
    }

}
