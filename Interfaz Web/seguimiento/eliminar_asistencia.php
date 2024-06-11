<?php
include '../conexion/funciones.php';

$pdo = pdo_connect_mysql();
$msg = '';

// Verificar que el ID de la asistencia existe
if (isset($_GET['id'])) {
    // Seleccionar el registro que se va a eliminar
    $stmt = $pdo->prepare('SELECT * FROM Asistencia WHERE ID_Asistencia = ?');
    $stmt->execute([$_GET['id']]);
    $asistencia = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$asistencia) {
        exit('¡La asistencia no existe con ese ID!');
    }

    // Confirmar la eliminación antes de proceder
    if (isset($_GET['confirm'])) {
        if ($_GET['confirm'] == 'yes') {
            // El usuario confirmó la eliminación, proceder a eliminar el registro
            $stmt = $pdo->prepare('DELETE FROM Asistencia WHERE ID_Asistencia = ?');
            $stmt->execute([$_GET['id']]);
            $msg = '¡Has eliminado la asistencia!';
	    header('Location: asistencia.php');
	    exit;
        } else {
            // El usuario canceló, redirigir de nuevo a la página de listado de asistencias
            header('Location: asistencia.php');
            exit;
        }
    }
} else {
    exit('¡No se ha especificado ningún ID!');
}
?>

<?=template_header('Eliminar Asistencia')?>

<div class="content delete">
    <h2>Eliminar Asistencia #<?=$asistencia['ID_Asistencia']?></h2>
    <?php if ($msg): ?>
        <p><?=$msg?></p>
    <?php else: ?>
        <p>¿Estás seguro de que quieres eliminar la asistencia #<?=$asistencia['ID_Asistencia']?>?</p>
        <div class="yesno">
            <a href="eliminar_asistencia.php?id=<?=$asistencia['ID_Asistencia']?>&confirm=yes">Sí</a>
            <a href="eliminar_asistencia.php?id=<?=$asistencia['ID_Asistencia']?>&confirm=no">No</a>
        </div>
    <?php endif; ?>
</div>

<?=template_footer()?>

