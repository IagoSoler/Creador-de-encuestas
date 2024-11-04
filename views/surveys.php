<?php
/*Ofrecerá esta página la lista en forma de tabla de todas las encuestas que pueda ver el usuario:
    -Si es admin, podrá ver todas las encuestas de la aplicación.
    -Si no goza de privilegios, podrá ver las encuestas que ha creado o en las que ha participado.*/
session_start();//Se crea una sesión (que tendrá por nombre de usuario el de la sesión inciada en "login.php").
require_once '../controllers/SurveyController.php'; //Se enlaza el presente archivo (Vista), a su respectivo Controlador, siguiendo arquitectura MVC.
require_once '../controllers/UserController.php';//Se enlaza el presente archivo (Vista), a su respectivo Controlador, siguiendo arquitectura MVC.

if (!isset ($_SESSION['username'])) {//Si no hay sesión iniciada, cualquier intento de acceder a esta página devovlerá al login.
    header("Location: login.php");
    exit;
}
$username = $_SESSION['username']; //Se obtiene el nombre del usuario logeado.
$surveyController = new SurveyController();//Se crea una instancia de la  clase de SurveyController.
$userController = new UserController();//Se crea una instancia de la  clase de UserController.
$surveyController->closeExpiredSurveys();//Se ejecuta este código para cambiar a "Cerrado" el "current_state" de las encuesta de la BBDD si ha llegado su vencimiento.
if ($isAdmin = $userController->isAdmin($username)) {//Se comprueba si el usuario logeado goza de privilegios de administrador en la tabla user de la BBDD. Se guarda como valor booleano en la varaible "$isAdmin"
    $surveys = $surveyController->getSurveys();//En caso afirmativo, se muestran todas las encuestas existentes.
} else {
    $surveys = $surveyController->getAnsweredSurveys($username);//De no ser así, se mostrarán las encuestas en las que hayas participado o creado.
}

//Existirán tres botones de "formulario". Dos dentro de la tabla: responder y eliminar. Y uno externo: Agregar encuesta.
//El botón de responder dentro de la tabla solo aparecerá para encuestas creadas por el usuario y que aun no haya repondido.
//El botón que figura despues de la tabla, permite agregar y responder encuestas de terceros, que aún no figuran en la tabla.

//Este es el botón de eliminar, captará de la fila de la tabla en que figura la ID correspondiente y ejecutará función deleteSurvey con esa ID.
if (isset($_POST["deleteSurvey"])) {
    $survey_id = $_POST["deleteSurvey"];
    $surveyController->deleteSurvey($survey_id);
    // Actualizará la página tras ejecutar la función.
    header("Location: surveys.php");
    exit;
}

//A continaución, ambos botones hacen lo mismo, pero el ID de la encuesta empleado emana de diferentes lugares.

if (isset($_POST["answerButton"])) {
    $survey_id = $_POST["answerButton"];//En este caso lo extrae de la tabla. Cogerá la ID la fila a la que correpsonde.
    header("Location: answer_survey.php?survey_id=" . $survey_id);
    exit;
}
if (isset($_POST["answerNewSurvey"])) {
    $survey_id = $_POST["addSurvey"];//Mientras, en este caso la ID será la insertada en el input caja de texto con nombre: addSurvey.
    header("Location: answer_survey.php?survey_id=" . $survey_id);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"><!--Diseño responsive para que se adapte al dispositivo--->
    <title>Encuestas</title>
    <link rel="stylesheet" href="../assets/styles.css"><!--Enlace a la página de estilos-->
</head>

<body>
    <?php include "../assets/header.php"; ?><!--Se incluye el header-->
    <main>
        <h1>Encuestas Disponibles</h1>
        <section>
            <h2>Lista de Encuestas</h2>
            <!--Se crea una tabla donde se listarán las encuestas con cierta información y botones.-->
            <div class="surveys-table-container">
                <table class="surveys-table">
                    <thead>
                        <tr><!--He aquí las cabeceras de la tabla-->
                            <th>Título</th>
                            <th>Creador</th>
                            <th>Fecha de Creación</th>
                            <th>Fecha de Fin</th>
                            <th>Resultados</th><!--Aqui constará un enlace que llevará a losr resultados-->
                            <th>Estado</th><!--Puede ser cerrado, respondido, o el botón para responder-->
                            <th>Compartir</th><!--Copia el código que se facilitaría a los usuarios qeu se desea invitar-->
                            <th>Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--Para cada encuesta devuelta, se itera y crea una fila en la tabla, donde figurará la inforamción correspodiente a su fila en la BBDD, en los siguientes términos:-->
                        <?php foreach ($surveys as $survey): ?>
                            <tr>
                                <td>
                                    <?php echo $survey['title']; ?><!--Título de la encuesta-->
                                </td>
                                <td>
                                    <?php echo $survey['creator_name']; ?><!--Nombre del creador, no su ID-->
                                </td>
                                <td>
                                    <?php echo $survey['creation_date_formatted']; ?><!--Fecha en formato europeo-->
                                </td>
                                <td>
                                    <?php echo $survey['end_date_formatted']; ?><!--Ídem-->
                                </td>

                                <td>
                                    <a href="results.php?survey_id=<?php echo $survey['id']; ?>">Ver</a><!--Enlace para ver los resultados-->
                                </td>
                                <td>
                                    <?php if ($survey['current_state'] == "cerrada") {
                                        echo "Cerrada";//Mostrará el estado cerrado y si la fecha de cierre es anterior a la actual.
                                    }else  if (!$surveyController->checkIfAnswered($survey['id'], $username)) { //De no estar cerrada, se comprueba si el usuario ya la ha respondido.?>
                                        <form method="post"><!--Si no la ha respondido, se muestra en botón que lleva responderla, incluyendo el ID en la URL-->
                                            <button type="submit" name="answerButton" 
                                                value="<?php echo $survey['id']; ?>">Responder</button>
                                        </form>

                                    <?php } else {
                                        echo "Respondida"; //Finalmente, en caso de que no estuviese cerrada pero sí respondida, indicará  dicha circunstancia
                                    } ?>
                                </td>
                                <td>
                                    <!--En la celda de compartir, se copia, mediante "onclick", la id de la encuesta, que permitirá compartirla a nuevos participantes-->
                                    <button class="OtherButtons"
                                        onclick="navigator.clipboard.writeText('<?php echo $survey['id']; ?>')">Copiar
                                        enlace</button>

                                </td>
                                <td>
                                    <!--Finalmente, si el usuario es el creador de la encuesta o tiene privilegios de Admin, tendrá la prerrogativa de eliminarla-->
                                    <?php if (strcasecmp($username, $survey['creator_name']) == 0|| $isAdmin) { ?>
                                        <form method="post">
                                            <button type="submit" name="deleteSurvey"
                                                value="<?php echo $survey['id']; ?>">Eliminar</button>
                                        </form>

                                    <?php } else{ 
                                        echo "Sin permisos";//En caso de no cumplir ninguno de esos requisitos, se mostrara el texto "sin permisos".
                                        } ?> 
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <section>
            <!--Finalmente, en este formulario se puede introducir un código facilitado por un creador o participante para agregar y responder una encuesta, que entonces aparecerá en la tabla-->
            <form method="post">
                <h2>Agregar Encuesta</h2>
                <label for="addSurvey"></label>
                <input type="text" name="addSurvey" id="surveyID" placeholder="Pon Aquí tu Código" required>
                <input type="submit" name="answerNewSurvey" value="Responder">
            </form>
        </section>
    </main>
    <?php include "../assets/footer.php"; ?><!--Se incluye el header-->
</body>

</html>