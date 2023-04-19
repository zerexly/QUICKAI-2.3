<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FlmngrServer\lib\action;

use EdSDK\FlmngrServer\lib\action\resp\RespUploadAddFile;
use EdSDK\FlmngrServer\lib\file\FileUploaded;
use EdSDK\FlmngrServer\lib\action\resp\Message;
use EdSDK\FlmngrServer\lib\MessageException;

class ActionUploadAddFile extends AActionUploadId
{
    public function getName()
    {
        return 'uploadAddFile';
    }

    public function run($req)
    {
        $this->validateUploadId($req);

        $file = null;
        if (!isset($req->url)) {
            if ($req->m_fileName === null || $req->m_file === null) {
                throw new MessageException(
                    Message::createMessage(Message::NO_FILE_UPLOADED)
                );
            }

            if (
                $this->m_config->getMaxUploadFileSize() > 0 &&
                $req->m_fileSize > $this->m_config->getMaxUploadFileSize()
            ) {
                throw new MessageException(
                    Message::createMessage(
                        Message::FILE_SIZE_EXCEEDS_LIMIT,
                        $req->m_fileName,
                        '' . $req->m_fileSize,
                        '' . $this->m_config->getMaxUploadFileSize()
                    )
                );
            }

            $file = new FileUploaded(
                $this->m_config,
                $req->uploadId,
                $req->m_fileName,
                $req->m_fileName
            );
            $ext = strtolower($file->getExt());
            $allowedExts = $this->m_config->getAllowedExtensions();
            $isAllowedExt = count($allowedExts) == 0;
            for ($i = 0; $i < count($allowedExts) && !$isAllowedExt; $i++) {
                $isAllowedExt = $allowedExts[$i] === $ext;
            }
            if (!$isAllowedExt) {
                $strExts = '';
                for ($i = 0; $i < count($allowedExts); $i++) {
                    if ($i > 0) {
                        $strExts .= ', ';
                    }
                    $strExts .= $allowedExts[$i];
                }
                throw new MessageException(
                    Message::createMessage(
                        Message::INCORRECT_EXTENSION,
                        $req->m_fileName,
                        $strExts
                    )
                );
            }
            $file->uploadAndCommit($req->m_file);
        } else {
            if (filter_var($req->url, FILTER_VALIDATE_URL) === false) {
                throw new MessageException(
                    Message::createMessage(
                        Message::DOWNLOAD_FAIL_INCORRECT_URL,
                        $req->url
                    )
                );
            }
            $host = strtolower(parse_url($req->url, PHP_URL_HOST));
            $isHostAllowed = false;
            $relocateHosts = $this->m_config->getRelocateFromHosts();
            for ($i = 0; $i < count($relocateHosts) && !$isHostAllowed; $i++) {
                if (strtolower($relocateHosts[$i]) === $host) {
                    $isHostAllowed = true;
                }
            }
            if (count($relocateHosts) == 0 || $isHostAllowed) {
                $file = new FileUploaded(
                    $this->m_config,
                    $req->uploadId,
                    null,
                    null
                );
                $file->rehost($req->url);
            } else {
                throw new MessageException(
                    Message::createMessage(
                        Message::DOWNLOAD_FAIL_HOST_DENIED,
                        $host
                    )
                );
            }
        }
        $resp = new RespUploadAddFile();
        $resp->file = $file->getData();
        return $resp;
    }
}
