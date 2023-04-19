<?php
require_once('config.php');
require_once('lib/idiorm.php');
require_once('db.php');
require_once('lib/HTMLPurifier/HTMLPurifier.standalone.php');
require_once('lib/urlify-master/URLify.php');
require_once('lib/class.HtmlTemplate.php');
require_once('lib/class.country.php');
require_once('lib/GoogleTranslate.php');
require_once('lib/class.upload.php-master/src/class.upload.php');
require_once('functions/func.global.php');
require_once('functions/func.email.php');
require_once('functions/func.admin.php');
require_once('functions/func.users.php');
require_once('functions/func.app.php');
require_once(APPPATH.'_links.php');

/**
 * Class autoload.
 * @param $class
 */
function class_loader( $class )
{
    if ( preg_match( '/^Sahil\\\\(.+)?([^\\\\]+)$/U', ltrim( $class, '\\' ), $match ) ) {
        $file = ROOTPATH . DIRECTORY_SEPARATOR
            . strtolower( str_replace( '\\', DIRECTORY_SEPARATOR, preg_replace( '/([a-z])([A-Z])/', '$1_$2', $match[1] ) ) )
            . $match[2]
            . '.php';
        if ( is_readable( $file ) ) {
            require_once $file;
        }
    }
}
spl_autoload_register( 'class_loader', true, true );
