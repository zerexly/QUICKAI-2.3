<?php

/**
 * Flmngr Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FlmngrServer\fs;

abstract class AFileSystem {

    abstract function getImagePreview($filePath, $width, $height);
    abstract function getImageOriginal($filePath);
    abstract function getDirs($hideDirs);
    abstract function deleteDir($dirPath);
    abstract function createDir($dirPath, $name);
    abstract function renameFile($filePath, $newName);
    abstract function renameDir($dirPath, $newName);
    abstract function getFiles($dirPath); // with "/root_dir_name" in the start
    abstract function deleteFiles($filesPaths, $formatSuffixes);
    abstract function copyFiles($filesPaths, $newPath);
    abstract function moveFiles($filesPaths, $newPath);
    abstract function moveDir($dirPath, $newPath);
    abstract function resizeFile($filePath, $newFileNameWithoutExt, $width, $height, $mode);
    abstract function copyDir($dirPath, $newPath);
    abstract function getDirZipArchive($dirPath, $out);

    abstract function getAbsolutePath($path);

    abstract function passThrough($fullPath, $mimeType);

    abstract function getFileStructure($dirPath, $fileName);
}
