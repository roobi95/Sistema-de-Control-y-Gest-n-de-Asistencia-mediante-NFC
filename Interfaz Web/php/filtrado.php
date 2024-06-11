<?php
require_once '../conexion/conexion_bd.php';

$tipoFiltrado = isset($_GET['tipoFiltro']) ? $_GET['tipoFiltro'] : '';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT ID_Usuario, Foto, Nombre, Apellidos, Curso_Perteneciente, Email, Telefono, Direccion, NFC_Tag, Tipo_Usuario, Contrasena FROM Usuarios";
    if (!empty($tipoFiltrado)) {
        $sql .= " WHERE Curso_Perteneciente IN (SELECT ID_Curso FROM Cursos WHERE Nombre_Curso = :curso)";
    }

    $stmt = $conn->prepare($sql);

    if (!empty($tipoFiltrado)) {
        $stmt->bindParam(':curso', $tipoFiltrado, PDO::PARAM_STR);
    }

    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "<table id='tabla_Usuarios' style='width: 100%; border=1'>";
        echo "<tr>";
        echo "<th>ID_Usuario</th>";
        echo "<th>Foto</th>";
        echo "<th>Nombre</th>";
        echo "<th>Apellidos</th>";
        echo "<th>Curso_Perteneciente</th>";
        echo "<th>Email</th>";
        echo "<th>Telefono</th>";
        echo "<th>Direccion</th>";
        echo "<th>NFC_Tag</th>";
        echo "<th>Tipo_Usuario</th>";
        echo "<th>Contrasena</th>";  
        echo "<th colspan='2'>Acciones</th>";      
        echo "</tr>";

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr id='fila_usuario_" . htmlspecialchars($row['ID_Usuario']) . "'>";
            echo "<td>" . htmlspecialchars($row['ID_Usuario']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Foto']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Apellidos']) . "</td>";            
            echo "<td>" . htmlspecialchars($row['Curso_Perteneciente']) . "</td>"; 
            echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Telefono']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Direccion']) . "</td>";
            echo "<td>" . htmlspecialchars($row['NFC_Tag']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Tipo_Usuario']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Contrasena']) . "</td>";
            echo "<td><button class='b2' onclick='confirmarEdicion(" . htmlspecialchars($row['ID_Usuario']) . ")'>Editar</button></td>";
            echo "<td><button class='b3' onclick='confirmarEliminacion(" . htmlspecialchars($row['ID_Usuario']) . ")'>Eliminar</button></td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "No se encontraron registros que coincidan con la consulta.";
    }
} catch (PDOException $e) {    
    echo "ERROR: " . $e->getMessage();
} finally {    
    if ($conn) {
        $conn = null;
    }
}
?>
