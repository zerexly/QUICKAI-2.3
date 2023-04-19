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
use Exception;

class FileCommited extends AFile
{
    const SIZE_PREVIEW = 'preview';
    const SIZE_FULL = 'full';

    protected $m_mainFile; // if set, this means this file is only modification (preview/original)

    protected $m_modificationName;

    public function __construct($config, $dir, $name)
    {
        parent::__construct($config, $dir, $name);
    }

    public function getBaseDir()
    {
        return $this->m_config->getBaseDir();
    }

    protected function getFileModification($modificationName)
    {
        if (!$this->isImage() || $this->m_mainFile != null) {
            throw new Exception(
                'Unable to get modification for not image or main image'
            );
        }
        $name =
            $this->getNameWithoutExt() .
            '-' .
            $modificationName .
            '.' .
            $this->getExt();
        $file = new FileCommited($this->m_config, $this->getDir(), $name);
        $file->m_modificationName = $modificationName;
        $file->m_mainFile = $this;
        return $file;
    }

    public function getFileOriginal()
    {
        return $this->getFileModification('original');
    }
    public function getFilePreview()
    {
        return $this->getFileModification('preview');
    }

    public function getModificationName()
    {
        return $this->m_modificationName;
    }

    public function getModifications()
    {
        $modifications = [];
        $f = $this->getFilePreview();
        if ($f->exists()) {
            $modifications[] = $f;
        }
        $f = $this->getFileOriginal();
        if ($f->exists()) {
            $modifications[] = $f;
        }
        return $modifications;
    }

    public function applySizes($sizes)
    {
        if (!$this->isImage()) {
            return;
        }

        $currPreviewWidth = -1;
        $currPreviewHeight = -1;
        $filePreview = $this->getFilePreview();
        if ($filePreview->exists()) {
            $currPreviewWidth = $filePreview->getImageWidth();
            $currPreviewHeight = $filePreview->getImageHeight();
        }

        $currFullWidth = $this->getImageWidth();
        $currFullHeight = $this->getImageHeight();

        $fileOriginal = $this->getFileOriginal();
        $fileOriginalOrFull = $this;
        if ($fileOriginal->exists()) {
            $fileOriginalOrFull = $fileOriginal;
        }

        $currOriginalWidth = $fileOriginalOrFull->getImageWidth();
        $currOriginalHeight = $fileOriginalOrFull->getImageHeight();

        if (isset($sizes[FileCommited::SIZE_PREVIEW])) {
            if (!$filePreview->exists()) {
                $fileOriginalOrFull->copyTo($filePreview);
            }
            $sizeName = FileCommited::SIZE_PREVIEW;
            $targetSize = $sizes->$sizeName;
            if (
                $targetSize->width != $currPreviewWidth ||
                $targetSize->height != $currPreviewHeight
            ) {
                if ($targetSize->width > 0 || $targetSize->height > 0) {
                    // Target size differs from current
                    if (
                        $targetSize->width < $currOriginalWidth ||
                        $targetSize->height < $currOriginalHeight ||
                        $targetSize->enlarge
                    ) {
                        // not fully auto
                        // We reduce size of image or have enlarge allowed
                        $filePreview->resizeImage($targetSize);
                    }
                }
            }
        }

        if (isset($sizes[FileCommited::SIZE_FULL])) {
            $sizeName = FileCommited::SIZE_FULL;
            $targetSize = $sizes->$sizeName;
            if (
                $targetSize->width != $currFullWidth ||
                $targetSize->height != $currFullHeight
            ) {
                if ($targetSize->width > 0 || $targetSize->height > 0) {
                    if (
                        $targetSize->width < $currOriginalWidth ||
                        $targetSize->height < $currOriginalHeight ||
                        $targetSize->enlarge
                    ) {
                        $originalExisted = $fileOriginal->exists();
                        if (!$originalExisted) {
                            $this->copyTo($fileOriginal);
                        }
                        if (
                            !$this->resizeImage($targetSize) &&
                            !$originalExisted
                        ) {
                            $fileOriginal->delete();
                        }
                    }
                }
            }
        }
    }

    public function getSizes()
    {
        $thisFileFullPath = $this->getFullPath();
        $thisName = $this->getNameWithoutExt();
        $thisFileDir = dirname($this);
        $files = array_diff(scandir($thisFileDir), ['..', '.']);
        $sizes = [];
        for ($i = 0; $i < count($files); $i++) {
            $file = $files[$i];
            $name = basename($file);
            $ext = Utils::getExt($name);
            if ($ext != null) {
                $name = substr($name, 0, strlen($name) - strlen($ext) - 1);
            }
            if (
                $thisFileFullPath !== $file &&
                strpos($name, $thisName . '-') === 0
            ) {
                $sizes[] = substr($name, strlen($thisName) + 1);
            }
        }
        return $sizes;
    }

    public function resizeImage($targetSize)
    {
        if (
            $this->m_config->getMaxImageResizeWidth() > 0 &&
            $targetSize->width > $this->m_config->getMaxImageResizeWidth()
        ) {
            throw new MessageException(
                Message::createMessage(
                    Message::MAX_RESIZE_WIDTH_EXCEEDED,
                    '' . $targetSize->width,
                    $this->getName(),
                    '' . $this->m_config->getMaxImageResizeWidth()
                )
            );
        }

        if (
            $this->m_config->getMaxImageResizeHeight() > 0 &&
            $targetSize->height > $this->m_config->getMaxImageResizeHeight()
        ) {
            throw new MessageException(
                Message::createMessage(
                    Message::MAX_RESIZE_HEIGHT_EXCEEDED,
                    '' . $targetSize->height,
                    $this->getName(),
                    '' . $this->m_config->getMaxImageResizeHeight()
                )
            );
        }

        $fileSrc = $this;
        if ($this->m_mainFile != null) {
            // if this is just a size of main file
            $fileSrc = $this->m_mainFile;
        }
        $fileOriginal = $fileSrc->getFileOriginal();
        if ($fileOriginal->exists()) {
            $fileSrc = $fileOriginal;
        }

        $imageWidth = $this->getImageWidth();
        $imageHeight = $this->getImageHeight();

        if ($targetSize->width == 0 && $targetSize->height == 0) {
            return false;
        }
        if ($targetSize->width == 0 && $targetSize->height == $imageHeight) {
            return false;
        }
        if ($targetSize->height == 0 && $targetSize->width == $imageWidth) {
            return false;
        }
        if (
            $targetSize->width > 0 &&
            $targetSize->height > 0 &&
            $targetSize->width == $imageWidth &&
            $targetSize->height == $imageHeight
        ) {
            return false;
        }

        // Calc full target size of image (with paddings)
        $scaleWWithPadding = -1;
        $scaleHWithPadding = -1;
        if ($targetSize->width > 0 && $targetSize->height > 0) {
            $scaleWWithPadding = $targetSize->width;
            $scaleHWithPadding = $targetSize->height;
        } elseif ($targetSize->width > 0) {
            $scaleWWithPadding = $targetSize->width;
            $scaleHWithPadding = floor(
                ($scaleWWithPadding / $imageWidth) * $imageHeight
            );
        } elseif ($targetSize->height > 0) {
            $scaleHWithPadding = $targetSize->height;
            $scaleWWithPadding = floor(
                ($scaleHWithPadding / $imageHeight) * $imageWidth
            );
        }

        if (
            ($scaleWWithPadding > $imageWidth ||
                $scaleHWithPadding > $imageHeight) &&
            !$targetSize->enlarge
        ) {
            $scaleWWithPadding = $imageWidth;
            $scaleHWithPadding = $imageHeight;
        }

        // Check we have not exceeded max width/height
        if (
            ($this->m_config->getMaxImageResizeWidth() > 0 &&
                $scaleWWithPadding >
                    $this->m_config->getMaxImageResizeWidth()) ||
            ($this->m_config->getMaxImageResizeHeight() > 0 &&
                $scaleHWithPadding > $this->m_config->getMaxImageResizeHeight())
        ) {
            $coef = max(
                $scaleWWithPadding / $this->m_config->getMaxImageResizeWidth(),
                $scaleHWithPadding / $this->m_config->getMaxImageResizeHeight()
            );
            $scaleWWithPadding = floor($scaleWWithPadding / $coef);
            $scaleHWithPadding = floor($scaleHWithPadding / $coef);
        }

        // Calc actual size of image (without paddings)
        $scaleW = -1;
        $scaleH = -1;
        if (
            $scaleWWithPadding / $imageWidth <
            $scaleHWithPadding / $imageHeight
        ) {
            $scaleW = $scaleWWithPadding;
            $scaleH = floor(($scaleW / $imageWidth) * $imageHeight);
        } else {
            $scaleH = $scaleHWithPadding;
            $scaleW = floor(($scaleH / $imageHeight) * $imageWidth);
        }

        if (
            $scaleWWithPadding == $imageWidth &&
            $scaleW == $imageWidth &&
            $scaleHWithPadding == $imageHeight &&
            $scaleH == $imageHeight
        ) {
            return false;
        } // no resize is needed

        $fitMode = FileCommited::FIT_EXACT;
        if ($targetSize->width == 0) {
            $fitMode = FileCommited::FIT_TO_HEIGHT;
        } elseif ($targetSize->height == 0) {
            $fitMode = FileCommited::FIT_TO_WIDTH;
        }
        $image = FileCommited::resizeImageNative(
            $this->getImage(),
            $scaleW,
            $scaleH,
            $fitMode
        );

        if ($scaleWWithPadding > $scaleW || $scaleHWithPadding > $scaleH) {
            $image = $this->addPaddingsToImageNative(
                $image,
                $scaleW,
                $scaleH,
                $scaleWWithPadding,
                $scaleHWithPadding
            );
        }

        $this->writeImage($image);

        return true;
    }

    private function writeImage($image)
    {
        switch (strtolower($this->getExt())) {
            case 'gif':
                imagegif($image, $this->getFullPath());
                break;
            case 'jpeg':
            case 'jpg':
                imagejpeg(
                    $image,
                    $this->getFullPath(),
                    $this->m_config->getJpegQuality()
                );
                break;
            case 'png':
                imagepng($image, $this->getFullPath());
                break;
            case 'bmp':
                imagewbmp($image, $this->getFullPath());
                break;
        }
    }

    const FIT_EXACT = 0;
    const FIT_TO_WIDTH = 1;
    const FIT_TO_HEIGHT = 2;
    public static function resizeImageNative($image, $scaleW, $scaleH, $fitMode)
    {
        $newW = $scaleW;
        $newH = $scaleH;
        if ($fitMode == FileCommited::FIT_TO_WIDTH) {
            $newH = round(($newW * $scaleH) / $scaleW);
        } elseif ($fitMode == FileCommited::FIT_TO_HEIGHT) {
            $newW = round(($newH * $scaleW) / $scaleH);
        }

        $newImage = imagecreatetruecolor($newW, $newH);
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        imagecopyresampled(
            $newImage,
            $image,
            0,
            0,
            0,
            0,
            $newW,
            $newH,
            imagesx($image),
            imagesy($image)
        );

        return $newImage;
    }

    private function addPaddingsToImageNative(
        $image,
        $scaleW,
        $scaleH,
        $scaleWWithPadding,
        $scaleHWithPadding
    ) {
        $imageWithPaddings = imagecreatetruecolor(
            $scaleWWithPadding,
            $scaleHWithPadding
        );
        imagesavealpha($imageWithPaddings, true);

        if (!FileCommited::isTransparent($image)) {
            $bgColor = imagecolorallocate($imageWithPaddings, 255, 255, 255);
        } else {
            $bgColor = imagecolorallocatealpha(
                $imageWithPaddings,
                0,
                0,
                0,
                127
            );
        }
        imagefill($imageWithPaddings, 0, 0, $bgColor);

        $left = floor(($scaleWWithPadding - $scaleW) / 2.0);
        $top = floor(($scaleHWithPadding - $scaleH) / 2.0);
        imagecopy(
            $imageWithPaddings,
            $image,
            $left,
            $top,
            0,
            0,
            imagesx($image),
            imagesy($image)
        );
        return $imageWithPaddings;
    }

    static function isTransparent($image)
    {
        $w = imagesx($image) - 1;
        $w2 = floor($w / 2.0);
        $h = imagesy($image) - 1;
        $h2 = floor($w / 2.0);
        if (FileCommited::isPixelTransparent($image, 0, 0)) {
            return true;
        }
        if (FileCommited::isPixelTransparent($image, $w, 0)) {
            return true;
        }
        if (FileCommited::isPixelTransparent($image, 0, $h)) {
            return true;
        }
        if (FileCommited::isPixelTransparent($image, $w, $h)) {
            return true;
        }
        if ($w2 != $w || $h2 != $h) {
            if (FileCommited::isPixelTransparent($image, $w2, 0)) {
                return true;
            }
            if (FileCommited::isPixelTransparent($image, $w, $h2)) {
                return true;
            }
            if (FileCommited::isPixelTransparent($image, $w2, $h)) {
                return true;
            }
            if (FileCommited::isPixelTransparent($image, 0, $h2)) {
                return true;
            }
        }
        return false;
    }

    static function isPixelTransparent($image, $x, $y)
    {
        $rgba = imagecolorat($image, $x, $y);
        $alpha = ($rgba & 0x7f000000) >> 24;
        return $alpha > 0;
    }

    public function isCommited()
    {
        return true;
    }
}
