<?php
//Este es el modelo para la gestión de encuestas y sus respuestas en la BBDD.Se enlaza al archivo Database, donde se crearaba la conexión a la BBDD.
require_once '../services/Database.php';
//Se crea la clase, se definen su atributos y su constructor, que simplemente requerirá la conexión a la BBDD
class SurveyModel
{
    private $database;
    private $conn;

    public function __construct()
    {
        $this->database = new Database();// Inicializa la conexión a la base de datos, creando una instancia de la clase Database.
        $this->conn = $this->database->getConnection();// Getter de la conexión a la base de datos.

    }

   // Método privado (solo se usa dentro de este archivo) para convertir un nombre de usuario en su ID correspondiente en la base de datos.
    private function usernameToId($username)
    {
        //Se prepara y ejecuta la sentencia SQL, donde se seleccionan los usuarios con ese nombre.
        $query = "SELECT id FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        //Como se devuelve un array, deberá escogerse el primero, pues no se pueden repetir nombres de usuario(no lo permitiría el registro).
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        return $user['id'];//Se devuelve el id de dicho usuario.
    }

    //Función para insertar encuestas en la tabla "surveys".
    public function createSurvey($title, $end_date, $username)
    {
        $user_Id = $this->usernameToId($username);//Se pasa el usuario de la sesión a ID, pues así figurará en el campo "created_by".
        $code = bin2hex(random_bytes(15)); //Se crea una cadena de caracteres hexadecimales aleatorios, que servirá de ID.

        $query = 'INSERT INTO survey (id, title, end_date, created_by) VALUES (?, ?, ?, ?)';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssss", $code, $title, $end_date, $user_Id);
        $stmt->execute();
        return $code; //Devuelve elcódigo aleatorio, es decir, la ID de esa encuesta.
    }
    //La siguiente función devuelve un array con todas las encuestas creadas.
    public function getSurveys()
    {
        //Se seleccionan todos los valores de la tabla encuestas y se establecen fechas en el formato europeo.
        //El inner join permite seleccionar el nombre del usuario de lat abla "users", en vez del ID que figura en la tabla "surveys".
        $query = "SELECT s.id, s.title, s.current_state, DATE_FORMAT(s.creation_date, '%d/%m/%Y') AS creation_date_formatted, DATE_FORMAT(s.end_date, '%d/%m/%Y') AS end_date_formatted, s.created_by, u.username AS creator_name 
        FROM survey s 
        INNER JOIN users u 
        ON s.created_by = u.id";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    //Cambia el estado a cerrado de las encuestas que han alcanzado su vencimiento
    function closeExpiredSurveys()
    {
        $cerrada = "Cerrada";
        $abierta = "Abierta";
        $query = "UPDATE survey 
                SET current_state = ? 
                WHERE end_date < CURDATE() 
                AND current_state = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $cerrada, $abierta);
        $stmt->execute();
    }


    //Similar a la función getSurveys, pero solo devuelve las encuestas que han sidos respondidas por el usuario facilitado por parámetro.
    //También devuelve las encuestas que ha creado dicho usuario, aunque aun no las haya respondido.
    public function getAnsweredSurveys($username)
    {
        $user_id = $this->usernameToId($username);
        /*Doble inner join para seleccionar tanto los usuarios que figuran en la tabla "users_in_survey" es decir, los participantes de dicha encuesta, 
         así como el usuario que figura como su creador (created_by), pasando en ambos casos de ID a nombre de usuario, de ahí el inner join con la talba usuarios.*/
        $sql = "SELECT s.id, s.title, s.current_state, DATE_FORMAT(s.creation_date, '%d/%m/%Y') AS creation_date_formatted, DATE_FORMAT(s.end_date, '%d/%m/%Y') AS end_date_formatted, s.created_by, u.username AS creator_name
        FROM survey s
        LEFT JOIN users_in_survey uis ON s.id = uis.survey_id AND uis.user_id = ?
        LEFT JOIN users u ON s.created_by = u.id
        WHERE uis.user_id IS NOT NULL OR s.created_by = ?;";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $user_id);
        $stmt->execute();

        if ($result = $stmt->get_result()) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return false;
        }
    }
    //Devuelve las encuestas que tengan esa ID (solo habrá una).
    public function getSurveyById($survey_id)
    {
        $stmt = $this->conn->prepare("SELECT title,end_date,current_state FROM survey WHERE id=?");
        $stmt->bind_param("s", $survey_id);
        $stmt->execute();
        $result = $stmt->get_result();
        // Verifica si hay resultados
        if ($result->num_rows > 0) {
            // Devuelve el primer resultado
            return $result->fetch_assoc();
        } else {
            // No hay resultados, devuelve false
            return false;
        }
    }
    //Devuelve las preguntas de una encuesta indicada por parámetro, con sus opciones correspondientes.
    public function getQuestions($survey_id)
    {
        //Es necesario unir a  la consulta la tabla "options", con todas las opciones que tegnan por question_id a cada pregunta
        $query = "SELECT questions.id, questions.statement,questions.question_type, options.id AS option_id, options.statement AS option_statement
        FROM questions
        JOIN options ON questions.id = options.question_id
        WHERE questions.survey_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $survey_id);
        $stmt->execute();
        $result = $stmt->get_result();
        /*Se crea un array de preguntas, donde se almacenan su enunciado, su tipo (única o múltiple) 
        y un "subarray" con todas las opciones relativas a esa pregunta, que a su vez tendrán un ID y un enunciado.*/
        $questions = [];
        while ($row = $result->fetch_assoc()) {
            $questions[$row['id']]['statement'] = $row['statement'];
            $questions[$row['id']]['question_type'] = $row['question_type'];
            $questions[$row['id']]['options'][] = ['id' => $row['option_id'], 'statement' => $row['option_statement']];
        }
        return $questions;
    }
    //Esta función insetará las respuestas en la tabla "answers".
    public function answerSurvey($survey_id, $username, $answers)//"$answers" será un array de varios selectores de "radio" o "checkbox"
    {
        $user_Id = $this->usernameToId($username);
        //Doble "foreach": Se guardarán  las opciones de cada pregunta, y  las preguntas de cada encuesta.
        //Para cada elemento del array, se guradará la opción seleccionada (y guardada en "$answers"), asociada a una pregunta y un usuario.
        foreach ($answers as $question_id => $option_ids) {
            foreach ($option_ids as $option_id) {
                $query = "INSERT INTO answers (question_id, user_id, option_id) VALUES (?, ?, ?)";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("iii", $question_id, $user_Id, $option_id);
                $stmt->execute();

            }
        }
        /*Además, al terminar de responder, ese usuario pasará a ser un participante de dicha encuesta, 
        figurando en la tabla "users_in_survey" con el ide de ese usuario y esa encusta.*/
        $query2 = "INSERT INTO users_in_survey  (survey_id , user_id) VALUES (?, ?)";
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->bind_param("si", $survey_id, $user_Id);
        $stmt2->execute();
        return $stmt->affected_rows > 0;
    }
    //Esta función eliminará la encuesta de la BBDD de datos. 
    //Para ello, se eliminarán de cada tabla los registros relativos a esa encuesta, según el orden de dependencias.
    public function deleteSurvey($survey_id)
    {
        $this->conn->begin_transaction();

        $deleteQuestions = "DELETE FROM questions WHERE survey_id = ?";
        $deleteOptions = "DELETE FROM options WHERE question_id IN (SELECT id FROM questions WHERE survey_id = ?)";
        $deleteAnswers = "DELETE FROM answers WHERE question_id IN (SELECT id FROM questions WHERE survey_id = ?)";
        $deleteUsersInSurvey = "DELETE FROM users_in_survey WHERE survey_id = ?";
        $deleteSurvey = "DELETE FROM survey WHERE id = ?";

        $stmt = $this->conn->prepare($deleteUsersInSurvey);
        $stmt->bind_param("s", $survey_id);
        $stmt->execute();

        $stmt = $this->conn->prepare($deleteAnswers);
        $stmt->bind_param("s", $survey_id);
        $stmt->execute();

        $stmt = $this->conn->prepare($deleteOptions);
        $stmt->bind_param("s", $survey_id);
        $stmt->execute();

        $stmt = $this->conn->prepare($deleteQuestions);
        $stmt->bind_param("s", $survey_id);
        $stmt->execute();

        $stmt = $this->conn->prepare($deleteSurvey);
        $stmt->bind_param("s", $survey_id);
        $stmt->execute();

        $this->conn->commit();//Se empleará un commit para asegurarse de que toda la acción se ejecuta de forma atómica y no se borren datos parcialmente.
        return $stmt->affected_rows > 0;

    }
    //Verifica si un el usuario facilitado ya ha respondido la encuesta indicado.
    public function checkIfAnswered($survey_id, $username)
    {
        $user_Id = $this->usernameToId($username);
        $stmt = $this->conn->prepare("SELECT *  FROM users_in_survey  WHERE survey_id =? AND user_id=? ");
        $stmt->bind_param("si", $survey_id, $user_Id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
        //Devolverá un valor booleano, indicando si figura esa combinación de usuario y encuesta en la tabla.
    }

    //Devuelve un array con los resultados de la encuesta pasada como parámetro.
    public function getSurveyResults($survey_id)
    {
        /*Se  seleccionan ciertos  los elementos de las tablas "questions", "options" y "answers".
        Se une la tabla  "questions" con "options" basándose en que cada opción pertenece a una pregunta, y luego se realiza un LEFT JOIN con answers para contar cuántas veces se ha seleccionado cada opción.
        Se guardará como "total_answers", que será necesario para constituir el gráfico.
        Se agrupará en función de la pregunta a la que corresponde cada opción, y de la opción a la que corresponde cada respuesta.*/
        $query = "SELECT questions.id as question_id, questions.statement as question, options.id as option_id, options.statement as option_statement, COUNT(answers.option_id) as total_answers
                FROM questions
                JOIN options ON questions.id = options.question_id
                LEFT JOIN answers ON options.id = answers.option_id AND questions.id = answers.question_id
                WHERE questions.survey_id = ?
                GROUP BY questions.id, options.id
                ORDER BY questions.id, options.id";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $survey_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
