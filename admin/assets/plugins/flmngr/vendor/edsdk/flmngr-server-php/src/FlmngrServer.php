<?php

/**
 * Flmngr Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FlmngrServer;

use EdSDK\FlmngrServer\lib\file\Utils;
use EdSDK\FlmngrServer\resp\Response;
use Exception;

use EdSDK\FlmngrServer\lib\JsonCodec;
use EdSDK\FlmngrServer\lib\action\resp\Message;
use EdSDK\FlmngrServer\lib\MessageException;

ini_set('display_errors', 0);

class FlmngrServer
{
    static function flmngrRequest($config)
    {
        if (FlmngrServer::checkUploadLimit())
            return; // file size exceed the limit from php.ini

        if (!isset($config['dirCache'])) {
            $config['dirCache'] = $config['dirFiles'];
            if (!isset($config['dirTmp']))
                $config['dirTmp'] = Utils::normalizeNoEndSeparator($config['dirFiles'] . '/.cache/.tmp');
        }

        if (!isset($config['dirTmp']))
            $config['dirTmp'] = Utils::normalizeNoEndSeparator($config['dirCache'] . '/.tmp');

        $frontController = new FlmngrFrontController($config);
        $request = $frontController->request;
        $config['filesystem'] = $frontController->filesystem;

        if (isset($request->post['embedPreviews'])) {
            $config['filesystem']->embedPreviews = $request->post['embedPreviews'];
            
        }

        $action = null;
        if ($request->requestMethod === 'POST') {
            // Default action is "upload" if requester tries to upload a file
            // This is support for generic files upload in WYSIWYG editors
            if (
                (isset($request->files['file']) ||
                    isset($request->files['upload'])) &&
                !isset($request->post['action']) &&
                !isset($request->post['data'])
            ) {
                $json = [
                    'action' => 'upload',
                ];
                $request->post['data'] = json_encode($json);
            }

            if (isset($request->post['action'])) {
                $action = $request->post['action'];
            }
            if ($action == null && isset($request->post['data'])) {
                $configUploader = [
                    'dirFiles' => $config['dirFiles'],
                    'dirTmp' => $config['dirTmp'],
                    'filesystem' => $config['filesystem'],
                    'config' => isset($config['uploader'])
                        ? $config['uploader']
                        : [],
                ];
                FileUploaderServer::fileUploadRequest(
                    $configUploader,
                    $request->post,
                    $request->files
                );
                return;
            }
        } else {
            if ($request->requestMethod === 'GET') {
                $action = $request->get['action'];
            } else {
                return;
            }
        }
        $config['request'] = $request;

        try {
            switch ($action) {
                case 'dirList':
                    $resp = FlmngrServer::reqDirList($config);
                    break;
                case 'dirCreate':
                    $resp = FlmngrServer::reqDirCreate($config);
                    break;
                case 'dirRename':
                    $resp = FlmngrServer::reqDirRename($config);
                    break;
                case 'dirDelete':
                    $resp = FlmngrServer::reqDirDelete($config);
                    break;
                case 'dirCopy':
                    $resp = FlmngrServer::reqDirCopy($config);
                    break;
                case 'dirMove':
                    $resp = FlmngrServer::reqDirMove($config);
                    break;
                case 'dirDownload':
                    $resp = FlmngrServer::reqDirDownload($config);
                    break;
                case 'fileList':
                    $resp = FlmngrServer::reqFileList($config);
                    break;
                case 'fileListPaged':
                    $resp = FlmngrServer::reqFileListPaged($config);
                    break;
                case 'fileListSpecified':
                    $resp = FlmngrServer::reqFileListSpecified($config);
                    break;
                case 'fileDelete':
                    $resp = FlmngrServer::reqFileDelete($config);
                    break;
                case 'fileCopy':
                    $resp = FlmngrServer::reqFileCopy($config);
                    break;
                case 'fileRename':
                    $resp = FlmngrServer::reqFileRename($config);
                    break;
                case 'fileMove':
                    $resp = FlmngrServer::reqFileMove($config);
                    break;
                case 'fileResize':
                    $resp = FlmngrServer::reqFileResize($config);
                    break;
                case 'fileOriginal':
                    $resp = FlmngrServer::reqFileOriginal($config); // will die after valid response or throw MessageException
                    break;
                case 'filePreview':
                    $resp = FlmngrServer::reqFilePreview($config); // will die after valid response or throw MessageException
                    break;
                case 'upload':
                    $resp = FlmngrServer::upload($config); // will die after valid response or throw MessageException
                    break;
                case 'getVersion':
                    $resp = FlmngrServer::getVersion();
                    break;
                default:
                    $resp = new Response(
                        Message::createMessage(Message::ACTION_NOT_FOUND),
                        null
                    );
            }
        } catch (MessageException $e) {
            $resp = new Response($e->getFailMessage(), null);
        }

        $strResp = JsonCodec::s_toJson($resp);

        try {
            http_response_code(200);
            header('Content-Type: application/json; charset=UTF-8');
            print $strResp;
        } catch (Exception $e) {
            error_log($e);
        }
    }

    private static function iniGetBytes($val)
    {
        $val = trim(ini_get($val));
        if ($val != '') {
            $last = strtolower(substr($val, strlen($val) - 1));
        } else {
            $last = '';
        }
        if ($last !== '') {
            $val = substr($val, 0, strlen($val) - 1);
        }

        switch ($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= 1024;
            // fall through
            case 'm':
                $val *= 1024;
            // fall through
            case 'k':
                $val *= 1024;
            // fall through
        }

        return $val;
    }

    private static function checkUploadLimit()
    {
        if (isset($_SERVER['CONTENT_LENGTH'])) {
            if (
                $_SERVER['CONTENT_LENGTH'] >
                FlmngrServer::iniGetBytes('post_max_size')
            ) {
                $resp = new Response(
                    Message::createMessage(
                        Message::FILE_SIZE_EXCEEDS_SYSTEM_LIMIT,
                        '' . $_SERVER['CONTENT_LENGTH'],
                        '' . FlmngrServer::iniGetBytes('post_max_size')
                    ),
                    null
                );

                $strResp = JsonCodec::s_toJson($resp);

                try {
                    http_response_code(200);
                    header('Content-Type: application/json; charset=UTF-8');
                    print $strResp;
                } catch (Exception $e) {
                    error_log($e);
                }

                return true;
            }
        }
        return false;
    }

    private static function reqDirCopy($config)
    {
        $dirPath = $config['request']->post['d'];
        $newPath = $config['request']->post['n'];
        try {
            $fileSystem = $config['filesystem'];
            $fileSystem->copyDir($dirPath, $newPath);
            return new Response(null, true);
        } catch (MessageException $e) {
            return new Response($e->getFailMessage(), null);
        }
    }

    private static function reqDirCreate($config)
    {
        $dirPath = $config['request']->post['d'];
        $name = $config['request']->post['n'];
        try {
            $fileSystem = $config['filesystem'];
            $fileSystem->createDir($dirPath, $name);
            return new Response(null, true);
        } catch (MessageException $e) {
            return new Response($e->getFailMessage(), null);
        }
    }

    private static function reqDirDelete($config)
    {
        $dirPath = $config['request']->post['d'];
        try {
            $fileSystem = $config['filesystem'];
            $fileSystem->deleteDir($dirPath);
            return new Response(null, true);
        } catch (MessageException $e) {
            return new Response($e->getFailMessage(), null);
        }
    }

    private static function reqDirDownload($config)
    {
        $dirPath = $config['request']->get['d'];
        // TODO:
    }

    private static function reqDirList($config)
    {
        try {
            $fileSystem = $config['filesystem'];
            $dirs = $fileSystem->getDirs(
                isset($config['request']->post['hideDirs'])
                    ? $config['request']->post['hideDirs']
                    : []
            );
        } catch (MessageException $e) {
            return new Response($e->getFailMessage(), null);
        }
        return new Response(null, $dirs);
    }

    private static function reqDirMove($config)
    {
        $dirPath = $config['request']->post['d'];
        $newPath = $config['request']->post['n'];
        try {
            $fileSystem = $config['filesystem'];
            $fileSystem->moveDir($dirPath, $newPath);
            return new Response(null, true);
        } catch (MessageException $e) {
            return new Response($e->getFailMessage(), null);
        }
    }

    private static function reqDirRename($config)
    {
        $dirPath = $config['request']->post['d'];
        $newName = $config['request']->post['n'];
        try {
            $fileSystem = $config['filesystem'];
            $fileSystem->renameDir($dirPath, $newName);
            return new Response(null, true);
        } catch (MessageException $e) {
            return new Response($e->getFailMessage(), null);
        }
    }

    private static function reqFileCopy($config)
    {
        $files = $config['request']->post['fs'];
        $newPath = $config['request']->post['n'];

        $filesPaths = preg_split('/\|/', $files);

        try {
            $fileSystem = $config['filesystem'];
            $fileSystem->copyFiles($filesPaths, $newPath);
            return new Response(null, true);
        } catch (MessageException $e) {
            return new Response($e->getFailMessage(), null);
        }
    }

    private static function reqFileDelete($config)
    {
        $files = $config['request']->post['fs'];
        $formatSuffixes = $config['request']->post['formatSuffixes'];

        $filesPaths = preg_split('/\|/', $files);

        try {
            $fileSystem = $config['filesystem'];
            $fileSystem->deleteFiles($filesPaths, $formatSuffixes);
            return new Response(null, true);
        } catch (MessageException $e) {
            return new Response($e->getFailMessage(), null);
        }
    }

    private static function reqFileList($config)
    {
        $path = $config['request']->post['d'];

        try {
            $fileSystem = $config['filesystem'];
            $files = $fileSystem->getFiles($path);
            return new Response(null, $files);
        } catch (MessageException $e) {
            return new Response($e->getFailMessage(), null);
        }
    }

    private static function reqFileListSpecified($config)
    {
        try {
            $fileSystem = $config['filesystem'];
            $files = $fileSystem->getFilesSpecified(
                $config['request']->post['files']
            );
            return new Response(null, $files);
        } catch (MessageException $e) {
            return new Response($e->getFailMessage(), null);
        }
    }

    private static function reqFileListPaged($config)
    {
        try {
            $fileSystem = $config['filesystem'];
            $files = $fileSystem->getFilesPaged(
                $config['request']->post['dir'],
                $config['request']->post['maxFiles'],
                isset($config['request']->post['lastFile']) ? $config['request']->post['lastFile'] : NULL,
                isset($config['request']->post['lastIndex']) ? $config['request']->post['lastIndex'] : NULL,
                isset($config['request']->post['whiteList']) ? $config['request']->post['whiteList'] : [],
                isset($config['request']->post['blackList']) ? $config['request']->post['blackList'] : [],
                isset($config['request']->post['filter']) ? $config['request']->post['filter'] : "**",
                $config['request']->post['orderBy'],
                $config['request']->post['orderAsc'],
                $config['request']->post['formatIds'],
                $config['request']->post['formatSuffixes']
            );

            return new Response(null, $files);
        } catch (MessageException $e) {
            return new Response($e->getFailMessage(), null);
        }
    }

    private static function reqFileMove($config)
    {
        $files = $config['request']->post['fs'];
        $newPath = $config['request']->post['n'];

        $filesPaths = preg_split('/\|/', $files);

        try {
            $fileSystem = $config['filesystem'];
            $fileSystem->moveFiles($filesPaths, $newPath);
            return new Response(null, true);
        } catch (MessageException $e) {
            return new Response($e->getFailMessage(), null);
        }
    }

    private static function reqFileOriginal($config)
    {
        $filePath = isset($config['request']->get['f'])
            ? $config['request']->get['f']
            : $config['request']->post['f'];

        try {
            $fileSystem = $config['filesystem'];
            list($mimeType, $f) = $fileSystem->getImageOriginal($filePath);
            header('Content-Type:' . $mimeType);
            fpassthru($f);
            die();
        } catch (MessageException $e) {
            return new Response($e->getFailMessage(), null);
        }
    }

    private static function reqFilePreview($config)
    {
        $filePath = isset($config['request']->get['f'])
            ? $config['request']->get['f']
            : $config['request']->post['f'];
        $width = isset($config['request']->get['width'])
            ? $config['request']->get['width']
            : (isset($config['request']->post['width'])
                ? $config['request']->post['width']
                : null);
        $height = isset($config['request']->get['height'])
            ? $config['request']->get['height']
            : (isset($config['request']->post['height'])
                ? $config['request']->post['height']
                : null);

        try {
            $fileSystem = $config['filesystem'];
            list($mimeType, $fullPath) = $fileSystem->getImagePreview(
                $filePath,
                $width,
                $height
            );
            $fileSystem->passThrough($fullPath, $mimeType);
            die(); // to prevent setting header after passing a file
        } catch (MessageException $e) {
            return new Response($e->getFailMessage(), null);
        }
    }

    private static function reqFileResize($config)
    {
        $filePath = $config['request']->post['f'];
        $newFileNameWithoutExt = $config['request']->post['n'];
        $maxWidth = $config['request']->post['mw'];
        $maxHeight = $config['request']->post['mh'];

        $mode = $config['request']->post['mode'];

        try {
            $fileSystem = $config['filesystem'];
            $resizedFilePath = $fileSystem->resizeFile(
                $filePath,
                $newFileNameWithoutExt,
                $maxWidth,
                $maxHeight,
                $mode
            );
            return new Response(null, $resizedFilePath);
        } catch (MessageException $e) {
            return new Response($e->getFailMessage(), null);
        }
    }

    private static function reqFileRename($config)
    {
        $filePath = $config['request']->post['f'];
        $newName = $config['request']->post['n'];

        try {
            $fileSystem = $config['filesystem'];
            $fileSystem->renameFile($filePath, $newName);
            return new Response(null, true);
        } catch (MessageException $e) {
            return new Response($e->getFailMessage(), null);
        }
    }

    private static function upload($config)
    {
        try {
            $configUploader = [
                'dirFiles' => $config['dirFiles'],
                'dirTmp' => $config['dirTmp'],
                'filesystem' => $config['filesystem'],
                'config' => isset($config['uploader'])
                    ? $config['uploader']
                    : [],
                'request' => $config['request'],
            ];

            $dir = isset($config['request']->post['dir']) ? $config['request']->post['dir'] : null;
            $post = [
                'action' => $config['request']->post['action'],
                'dir' => $dir,
                'data' => JsonCodec::s_toJson([
                    'action' => $config['request']->post['action'],
                    'dir' => $dir
                ]),
            ];
            FileUploaderServer::fileUploadRequest(
                $configUploader,
                $post,
                $config['request']->files
            );
        } catch (MessageException $e) {
            return new Response($e->getFailMessage(), null);
        }
    }

    private static function getVersion()
    {
        return new Response(null, ['version' => '4', 'language' => 'php']);
    }
}
