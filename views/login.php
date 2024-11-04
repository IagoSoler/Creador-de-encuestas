<?php
/**Sirve la presente como página de e incio de la aplicación, a donde remitirá el index 
  y cualquier otra página si se intentase acceder sin un usuario acreditado.
  Naturalmente, en esta página se efectuará el login. También ofrece un enlace al registro */
session_start();//Se crea una sesión (que tendrá por nombre de usuario el de la sesión inciada en "login.php").
require_once '../controllers/UserController.php';//Se enlaza el presente archivo (Vista), a su respectivo Controlador, siguiendo arquitectura MVC.
$userController = new UserController();//Se crea una instancia de la  clase de UserController.
if (isset($_POST['login'])) {//Al pulsar el botón de "Iniciar Sesión", se envía el formulario por método POST y se ejecuta el sigueinte código.
    $username = $_POST['username'];//Se recogen en el formulario el nombre y contraseña escritas por el usuario.
    $password = $_POST['password'];

   //Se pasan ambos valores como parámetros de la función loginUser.
    if ($userController->loginUser($username, $password)) {//En caso de devovler true (es decir, se ha encontrado una fila coincidente en la BBDD).
        $_SESSION['username'] = $username;//Se crea la sesión con el nombre correspondiente.
        header("Location: home.php");//Se redirige al menú de inicio.
        exit;
    } else {
        $error = "Nombre de usuario o contraseña incorrectos.";//En caso de que no coincidiese ninguna fila, se guarda el emsnaje de rror en la varaible $error.
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"><!--Diseño responsive para que se adapte al dispositivo--->
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="../assets/styles.css"><!--Enlace a la página de estilos-->
</head>
<body>
<?php include "../assets/header.php"; ?><!--Se incluye el header-->

    <main>
        <section>
            <h2>Inicio de Sesión</h2>
            <form method="post"><!--Al pulsar el botón submit del formulario, se enviarán los dato a la página actual por el método "post"-->
                <!--Formulario para insertar usuario y contraseña-->
                <label for="username">Nombre de usuario:</label>
                <input type="text" id="username" name="username" required>
                <br>
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
                <br>
                <br>
                <button type="submit" name="login">Iniciar sesión</button><!--Botón para enviar el formulario-->
            </form>
             <!--Si al pulsar el botón se guardase el mensaje de error anteriormente indicado, éste se imprimirá por pantalla en rojo -->
            <?php if (isset($error)): ?>
                <p style="color: red"><?php echo $error; ?></p>
            <?php endif; ?>
            <p>¿No tienes usuario? <a href="register.php">Regístrate</a></p><!--Alternativamente, se podrá pulsar en este enlace que llevará a la página de registro-->
        </section>
    </main>
    <?php include "../assets/footer.php"; ?><!--Se incluye el footer-->
</body>
</html>
