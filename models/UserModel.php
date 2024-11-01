<?php
//Este es el modelo para la gestión de usuarios en la BBDD.Se enlaza al archivo Database, donde se crearaba la conexión a la BBDD.
require_once '../services/Database.php';
//Se crea la clase, se definen su atributos y su constructor, que simplemente requerirá la conexión a la BBDD
class UserModel
{
    private $database;
    private $conn;

    public function __construct()
    {
        $this->database = new Database();// Inicializa la conexión a la base de datos, creando una instancia de la clase Database.
        $this->conn = $this->database->getConnection();//Getter de la conexión a la base de datos.
    }

    //Comprueba si el nombre ya figura en la tabla "users" de la BBDD. Devuelve un valor booleano si hay más de un caso.
    //Es privado pues únicamente se emplea en la función registerUser, que solo figura en este archivo
    private function usernameExists($username)
    {
        $query = 'SELECT * FROM users where username = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    //Función homóloga a la anterior, pero con el correo pasado como parámetro.
    //Es privado pues únicamente se emplea en la función registerUser, que solo figura en este archivo
    private function emailExists($email)
    {
        $query = 'SELECT * FROM users where user_email = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    //Esta es la función empleada para registrar un nuevo usuario.
   
    public function registerUser($username, /* $email, */ $password)
    {
         //Se emplean las dos funciones anteriores para comprobar si el correo y usuario ya figuran en la BBDD.
        if ($this->usernameExists($username)/*  || $this->emailExists($email) */) {
            return false;//Si figuran devuelve falso, que en el archivo de vista de registro se manifestará en los términos adecuados
        }
        //Si devuelve verdadero, entonces se procede a insertar en la BBDD y devolver el booleano confirmando si afectó a alguna línea
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);//Para mayor privacidad se "hashea" la contraseña
        $query = 'INSERT INTO users (username, user_password, user_email) VALUES (?, ?, ?)';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $username, $hashedPassword, $email);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    /*Para logear al usuario: Simplemente comprueba si el usuario figuran en la BBDD, 
    y comprueba si su contraseña es correcta, tras verificar el "hash"*/
    public function loginUser($username, $password)
    {
        $query = 'SELECT * FROM users where username = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            return password_verify($password, $user['user_password']);
        }
        return false;
    }
    //Mediante esta fórmula, sencillamente se comprueba si el usuario tiene el rol "admin" como "role" en la tabla "users"
    public function isAdmin($username)
    {
        $userRole = "admin";
        $query = 'SELECT * FROM users where username = ? AND role = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $username,$userRole);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

}
