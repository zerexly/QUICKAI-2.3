<?php
/********************************************************
 * Template Engine
 * Class for templating Sahil Framework
 ********************************************************
 * @package Template Engine
 * @author Devendra Katariya
 * @copyright 20016-22 Sahil
 ********************************************************/
class HtmlTemplate
{

    /**
     * Private Constructor.
     */
    private function __construct(){}

    /**
     * Render a template file.
     *
     * @param string $template
     * @param array $variables
     * @param bool $echo
     * @param bool $external
     * @return string|void
     */
    public static function display($template, $variables = array(), $echo = true, $external = false )
    {
        global $config;
        extract($variables);
        if(checkloggedin()) {
            $user_id = $_SESSION['user']['id'];
            $username = $_SESSION['user']['username'];
            $usertype = $_SESSION['user']['user_type'];
            $is_login = 1;
            $userdata = get_user_data(null,$user_id);
            $userpic = $userdata['image'];
            $userstatus = $userdata['status'];
        }
        else {
            $user_id = '';
            $username = '';
            $usertype = '';
            $userpic = '';
            $is_login = 0;
            $userstatus = 1;
        }
        ob_start();
        ob_implicit_flush(0);

        if(!$external){
            $templates = ROOTPATH.'/templates/'.$config['tpl_name'] . '/'. $template. '.php';
        }else{
            $templates = ROOTPATH.'/'. $template. '.php';
        }

        try {
            include $templates;
        } catch ( \Exception $e ) {
            ob_end_clean();
            error_log($e->getMessage());
        }

        if ( $echo ) {
            echo ob_get_clean();
        } else {
            return ob_get_clean();
        }
    }
}