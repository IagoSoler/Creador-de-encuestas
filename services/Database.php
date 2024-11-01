<?php
/*Este archivo servirá para crear la conexión a la BBDD, creando la clase Database 
con el setter y getter  que usarán los modelos de la apliacación*/
class Database
{
    
    private $conn;

    public function __construct()
    {
        $this->conn = $this->setConnection();
    }

    private function setConnection()//Setter de la conexión.
    {
        //Se definen los parámetros de la conexión para el servidor local, en el presente caso.
        $servername = "localhost";
        $serverusername = "root";
        $serverpassword = "";
        $dbname = "bbddproyectodaw_iagosoler";

        $conn = new mysqli($servername, $serverusername, $serverpassword, $dbname);


        return $conn;//Se devuelve el resultado para enviarlo al getter.
    }
    public function getConnection()//Getter de la conexión.
    {
        return $this->conn; //Que se crea en el setter.
    }
}
