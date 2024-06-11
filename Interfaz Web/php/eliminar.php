<?php
include '../conexion/funciones.php';
$pdo = pdo_connect_mysql();
$msg = '';

// Verificar que el ID del usuario existe
if (isset($_GET['id'])) {
    // Seleccionar el registro que se va a eliminar
    $stmt = $pdo->prepare('SELECT * FROM Usuarios WHERE ID_Usuario = ?');
    $stmt->execute([$_GET['id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$usuario) {
        exit('¡El usuario no existe con ese ID!');
    }

    // Confirmar la eliminación antes de proceder
    if (isset($_GET['confirm'])) {
        if ($_GET['confirm'] == 'yes') {
            // El usuario confirmó la eliminación, proceder a eliminar el registro
            $stmt = $pdo->prepare('DELETE FROM Usuarios WHERE ID_Usuario = ?');
            $stmt->execute([$_GET['id']]);
            $msg = '¡Has eliminado el usuario!';
        } else {
            // El usuario canceló, redirigir de nuevo a la página de listado de usuarios
            header('Location: menu.php');
            exit;
        }
    }
} else {
    exit('¡No se ha especificado ningún ID!');
}
?>

<?=template_header('Eliminar Usuario')?>

<div class="content delete">
    <h2>Eliminar Usuario #<?=$usuario['ID_Usuario']?></h2>
    <?php if ($msg): ?>
        <p><?=$msg?></p>
    <?php else: ?>
        <p>¿Estás seguro de que quieres eliminar el usuario #<?=$usuario['ID_Usuario']?>?</p>
        <div class="yesno">
            <a href="eliminar.php?id=<?=$usuario['ID_Usuario']?>&confirm=yes">Sí</a>
            <a href="eliminar.php?id=<?=$usuario['ID_Usuario']?>&confirm=no">No</a>
        </div>
    <?php endif; ?>
</div>

<?=template_footer()?>
