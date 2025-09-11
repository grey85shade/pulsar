<?php
class sessionManager
{
    function __construct() 
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function setSession ($user, $id, $admin = null)
    {
        //session_set_cookie_params(['lifetime' => 3600, 'SameSite'=> 'Lax']);
        $_SESSION['loggedin'] = true;
        $_SESSION['user'] = $user;
        $_SESSION['idUser'] = $id;
        $_SESSION['admin'] = $admin;
    }

    public function sessionDestroy ()
    {
        $_SESSION['loggedin'] = false;
        $_SESSION['user'] = '';
        $_SESSION['idUser'] = null;
        $_SESSION['admin'] = null;
    }

    public function isLogged ()
    {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            return true;
        }
        return false;
    }

    public function setFlash($message, $type = 'success') {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
    }
    
    public function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}