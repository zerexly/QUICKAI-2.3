<?php

/**
 * Flmngr Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FlmngrServer\fs;

use EdSDK\FlmngrServer\lib\action\resp\Message;
use EdSDK\FlmngrServer\lib\file\blurHash\Blurhash;
use EdSDK\FlmngrServer\lib\file\Utils;
use EdSDK\FlmngrServer\lib\MessageException;
use EdSDK\FlmngrServer\model\FMMessage;

class CachedFile {

    private $fileRelative;
    private $fileAbsolute;
    private $dirFiles;
    private $dirCache;

    private $cacheFileAbsolute; // $dirCache/path/to/file.jpg (.json|.png will be added later)
    private $cacheFileJsonAbsolute;
    private $cacheFilePreviewAbsolute;

    function __construct(
        $fileRelative, // Example: /path/to/file.jpg
        $fileAbsolute, // Example: $dirFiles/path/to/file.jpg
        $dirFiles,
        $dirCache
    ) {
        $this->fileRelative = $fileRelative;
        $this->fileAbsolute = $fileAbsolute;
        $this->dirFiles = $dirFiles;
        $this->dirCache = $dirCache;

        $this->cacheFileAbsolute = Utils::normalizeNoEndSeparator($this->dirCache) . $fileRelative;
        if ($dirCache === $dirFiles) {
            $i = strrpos($this->cacheFileAbsolute, '/');
            $this->cacheFileAbsolute = substr($this->cacheFileAbsolute, 0, $i + 1) .
                '.cache/' . substr($this->cacheFileAbsolute, $i + 1);
        }

        $this->cacheFileJsonAbsolute = $this->cacheFileAbsolute . '.json';
        $this->cacheFilePreviewAbsolute = $this->cacheFileAbsolute . '.png';

        clearstatcache(TRUE, $this->dirCache);
        if (!file_exists($this->dirCache)) {
            if (!mkdir($this->dirCache, 0777, TRUE)) {
                error_log("Unable to create cache directory: " . $this->dirCache);
                throw new MessageException(
                    FMMessage::createMessage(
                        FMMessage::FM_UNABLE_TO_CREATE_DIRECTORY
                    )
                );
            }
        }
    }

    // Clears cache for this file
    function delete() {
        @unlink($this->cacheFileJsonAbsolute);
        @unlink($this->cacheFilePreviewAbsolute);
    }

    function getInfo()
    {
        if (!file_exists($this->cacheFileJsonAbsolute)) {

            try {

                $size = @getimagesize($this->fileAbsolute);

                if ($size == FALSE) {
                    error_log("Unable to get size in file " . $this->cacheFileJsonAbsolute);
                    return NULL;
                }

                $width = $size[0];
                $height = $size[1];

                // We do not calculate BlurHash here due to this is a long operation
                // BlurHash will be calculated and JSON file will be updated on the first getImagePreview() call

                clearstatcache(TRUE, $this->fileAbsolute);
                $info = array(
                    'width' => $width,
                    'height' => $height,
                    'mtime' => filemtime($this->fileAbsolute),
                    'size' => filesize($this->fileAbsolute)
                );
                $this->writeInfo($info);

            } catch (Exception $e) {
                error_log("Exception while getting image size of " . $this->fileAbsolute);
                error_log($e);
            }
        }

        $content = file_get_contents($this->cacheFileJsonAbsolute);
        if ($content === FALSE) {
            error_log("Unable to read file " . $this->cacheFileJsonAbsolute);
            return NULL;
        }

        $json = json_decode($content, true);
        if ($json === null) {
            error_log("Unable to parse JSON from file " . $this->cacheFileJsonAbsolute);
            return NULL;
        }

        return $json;
    }

    private function writeInfo($info) {
        $dirname = dirname($this->cacheFileJsonAbsolute);
        if (!is_dir($dirname)) {
            mkdir($dirname, 0777, TRUE);
        }
        $f = fopen($this->cacheFileJsonAbsolute, 'w');
        fwrite($f, json_encode($info));
        fclose($f);
    }

    function getPreview($width, $height)
    {
        $cacheFilePreviewAbsolute = $this->cacheFileAbsolute . '.png';

        if (file_exists($cacheFilePreviewAbsolute)) {
            $info = $this->getInfo();
            clearstatcache(TRUE, $this->fileAbsolute);
            if (
                $info == NULL ||
                $info['mtime'] !== filemtime($this->fileAbsolute) ||
                $info['size'] !== filesize($this->fileAbsolute)
            ) {
                // Delete preview if it was changed, will be recreated below
                unlink($cacheFilePreviewAbsolute);
            }
        }

        $resizedImage = null;
        if (!file_exists($cacheFilePreviewAbsolute)) {
            $image = null;
            switch (Utils::getMimeType($this->fileAbsolute)) {
                case 'image/gif':
                    $image = @imagecreatefromgif($this->fileAbsolute);
                    break;
                case 'image/jpeg':
                    $image = @imagecreatefromjpeg($this->fileAbsolute);
                    break;
                case 'image/png':
                    $image = @imagecreatefrompng($this->fileAbsolute);
                    break;
                case 'image/bmp':
                    $image = @imagecreatefromwbmp($this->fileAbsolute);
                    break;
                case 'image/webp':
                    // If you get problems with WEBP preview creation, please consider updating GD > 2.2.4
                    // https://stackoverflow.com/questions/59621626/converting-webp-to-jpeg-in-with-php-gd-library
                    $image = @imagecreatefromwebp($this->fileAbsolute);
                    break;
                case 'image/svg+xml':
                    return ['image/svg+xml', fopen($this->fileAbsolute, 'rb')];
            }

            // Somewhy it can not read ONLY SOME JPEG files, we've caught it on Windows + IIS + PHP
            // Solution from here: https://github.com/libgd/libgd/issues/206
            if (!$image) {
                $image = imagecreatefromstring(file_get_contents($this->fileAbsolute));
            }
            // end of fix

            if (!$image) {
                throw new MessageException(
                    Message::createMessage(Message::IMAGE_PROCESS_ERROR)
                );
            }
            imagesavealpha($image, true);

            // TODO:
            // throw new MessageException(FMMessage.createMessage(FMMessage.FM_UNABLE_TO_CREATE_PREVIEW));

            $imageInfo = Utils::getImageInfo($this->fileAbsolute);
            $xx = $imageInfo->width;
            $yy = $imageInfo->height;
            $ratio_original = $xx / $yy; // ratio original

            if ($width == NULL) {
                $width = floor($ratio_original * $height);
            } else if ($height == NULL) {
                $height = floor((1 / $ratio_original) * $width);
            }

            $ratio_thumb = $width / $height; // ratio thumb

            if ($ratio_original >= $ratio_thumb) {
                $yo = $yy;
                $xo = ceil(($yo * $width) / $height);
                $xo_ini = ceil(($xx - $xo) / 2);
                $xy_ini = 0;
            } else {
                $xo = $xx;
                $yo = ceil(($xo * $height) / $width);
                $xy_ini = ceil(($yy - $yo) / 2);
                $xo_ini = 0;
            }

            $resizedImage = imagecreatetruecolor($width, $height);

            $colorGray1 = imagecolorallocate($resizedImage, 240, 240, 240);
            $colorGray2 = imagecolorallocate($resizedImage, 250, 250, 250);
            $rectSize = 20;
            for ($x = 0; $x <= floor($width / $rectSize); $x++)
                for ($y = 0; $y <= floor($height / $rectSize); $y++)
                    imagefilledrectangle($resizedImage, $x*$rectSize, $y*$rectSize, $width, $height, ($x + $y) % 2 == 0 ? $colorGray1 : $colorGray2);


            imagecopyresampled(
                $resizedImage,
                $image,
                0,
                0,
                $xo_ini,
                $xy_ini,
                $width,
                $height,
                $xo,
                $yo
            );

            $i = strrpos($cacheFilePreviewAbsolute, '/');
            $cacheDirPreviewAbsolute = substr($cacheFilePreviewAbsolute, 0, $i);
            clearstatcache(TRUE, $cacheDirPreviewAbsolute);
            if (!file_exists($cacheDirPreviewAbsolute)) {
                if (!mkdir($cacheDirPreviewAbsolute, 0777, TRUE)) {
                    error_log("Unable to create cache directory: " . $this->dirCache);
                    throw new MessageException(
                        FMMessage::createMessage(
                            FMMessage::FM_UNABLE_TO_CREATE_DIRECTORY
                        )
                    );
                }
            }

            if (imagepng($resizedImage, $cacheFilePreviewAbsolute) === false) {
                throw new MessageException(
                    FMMessage::createMessage(
                        FMMessage::FM_UNABLE_TO_WRITE_PREVIEW_IN_CACHE_DIR,
                        $cacheFilePreviewAbsolute
                    )
                );
            }
        }

        // Update BlurHash if required
        if (!isset($cachedImageInfo["blurHash"])) {

            if ($resizedImage == null)
                $resizedImage = @imagecreatefrompng($cacheFilePreviewAbsolute);
            $previewImageInfo = Utils::getImageInfo($cacheFilePreviewAbsolute);

            $pixels = [];
            for ($y = 0; $y < $previewImageInfo->height; $y++) {
                $row = [];
                for ($x = 0; $x < $previewImageInfo->width; $x++) {
                    $index = imagecolorat($resizedImage, $x, $y);
                    $colors = imagecolorsforindex($resizedImage, $index);
                    $row[] = [$colors['red'], $colors['green'], $colors['blue']];
                }
                $pixels[] = $row;
            }

            $components_x = 4;
            $components_y = 3;

            $cachedImageInfo = $this->getInfo();
            if (count($pixels) > 0) {
                $cachedImageInfo["blurHash"] = Blurhash::encode($pixels, $components_x, $components_y);
                $this->writeInfo($cachedImageInfo);
            }
        }

        return ['image/png', $cacheFilePreviewAbsolute];
    }


}