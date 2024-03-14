<?php
include '../JWTHandler.php';
include '../env.php';

$secret = $_SESSION["SECRET"];
$servidor = $_SESSION["SERVER"];
$usuario = $_SESSION["USER"];
$pwd = $_SESSION["PWD"];
$db = $_SESSION["DB"];

function registerUser($username, $email, $password, $password_confirm, $secret, $servidor, $usuario, $pwd, $db)
{
    // Verificar si las contraseñas coinciden
    if ($password !== $password_confirm) {
        return "<p style='color: red; margin: 0px;'> Las contraseñas no coinciden. </p>";
    }
    //Hasheo de contraseña
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $user_data = [
        "username" => $username,
        "email" => $email,
        "password" => $hashed_password,
    ];

    try {
        $conexion = new PDO("mysql:host=$servidor;dbname=$db", $usuario, $pwd);

        // Verificar si el usuario ya existe
        $stmt_check = $conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE username = :username OR correo = :email");
        $stmt_check->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt_check->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt_check->execute();

        $user_exists = $stmt_check->fetchColumn();

        if ($user_exists > 0) {
            // El usuario ya existe, devuelve un mensaje de error
            return "<p style='color: red; margin: 0px;'> El usuario o el correo electrónico ya está registrado. </p>";
        }

        // Insertar el nuevo usuario en la base de datos
        $sql = "INSERT INTO usuarios (username, password, correo) VALUES (:username, :password, :email)";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':username', $user_data["username"], PDO::PARAM_STR);
        $stmt->bindParam(':password', $user_data["password"], PDO::PARAM_STR);
        $stmt->bindParam(':email', $user_data["email"], PDO::PARAM_STR);
        $stmt->execute();

        $token = JWTHandler::encodeJWT($user_data, $secret);
        setcookie("TOKEN", $token, time() + 3600, "/");
        header("Location: dashboard.php");


    } catch (PDOException $error) {
        // Error de conexión o inserción
        echo "Error: " . $error->getMessage();
    }

    return JWTHandler::encodeJWT($user_data, $secret);
}

$result = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $us_r = htmlspecialchars($_POST['nombre']);
    $cor_r = htmlspecialchars($_POST['email']);
    $pwd_r = htmlspecialchars($_POST['password']);
    $pwd_c_r = htmlspecialchars($_POST['confirm_password']);

    $token_register = registerUser($us_r, $cor_r, $pwd_r, $pwd_c_r, $secret, $servidor, $usuario, $pwd, $db);

    $result = $token_register;

}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>NotePad: gestor de notas</title>
    <link rel="icon" href="assets/favicon.ico">
</head>

<body>
    <div class="boxRegister">
        <h2>Registro de Usuario</h2>
        <?php if ($result)
            echo $result ?>
            <form action="index.php" method="post">
                <label for="nombre_registro">Nombre de usuario:</label>
                <input type="text" name="nombre" id="nombre_registro" required>
                <br>
                <label for="email_registro">Correo electrónico:</label>
                <input type="email" name="email" id="email_registro" required>
                <br>
                <label for="password_registro">Contraseña:</label>
                <input type="password" name="password" id="password_registro" required>
                <br>
                <label for="password_confirm">Confirmar contraseña:</label>
                <input type="password" name="confirm_password" id="password_confirm" required>
                <br>
                <input type="submit" name="register" value="Registrar">
            </form>
            <a href="login.php">in Login</a>
        </div>
    </body>

    </html>