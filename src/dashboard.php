<?php
include '../JWTHandler.php';
include "../env.php";

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

        // Muestra el nombre del usuario
        echo "Bienvenido, $username!";
        echo "<a href='index.php' onclick='return confirm(\"¿Estás seguro de cerrar sesión?\")'>Logout</a>";
    } else {
        echo "Error al decodificar el token.";
    }
} else {
    echo "Token no encontrado.";
}
?>
