<?php
/**En esta página se efectua la lógica de "logout", es decir, de cierre de sesión, tras lo cual ofrecerá un enlace al inicio */
require_once '../controllers/UserController.php';//Se enlaza el presente archivo (Vista), a su respectivo Controlador, siguiendo arquitectura MVC.
$userController = new UserController();//Se crea una instancia de la  clase de UserController.
$userController->logoutUser();//Se destruye la sesión actual.
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"><!--Diseño responsive para que se adapte al dispositivo--->
    <title>Registro</title>
    <link rel="stylesheet" href="../assets/styles.css"><!--Enlace a la página de estilos-->

</head>

<body>


    <main>
        <section>
            <p>Sesión cerrada</P><!--Mensaje de la página-->
            <a href="../index.php">Volver al inicio</a><!--Enlace al menú de incio de sesión-->
        </section>
    </main>

</body>

</html>