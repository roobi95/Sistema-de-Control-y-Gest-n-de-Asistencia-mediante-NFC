<?php
include '../conexion/funciones.php';

$pdo = pdo_connect_mysql();
$msg = '';

// Verificar si se ha enviado el formulario de edición
if (isset($_POST['submit'])) {
    // Obtener los datos del formulario
    $id_tarjeta = isset($_POST['id']) ? $_POST['id'] : null;
    $nfc_tag = isset($_POST['nfc_tag']) ? $_POST['nfc_tag'] : '';

    // Validar los datos (puedes agregar más validaciones si es necesario)

    // Actualizar la tarjeta NFC con los nuevos valores
    $stmt = $pdo->prepare('UPDATE Tarjetas_NFC SET NFC_Tag = ? WHERE ID_Tarjeta_NFC = ?');
    $stmt->execute([$nfc_tag, $id_tarjeta]);

    // Redireccionar después de la actualización
    header('Location: listar_tarjetas.php');
    exit;
}

// Verificar si se proporcionó un ID válido
if (!isset($_GET['id'])) {
    exit('¡ID de tarjeta NFC no proporcionado!');
}

// Obtener la tarjeta NFC de la base de datos
$stmt = $pdo->prepare('SELECT * FROM Tarjetas_NFC WHERE ID_Tarjeta_NFC = ?');
$stmt->execute([$_GET['id']]);
$tarjeta = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si la tarjeta NFC existe
if (!$tarjeta) {
    exit('¡Tarjeta NFC no encontrada!');
}
?>

<?=template_header('Editar Tarjeta NFC')?>

<div class="content update">
    <h2>Editar Tarjeta NFC</h2>
    <form action="editar_tarjeta.php" method="post">
        <input type="hidden" name="id" value="<?=$tarjeta['ID_Tarjeta_NFC']?>">
        <label for="nfc_tag">NFC Tag</label>
        <input type="text" name="nfc_tag" id="nfc_tag" value="<?=$tarjeta['NFC_Tag']?>" required>
        <input type="submit" name="submit" value="Guardar Cambios">
    </form>
</div>

<?=template_footer()?>

