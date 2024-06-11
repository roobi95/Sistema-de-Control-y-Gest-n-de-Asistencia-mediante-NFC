<?php
$servername = "localhost";
$username = "clasesBD";
$password = "cl@sesBD95";
$database = "prototipo_tfg";

try {    
    $conn = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   
} catch (PDOException $e) {    
    die("Conexión fallida: " . $e->getMessage());
} finally {    
    if ($conn) {
        $conn = null;
    }
}
?>
