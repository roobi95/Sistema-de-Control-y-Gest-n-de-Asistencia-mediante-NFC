<?php
require_once 'conexion_bd.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $Nombre = htmlspecialchars($_POST['nombre']);
    $Contrasena = htmlspecialchars($_POST['password']);

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE Nombre = :nombre");
        $stmt->bindParam(':nombre', $Nombre, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            if (password_verify($Contrasena, $usuario['Contrasena_Hash'])) {
                // Establecer la variable de sesión para el nombre del usuario
                $_SESSION['nombre'] = $usuario['Nombre'];

                // Redirigir según el tipo de usuario
                switch ($usuario['Tipo_Usuario']) {
                    case 'Administrador':
                        header('Location: ../php/menu.php');
                        exit();
                    case 'Profesor':
                        header('Location: ../php/menu_profesorado.php');
                        exit();
                    case 'Alumno':
                        header('Location: ../seguimiento/faltas_alumno.php');
                        exit();
                    default:
                        // En caso de un tipo de usuario desconocido
                        $error_message = "Tipo de usuario desconocido";
                        header('Location: ../index.html?error=' . urlencode($error_message));
                        exit();
                }
            } else {
                $error_message = "Credenciales incorrectas";
                header('Location: ../index.html?error=' . urlencode($error_message));
                exit();
            }
        } else {
            $error_message = "Credenciales incorrectas";
            header('Location: ../index.html?error=' . urlencode($error_message));
            exit();
        }
    } catch (PDOException $e) {
        $error_message = "Error en la base de datos: " . $e->getMessage();
        header('Location: ../index.html?error=' . urlencode($error_message));
        exit();
    } finally {
        if ($conn) {
            $conn = null;
        }
    }
}
?>

