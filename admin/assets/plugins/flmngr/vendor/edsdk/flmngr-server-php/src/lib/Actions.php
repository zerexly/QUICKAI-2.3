<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FlmngrServer\lib;

use EdSDK\FlmngrServer\lib\action\ActionError;
use EdSDK\FlmngrServer\lib\action\ActionUploadAddFile;
use EdSDK\FlmngrServer\lib\action\ActionUploadCancel;
use EdSDK\FlmngrServer\lib\action\ActionUploadCommit;
use EdSDK\FlmngrServer\lib\action\ActionUploadInit;
use EdSDK\FlmngrServer\lib\action\ActionUploadRemoveFile;
use EdSDK\FlmngrServer\lib\action\ActionQuickUpload;

class Actions
{
    protected $m_actions = [];

    public function __construct()
    {
        $this->m_actions[] = new ActionError();

        $this->m_actions[] = new ActionUploadInit();
        $this->m_actions[] = new ActionUploadAddFile();
        $this->m_actions[] = new ActionUploadRemoveFile();
        $this->m_actions[] = new ActionUploadCommit();
        $this->m_actions[] = new ActionUploadCancel();
        $this->m_actions[] = new ActionQuickUpload();
    }

    public function getActionError()
    {
        return $this->getAction('error');
    }

    public function getAction($name)
    {
        for ($i = 0; $i < count($this->m_actions); $i++) {
            if ($this->m_actions[$i]->getName() === $name) {
                return $this->m_actions[$i];
            }
        }
        return null;
    }
}
