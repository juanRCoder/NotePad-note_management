<?php
include '../JWTHandler.php';
include "../env.php";

$secret = $_SESSION["SECRET"];
$servidor = $_SESSION["SERVER"];
$usuario = $_SESSION["USER"];
$pwd = $_SESSION["PWD"];
$db = $_SESSION["DB"];

// Intenta obtener el token de la cookie
$token = isset($_COOKIE["TOKEN"]) ? $_COOKIE["TOKEN"] : null;
$secret = $_SESSION["SECRET"];


// Si el token está presente, decódificalo
if ($token) {
    // Decodificar el token (asumiendo que JWTHandler tiene un método para decodificar)
    $decoded_token = JWTHandler::decodeJWT($token, $secret);

    // Verifica si la decodificación fue exitosa
    if ($decoded_token) {
        // Accede a la información del usuario
        $username = $decoded_token["username"];
        $usuarioCurrent = $username;
    } else {
        echo "Error al decodificar el token.";
    }

    try {
        $conexion = new PDO("mysql:host=$servidor;dbname=$db", $usuario, $pwd);
        //CONSULTA PARA OBTNER EL ID DEL USUARIO ACTUAL
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE username = :username");
        $stmt->bindParam(':username', $usuarioCurrent, PDO::PARAM_STR);

        $stmt->execute();
        $idUser = $stmt->fetchColumn();
    } catch (e) {

    }
} else {
    echo "Token no encontrado.";
}

//CONSULTA POST PARA CREAR UNA NUEVA NOTA
// Verificar si la solicitud es una solicitud POST y si tiene datos de nota
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['note'])) {
    // Obtener la nota de la solicitud
    $noteContent = $_POST['note'];

    try {
        $conexion = new PDO("mysql:host=$servidor;dbname=$db", $usuario, $pwd);

        // CONSULTA PARA ALMACENAR LA NOTA CON EL ID DEL USUARIO
        $stmt_two = $conexion->prepare("INSERT INTO notas (id_user, note) VALUES (:idUser, :noteContent)");
        $stmt_two->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        $stmt_two->bindParam(':noteContent', $noteContent, PDO::PARAM_STR);
        $stmt_two->execute();


    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}


//CONSULTA PARA OBTENER TODAS LAS NOTAS ASOCIADAS AL USUARIO ACTUAL
try {
    $conexion = new PDO("mysql:host=$servidor;dbname=$db", $usuario, $pwd);
    $getData = $conexion->prepare("SELECT note FROM notas WHERE id_user = :id");
    $getData->bindParam(':id', $idUser, PDO::PARAM_INT);
    $getData->execute();
    $data = $getData->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css" />
    <title>NotePad</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>

<body>
    <h1>NotePad:</h1>
    <h3>Gestor de notas</h3>
    <article class="noteContainer">
        <div class="boxInfo">
            <?php
            if ($decoded_token) {
                echo "<p>Bienvenido, $username!</p>";
                echo "<a href='index.php' onclick='return confirm(\"¿Estás seguro de cerrar sesión?\")'>Logout</a>";
            }
            ?>
        </div>
        <button class="ShowBoxAdd">Add Note</button>
        <form class="BoxAddNote" method="POST">
            <textarea class="textNote" name="note" id="note" placeholder="write note.."></textarea>
            <div>
                <button class="SubmitNote">Create</button>
                <button class="HiddenBoxAdd">Leave</button>
            </div>
        </form>
        <?php
        if (!empty($data)) {
            foreach ($data as $nota) {
                echo "<p>$nota</p>";
            }
        } else {
            echo "<p>No tienes notas almacenadas.</p>";
        }
        ?>
    </article>
    <script src="script.js"></script>
</body>

</html>