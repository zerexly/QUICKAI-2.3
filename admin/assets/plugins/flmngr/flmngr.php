<?php

require '../../../inner-includes.php';
require __DIR__ . '/vendor/autoload.php';

use EdSDK\FlmngrServer\FlmngrServer;

FlmngrServer::flmngrRequest(
    array(
        'dirFiles' => ROOTPATH.'/storage/files',
    )
);