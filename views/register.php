<?php
/*Esta es la página de registro, donde se podrá registrar un nuevo usuario*/
require_once '../Controllers/UserController.php';//Se enlaza el presente archivo (Vista), a su respectivo Controlador, siguiendo arquitectura MVC.
$userController = new UserController();//Se crea una instancia de la  clase de UserController.
$userController->logoutUser();//Se destruye la sesión actual, en caso de que el usuario ya estuviese logeado, para evitar errores.
session_start();//Se crea una sesión, que servirá para guradar la sesión del suuario en caso de que se registrase.
if (isset ($_POST['submit'])) {//Al pulsar el botón de "Registrarse", se envía el formulario por método POST y se ejecuta el sigueinte código.
    $username = $_POST['username'];//Se recogen en el formulario el nombre, correo y contraseña escritos por el usuario.
    /* $email = $_POST['email']; */
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password']; // Nuevo campo para confirmar la contraseña
    // Se verifica que las contraseñas coincidan
    if ($password !== $confirmPassword) {
        $error = "Las contraseñas no coinciden."; // Mensaje de error si no coinciden
    } else {
        //Se pasan dichos valores como parámetros de la función registerUser.
        if ($userController->registerUser($username,/*  $email, */ $password)) {//Si al ejecutar dicha función se insertase uan nueva fila en la BBDD, devolverá true.
            $userController->loginUser($username, $password);//Inmediatmente se inciará sesión con los mismo valores.
            $_SESSION['username'] = $username;//Se creará la sesión guardando en ella el nombre del nuevo usuario.
            header("Location: home.php");//Se redirige al menú de inicio.
            exit;
        } else {
            $error = "El registro ha fallado. El nombre de usuario ya existe.";//En caso de que no coincidiese ninguna fila, se guarda el mensaje en la variable $error.
        }
    }
}
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
    <?php include "../assets/header.php"; ?><!--Se incluye el header-->
    <main>
        <section>
            <h2>Formulario de Registro</h2>
            <form method="post"><!--Al pulsar el botón submit del formulario, se enviarán los dato a la página actual por el método "post"-->
                <!--Formulario para insertar usuario, correo y contraseña-->
                <label for="username">Nombre de usuario:</label>
                <input type="text" id="username" name="username" required>
                <br>
                <label for="email">Correo electrónico:(No utilizado por razones de privacidad)</label>
                <input type="email" id="email" name="email" disabled>
                <br>
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
                <br>
                <label for="confirm_password">Confirmar Contraseña:</label> <!-- Nuevo campo para confirmar la contraseña -->
                <input type="password" id="confirm_password" name="confirm_password" required>
                <br>
                <br>
                <button type="submit" name="submit">Registrarse</button><!--Botón para enviar el formulario-->
            </form>
             <!--Si al pulsar el botón se guardase el mensaje de error anteriormente indicado, éste se imprimirá por pantalla en rojo -->
            <?php if (isset ($error)): ?>
                <p style="color: red">
                    <?php echo $error; ?>
                </p>
            <?php endif; ?>
            <p>¿Ya tienes usuario? <a href="login.php">Iniciar sesión</a></p><!--Alternativamente, se podrá pulsar en este enlac,e que lelvará a la página de incio de sesión-->
        </section>
    </main>
    <?php include "../assets/footer.php"; ?><!--Se incluye el footer-->
</body>

</html>