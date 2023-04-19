<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FlmngrServer\lib\action\req;

class ReqUploadCommit extends ReqUploadId
{
    public $sizes; // of [enlarge: boolean, width: number, height: number]
    public $doCommit;
    public $autoRename;
    public $dir;
    public $files; // of [name: string, newName: string]
}
