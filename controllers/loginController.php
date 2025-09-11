<?php

class loginController
{
    public function index () 
    {
        require_once ("views/login/index.php");
    }

    public function loginAction ()
    {
        if ( !isset($_POST['username'], $_POST['password']) ) {
            header('Location: /login');
        } else {
            $u = filter_var($_POST['username'],FILTER_SANITIZE_STRING);
            $p = filter_var($_POST['password'],FILTER_SANITIZE_STRING);

            $db = new dbRepository();
            $user = $db->getUser($_POST['username']);

            if ($user !== null && password_verify($p, $user['pass'])) {
                $se = new sessionManager;
                $se->setSession($user['user'], $user['id'], $user['admin']);
                header('Location: /dash');
            }
            else {
                sleep(2);
                header('Location: /login');
            }
        }
    }

    public function logout ()
    {
        $se = new sessionManager;
        $se->sessionDestroy();
        header('Location: /login');
    }


}
