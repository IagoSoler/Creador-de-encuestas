<?php
//Este es el modelo para la gestión de preguntas en la BBDD. Se enlaza al archivo Database, donde se crearaba la conexión a la BBDD.
require_once '../services/Database.php';
//Se crea la clase, se definen su atributos y su constructor, que simplemente requerirá la conexión a la BBDD.
class QuestionModel
{
    private $database;
    private $conn;

    public function __construct() {
        $this->database = new Database();// Inicializa la conexión a la base de datos, creando una instancia de la clase Database.
        $this->conn = $this->database->getConnection();//Getter de la conexión a la base de datos.
    }

   
//Función para insertar las preguntas creadas en la tabla "questions" de la BBDD
    public function createQuestion($survey_id, $statement, $options, $answerType)
    {
        /*Se prepara y ejcuta la consulta para insertar una pregunta con los valores:
        La encuesta a la que correponde, el enunciado de la pregunta y su tipo (respuesta única o múltiple)*/
        $query = "INSERT INTO questions (survey_id, statement,question_type ) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $survey_id, $statement,$answerType);
        $stmt->execute();

        //A continuación, para cada opción creada junto con la pregunta, se insertan las opciones en la tabla "options".
        //En ella figurará la id de la pregunta correspondiente y el enunciado de la opción.
        $question_id = $stmt->insert_id;//Este código permite obtener la id de la pregunta que se acaba de crear (nótese que el id de la tabla questions es un "autoincrement").
        foreach ($options as $option) { 
            $query = "INSERT INTO options (question_id, statement) VALUES (?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("is", $question_id, $option);
            $stmt->execute();
        }
        return true; //Devuelve verdadero si se ejecutó correctamente
    }
}
