<?php
/*En esta página se creará una encuesta, indicando su título y fecha de cierre. Despues se procederá a la página 
create_questions.php, transportando en la URL la ID de la nueva encuesta creada */
session_start();//Se crea una sesión (que tendrá por nombre de usuario el de la sesión inciada en "login.php").
require_once '../controllers/SurveyController.php';//Se enlaza el presente archivo (Vista), a su respectivo Controlador, siguiendo arquitectura MVC.

if (!isset ($_SESSION['username'])) {//Si no hay sesión iniciada, cualquier intento de acceder a esta página devovlerá al login.
    header("Location: login.php");
    exit;
}

$surveyController = new SurveyController();//Se crea una instancia de la  clase de SurveyController.
//Al pulsar el botón "Crear encuesta" se ejecuta el siguiente código:
if (isset ($_POST['createButton'])) {
    $title = $_POST['title'];//Se guarda el título enviado desde el formulario.
    $end_date = $_POST['end_date'];//Se hace lo propio con al fecha de cierre seleccionada.
    
    if (strtotime($end_date) < time()) {// Comprobar si la fecha de cierre es posterior a la actual.
        $error = "La fecha de cierre debe ser posterior a la fecha actual.";//En caso de ser inferior, se guardará el error en una variable, y no se creará la encuesta.
    //En caso de estar bien la fecha de cierre, se llamará a la función de crear encuestas de la clase SurveyController.    
    } else if ($survey_id = $surveyController->createSurvey($title, $end_date, $_SESSION['username'])) {//Se pasarán por parámetro los valores del formulario, así como el usuario en la seisón actual.
        //En caso de que la encuesta se haya creado con éxito, llevará a la página para crear las preguntas, insertando en al URL la ID de la encuesta creada, que es devuelta al ejecutar correctamente la función createSurvey().
        header("Location: ../views/create_questions.php?survey_id=$survey_id");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"><!--Diseño responsive para que se adapte al dispositivo--->
    <title>Nueva Encuesta</title>
    <link rel="stylesheet" href="../assets/styles.css"><!--Enlace a la página de estilos-->
</head>

<body>
    <?php include "../assets/header.php"; ?><!--Se incluye el header-->



    <main>
        <h1>Crear Nueva Encuesta</h1>
        <section>
            <h2>Formulario de Creación de Encuesta</h2>
            <!--Este es un sencillo formulario dónde se insertarán el Nombre y fecha de cierre de la encuesta-->
            <form method="post" >
                <label for="title">Título:</label>
                <input type="text" id="title" name="title" required>
                <br>
                <label for="end_date">Fecha de Cierre:</label>
                <input type="date" id="end_date" name="end_date" required>
                <br>
                <br>
                <button type="submit" name="createButton">Crear Encuesta</button>
            </form>
            <!--Si al pulsar el botón se guardase el mensaje de error anteriormente indicado, éste se imprimirá por pantalla en rojo -->
            <?php if (isset ($error)): ?>
                <p style="color: red">
                    <?php echo $error; ?>
                </p>
            <?php endif; ?>
        </section>
    </main>
    <?php include "../assets/footer.php"; ?><!--Se incluye el footer-->
</body>

</html>