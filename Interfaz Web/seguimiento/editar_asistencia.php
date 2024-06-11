<?php
include '../conexion/funciones.php';

$pdo = pdo_connect_mysql();

// Verificar si se ha enviado el formulario de edición
if (isset($_POST['submit'])) {
    // Obtener los datos del formulario
    $id_asistencia = isset($_POST['id']) ? $_POST['id'] : null;
    $hora_entrada = isset($_POST['hora_entrada']) ? $_POST['hora_entrada'] : '';
    $hora_salida = isset($_POST['hora_salida']) ? $_POST['hora_salida'] : '';

    // Validar los datos (puedes agregar más validaciones si es necesario)

    // Actualizar la entrada de asistencia con los nuevos valores
    $stmt = $pdo->prepare('UPDATE Asistencia SET Hora_Entrada = ?, Hora_Salida = ? WHERE ID_Asistencia = ?');
    $stmt->execute([$hora_entrada, $hora_salida, $id_asistencia]);

    // Redireccionar después de la actualización
    header('Location: asistencia.php');
    exit;
}

// Verificar si se proporcionó un ID válido
if (!isset($_GET['id'])) {
    exit('ID de asistencia no proporcionado.');
}

// Obtener la asistencia de la base de datos
$stmt = $pdo->prepare('SELECT * FROM Asistencia WHERE ID_Asistencia = ?');
$stmt->execute([$_GET['id']]);
$asistencia = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si la asistencia existe
if (!$asistencia) {
    exit('Asistencia no encontrada.');
}
?>

<?=template_header('Editar Asistencia')?>

<div class="content update">
    <h2>Editar Asistencia</h2>
    <form action="editar_asistencia.php" method="post">
        <input type="hidden" name="id" value="<?=$asistencia['ID_Asistencia']?>">
        <label for="hora_entrada">Hora de Entrada</label>
        <input type="time" name="hora_entrada" id="hora_entrada" value="<?=substr($asistencia['Hora_Entrada'], 0, 5)?>" required>
        <label for="hora_salida">Hora de Salida</label>
        <input type="time" name="hora_salida" id="hora_salida" value="<?=substr($asistencia['Hora_Salida'], 0, 5)?>" required>
        <input type="submit" name="submit" value="Guardar Cambios">
    </form>
</div>

<?=template_footer()?>

