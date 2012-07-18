<?php
/**
 * Action_Plugin_Browserid class
 * 
 * PHP Version 5.3.10
 * 
 * @category Plugi
 * @package  DokuWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License
 * @link     http://www.dokuwiki.org/dokuwiki
 * */
if (!defined('DOKU_INC')) {
    die();
}
if (!defined('DOKU_PLUGIN')) {
    define('DOKU_PLUGIN', DOKU_INC.'lib/plugins/');
}
require_once DOKU_PLUGIN.'action.php';

/**
 * BrowserID (Persona) login
 * 
 * PHP Version 5.3.10
 * 
 * @category Plugi
 * @package  DokuWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License
 * @link     http://www.dokuwiki.org/dokuwiki
 * */
class Action_Plugin_Browserid extends DokuWiki_Action_Plugin
{
    /**
     * plugin should use this method to register its handlers
     * with the DokuWiki's event controller
     *
     * @param object &$controller DokuWiki's event controller object.
     * Also available as global $EVENT_HANDLER
     *
     * @return   not required
     */
    function register(&$controller)
    {
        $controller->register_hook(
            "HTML_LOGINFORM_OUTPUT", "BEFORE", $this, "insertButton"
        );
        $controller->register_hook(
            "AUTH_LOGIN_CHECK", "BEFORE", $this, "login"
        );
    }
    
    /**
     * Add BrowserID button on login page
     * 
     * @param object &$event Event
     * 
     * @return void
     * */
    function insertButton(&$event)
    {
        array_splice(
            $event->data->_content, 5, 0, array(array(
                "_elem"=>"button",
                "value"=>"BrowserID",
                "class"=>"button",
                "type"=>"submit",
                "id"=>"browserid"
            ))
        );
    }
    
    /**
     * Login
     * 
     * @param object &$event Event
     * 
     * @return void
     * */
    function login(&$event) 
    {
        $curl = curl_init("https://browserid.org/verify");
        if (isset($_GET["assertion"])) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt(
                $curl, CURLOPT_POSTFIELDS, "assertion=".strval(
                    $_GET["assertion"]
                )."&audience=".DOKU_URL
            );
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $response=json_decode(strval(curl_exec($curl)));
            curl_close($curl);
             
            if ($response->status==="okay") {
                global $auth;
                $filter['mail']=$response->email;
                $users = $auth->retrieveUsers(0, 0, $filter);
                if (!empty($users)) {
                    foreach ($users as $user=>$info) {
                        $_SERVER['REMOTE_USER'] = $user;
                        global $USERINFO;
                        $USERINFO = $auth->getUserData($user);
                        $event->preventDefault();
                    }
                } else {
                    global $lang;
                    msg($lang['badlogin'], -1);
                }
            } else {
                msg($response->reason, -1);
            }
        }
    }
}
