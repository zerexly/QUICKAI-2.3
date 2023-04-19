<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FlmngrServer\lib\config;

interface IConfig
{
    public function setTestConfig($testConf);

    public function getBaseDir();
    public function getTmpDir();

    public function getMaxUploadFileSize();
    public function getAllowedExtensions();
    public function getJpegQuality();

    public function getMaxImageResizeWidth();
    public function getMaxImageResizeHeight();

    public function getCrossDomainUrl();

    public function doKeepUploads();

    public function isTestAllowed();

    public function getRelocateFromHosts();
}
