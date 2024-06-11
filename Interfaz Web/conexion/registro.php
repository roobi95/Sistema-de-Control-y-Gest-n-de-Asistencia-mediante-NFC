<?php
require_once 'conexion_bd.php';

// Verificar si se recibieron datos por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir datos del formulario y limpiarlos
    $nombre = htmlspecialchars($_POST["nombre"]);
    $apellidos = htmlspecialchars($_POST["apellidos"]);
    $email = htmlspecialchars($_POST["email"]);
    $contrasena = $_POST["contrasena"];

    // Hashear la contraseña
    $hash = password_hash($contrasena, PASSWORD_DEFAULT);

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Preparar la consulta SQL con prepared statements
        $stmt = $conn->prepare("INSERT INTO Usuarios (Nombre, Apellidos, Email, Contrasena_Hash) VALUES (:nombre, :apellidos, :email, :hash)");
        
        // Vincular parámetros
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':apellidos', $apellidos, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':hash', $hash, PDO::PARAM_STR);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo "Usuario registrado exitosamente.";
        } else {
            echo "Error al registrar el usuario.";
        }
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}
?>
