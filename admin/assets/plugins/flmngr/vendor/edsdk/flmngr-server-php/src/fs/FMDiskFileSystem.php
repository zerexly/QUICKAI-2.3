<?php

/**
 * Flmngr Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FlmngrServer\fs;

use EdSDK\FlmngrServer\fs\CachedFile;
use EdSDK\FlmngrServer\lib\action\resp\Message;
use EdSDK\FlmngrServer\lib\file\Utils;
use EdSDK\FlmngrServer\lib\MessageException;
use EdSDK\FlmngrServer\model\FMDir;
use EdSDK\FlmngrServer\model\FMFile;
use EdSDK\FlmngrServer\model\FMMessage;
use EdSDK\FlmngrServer\model\ImageInfo;
use Exception;

class FMDiskFileSystem extends AFileSystem
{
    private $dirFiles;

    private $dirCache;

    public $embedPreviews = false;

    function __construct($config)
    {
        $this->dirFiles = $config['dirFiles'];
        if (substr($this->dirFiles, -1) == '/')
            $this->dirFiles = substr($this->dirFiles, 0, -1);

        $this->dirCache = $config['dirCache'];
        if (substr($this->dirCache, -1) == '/')
            $this->dirCache = substr($this->dirCache, 0, -1);
    }

    const MAX_DEPTH = 20;

    function getDirs($hideDirs)
    {
        $dirs = [];
        $fDir = $this->dirFiles;
        if (!file_exists($fDir) || !is_dir($fDir)) {
            throw new MessageException(
                FMMessage::createMessage(FMMessage::FM_ROOT_DIR_DOES_NOT_EXIST)
            );
        }

        $hideDirs[] = '.cache';

        $this->getDirs__fill($dirs, $fDir, $hideDirs, '', 0);
        return $dirs;
    }

    private function getDirs__fill(&$dirs, $fDir, $hideDirs, $path, $currDepth)
    {
        $files = scandir($fDir);

        $i = strrpos($fDir, '/');
        if ($i !== false) {
            $dirName = substr($fDir, $i + 1);
        } else {
            $dirName = $fDir;
        }

        if ($files === false) {
            throw new MessageException(
                FMMessage::createMessage(
                    FMMessage::FM_UNABLE_TO_LIST_CHILDREN_IN_DIRECTORY
                )
            );
        }

        $dirsCount = 0;
        $filesCount = 0;
        for ($i = 0; $i < count($files); $i++) {
            $file = $files[$i];
            if ($file === '.' || $file === '..') {
                continue;
            }
            if (is_file($fDir . '/' . $file)) {
                $filesCount++;
            } else {
                if (is_dir($fDir . '/' . $file)) {
                    $dirsCount++;
                }
            }
        }

        $dir = new FMDir($dirName, $path, $filesCount, $dirsCount);
        $dirs[] = $dir;

        for ($i = 0; $i < count($files); $i++) {
            $file = $files[$i];
            if ($file !== '.' && $file !== '..') {

                $isHide = FALSE;
                for ($j = 0; $j < count($hideDirs) && !$isHide; $j ++)
                    $isHide = $isHide || fnmatch($hideDirs[$j], $file);

                if (is_dir($fDir . '/' . $file) && !$isHide && $currDepth < self::MAX_DEPTH) {
                    $this->getDirs__fill(
                        $dirs,
                        $fDir . '/' . $file,
                        $hideDirs,
                        $path . (strlen($path) > 0 ? '/' : '') . $dirName,
                        $currDepth + 1
                    );
                }
            }
        }
    }

    private function getRelativePath($path)
    {
        if (strpos($path, '..') !== false) {
            throw new MessageException(
                FMMessage::createMessage(
                    FMMessage::FM_DIR_NAME_CONTAINS_INVALID_SYMBOLS
                )
            );
        }

        if (strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }

        $rootDirName = $this->getRootDirName();

        if (strpos($path, '/' . $rootDirName) !== 0) {
            throw new MessageException(
                FMMessage::createMessage(FMMessage::FM_DIR_NAME_INCORRECT_ROOT)
            );
        }

        return substr($path, strlen('/' . $rootDirName));
    }

    function getAbsolutePath($path)
    {
        return $this->dirFiles . $this->getRelativePath($path);
    }

    private function rmDirRecursive($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!$this->rmDirRecursive($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($dir);
    }

    function deleteDir($dirPath)
    {
        $fullPath = $this->getAbsolutePath($dirPath);
        $res = $this->rmDirRecursive($fullPath);
        if ($res === false) {
            throw new MessageException(
                FMMessage::createMessage(
                    FMMessage::FM_UNABLE_TO_DELETE_DIRECTORY
                )
            );
        }
    }

    function createDir($dirPath, $name)
    {
        if (strpos($name, '..') !== false || strpos($name, '/') !== false) {
            throw new MessageException(
                FMMessage::createMessage(
                    FMMessage::FM_DIR_NAME_CONTAINS_INVALID_SYMBOLS
                )
            );
        }

        $fullPath = $this->getAbsolutePath($dirPath) . '/' . $name;
        $res = file_exists($fullPath) || mkdir($fullPath, 0777, TRUE);
        if ($res === false) {
            throw new MessageException(
                FMMessage::createMessage(
                    FMMessage::FM_UNABLE_TO_CREATE_DIRECTORY
                )
            );
        }
    }

    private function renameFileOrDir($path, $newName)
    {
        if (
            strpos($newName, '..') !== false ||
            strpos($newName, '/') !== false
        ) {
            throw new MessageException(
                FMMessage::createMessage(
                    FMMessage::FM_DIR_NAME_CONTAINS_INVALID_SYMBOLS
                )
            );
        }

        $fullPath = $this->getAbsolutePath($path);

        $i = strrpos($fullPath, '/');
        $fullPathDst = substr($fullPath, 0, $i + 1) . $newName;
        if (is_file($fullPathDst)) {
            throw new MessageException(
                Message::createMessage(Message::FILE_ALREADY_EXISTS, $newName)
            );
        }

        $res = rename($fullPath, $fullPathDst);
        if ($res === false) {
            throw new MessageException(
                FMMessage::createMessage(FMMessage::FM_UNABLE_TO_RENAME)
            );
        }
    }

    public function renameFile($filePath, $newName)
    {
        $this->renameFileOrDir($filePath, $newName);
    }

    public function renameDir($dirPath, $newName)
    {
        $this->renameFileOrDir($dirPath, $newName);
    }

    private function profile($text, $start) {
        $now = microtime(true);
        $time = $now - $start;
        //error_log($text. " done in ".number_format($time/1000, 5, ",", "")." sec\n");
        return $now;
    }

    // $files are like: "file.jpg" or "dir/file.png" - they start not with "/root_dir/"
    // This is because we need to validate files before dir tree is loaded on a client
    public function getFilesSpecified(
        $files
    ) {
        $result = [];
        for ($i = 0; $i < count($files); $i++) {

            $file = '/' . $files[$i];

            if (strpos($file, '..') !== false) {
                throw new MessageException(
                    FMMessage::createMessage(
                        FMMessage::FM_DIR_NAME_CONTAINS_INVALID_SYMBOLS
                    )
                );
            }

            $filePath = "/" . $this->getRootDirName() . $file;

            // Remove end slash
            if (is_file($this->getAbsolutePath($filePath))) {

                $index = strrpos($filePath, "/");
                $dirPath = substr($filePath, 0, $index);
                $fileName = substr($filePath, $index + 1);

                $result[] = array(
                    "dir" => substr($dirPath, strlen($this->getRootDirName()) + 1),
                    "file" => $this->getFileStructure($dirPath, $fileName)
                );
            }

        }
        return $result;
    }

    public function getFilesPaged(
        $dirPath,
        $maxFiles,
        $lastFile,
        $lastIndex,
        $whiteList,
        $blackList,
        $filter,
        $orderBy,
        $orderAsc,
        $formatIds,
        $formatSuffixes
    )
    {
        $now = microtime(true);
        $start = $now;

        $fullPath = $this->getAbsolutePath($dirPath);

        if (!is_dir($fullPath)) {
            throw new MessageException(
                Message::createMessage(Message::DIR_DOES_NOT_EXIST, $dirPath)
            );
        }

        $files = array(); // file name to sort values (like [filename, date, size])
        $formatFiles = array(); // format to array(owner file name to file name)
        foreach ($formatIds as $formatId) {
            $formatFiles[$formatId] = array();
        }

        $fFiles = scandir($fullPath);
        if ($fFiles === false) {
            throw new MessageException(
                FMMessage::createMessage(FMMessage::FM_DIR_CANNOT_BE_READ)
            );
        }

        $now = $this->profile("Scan dir", $now);

        foreach ($fFiles as $file) {

            if ($file == '.' || $file == '..' || !is_file($fullPath . '/' . $file))
                continue;

            $format = null;
            $name = Utils::getNameWithoutExt($file);
            if (Utils::isImage($file)) {
                for ($i = 0; $i < count($formatIds); $i++) {
                    $isFormatFile = Utils::endsWith($name, $formatSuffixes[$i]);
                    if ($isFormatFile) {
                        $format = $formatSuffixes[$i];
                        $name = substr($name, 0, -strlen($formatSuffixes[$i]));
                        break;
                    }
                }
            }

            $ext = Utils::getExt($file);
            if ($ext != NULL)
                $name = $name . '.' . $ext;

            $fieldName = $file;
            $fieldDate = filemtime($fullPath . '/' . $file);
            $fieldSize = filesize($fullPath . '/' . $file);
            if ($format == NULL) {
                switch ($orderBy) {
                    case 'date':
                        $files[$file] = [$fieldDate, $fieldName, $fieldSize];
                        break;
                    case 'size':
                        $files[$file] = [$fieldSize, $fieldName, $fieldDate];
                        break;
                    case 'name':
                    default:
                        $files[$file] = [$fieldName, $fieldDate, $fieldSize];
                        break;
                }
            } else {
                $formatFiles[$format][$name] = $file;
            }
        }
        $now = $this->profile("Fill image formats", $now);

        // Remove files outside of white list, and their formats too
        if (count($whiteList) > 0) { // only if whitelist is set
            foreach ($files as $file => $v) {

                $isMatch = false;
                foreach ($whiteList as $mask) {
                    if (fnmatch($mask, $file) === TRUE)
                        $isMatch = true;
                }

                if (!$isMatch) {
                    unset($files[$file]);
                    foreach ($formatFiles as $format => $formatFilesCurr) {
                        if (isset($formatFilesCurr[$file]))
                            unset($formatFilesCurr[$file]);
                    }
                }
            }
        }

        $now = $this->profile("White list", $now);

        // Remove files outside of black list, and their formats too
        foreach ($files as $file => $v) {

            $isMatch = false;
            foreach ($blackList as $mask) {
                if (fnmatch($mask, $file) === TRUE)
                    $isMatch = true;
            }

            if ($isMatch) {
                unset($files[$file]);
                foreach ($formatFiles as $format => $formatFilesCurr) {
                    if (isset($formatFilesCurr[$file]))
                        unset($formatFilesCurr[$file]);
                }
            }
        }

        $now = $this->profile("Black list", $now);

        // Remove files not matching the filter, and their formats too
        foreach ($files as $file => $v) {

            $isMatch = fnmatch($filter, $file) === TRUE;
            if (!$isMatch) {
                unset($files[$file]);
                foreach ($formatFiles as $format => $formatFilesCurr) {
                    if (isset($formatFilesCurr[$file]))
                        unset($formatFilesCurr[$file]);
                }
            }
        }

        $now = $this->profile("Filter", $now);

        uasort($files, function ($arr1, $arr2) {

            for ($i=0; $i<count($arr1); $i++) {
                if (is_string($arr1[$i])) {
                    $v = strnatcmp($arr1[$i], $arr2[$i]);
                    if ($v !== 0)
                        return $v;
                } else {
                    if ($arr1[$i] > $arr2[$i])
                        return 1;
                    if ($arr1[$i] < $arr2[$i])
                        return -1;
                }
            }

            return 0;
        });

        $fileNames = array_keys($files);

        if (strtolower($orderAsc) !== "true") {
            $fileNames = array_reverse($fileNames);
        }

        $now = $this->profile("Sorting", $now);

        $startIndex = 0;
        if ($lastIndex)
            $startIndex = $lastIndex + 1;
        if ($lastFile) { // $lastFile priority is higher than $lastIndex
            $i = array_search($lastFile, $fileNames);
            if ($i !== FALSE) {
                $startIndex = $i + 1;
            }
        }

        $isEnd = $startIndex + $maxFiles >= count($fileNames); // are there any files after current page?
        $fileNames = array_slice($fileNames, $startIndex, $maxFiles);

        $now = $this->profile("Page slice", $now);

        $resultFiles = array();

        // Create result file list for output,
        // attach image attributes and image formats for image files.
        foreach ($fileNames as $fileName) {

            $resultFile = $this->getFileStructure($dirPath, $fileName);

            // Find formats of these files
            foreach ($formatIds as $formatId) {
                if (array_key_exists($fileName, $formatFiles[$formatId])) {
                    $formatFileName = $formatFiles[$formatId][$fileName];

                    $formatFile = $this->getFileStructure($dirPath, $formatFileName);
                    $resultFile['formats'][$formatId] = $formatFile;
                }
            }

            $resultFiles[] = $resultFile;
        }

        $now = $this->profile("Create output list", $now);
        $this->profile("Total", $start);

        return array(
            'files' => $resultFiles,
            'isEnd' => $isEnd
        );
    }

    public function getFileStructure($dirPath, $fileName) {

        $fullPath = $this->getAbsolutePath($dirPath);

        $resultFile = array(
            'name' => $fileName,
            'size' => filesize($fullPath . '/' . $fileName),
            'timestamp' => filemtime($fullPath . '/' . $fileName) * 1000,
        );

        if (Utils::isImage($fileName)) {

            $imageInfo = $this->getCachedImageInfo($dirPath . '/' . $fileName);
            $resultFile['width'] = isset($imageInfo['width']) ? $imageInfo['width'] : NULL;
            $resultFile['height'] = isset($imageInfo['height']) ? $imageInfo['height'] : NULL;
            $resultFile['blurHash'] = isset($imageInfo['blurHash']) ? $imageInfo['blurHash'] : NULL;

            $resultFile['formats'] = array();
        }

        return $resultFile;
    }

    public function getFiles($dirPath)
    {
        // with "/root_dir_name" in the start

        $fullPath = $this->getAbsolutePath($dirPath);

        if (!is_dir($fullPath)) {
            throw new MessageException(
                Message::createMessage(Message::DIR_DOES_NOT_EXIST, $dirPath)
            );
        }

        $fFiles = scandir($fullPath);
        if ($fFiles === false) {
            throw new MessageException(
                FMMessage::createMessage(FMMessage::FM_DIR_CANNOT_BE_READ)
            );
        }

        $files = [];
        for ($i = 0; $i < count($fFiles); $i++) {
            $fFile = $fFiles[$i];

            if (preg_match('/-(preview|medium|original)\\.[^.]+$/', $fFile) === 1)
                continue;

            $fileFullPath = $fullPath . '/' . $fFile;
            if (is_file($fileFullPath)) {
                $preview = null;
                try {
                    $imageInfo = Utils::getImageInfo($fileFullPath);
                    if ($this->embedPreviews === TRUE) {
                        list($previewFormat, $previewFile) = $this->getImagePreview($dirPath . '/' . $fFile, 159, 139);
                        $previewData = file_get_contents($previewFile);
                        $preview = "data:" . $previewFormat . ";base64," . base64_encode($previewData);
                    }

                } catch (Exception $e) {
                    $imageInfo = new ImageInfo();
                    $imageInfo->width = null;
                    $imageInfo->height = null;
                }
                $file = new FMFile(
                    $dirPath,
                    $fFile,
                    filesize($fileFullPath),
                    filemtime($fileFullPath),
                    $imageInfo
                );
                if ($preview != null) {
                    $file->preview = $preview;
                }
    

                $files[] = $file;
            }
        }

        return $files;
    }

    public function getImageSize($file)
    {
        return @getimagesize($file);
    }

    private function getRootDirName()
    {
        $i = strrpos($this->dirFiles, '/');
        if ($i === false) {
            return $this->dirFiles;
        }
        return substr($this->dirFiles, $i + 1);
    }

    // "suffixes" is an optional parameter (does not supported by Flmngr UI v1)
    function deleteFiles($filesPaths, $formatSuffixes)
    {
        for ($i = 0; $i < count($filesPaths); $i++) {
            $fullPath = $this->getAbsolutePath($filesPaths[$i]);
            $fullPaths = [$fullPath];

            $index = strrpos($fullPath,  '.');
            if ($index > -1) {
                $fullPathPrefix = substr($fullPath, 0, $index);
            } else {
                $fullPathPrefix = $fullPath;
            }
            if (isset($formatSuffixes) && is_array($formatSuffixes)) {
                for ($j=0; $j<count($formatSuffixes); $j++) {
                    $exts = ["png", "jpg", "jpeg", "webp"];
                    for ($k=0; $k<count($exts); $k++)
                        $fullPaths[] = $fullPathPrefix . $formatSuffixes[$j] . '.' . $exts[$k];
                }
            }

            $cachedFile = $this->getCachedFile($filesPaths[0]);
            $cachedFile->delete();

            for ($j=0; $j<count($fullPaths); $j++) {
                // Previews can not exist, but original file must present
                if (is_file($fullPaths[$j]) || $j === 0) {
                    $res = unlink($fullPaths[$j]);
                    if ($res === false) {
                        throw new MessageException(
                            Message::createMessage(
                                Message::UNABLE_TO_DELETE_FILE,
                                $fullPaths[$j]
                            )
                        );
                    }
                }
            }
        }
    }

    function copyFiles($filesPaths, $newPath)
    {
        for ($i = 0; $i < count($filesPaths); $i++) {
            $fullPathSrc = $this->getAbsolutePath($filesPaths[$i]);

            $index = strrpos($fullPathSrc, '/');
            $name =
                $index === false
                    ? $fullPathSrc
                    : substr($fullPathSrc, $index + 1);
            $fullPathDst = $this->getAbsolutePath($newPath) . '/' . $name;

            $res = copy($fullPathSrc, $fullPathDst);
            if ($res === false) {
                throw new MessageException(
                    FMMessage::createMessage(
                        FMMessage::FM_ERROR_ON_COPYING_FILES
                    )
                );
            }
        }
    }

    function moveFiles($filesPaths, $newPath)
    {
        for ($i = 0; $i < count($filesPaths); $i++) {
            $fullPathSrc = $this->getAbsolutePath($filesPaths[$i]);

            $index = strrpos($fullPathSrc, '/');
            $name =
                $index === false
                    ? $fullPathSrc
                    : substr($fullPathSrc, $index + 1);
            $fullPathDst = $this->getAbsolutePath($newPath) . '/' . $name;

            $res = rename($fullPathSrc, $fullPathDst);
            if ($res === false) {
                throw new MessageException(
                    FMMessage::createMessage(
                        FMMessage::FM_ERROR_ON_MOVING_FILES
                    )
                );
            }
        }
    }

    // $mode:
    // "ALWAYS"
    // To recreate image preview in any case (even it is already generated before)
    // Used when user uploads a new image and needs to get its preview

    // "DO_NOT_UPDATE"
    // To create image only if it does not exist, if exists - its path will be returned
    // Used when user selects existing image in file manager and needs its preview

    // "IF_EXISTS"
    // To recreate preview if it already exists
    // Used when file was reuploaded, edited and we recreate previews for all formats we do not need right now, but used somewhere else

    // File uploaded / saved in image editor and reuploaded: $mode is "ALWAYS" for required formats, "IF_EXISTS" for the others
    // User selected image in file manager:                  $mode is "DO_NOT_UPDATE" for required formats and there is no requests for the otheres
    function resizeFile(
        $filePath,
        $newFileNameWithoutExt,
        $width,
        $height,
        $mode
    ) {
        // $filePath here starts with "/", not with "/root_dir"
        $rootDir = $this->getRootDirName();
        $filePath = '/' . $rootDir . $filePath;
        $srcPath = $this->getAbsolutePath($filePath);
        $index = strrpos($srcPath, '/');
        $oldFileNameWithExt = substr($srcPath, $index + 1);
        $newExt = 'png';
        $oldExt = strtolower(Utils::getExt($srcPath));
        if ($oldExt === 'jpg' || $oldExt === 'jpeg') {
            $newExt = 'jpg';
        }
        if ($oldExt === 'webp') {
            $newExt = 'webp';
        }
        $dstPath =
            substr($srcPath, 0, $index) .
            '/' .
            $newFileNameWithoutExt .
            '.' .
            $newExt;

        if (
            Utils::getNameWithoutExt($dstPath) ===
            Utils::getNameWithoutExt($srcPath)
        ) {
            // This is `default` format request - we need to process the image itself without changing its extension
            $dstPath = $srcPath;
        }

        if ($mode === 'IF_EXISTS' && !file_exists($dstPath)) {
            throw new MessageException(
                Message::createMessage(
                    FMMessage::FM_NOT_ERROR_NOT_NEEDED_TO_UPDATE
                )
            );
        }

        if ($mode === 'DO_NOT_UPDATE' && file_exists($dstPath)) {
            $url = substr($dstPath, strlen($this->dirFiles) + 1);
            if (strpos($url, '/') !== 0) {
                $url = '/' . $url;
            }
            return $url;
        }

        $image = null;
        switch (Utils::getMimeType($srcPath)) {
            case 'image/gif':
                $image = @imagecreatefromgif($srcPath);
                break;
            case 'image/jpeg':
                $image = @imagecreatefromjpeg($srcPath);
                break;
            case 'image/png':
                $image = @imagecreatefrompng($srcPath);
                break;
            case 'image/bmp':
                $image = @imagecreatefromwbmp($srcPath);
                break;
            case 'image/webp':
                // If you get problems with WEBP preview creation, please consider updating GD > 2.2.4
                // https://stackoverflow.com/questions/59621626/converting-webp-to-jpeg-in-with-php-gd-library
                $image = @imagecreatefromwebp($srcPath);
                break;
            case 'image/svg+xml':
                // Return SVG as is
                $url = substr($srcPath, strlen($this->dirFiles) + 1);
                if (strpos($url, '/') !== 0) {
                    $url = '/' . $url;
                }
                return $url;
        }

        // Somewhy it can not read ONLY SOME JPEG files, we've caught it on Windows + IIS + PHP
        // Solution from here: https://github.com/libgd/libgd/issues/206
        if (!$image) {
            $image = imagecreatefromstring(file_get_contents($srcPath));
        }
        // end of fix

        if (!$image) {
            throw new MessageException(
                Message::createMessage(Message::IMAGE_PROCESS_ERROR)
            );
        }
        imagesavealpha($image, true);

        $imageInfo = Utils::getImageInfo($srcPath);

        $originalWidth = $imageInfo->width;
        $originalHeight = $imageInfo->height;

        $needToFitWidth = $originalWidth > $width && $width > 0;
        $needToFitHeight = $originalHeight > $height && $height > 0;
        if ($needToFitWidth && $needToFitHeight) {
            if ($width / $originalWidth < $height / $originalHeight) {
                $needToFitHeight = false;
            } else {
                $needToFitWidth = false;
            }
        }

        if (!$needToFitWidth && !$needToFitHeight) {
            // if we generated the preview in past, we need to update it in any case
            if (
                !file_exists($dstPath) ||
                $newFileNameWithoutExt . '.' . $oldExt === $oldFileNameWithExt
            ) {
                // return old file due to it has correct width/height to be used as a preview
                $url = substr($srcPath, strlen($this->dirFiles) + 1);
                if (strpos($url, '/') !== 0) {
                    $url = '/' . $url;
                }
                return $url;
            } else {
                $width = $originalWidth;
                $height = $originalHeight;
            }
        }

        if ($needToFitWidth) {
            $ratio = $width / $originalWidth;
            $height = $originalHeight * $ratio;
        } elseif ($needToFitHeight) {
            $ratio = $height / $originalHeight;
            $width = $originalWidth * $ratio;
        }

        $resizedImage = imagecreatetruecolor($width, $height);
        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);
        imagecopyresampled(
            $resizedImage,
            $image,
            0,
            0,
            0,
            0,
            $width,
            $height,
            $originalWidth,
            $originalHeight
        );

        $result = false;
        $ext = strtolower(Utils::getExt($dstPath));
        if ($ext === 'png') {
            $result = imagepng($resizedImage, $dstPath);
        } elseif ($ext === 'jpg' || $ext === 'jpeg') {
            $result = imagejpeg($resizedImage, $dstPath);
        } elseif ($ext === 'bmp') {
            $result = imagebmp($resizedImage, $dstPath);
        } elseif ($ext === 'webp') {
            $result = imagewebp($resizedImage, $dstPath);
        } else {
            $result = true;
        } // do not resize other formats (i. e. GIF)

        if ($result === false) {
            throw new MessageException(
                FMMessage::createMessage(
                    FMMessage::FM_UNABLE_TO_WRITE_PREVIEW_IN_CACHE_DIR,
                    $dstPath
                )
            );
        }

        $url = substr($dstPath, strlen($this->dirFiles) + 1);
        if (strpos($url, '/') !== 0) {
            $url = '/' . $url;
        }
        return $url;
    }

    function copyCommited($from, $to)
    {
        return Utils::copyFile($from, $to);
    }

    function moveDir($dirPath, $newPath)
    {
        $fullPathSrc = $this->getAbsolutePath($dirPath);

        $index = strrpos($fullPathSrc, '/');
        $name =
            $index === false ? $fullPathSrc : substr($fullPathSrc, $index + 1);
        $fullPathDst = $this->getAbsolutePath($newPath) . '/' . $name;

        $res = rename($fullPathSrc, $fullPathDst);
        if ($res === false) {
            throw new MessageException(
                FMMessage::createMessage(FMMessage::FM_ERROR_ON_MOVING_FILES)
            );
        }
    }

    function copyDir($dirPath, $newPath)
    {
        $fullPathSrc = $this->getAbsolutePath($dirPath);

        $index = strrpos($fullPathSrc, '/');
        $name =
            $index === false ? $fullPathSrc : substr($fullPathSrc, $index + 1);
        $fullPathDst = $this->getAbsolutePath($newPath) . '/' . $name;

        $res = $this->copyDir__recurse($fullPathSrc, $fullPathDst);
        if ($res === false) {
            throw new MessageException(
                FMMessage::createMessage(FMMessage::FM_ERROR_ON_MOVING_FILES)
            );
        }
    }

    private function copyDir__recurse($src, $dst)
    {
        $dir = opendir($src);
        mkdir($dst, 0777, TRUE);
        while (false !== ($file = readdir($dir))) {
            if ($file != '.' && $file != '..') {
                if (is_dir($src . '/' . $file)) {
                    $res = $this->copyDir__recurse(
                        $src . '/' . $file,
                        $dst . '/' . $file
                    );
                    if ($res === false) {
                        return false;
                    }
                } else {
                    $res = copy($src . '/' . $file, $dst . '/' . $file);
                    if ($res === false) {
                        return false;
                    }
                }
            }
        }
        closedir($dir);
        return true;
    }

    function getImageOriginal($filePath)
    {
        $mimeType = Utils::getMimeType($filePath);
        if ($mimeType == null) {
            throw new MessageException(
                FMMessage::createMessage(FMMessage::FM_FILE_IS_NOT_IMAGE)
            );
        }

        $fullPath = $this->getAbsolutePath($filePath);

        if (file_exists($fullPath)) {
            $f = fopen($fullPath, 'rb');
            if ($f) {
                return [$mimeType, $f];
            }
        }
        throw new MessageException(
            FMMessage::createMessage(FMMessage::FM_FILE_DOES_NOT_EXIST)
        );
    }

    function passThrough($fullPath, $mimeType)
    {
        $f = fopen($fullPath, 'rb');
        header('Content-Type:' . $mimeType);
        fpassthru($f);
    }

    function getDirZipArchive($dirPath, $out)
    {
        // TODO: Implement getDirZipArchive() method.
    }

    function getCachedFile($filePath) {
        return new CachedFile(
            $this->getRelativePath($filePath),
            $this->getAbsolutePath($filePath),
            $this->dirFiles,
            $this->dirCache
        );
    }

    function getCachedImageInfo($filePath) {
        return $this->getCachedFile($filePath)->getInfo();
    }

    function getImagePreview($filePath, $width, $height)
    {
        return $this->getCachedFile($filePath)->getPreview($width, $height);
    }
}
