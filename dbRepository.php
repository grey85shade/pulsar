<?php

 class dbRepository {

    public $con;

    function __construct() 
    {
        $servername = "localhost";
        $config = new config();
        $dbInfo = $config->getDBInfo();

        // Create connection
        $this->con = new mysqli($servername, $dbInfo['user'], $dbInfo['pass'], $dbInfo['name']);
        // Check connection
        if ($this->con->connect_error) {
            die("Connection failed: " . $this->con->connect_error);
        }
    }
    
    function  __destruct()
    {
        $this->con->close();
    }

    // Agregar un nuevo usuario
    public function addUser($user, $name, $surname, $password, $mail)
    {
        // Comprobar si el usuario o el mail ya existen
        $user = $this->con->real_escape_string($user);
        $mail = $this->con->real_escape_string($mail);
        $check = $this->con->query("SELECT id FROM users WHERE user = '$user' OR mail = '$mail'");
        if ($check && $check->num_rows > 0) {
            return false; // Usuario o mail ya existen
        }

        $name = $this->con->real_escape_string($name);
        $surname = $this->con->real_escape_string($surname);
        $password = $this->con->real_escape_string($password);

        $sql = "INSERT INTO users (user, name, surname, pass, mail) VALUES ('$user', '$name', '$surname', '$password', '$mail')";
        return $this->con->query($sql);
    }
    
    public function getUser ($user) {
        
        $sql = "SELECT * FROM users where user = '".$user."'";
        $result = $this->con->query($sql);

        if ($result->num_rows === 1) {
            //return $result->fetch_all(MYSQLI_ASSOC);
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    // Obtener usuario por ID
    public function getUserById($id) {
        $id = intval($id);
        $sql = "SELECT * FROM users WHERE id = $id";
        $result = $this->con->query($sql);
        if ($result && $result->num_rows === 1) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    // Obtener todos los usuarios
    public function getAllUsers() {
        $sql = "SELECT id, user, name, surname, mail, admin FROM users ORDER BY name";
        $result = $this->con->query($sql);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }

    // Actualizar usuario
    public function updateUser($id, $name, $surname, $mail, $password = null) {
        $id = intval($id);
        $name = $this->con->real_escape_string($name);
        $surname = $this->con->real_escape_string($surname);
        $mail = $this->con->real_escape_string($mail);
        if ($password) {
            $password = $this->con->real_escape_string($password);
            $sql = "UPDATE users SET name = '$name', surname = '$surname', mail = '$mail', pass = '$password' WHERE id = $id";
        } else {
            $sql = "UPDATE users SET name = '$name', surname = '$surname', mail = '$mail' WHERE id = $id";
        }
        return $this->con->query($sql);
    }
    public function updateUserFull($id, $user, $name, $surname, $mail, $admin, $password = null)
    {
        $id = intval($id);
        $user = $this->con->real_escape_string($user);
        $name = $this->con->real_escape_string($name);
        $surname = $this->con->real_escape_string($surname);
        $mail = $this->con->real_escape_string($mail);
        $admin = intval($admin);

        if ($password) {
            $password = $this->con->real_escape_string($password);
            $sql = "UPDATE users SET user='$user', name='$name', surname='$surname', mail='$mail', admin=$admin, pass='$password' WHERE id=$id";
        } else {
            $sql = "UPDATE users SET user='$user', name='$name', surname='$surname', mail='$mail', admin=$admin WHERE id=$id";
        }
        return $this->con->query($sql);
    }
    public function deleteUserById($id)
    {
        $id = intval($id);
        $sql = "DELETE FROM users WHERE id = $id";
        return $this->con->query($sql);
    }
}