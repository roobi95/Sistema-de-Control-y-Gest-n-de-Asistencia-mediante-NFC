<?php
include '../conexion/funciones.php';
$pdo = pdo_connect_mysql();
$msg = '';

// Verificar que el ID de la tarjeta NFC existe
if (isset($_GET['id'])) {
    // Seleccionar el registro que se va a eliminar
    $stmt = $pdo->prepare('SELECT * FROM Tarjetas_NFC WHERE ID_Tarjeta_NFC = ?');
    $stmt->execute([$_GET['id']]);
    $tarjeta = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$tarjeta) {
        exit('¡La tarjeta NFC no existe con ese ID!');
    }

    // Confirmar la eliminación antes de proceder
    if (isset($_GET['confirm'])) {
        if ($_GET['confirm'] == 'yes') {
            // El usuario confirmó la eliminación, proceder a eliminar el registro
            $stmt = $pdo->prepare('DELETE FROM Tarjetas_NFC WHERE ID_Tarjeta_NFC = ?');
            $stmt->execute([$_GET['id']]);
            $msg = '¡Has eliminado la tarjeta NFC!';
            header('Location: listar_tarjetas.php');
            exit;
        } else {
            // El usuario canceló, redirigir de nuevo a la página de listado de tarjetas NFC
            header('Location: listar_tarjetas.php');
            exit;
        }
    }
} else {
    exit('¡No se ha especificado ningún ID!');
}
?>

<?=template_header('Eliminar Tarjeta NFC')?>

<div class="content delete">
    <h2>Eliminar Tarjeta NFC #<?=$tarjeta['ID_Tarjeta_NFC']?></h2>
    <?php if ($msg): ?>
        <p><?=$msg?></p>
    <?php else: ?>
        <p>¿Estás seguro de que quieres eliminar la tarjeta NFC #<?=$tarjeta['ID_Tarjeta_NFC']?>?</p>
        <div class="yesno">
            <a href="eliminar_tarjeta.php?id=<?=$tarjeta['ID_Tarjeta_NFC']?>&confirm=yes">Sí</a>
            <a href="eliminar_tarjeta.php?id=<?=$tarjeta['ID_Tarjeta_NFC']?>&confirm=no">No</a>
        </div>
    <?php endif; ?>
</div>

<?=template_footer()?>

