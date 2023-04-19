<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FlmngrServer\lib\action\resp;

class Message
{
    const FILE_ERROR_SYNTAX = -1; // args: name
    const FILE_ERROR_DOES_NOT_EXIST = -2;
    const FILE_ERROR_INCORRECT_IMAGE_EXT_CHANGE = -3; // args: oldExt, newExt

    const ACTION_NOT_FOUND = 0;
    const UNABLE_TO_CREATE_UPLOAD_DIR = 1;
    const UPLOAD_ID_NOT_SET = 2;
    const UPLOAD_ID_INCORRECT = 3;
    const MALFORMED_REQUEST = 4;
    const NO_FILE_UPLOADED = 5;
    const FILE_SIZE_EXCEEDS_LIMIT = 6; // args: name, size, maxSize
    const INCORRECT_EXTENSION = 7; // args: name, allowedExtsStr
    const WRITING_FILE_ERROR = 8; // args: name
    const UNABLE_TO_DELETE_UPLOAD_DIR = 9;
    const UNABLE_TO_DELETE_FILE = 10; // args: name
    const DIR_DOES_NOT_EXIST = 11; // args: name
    const FILES_NOT_SET = 12;
    const FILE_IS_NOT_IMAGE = 13;
    const DUPLICATE_NAME = 14;
    const FILE_ALREADY_EXISTS = 15; // args: name
    const FILES_ERRORS = 16; // files args: filesWithErrors
    const UNABLE_TO_COPY_FILE = 17; // args: name, dstName
    const IMAGE_PROCESS_ERROR = 18;
    const MAX_RESIZE_WIDTH_EXCEEDED = 19; // args: width, maxWidth, name
    const MAX_RESIZE_HEIGHT_EXCEEDED = 20; // args: height, maxHeight, name
    const UNABLE_TO_WRITE_IMAGE_TO_FILE = 21; // args: name
    const INTERNAL_ERROR = 22;
    const DOWNLOAD_FAIL_CODE = 23; // args: httpCode
    const DOWNLOAD_FAIL_IO = 24; // args: IO_Exceptions_text
    const DOWNLOAD_FAIL_HOST_DENIED = 25; // args: host name
    const DOWNLOAD_FAIL_INCORRECT_URL = 26; // args: url
    // 27 and 28 reserved for demo server
    const FILE_SIZE_EXCEEDS_SYSTEM_LIMIT = 29; // args: size, maxSize, like #6, but a limit from php.ini file

    public $code;
    public $args;
    public $files;

    private function __construct()
    {
    }

    public static function createMessage(
        $code,
        $arg1 = null,
        $arg2 = null,
        $arg3 = null
    ) {
        $msg = new Message();
        $msg->code = $code;
        if ($arg1 != null) {
            $msg->args = [];
            $msg->args[] = $arg1;
            if ($arg2 != null) {
                $msg->args[] = $arg2;
                if ($arg3 != null) {
                    $msg->args[] = $arg3;
                }
            }
        }
        return $msg;
    }

    public static function createMessageByFiles($code, $files)
    {
        $msg = new Message();
        $msg->code = $code;
        $msg->files = $files;
        return $msg;
    }

    public static function createMessageByFile($code, $file)
    {
        $msg = new Message();
        $msg->code = $code;
        $msg->files = [];
        $msg->files[] = $file;
        return $msg;
    }
}
