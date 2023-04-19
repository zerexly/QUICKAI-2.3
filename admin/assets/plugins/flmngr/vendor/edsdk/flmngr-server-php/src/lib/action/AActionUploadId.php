<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FlmngrServer\lib\action;

use EdSDK\FlmngrServer\lib\action\resp\Message;
use EdSDK\FlmngrServer\lib\MessageException;

abstract class AActionUploadId extends AAction
{
    protected function validateUploadId($req)
    {
        if ($req->uploadId === null) {
            throw new MessageException(
                Message::createMessage(Message::UPLOAD_ID_NOT_SET)
            );
        }

        $dir = $this->m_config->getTmpDir() . '/' . $req->uploadId;
        if (!file_exists($dir) || !is_dir($dir)) {
            throw new MessageException(
                Message::createMessage(Message::UPLOAD_ID_INCORRECT)
            );
        }
    }
}
