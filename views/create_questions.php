<?php
/* En esta página se crearán las preguntas y sus opciones para  una encuesta determinada.
La encuesta se identificará con el ID que se transporta en la URL, mediante el método GET*/ 
session_start();//Se crea una sesión (que tendrá por nombre de usuario el de la sesión inciada en "login.php").
require_once '../controllers/questionController.php';//Se enlaza el presente archivo (Vista), a su respectivo Controlador, siguiendo arquitectura MVC.
require_once '../controllers/surveyController.php';

if (!isset ($_SESSION['username'])) {//Si no hay sesión iniciada, cualquier intetno de acceder a esta página devovlerá al login.
    header("Location: login.php");
    exit;
}

$questionController = new questionController();//Se crea una instancia de la clase de QuestionController.
$surveyController = new surveyController();
$survey_id = $_GET['survey_id'];//Se obtiene el id de la encuesta seleccionada, que figurará en la URL de la página.
$questions = $surveyController->getQuestions($survey_id);
$survey = $surveyController->getSurveyById($survey_id);
if(!$survey){ header("Location: home.php");}
if (isset ($_POST['addQuestion'])) {//Al pulsarse el botón "Añadir Pregunta" se envían los siguientes datos del formulario:
    $statement = $_POST['statement'];//Enunciado de la pregunta.
    $numFields = $_POST['numFields'];//Número de opciones  de la pregunta.
    $answerType = $_POST['typeCheck'];//Tipo de pregunta: Respuesta única o múltiple.
    $options = array();//A continuación se crea un array donde se guardarán todas las opciones.
    for ($i = 1; $i <= $numFields; $i++) {//Bucle for que se repite tantas veces como opcines se hayan seleccionado.
        if (!empty ($_POST['respuesta' . $i])) {//Si no está vacía
            $options[] = $_POST['respuesta' . $i];//Se recogen los datos de de las cajas de texto del formulario y se guardan en el array de opciones.
        }
    }
    if ($questionController->createQuestion($survey_id, $statement, $options, $answerType)) {//Se solicita la función de creación de preguntas.
        $message = "Pregunta creada con éxito.";//En caso de éxito se recoge dicha circunstancia en la varible $message, que se empleará mas adelante.
    } else {
        $message =  "Error al crear la pregunta.";//Idéntico al punto anterior, pero en caso de error.
    }
}
if (isset($_POST['finishSurvey'])) {//En caso de pulsar el botón "Finalizar", se mostrará la lista de encuestas, con la nueva ya creada.

    if ($questions){
        header("Location: home.php");
    } else {
        $surveyController->deleteSurvey($survey_id);
        header("Location: home.php");
    }
    
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!--Diseño responsive para que se adapte al dispositivo--->
    <title>Crear Preguntas</title>
    <link rel="stylesheet" href="../assets/styles.css"><!--Enlace a la página de estilos-->
</head>

<body>
    <script src="../assets/jquery-3.6.0.min.js"></script><!--Se incluyen las dependencias de la librearía de jquery 3.6-->
    <script src="../assets/script.js"></script><!--Se enlaza al archivo de javascript(en cuyos comentarios se detalla su funcionamiento) de la carpeta assets-->
    <?php include "../assets/header.php"; ?><!--Se incluye el header-->
    <main>
    <section class="MainMenu">
        <h2><?php  echo $survey['title'] ?></h2>
        <button id="showFormButton" class="OtherButtons">Nueva pregunta</button>
        <form id="NewQuestionForm"  method="post" hidden><!--Al pulsar el botón addQuestion del formulario, se enviarán los dato a la página actual por el método "post"-->
            <label for="statement">Enunciado:</label><!--Label e input para el enunciado de la pregunta-->
            <input type="text" name="statement" id="statement" required><br><br>
            <label for="typeCheck">Tipo de respuesta:</label><br><br><!--Botón de "radio" para seleccionar el tipo de pregunta-->
            <input type="radio" name="typeCheck" id="uniqueAnswer" value="res_unica" checked><!--Al compartir "name", será una selección excluyente: no se podrán seleccionar ambos-->
            <label class="tipoRespuesta" for="respuestaUnica">Respuesta única</label>
            <br>
            <input type="radio" name="typeCheck" id="multipleAnswer" value="res_multiple">
            <label class="tipoRespuesta" for="respuestaMultiple">Respuesta múltiple</label>
            <br><br>
            <label for="numFields">Número de campos:</label><!--Aquí es dónde se empleará el código javascript-->
            <!--Cuando se elige un número (actualmente de 1 a 10), aparecerán ese número de input cajas de texto con el label Opción i -->
            <select id="numFields" name="numFields" required><!--El required obliga a seleccionar un número distinto del valor por defecto:"Seleccionar un número"-->
                <option value="">Selecciona un número</option>
                <?php
                //Este bucle for muestra los valores que se mostrarán en el desplegable y que el usuario podrá seleccionar como número de opciones.
                for ($i = 1; $i <= 10; $i++) {//Se mostrarán los números del 1 al 10. Podrían cambiarse libremente. Se ha considerado 10 un número suficiente como número máximo de opciones.
                    echo "<option value='$i'>$i</option>";
                }
                ?>
            </select>
            <br><br>
            <div id="inputFields"></div>
            <input type="submit" name="addQuestion" value="Añadir Pregunta" id="addQuestionButton" ><!--Botón para enviar el primer formulario-->
            
        </form>
        <br>
        <br>
        <br>
        </section>
        <form method="post" ><!--Botón para finalizar la encuesta-->
            <p>Cuando hayas creado todas las preguntas:</p>
            <input type="submit" name="finishSurvey" value="Finalizar" id="addQuestionButton">
        </form>
        <?php if (isset($message)): ?><!--Aquí se devolverá el mensaje que se guardaba en el apartado PHP de la página, indicando éxito o error al crear la pregunta, tras enviar el formulario-->
                <p ><?php echo $message; ?></p>
            <?php endif; ?>
    </main>


    <?php include "../assets/footer.php"; ?> <!--Se incluye el footer-->
</body>

</html>