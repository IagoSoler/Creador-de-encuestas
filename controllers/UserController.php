<?php
//Este archivo es el controlador de la lógica de usuarios. Se incluye al modelo de encuestas.
require_once '../models/UserModel.php';
////Se crea una instancia de  de la clase "userModel", se incluyen atributos, constructor, se enlazan las funciones del modelo y se crean las del controlador.
class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }
/* $email,$email, */
    public function registerUser($username,  $password) {
        return $this->userModel->registerUser($username,  $password);
    }

    public function loginUser($username, $password) {
        return $this->userModel->loginUser($username, $password);
    }
    public function isAdmin($username) {
        return $this->userModel->isAdmin($username);
    }
    /*A diferencia de las demás funciones, logoutUser no requiere interacción
     con la BBDD, por lo que figurará en el controlador y no en el modelo.*/
    public function logoutUser() {
        session_start();
        session_destroy();
 
    }
}
