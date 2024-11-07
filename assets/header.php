
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"><!--Diseño responsive para que se adapte al dispositivo--->
</head>
<body>
    <header>
        <h1 class="header-title">Tu creador de Encuestas Online</h1> <!--Se definen las clases para el estilo-->
        <div class="header-nav">
        <?php
        //Si la sesión está inciada, aparecerá el nombre del usuario y un botón para cerrar sesión, que llevará a "logout.php".
        if (isset ($_SESSION['username'])) {
            echo 'Conectado como: '. $_SESSION['username']."  ";
            echo '<button onclick="window.location.href=\'logout.php\';">Cerrar Sesión</button>';
        }
        ?>
        </div>
    </header>
</body>

</html>