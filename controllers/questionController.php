<?php
//Este archivo es el controlador de preguntas. Evidentemente incluirá al modelo correspondiente.
require_once '../models/QuestionModel.php';

//Se crea una clase con el atributo, constructor y función.
class QuestionController {

    private $questionModel; //Aquí figurará la clase instanciada del modelo de preguntas.

    public function __construct() {

        $this->questionModel = new questionModel(); //Se crea una instancia de la clase questionModel.
    }
    //Enlaza las funciones correspondientes al modelo de preguntas.
    public function createQuestion($survey_id, $statement, $options, $answerType) {
        return $this->questionModel->createQuestion($survey_id, $statement, $options, $answerType);
    }
}
