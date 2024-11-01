<?php
//Este archivo es el controlador de encuestas. Se incluye el modelo de encuestas.
require_once '../models/SurveyModel.php';
//Se crea una instancia de  de la clase "SurveyModel", se incluyen atributos, constructor y se enlazan las funciones del modelo.
class SurveyController {
    private $surveyModel;

    public function __construct() {
        $this->surveyModel = new SurveyModel();
    }
    public function createSurvey($title, $end_date, $username) {
        return $this->surveyModel->createSurvey($title, $end_date, $username);
    }
    
    public function getSurveys() {
        return $this->surveyModel->getSurveys();
    }
    public function closeExpiredSurveys() {
        return $this->surveyModel->closeExpiredSurveys();
    }
    public function getAnsweredSurveys($username) {
        return $this->surveyModel->getAnsweredSurveys($username);
    }
    public function getSurveyById($survey_id) {
        return $this->surveyModel->getSurveyById($survey_id);
    }
    public function getQuestions($survey_id) {
        return $this->surveyModel->getQuestions($survey_id);
    }
    public function answerSurvey($survey_id, $username, $answers) {
        return $this->surveyModel->answerSurvey($survey_id, $username, $answers);
    }
    public function deleteSurvey($survey_id) {
        return $this->surveyModel->deleteSurvey($survey_id);
    }
    public function checkIfAnswered($survey_id, $username) {
        return $this->surveyModel->checkIfAnswered($survey_id, $username,);
    }

    public function getSurveyResults($survey_id) {
        $results = $this->surveyModel->getSurveyResults($survey_id);//Una vez obtenidos los datos de la BBDD, es preciso organizarlos.
        $organizedResults = [];//Para ello se almacenarán en un nuevo array
        foreach ($results as $result) {// Itera sobre cada resultado para cada pregunta.
            // Extrae la pregunta del resultado a iterar.
            $question = $result['question'];
            // Verifica si la pregunta ya existe en el array.
            if (!isset($organizedResults[$question])) {
                // Si no existe, inicializa un nuevo array para esa pregunta.
                $organizedResults[$question] = [];
            }
            // Agrega la opción y su conteo de respuestas al array organizado bajo la pregunta correspondiente.
            $organizedResults[$question][$result['option_statement']] = $result['total_answers'];
        }
        // Retorna el array organizado con los resultados de la encuesta.
        return $organizedResults;
            }
    
}
