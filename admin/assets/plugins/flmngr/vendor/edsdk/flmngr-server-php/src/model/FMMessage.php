<?php

/**
 * Flmngr Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FlmngrServer\model;

use EdSDK\FlmngrServer\lib\action\resp\Message;

class FMMessage extends Message
{
    const FM_FILE_DOES_NOT_EXIST = 10001; // File does not exist: %1
    const FM_UNABLE_TO_WRITE_PREVIEW_IN_CACHE_DIR = 10002; // Unable to write a preview into cache directory
    const FM_UNABLE_TO_CREATE_PREVIEW = 10003; // Unable to create a preview
    const FM_DIR_NAME_CONTAINS_INVALID_SYMBOLS = 10004; // Directory name contains invalid symbols
    const FM_DIR_NAME_INCORRECT_ROOT = 10005; // Directory has incorrect root
    const FM_FILE_IS_NOT_IMAGE = 10006; // File is not an image
    const FM_ROOT_DIR_DOES_NOT_EXIST = 10007; // Root directory does not exists
    const FM_UNABLE_TO_LIST_CHILDREN_IN_DIRECTORY = 10008; // Unable to list children in the directory
    const FM_UNABLE_TO_DELETE_DIRECTORY = 10009; // Unable to delete the directory
    const FM_UNABLE_TO_CREATE_DIRECTORY = 10010; // Unable to create a directory
    const FM_UNABLE_TO_RENAME = 10011; // Unable to rename
    const FM_DIR_CANNOT_BE_READ = 10012; // Directory can not be read
    const FM_ERROR_ON_COPYING_FILES = 10013; // Error on copying files
    const FM_ERROR_ON_MOVING_FILES = 10014; // Error on moving files

    const FM_NOT_ERROR_NOT_NEEDED_TO_UPDATE = 10015; // Not an error. Request asked not to create a preview if it already exists
}
