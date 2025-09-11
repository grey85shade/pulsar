<?php

class mainController
{
    public function index () 
    {
        echo 'Dash index <br>';
        echo $_SESSION['user'];
    }

    public function notFound ()
    {
        require_once ("views/main/404.html");
    }
}