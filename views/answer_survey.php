<?php
/*En esta página se mostrarán las preguntas y sus opciones para responder a una encuesta determinada.
La encuesta se identificará con el ID que se transporta en la URL, mediante el método GET*/ 
session_start();//Se crea una sesión (que tendrá por nombre de usuario el de la sesión inciada en "login.php").
require_once '../controllers/SurveyController.php'; //Se enlaza el presente archivo (Vista), a su respectivo Controlador, siguiendo arquitectura MVC.

if (!isset ($_SESSION['username'])) {//Si no hay sesión iniciada, cualquier intento de acceder a esta página devovlerá al login.
    header("Location: login.php");
    exit;
}

$surveyController = new SurveyController();//Se crea una instancia de la  clase de SurveyController.
$survey_id = $_GET['survey_id'];//Se obtiene el id de la encuesta seleccionada, que figurará en la URL de la página.


$username = $_SESSION['username'];//Se recoge el nombre del usuario logeado actualmente.
if (!$survey = $surveyController->getSurveyById($survey_id)) {//Mediante la id obtenida, se obtienen los datos de la encuesta en la BBDD.
    die ('Esta encuesta no existe'); //Si el código de la encuesta no existe devolverá este error.
}
$questions = $surveyController->getQuestions($survey_id);//También se obtienen las preguntas de esa encuesta y sus opciones.
if ($survey['current_state'] =="cerrada") {//Si la encuesta tiene como estado "cerrada".
    die ('Esta encuesta esta cerrada desde ' . $survey['end_date']); //devolverá una advertencia y no dejará responder
}
if ($surveyController->checkIfAnswered($survey_id, $username)) {//en caso de que ya haya sido respondida (el usuario figura en "user_in_surey" con esa misma encusta)
    die ('Ya ha respondido a esta encuesta'); //Devolverá esta otra advertencia y no permitirá responderla de nuevo.
}


if (isset ($_POST["Submit"])) {//Al pulsar el botón submit (único botón de esta página), se captarán los datos del formulario,
    $answers = $_POST['answers']; // donde answers el es un array con todas opciones escogidas para cada pregunta.


    if ($surveyController->answerSurvey($survey_id, $username, $answers)) { //que serán pasados como parámetros de esta función.
        header("Location: surveys.php");//En caso de éxito se devolverá a la lista de encuestas.
        exit();
    } else {
        echo "Error";//En caso contrario indicará error, 
    }
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"><!--Diseño responsive para que se adapte al dispositivo--->
    <title>Responder Encuestas</title>
    <link rel="stylesheet" href="../assets/styles.css"><!--Enlace a la página de estilos-->
</head>

<body>
    <?php include "../assets/header.php"; ?><!--Se incluye el header-->
    <main>
    <h1>Responder Encuesta</h1>
        <section>
            <h2>
                <?php echo $survey['title']; ?><!--Se indica el tíutlo de la actual encuesta, obtenido mediante la función getSurveyById -->
            </h2>
            <form method="post"><!--Al pulsar el botón submit del formulario, se enviarán los dato a la página actual por el método "post"-->
            
                <?php foreach ($questions as $question_id => $question): ?><!--Se crea un bucle que recorre todas las preguntas que figuran en la BBDD para esa encuesta-->
                    <div class="question-box">
                    <p class="question-statement">
                        <?php echo htmlspecialchars($question['statement']); ?><!--Para cada pregunta se imprime su enunciado, asegurándose de que los caracteres especiales son interpretables por el navegador-->
                    </p>
                    <p>
                    <?php if ($question['question_type'] == "res_unica") {//Según el tipo que figure en la BBDD para cada pregunta.
                            echo "<br>Escoge una: ";//Se indicará si es de repsuesta única.
                        } else {
                            echo "<br>Puedes elegir varias:";//O de respuesta múltiple.
                        }
                        ?>
                    </p>
                    <?php foreach ($question['options'] as $option)://Ahora se recorren todas las opciones que hay en cada pregunta de la encuesta.
                        if ($question['question_type'] == "res_unica") {
                            //De nuevo, según el tipo de la pregunta, lasopciones se imprimirán con un input de tipo radio(único) o checkbox(mútiple).
                            ?>
                            <!--A nivel de lógica interna, se guardarán la ID de la opción, y la ID de la pregunta a la que corresponde, nótese que aquí se constituye la información que se enviará como "answers" desde el formulario-->
                            <input type="radio" name="answers[<?php echo $question_id; ?>][]" value="<?php echo $option['id']; ?>"
                                required><!--En caso de respuesta única, el required hará obligatorio escoger una de las respuestas-->
                        <?php } else { ?>
                            <input type="checkbox" name="answers[<?php echo $question_id; ?>][]"
                                value="<?php echo $option['id']; ?>">

                            <label>
                            <?php }
                        echo htmlspecialchars($option['statement']); ?><!--Depurando caracteres especiales, se muestra el nombre de la opción, que aparecerá tras su radio o checkbox-->
                        </label><br>
                    <?php endforeach; ?>
                    </div>    
                <?php endforeach; ?>
                

                <input type="submit" name="Submit" value="Responder"><!--Con este botón se enviará el formulario-->



            </form>

        </section>
    </main>
    <?php include "../assets/footer.php"; ?><!--Se incluye el footer-->
</body>

</html>