<?php
include '../JWTHandler.php';
include "../env.php";

$secret = $_SESSION["SECRET"];
$servidor = $_SESSION["SERVER"];
$usuario = $_SESSION["USER"];
$pwd = $_SESSION["PWD"];
$db = $_SESSION["DB"];

// Intenta obtener el token de la cookie
$token = isset ($_COOKIE["TOKEN"]) ? $_COOKIE["TOKEN"] : null;
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

//POST - CREATE
//CONSULTA POST PARA CREAR UNA NUEVA NOTA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset ($_POST['note'])) {
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

//DELETE - DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset ($_POST['idDelete'])) {
    $idDelete = $_POST['idDelete'];

    try {
        // DELETE FROM animales WHERE id = 4;
        $conexion = new PDO("mysql:host=$servidor;dbname=$db", $usuario, $pwd);
        $stmt_four = $conexion->prepare("DELETE FROM notas WHERE id = :idDelete");
        $stmt_four->bindParam(':idDelete', $idDelete, PDO::PARAM_INT);
        $stmt_four->execute();

    } catch (PDOException $e) {
        echo "Error al eliminar nota: " . $e->getMessage();
    }
}

//PUT - UPDATE
//CONSULTA PARA ACTUALIZAR LA NOTA SELECCIONADA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset ($_POST['idNote'])) {

    $updateContent = $_POST['noteUpdate'];
    $idNote = $_POST['idNote'];

    try {
        // Realizar la conexión a la base de datos
        $conexion = new PDO("mysql:host=$servidor;dbname=$db", $usuario, $pwd);

        // CONSULTA PARA ACTUALIZAR LA NOTA
        $stmt_three = $conexion->prepare("UPDATE notas SET note = :noteContent WHERE id = :idNote");
        $stmt_three->bindParam(":noteContent", $updateContent, PDO::PARAM_STR);
        $stmt_three->bindParam(":idNote", $idNote, PDO::PARAM_INT);
        $stmt_three->execute();

    } catch (PDOException $e) {
        echo "Error al actualizar la nota: " . $e->getMessage();
    }
}

//GET - READ
//CONSULTA PARA OBTENER TODAS LAS NOTAS ASOCIADAS AL USUARIO ACTUAL
try {
    $conexion = new PDO("mysql:host=$servidor;dbname=$db", $usuario, $pwd);
    $getData = $conexion->prepare("SELECT note, id FROM notas WHERE id_user = :id");
    $getData->bindParam(':id', $idUser, PDO::PARAM_INT);
    $getData->execute();
    $data = $getData->fetchAll(PDO::FETCH_ASSOC);

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
    <title>NotePad: gestor de notas</title>
    <link rel="icon" href="assets/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<style>
    body {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        height: 100%;
        background-size: auto;
    }
</style>

<body>
    <article class="appContainer">
        <div class="titleApp">
            <h1>NotePad:</h1>
            <h3>note manager</h3>
        </div>
        <article class="noteContainer">
            <div class="boxInfo">
                <?php
                if ($decoded_token) {
                    echo "<p>Welcome, <b style='color: turquoise;'>$username!</b></p>";
                    echo "<button>
                        <a href='index.php' onclick='return confirm(\"¿Estás seguro de cerrar sesión?\")'>
                            <p class='texto'>Logout</p>
                            <svg class='svg' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><path fill='#ffffff' d='M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z'/></svg>
                        </a>
                    </button>";
                }
                ?>
            </div>
            <div class="boxAdd">
                <button class="addShowBox">
                    <p class='texto'>Add Note</p><svg class='svg' xmlns='http://www.w3.org/2000/svg'
                        viewBox='0 0 448 512'>
                        <path fill='#ffffff'
                            d='M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z' />
                    </svg>
                </button>
            </div>
            <!-- CREATE NOTE -->
            <form class="addNoteBox" method="POST">
                <textarea class="textNote" name="note" id="note" placeholder="Maximo (100 palabras..)"></textarea>
                <div class="boxSend">
                    <button class="submitNote">Create</button>
                    <button class="hiddenBox">Leave</button>
                </div>
            </form>
            <!-- UPDDATE NOTE -->
            <form class="updateNoteBox" method="POST">
                <textarea class="textNote" name="noteUpdate" id="note"></textarea>
                <input class="sendIdNote" type="hidden" name="idNote" value="">
                <div class="boxSend">
                    <button class="updateNote">Update</button>
                    <button class="hiddenBox">Leave</button>
                </div>
            </form>
            <!-- DELETE NOTE -->
            <form class="deleteNoteBox" style='display: none' method="post">
                <input type="hidden" name="idDelete" id="idDelete" value="" />
            </form>
            <!-- READ NOTE -->
            <?php
            if (!empty ($data)) {
                foreach ($data as $row) {
                    $idNote = $row['id'];
                    $nota = $row['note'];
                    echo "<div class='boxNote'>
                <p class='nota'>$nota</p>
                <p style='display: none;' id='idNota'>$idNote</p>
                <div class='boxOptions'>
                    <button class='btnUpdate'>
                        <p class='texto'>Update</p>
                    </button>
                    <button class='btnDelete' style='margin-bottom: 0px;'>
                        <p class='texto'>Delete</p>
                    </button>
                </div>
            </div>";
                }
            } else {
                echo "<p class='notNotes'>No tienes notas almacenadas.</p>";
            }
            ?>
        </article>
        <script src="script.js"></script>
    </article>
</body>

</html>