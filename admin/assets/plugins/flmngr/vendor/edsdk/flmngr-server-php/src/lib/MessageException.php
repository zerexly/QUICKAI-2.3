<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FlmngrServer\lib;

use Exception;

class MessageException extends Exception
{
    protected $m_message;

    public function __construct($message)
    {
        parent::__construct();
        $this->m_message = (array) $message;
    }

    public function getFailMessage()
    {
        return $this->m_message;
    }
}
