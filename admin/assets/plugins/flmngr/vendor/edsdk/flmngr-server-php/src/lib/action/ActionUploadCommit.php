<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FlmngrServer\lib\action;

use EdSDK\FlmngrServer\lib\file\FileUploaded;
use EdSDK\FlmngrServer\lib\file\Utils;
use EdSDK\FlmngrServer\lib\action\resp\Message;
use EdSDK\FlmngrServer\lib\action\resp\RespOk;
use EdSDK\FlmngrServer\lib\action\resp\RespUploadCommit;
use EdSDK\FlmngrServer\lib\MessageException;
use EdSDK\FlmngrServer\model\FMMessage;
use Exception;

class ActionUploadCommit extends AActionUploadId
{
    public function getName()
    {
        return 'uploadCommit';
    }

    protected function validateSize($size, $sizeName)
    {
        $size->enlarge = $this->validateBoolean(
            $size->enlarge,
            $sizeName === 'preview'
        );
        $size->width = $this->validateInteger($size->width, 0);
        $size->height = $this->validateInteger($size->height, 0);
    }

    protected function validateSizes($req)
    {
        if (!isset($req->sizes) || $req->sizes === null) {
            $req->sizes = [];
        } else {
            $sizesNames = ['full', 'preview'];
            //theoretical workaround for 7.4
            $req->sizes = (array) $req->sizes;
            for ($i = 0; $i < count($sizesNames); $i++) {
                $sizeName = $sizesNames[$i];
                if ($req->sizes[$sizeName]) {
                    $this->validateSize($req->sizes[$sizeName], $sizeName);
                }
            }
        }
    }


    // Can be run in three modes:
    // mode = "ASK" (legacy: autoRename=false)
    //      Will fail if any filename conflicts.
    // mode = "AUTORENAME" (legacy: autoRename=true)
    //      Will automatically find a new filename for uploaded files in case of conflicts
    // mode = "OVERWRITE"
    //      Will silently overwrite files in case of any conflicts.
    //
    // imageFormats = [{suffix: "_preview", maxWidth: 1000, maxHeight: 800}, ...]
    //      In all cases if this parameter is set, file formats will to be recalculated automatically
    //      (only for existing formats)
    public function run($req)
    {
        $this->validateUploadId($req);

        $this->validateSizes($req);

        $req->doCommit = $this->validateBoolean($req->doCommit, true);

        // Legacy way to set mode
        if (isset($req->autoRename) && !isset($req->mode))
            $req->mode = $this->validateBoolean($req->autoRename, false) ? "AUTORENAME" : "ASK";

        if (
            !isset($req->mode) ||
            array_search($req->mode, ["ASK", "AUTORENAME", "OVERWRITE"]) === FALSE
        )  {
            throw new MessageException(
                Message::createMessage(Message::INTERNAL_ERROR, $req->dir)
            );
        }

        $req->dir = $this->validateString($req->dir, '');

        if (strpos($req->dir, '/') !== 0) {
            $req->dir = '/' . $req->dir;
        }

        if (Utils::normalizeNoEndSeparator($req->dir) === null) {
            throw new MessageException(
                Message::createMessage(Message::DIR_DOES_NOT_EXIST, $req->dir)
            );
        }

        $req->dir = Utils::normalizeNoEndSeparator($req->dir) . '/';

        $dir = $this->m_config->getBaseDir() . $req->dir;
        if (!file_exists($dir) && !mkdir($dir, 0777, TRUE)) {
            throw new MessageException(
                Message::createMessage(Message::DIR_DOES_NOT_EXIST, $req->dir)
            );
        }

        if ($req->files === null || count($req->files) == 0) {
            throw new MessageException(
                Message::createMessage(Message::FILES_NOT_SET)
            );
        }

        $filesToCommit = [];
        for ($i = 0; $i < count($req->files); $i++) {
            $fileDef = $req->files[$i];

            if ($fileDef->name === null) {
                throw new MessageException(
                    Message::createMessage(Message::MALFORMED_REQUEST)
                );
            }

            if (!isset($fileDef->newName) || $fileDef->newName === null) {
                $fileDef->newName = $fileDef->name;
            }

            $file = new FileUploaded(
                $this->m_config,
                $req->uploadId,
                $fileDef->name,
                $fileDef->newName
            );
            $filesToCommit[] = $file;

            if (!$file->isImage() && count($req->sizes) > 0) {
                $file->addCustomError(
                    Message::createMessage(Message::FILE_IS_NOT_IMAGE)
                );
            }
        }

        // Check there are no equal names
        for ($i = 0; $i < count($filesToCommit); $i++) {
            $name = $filesToCommit[$i]->getNewName();
            for ($j = 0; $j < count($filesToCommit); $j++) {
                $name2 = $filesToCommit[$j]->getNewName();
                if ($i != $j && $name === $name2) {
                    $filesToCommit[$i]->addCustomError(
                        Message::createMessage(Message::DUPLICATE_NAME)
                    );
                    break;
                }
            }
        }

        // Check files for errors
        for ($i = 0; $i < count($filesToCommit); $i++) {
            $file = $filesToCommit[$i];
            $file->checkForErrors(true);
            if ($req->mode === "ASK") {
                $file->checkForConflicts($req->dir);
            }
        }

        $filesToCommitWithErrors = [];
        for ($i = 0; $i < count($filesToCommit); $i++) {
            if (count($filesToCommit[$i]->getErrors()) > 0) {
                $filesToCommitWithErrors[] = $filesToCommit[$i]->getData();
            }
        }

        if (count($filesToCommitWithErrors) > 0) {
            throw new MessageException(
                Message::createMessageByFiles(
                    Message::FILES_ERRORS,
                    $filesToCommitWithErrors
                )
            );
        }

        // Validation ended
        if (!$req->doCommit) {
            return new RespOk();
        }

        // 1. Commit
        $filesCommited = [];
        for ($i = 0; $i < count($filesToCommit); $i++) {
            $fileToCommit = $filesToCommit[$i];
            $fileCommited = $fileToCommit->commit($req->dir, $req->mode);
            $filesCommited[] = $fileCommited;

            $dirRoot = Utils::removeTrailingSlash($this->m_config->getBaseDir());
            $index = strrpos($dirRoot, '/');
            if ($index !== FALSE) {
                $dirRoot = substr($dirRoot, $index + 1);
            }

            $cachedFile = $this->m_config->getFS()->getCachedFile("/" . $dirRoot . "/" . $fileCommited->getPath());
            $cachedFile->delete();

            if ($req->imageFormats != NULL) {

                $path = $fileCommited->getPath();
                $defaultFormatFileName = $path;
                $index = strrpos($path, '/');
                if ($index !== FALSE) {
                    $defaultFormatFileName = substr($path, $index + 1);
                }

                // Remove root dir
                $path = substr($path, 1);
                $index = strpos($path, '/');
                if ($index !== FALSE)
                    $path = substr($path, $index + 1);
                $path = '/' . $path;

                $defaultFormatFileName = Utils::getNameWithoutExt($defaultFormatFileName);

                foreach ($req->imageFormats as $imageFormat) {

                    $formatFileName = $defaultFormatFileName;

                    // "default" format has NULL suffix (original filename)
                    $formatFileName .= ($imageFormat->suffix != NULL ? $imageFormat->suffix : "");

                    try {
                        $this->m_config->getFS()->resizeFile(
                            $path,
                            $formatFileName,
                            $imageFormat->maxWidth,
                            $imageFormat->maxHeight,
                            "IF_EXISTS"
                        );
                    } catch (MessageException $e) {
                        if ($e->getFailMessage()["code"] !== FMMessage::FM_NOT_ERROR_NOT_NEEDED_TO_UPDATE) {
                            error_log("Error on resizing file for overwritten one:\n");
                            error_log(print_r($e->getFailMessage(), TRUE));
                        }
                    }
                }
            }
        }

        // 2. Remove uploadAndCommit directory

        if (!$this->m_config->doKeepUploads()) {
            try {
                Utils::delete(
                    $this->m_config->getTmpDir() . '/' . $req->uploadId
                );
            } catch (Exception $e) {
                error_log($e);
                // Error, but we do not throw anything - we've commited files and need to return them
            }
        }

        // 3. Send response with the list of files copied

        $files = [];
        for ($i = 0; $i < count($filesCommited); $i++) {
            $files[] = $filesCommited[$i]->getData();
        }

        $resp = new RespUploadCommit();
        $resp->files = $files;

        $files2 = [];
        for ($i = 0; $i < count($filesCommited); $i++) {

            $dirRoot = Utils::removeTrailingSlash($this->m_config->getBaseDir());
            $index = strrpos($dirRoot, '/');
            if ($index !== FALSE) {
                $dirRoot = substr($dirRoot, $index + 1);
            }

            $files2[] = $this->m_config->getFS()->getFileStructure(
                '/' . $dirRoot . $filesCommited[$i]->getDir(),
                $filesCommited[$i]->getName()
            );
        }
        $resp->files2 = $files2;

        return $resp;
    }
}
