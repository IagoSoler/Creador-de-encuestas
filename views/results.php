<?php
/* En esta página se mostrarán los resultados de cada pregunta de   una encuesta determinada.
La encuesta se identificará con el ID que se transporta en la URL, mediante el método GET*/ 
session_start();//Se crea una sesión (que tendrá por nombre de usuario el de la sesión inciada en "login.php").
require_once '../controllers/SurveyController.php'; //Se enlaza el presente archivo (Vista), a su respectivo Controlador, siguiendo arquitectura MVC.

if (!isset ($_SESSION['username'])) {//Si no hay sesión iniciada, cualquier intento de acceder a esta página devovlerá al login.
    header("Location: login.php");
    exit;
}

$surveyController = new SurveyController();//Se crea una instancia de la  clase de SurveyController.
$survey_id = $_GET['survey_id'];//Se obtiene el id de la encuesta seleccionada, que figurará en la URL de la página.
if (!$survey = $surveyController->getSurveyById($survey_id)) {//Mediante la id obtenida, se obtienen los datos de la encuesta en la BBDD.
    die ('Esta encuesta no existe'); //Si el código de la encuesta no existe devolverá este error.
}
$organizedResults = $surveyController->getSurveyResults($survey_id);//Idénticamente  se extraen los resultados de la encuesta en la BBDD, es decir, las opciones que han sido escogidas para cada pregunta de la encuesta indicada mediante survey_id.

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"><!--Diseño responsive para que se adapte al dispositivo--->
    <title>Resultados de la Encuesta</title>
    <link rel="stylesheet" href="../assets/styles.css"><!--Enlace a la página de estilos-->

</head>

<body>
    <?php include "../assets/header.php"; ?><!--Se incluye el header-->
    <main>
        <h1>Resultados de
            <?php echo $survey['title']; ?><!--Se Muestra el nombre de esa encuesta-->
        </h1>
        <?php foreach ($organizedResults as $question => $options): ?><!--Se itera mediante un foreach para cada pregunta dentro de esa encuesta-->
            <section class="chart">
                <h3>
                    <?php echo $question; ?><!--Se muestra el título de dicha pregunta-->
                </h3>
                <?php
                $maxAnswers = max(array_values($options));//Se comprueba cúal es la opción más escogida dentro de esa pregunta. Esto servirá para definir el tamañao de cada gráfico.
                foreach ($options as $option => $count): ?><!--Dentro de cada pregunta, se itera sobre cada una de sus opciones-->
                <!--Para cada pregunta se cuenta el número de respuestas y se crea el gráfico, que seimpre tendrá el tamaño del 100% de la barra para la opcion más escogida, manteniendo las demás un tamaño proporcional a su conteo.-->
                    <div class="bar" style="width: <?php echo ($maxAnswers > 0 ? ($count / $maxAnswers * 100) : 0); ?>%"><!--No obstante, si no constansen respuestas para esa pregunta , se asigna directamente el valor de cero, para evitar divisiones entre 0-->
                        <div class="bar-text" style="white-space: nowrap;"><!--Nowrap evita saltos de línea en la leyenda del gráfico-->
                            <?php
                            echo $option . ": " . $count;//Dentro del gráfico, figuran el nombre de la opción y el número de veces escogida.
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>
        <?php endforeach; ?>
    </main>
    <?php include "../assets/footer.php"; ?><!--Se incluye el footer-->
</body>

</html>