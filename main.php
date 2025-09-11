<?php 
require ("config.php");
require ("session.php");
require ("dbRepository.php");
require ("router.php");

$router = new router;
list($controller, $action, $layout, $loggedin, $mobile, $urlVariable) = $router->getRoute();

// Si elcontroller no es publico miramos si hay login
if ($loggedin === true) {
    $sessionManager = new sessionManager();
    if ($sessionManager->isLogged() !== true) {
        $controller = 'loginController';
        $action = 'index';
        $layout = false;
    }
}

// Si es una pagina normal usamos layout, sino directamente la accion
// Si detectamos dispositivo mobil lo mandamos al layout de mobil
if ($layout === true) {
    if ($mobile) {
        require ("layout-mobile.php");
    } else {
        require ("layout.php");
    }
    
} else {
    $controllerInstance = new $controller;
    // Detecta si hay parámetros extra en la URL
    if (!empty($urlVariable)) {
        $controllerInstance->$action($urlVariable);
    } else {
        $controllerInstance->$action();
    }
}