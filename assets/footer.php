<!--Éste archivo es el "Footer" que figurará en las páginas prinicpales de la vista-->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"><!--Diseño responsive para que se adapte al dispositivo--->
</head>

<body>
    <footer>
        <?php
        //Si la sesión está inciada, aparecerá un botón para dirigirse al menúpr incipal en el pie de cada página.
        if (isset($_SESSION['username'])) {
            echo '<a href="home.php">Volver al menú principal</a>';
        }
        ?>

        <p>Copyright &copy; 2024 - Iago Soler Veira</p>
    </footer>
</body>

</html>