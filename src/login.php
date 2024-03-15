<?php
session_start();
include '../JWTHandler.php';

$secret = $_SESSION["SECRET"];
$servidor = $_SESSION["SERVER"];
$usuario = $_SESSION["USER"];
$pwd = $_SESSION["PWD"];
$db = $_SESSION["DB"];

function loginUser($username, $password, $secret, $servidor, $db, $usuario, $pwd)
{
    try {
        $conexion = new PDO("mysql:host=$servidor;dbname=$db", $usuario, $pwd);

        $stmt = $conexion->prepare("SELECT password FROM usuarios WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        $stored_password = $stmt->fetchColumn();

        // Dentro de loginUser
        if (password_verify($password, $stored_password)) {
            $user_data = [
                "username" => $username,
                "password" => $stored_password
            ];

            $token = JWTHandler::encodeJWT($user_data, $secret);
            setcookie("TOKEN", $token, time() + 3600, "/");
            header("Location: dashboard.php");

        } else {
            return "<p style='color: red; margin: 0px;'> Usuario no registrado o credenciales incorrectas. </p>";
        }

    } catch (PDOException $error) {
        return "Error de conexión: " . $error->getMessage();
    }
}

$resultLog = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username_login = htmlspecialchars($_POST['nombre']);
    $password_login = htmlspecialchars($_POST['password']);

    $login_result = loginUser($username_login, $password_login, $secret, $servidor, $db, $usuario, $pwd);

    $resultLog = $login_result;

}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NotePad: gestor de notas</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="assets/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="boxRegister" id="boxLogin">
        <h2 class="boxRegister_h2">Iniciar Sesión</h2>
        <?php if ($resultLog)
            echo $resultLog ?>
            <form action="login.php" method="post">
                <div class="boxInput">
                    <label for="nombre_login">Username</label>
                    <input type="text" name="nombre" id="nombre_login" required>
                </div>
                <div class="boxInput">
                    <label for="password_login">Password</label>
                    <input type="password" name="password" id="password_login" required>
                </div>
                <input class="sendPost" type="submit" name="login" value="Log in">
            </form>
            <div class="linkRegisterLogin">
                <a href="index.php">in Register</a>
            </div>
        </div>
    </body>

    </html>