<?php

class router {

    private $layout = true;
    private $loggedin = true;
    private $controller = '';
    private $action = '';
    private $map;
    private $mobile;


    public function getRoute (): array
    {
        // Recivimos la ruta
        $request = $_SERVER['REQUEST_URI'];
        $this->map = explode('/', $request);

        $this->controller = !empty($this->map[1]) ? $this->map[1] : 'login';
        $this->action = !empty($this->map[2]) ? $this->map[2] : 'index';
        
        $this->selectController();
        $this->actionValid();
        $this->isMobile();
        $varUrl = isset($this->map[3]) ? $this->map[3] : null;
        
        return [$this->controller, $this->action, $this->layout, $this->loggedin, $this->mobile, $varUrl];
    }

    
    private function selectController () 
    {
        // mapeamos
        switch ($this->controller) {
            case '/' :
            case '' :
            case'dash' : 
                $this->loggedin = true;
                $this->controller = 'dashController';
                break;

            case 'ajax' :
                $this->layout = false;
                $this->controller = 'ajaxController';
                break;
            
            case 'userConfig' :
                $this->layout = true;
                $this->controller = 'userConfigController';
                break;

            case 'login' :
                $this->layout = false;
                $this->loggedin = false;
                $this->controller = 'loginController';
                break;
                
            default:
                $this->controller = 'mainController';
                $this->action = 'notFound';
                $this->layout = false;
                $this->loggedin = false;
                break;
        }
    }

    // Comprobamos si la accion existe
    private function actionValid ()
    {
        require_once("controllers/" . $this->controller . ".php");
        $disponibleActions = get_class_methods($this->controller);
        if (!in_array($this->action, $disponibleActions)) {
            $this->controller = 'mainController';
            $this->action = 'notFound';
            $this->layout = false;
            $this->loggedin = false;
        }
    }

    private function isMobile() {
        $this->mobile = preg_match(
            "/(android|webos|avantgo|iphone|ipad|ipod|blackberry|iemobile|bolt|boost|cricket|docomo|fone|hiptop|mini|opera mini|kitkat|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", 
            $_SERVER["HTTP_USER_AGENT"]
        );
    }
}