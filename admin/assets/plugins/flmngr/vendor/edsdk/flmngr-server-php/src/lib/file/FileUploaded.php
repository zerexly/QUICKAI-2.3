<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FlmngrServer\lib\file;

use EdSDK\FlmngrServer\lib\action\resp\Message;
use EdSDK\FlmngrServer\lib\MessageException;

class FileUploaded extends AFile
{
    protected $m_newName;

    protected $m_confilictsErrors = [];
    protected $m_customErrors = [];

    public function __construct($config, $dir, $name, $newName)
    {
        parent::__construct($config, $dir, $name);
        $this->m_newName = $newName;
    }

    public function getBaseDir()
    {
        return $this->m_config->getTmpDir();
    }

    public function getNewName()
    {
        return $this->m_newName;
    }

    public function checkForErrors($checkForExist)
    {
        if (!parent::checkForErrors($checkForExist)) {
            return false;
        }

        if (
            $this->m_newName !== $this->getName() &&
            !Utils::isFileNameSyntaxOk($this->m_newName)
        ) {
            $this->m_commonErrors[] = Message::createMessage(
                Message::FILE_ERROR_SYNTAX,
                $this->m_newName
            );
        }

        if (Utils::isImage($this->getName())) {
            $ext = $this->getExt();
            $newExt = Utils::getExt($this->m_newName);
            if ($ext !== $newExt) {
                if (
                    !($ext === 'jpg' && $newExt === 'jpeg') &&
                    !($ext === 'jpeg' && $newExt === 'jpg')
                ) {
                    $this->m_commonErrors[] = Message::createMessage(
                        Message::FILE_ERROR_INCORRECT_IMAGE_EXT_CHANGE,
                        $ext,
                        $newExt
                    );
                }
            }
        }
        return true;
    }

    public function addCustomError($message)
    {
        $this->m_customErrors[] = $message;
    }

    public function getErrors()
    {
        $errors = (array) parent::getErrors();
        for ($i = 0; $i < count($this->m_confilictsErrors); $i++) {
            $errors[] = $this->m_confilictsErrors[$i];
        }
        for ($i = 0; $i < count($this->m_customErrors); $i++) {
            $errors[] = $this->m_customErrors[$i];
        }
        return $errors;
    }

    public function getCommitedFile($dir)
    {
        return new FileCommited($this->m_config, $dir, $this->m_newName);
    }

    public function checkForConflicts($dir)
    {
        $this->m_confilictsErrors = [];

        $file = $this->getCommitedFile($dir);
        if ($file->exists()) {
            $this->m_confilictsErrors[] = Message::createMessage(
                Message::FILE_ALREADY_EXISTS,
                $file->getName()
            );
        }

        if ($file->isImage()) {
            $fileOriginal = $file->getFileOriginal();
            if ($fileOriginal->exists()) {
                $this->m_confilictsErrors[] = Message::createMessage(
                    Message::FILE_ALREADY_EXISTS,
                    $fileOriginal->getName()
                );
            }

            $filePreview = $file->getFilePreview();
            if ($filePreview->exists()) {
                $this->m_confilictsErrors[] = Message::createMessage(
                    Message::FILE_ALREADY_EXISTS,
                    $filePreview->getName()
                );
            }
        }
    }

    public function uploadAndCommit($file)
    {
        $initName = $this->getName();
        $this->setFreeFileName();

        if (!move_uploaded_file($file['tmp_name'], $this->getFullPath())) {
            throw new MessageException(
                Message::createMessage(Message::WRITING_FILE_ERROR, $initName)
            );
        }
    }

    public function rehost($url)
    {
        $dUrl = URLDownloader::download(
            $url,
            $this->getBaseDir() . '/' . $this->getDir()
        );
        $this->setName($dUrl->fileName);
    }

    // $mode is the same is in ActionUploadCommit.run(): "ASK" | "AUTORENAME" | "OVERWRITE"
    public function commit($dir, $mode)
    {
        $file = $this->getCommitedFile($dir);
        if ($mode === "AUTORENAME") {
            $file->setFreeFileName();
        }
        $this->copyCommited($file);
        return $file;
    }

    public function isCommited()
    {
        return false;
    }
}
