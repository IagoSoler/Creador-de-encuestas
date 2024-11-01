<?php
/*Esta página será el menú principal, desde donde el usuario podrá redirigirse a otras partes de la aplicación web */
session_start();//Se crea una sesión (que tendrá por nombre de usuario el de la sesión inciada en "login.php").
require_once '../controllers/UserController.php';//Se enlaza el presente archivo (Vista), a su respectivos Controladores, siguiendo arquitectura MVC.
require_once '../controllers/SurveyController.php';

if (!isset ($_SESSION['username'])) {//Si no hay sesión iniciada, cualquier intento de acceder a esta página devovlerá al login.
    header("Location: login.php");
    exit;
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"><!--Diseño responsive para que se adapte al dispositivo--->
    <title>Inicio</title>
    <link rel="stylesheet" href="../assets/styles.css"><!--Enlace a la página de estilos-->
</head>

<body>
    <?php include "../assets/header.php"; ?>
    <main>
        <h1>Bienvenido,
            <?php echo $_SESSION['username']; ?><!--Se incluye el header-->
        </h1>
        <h2>Menú principal</h2>
        <!--Se crea la sección cuyo estilo figurará como MainMenu en CSS-->
        <section class="MainMenu">
            <!--en ella se muestran dos botones, que contituyen meros enlaces a las páginas indicadas.-->
            <button class ="OtherButtons" onclick="window.location.href='create_survey.php'">Crear encuesta</button>
            <button class ="OtherButtons" onclick="window.location.href='surveys.php'">Consultar mis encuestas</button>
        </section>
    </main>
    <?php include "../assets/footer.php"; ?><!--Se incluye el footer-->
</body>

</html>