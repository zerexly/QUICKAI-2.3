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

class DownloadedURL
{
    public $fileName = null;
    public $contentType = null;
    public $contentLength = -1;
}

class URLDownloader
{
    public static function download($url, $dir)
    {
        $result = URLDownloader::downloadURL($url, $dir);
        return $result;
    }

    private static function downloadURL($url, $dir)
    {
        $curl = curl_init($url);
        curl_setopt(
            $curl,
            CURLOPT_USERAGENT,
            'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.95 Safari/537.11'
        );
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // for not redirecting response to stdout
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // allow redirects

        $headers = [];
        curl_setopt($curl, CURLOPT_HEADERFUNCTION, function (
            $curl_,
            $header
        ) use (&$headers) {
            $len = strlen($header);
            $header = explode(':', $header, 2);
            if (count($header) < 2) {
                // ignore invalid headers
                return $len;
            }
            $name = strtolower(trim($header[0]));
            if (!array_key_exists($name, $headers)) {
                $headers[$name] = [trim($header[1])];
            } else {
                $headers[$name][] = trim($header[1]);
            }
            return $len;
        });

        $result = new DownloadedURL();
        $fileName = '';
        $response = curl_exec($curl);

        if ($response !== false) {
            $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($responseCode == '200') {
                if (array_key_exists('Content-Type', $headers)) {
                    $result->contentType = $headers['Content-Type'];
                }
                if (array_key_exists('Content-Length', $headers)) {
                    $result->contentLength = $headers['Content-Length'];
                }
                if (array_key_exists('Content-Disposition', $headers)) {
                    $contentDisposition = $headers['Content-Disposition'];
                    $index = strpos($contentDisposition, 'filename=');
                    if ($index !== false) {
                        $fileName = substr($contentDisposition, $index + 10);
                    }
                }
                if (strlen(trim($fileName)) == 0) {
                    $index = strrpos($url, '/');
                    $fileName = substr($url, $index + 1);
                    $index = strpos($fileName, '?');
                    if ($index !== false) {
                        $fileName = substr($fileName, 0, $index);
                    }
                }
                if (strlen(trim($fileName)) === 0) {
                    $fileName = 'url';
                }
                $fileName = Utils::fixFileName($fileName);
                $fileName = Utils::getFreeFileName($dir, $fileName, false);
            } else {
                throw new MessageException(
                    Message::createMessage(
                        Message::DOWNLOAD_FAIL_CODE,
                        $responseCode
                    )
                );
            }
        } else {
            throw new MessageException(
                Message::createMessage(
                    Message::DOWNLOAD_FAIL_IO,
                    curl_error($curl)
                )
            );
        }

        $saveFilePath = $dir . '/' . $fileName;
        file_put_contents($saveFilePath, $response);
        curl_close($curl);

        $result->fileName = $fileName;
        return $result;
    }
}
