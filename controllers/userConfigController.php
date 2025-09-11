<?php

require_once 'AppUtils.php';

class userConfigController
{
    public function update()
    {
        if (!isset($_POST['name'], $_POST['surname'], $_POST['mail'])) {
            $db = new dbRepository();
            $user = $db->getUserById($_SESSION['idUser']);
            require_once("views/userConfig/update.php");
        } else {
            $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
            $surname = filter_var($_POST['surname'], FILTER_SANITIZE_STRING);
            $mail = filter_var($_POST['mail'], FILTER_SANITIZE_EMAIL);
            $password = isset($_POST['password']) && $_POST['password'] !== '' ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

            $db = new dbRepository();
            $result = $db->updateUser($_SESSION['idUser'], $name, $surname, $mail, $password);

            if ($result) {
                AppUtils::setFlash('Usuario modificado correctamente.', 'success');
            } else {
                AppUtils::setFlash('Error al modificar el usuario.', 'error');
            }
            
            header('Location: /userConfig/update');
            exit();
        }   
    }

    // Procesar registro de usuario nuevo
    public function list()
    {
        if (!isset($_SESSION['idUser'])) {
            header('Location: /login');
            exit;
        }
        $db = new dbRepository();
        $users = $db->getAllUsers();
        require_once ("views/userConfig/list.php");
    }

    public function register()
    {

        if ($_SESSION['admin'] != true) {
            header('Location: /dash/index');
            exit;
        }
        if (!isset($_POST['name'], $_POST['surname'], $_POST['mail'])) {
            require_once ("views/userConfig/register.php");
        } else {
        
            $user = filter_var($_POST['user'], FILTER_SANITIZE_STRING);
            $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
            $surname = filter_var($_POST['surname'], FILTER_SANITIZE_STRING);
            $mail = filter_var($_POST['mail'], FILTER_SANITIZE_EMAIL);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $db = new dbRepository();
            $result = $db->addUser($user, $name, $surname, $password, $mail);

            if ($result) {
                header('Location: /dash/index');
            } else {
                header('Location: /userConfig/register');
            }
        }
    }

    public function delete($id)
    {
        if (!isset($_SESSION['idUser']) || $_SESSION['admin'] != 1) {
            header('Location: /dash/index');
            exit;
        }

        $db = new dbRepository();
        $result = $db->deleteUserById((int)$id);

        if ($result) {
            AppUtils::setFlash('Usuario eliminado correctamente.', 'success');
        } else {
            AppUtils::setFlash('No se pudo eliminar el usuario.', 'error');
        }
        header('Location: /userConfig/list');
        exit;
    }

    public function updateUser()
    {
        if (!isset($_SESSION['idUser']) || $_SESSION['admin'] != 1) {
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            exit;
        }
        AppUtils::logEvent('update user', 'info');
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $user = AppUtils::sanitize($_POST['user']);
        $name = AppUtils::sanitize($_POST['name']);
        $surname = AppUtils::sanitize($_POST['surname']);
        $mail = AppUtils::sanitize($_POST['mail']);
        $admin = isset($_POST['admin']) ? (int)$_POST['admin'] : 0;
        $password = isset($_POST['password']) && $_POST['password'] !== '' ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

        $db = new dbRepository();
        $result = $db->updateUserFull($id, $user, $name, $surname, $mail, $admin, $password);

        if ($result) {
            AppUtils::setFlash('Usuario modificado correctamente.', 'success');
            AppUtils::logEvent('User ' . $name . ' Modificado correctamente', 'info');
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al modificar']);
        }
        header('Location: /userConfig/list');
        exit;
    }
}
