<?php
namespace EdSDK\FlmngrServer\lib;

use EdSDK\FlmngrServer\lib\IFmRequest;

class DrupalRequest extends IFmRequest
{
    public function parseRequest()
    {
        $request = $this->config['drupalRequestStack']->getCurrentRequest();
        $this->requestMethod = $request->getMethod();
        $this->files = $_FILES;
        $this->post = $request->request->all();
        $this->get = $request->query->all();
    }
}
