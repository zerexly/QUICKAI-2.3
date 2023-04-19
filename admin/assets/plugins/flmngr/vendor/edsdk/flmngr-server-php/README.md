# Flmngr - file manager

> Module for PHP for server-side file management

Use Flmngr file manager to upload and manage files and images on your website. Works great together with [ImgPen](https://imgpen.com) which adds feature to edit images right from file manager.


## Install

With [Composer](https://getcomposer.org/) installed, run

```
$ composer require edsdk/flmngr-server-php
```


## Usage

To handle some URL you want in your web application, create a file which will be entry point for all requests, e. g. `flmngr.php`:

```php
<?php

    require __DIR__ . '/vendor/autoload.php';
        
    use EdSDK\FlmngrServer\FlmngrServer;
    
    // Uncomment line below to enable CORS if your request domain and server domain are different
    // header('Access-Control-Allow-Origin: *');
    
    echo FlmngrServer::flmngrRequest([
        'dirFiles' => __DIR__ . '/files',
    ]);
```

This file `flmngr.php` should be placed on the same level with `vendor` directory. If can be placed in some other place too, but do not forget to change path in `require` call.

Do not forget to create directories you point to and set correct permissions (read and write) for access to them.

If you want to allow access to uploaded files (usually you do) please do not forget to open access to files directory.

Please also see [example of usage](https://packagist.org/packages/edsdk/flmngr-example-php) Flmngr with ImgPen for editing and uploading images.


## Server languages support

Current package is targeted to serve uploads inside PHP environment.

Another backends are also available:

- Node (TypeScript/JavaScript)
- PHP
- Java


## See Also

- [N1ED](https://n1ed.com) - Flmngr server perfectly works with #1 free HTML WYSIWYG Editor which can be installed on your website (any CMS).  


## License

GNU General Public License version 3 or later; see LICENSE.txt