<?php
include '../conexion/funciones.php';
// Conectar a la base de datos MySQL
$pdo = pdo_connect_mysql();
// Obtener la página a través de la solicitud GET (parámetro URL: page), si no existe, establecer la página en 1 por defecto
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
// Número de registros para mostrar en cada página
$records_per_page = 10;

// Preparar la declaración SQL y obtener los registros de la tabla Tarjetas_NFC, LIMIT determinará la página
$stmt = $pdo->prepare('SELECT * FROM Tarjetas_NFC ORDER BY ID_Tarjeta_NFC LIMIT :current_page, :record_per_page');
$stmt->bindValue(':current_page', ($page - 1) * $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':record_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->execute();
// Obtener los registros para mostrarlos en nuestra plantilla.
$tarjetas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener el número total de tarjetas, esto determinará si debe haber un botón siguiente y anterior
$num_tarjetas = $pdo->query('SELECT COUNT(*) FROM Tarjetas_NFC')->fetchColumn();
?>

<?= template_header('Listado de Tarjetas NFC') ?>

<div class="content read">
    <h2>Listado de Tarjetas NFC</h2>
<div class="table-container">
    <table>
        <thead>
            <tr>
                <td>ID Tarjeta NFC</td>
                <td>NFC Tag</td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tarjetas as $tarjeta): ?>
            <tr>
                <td><?= $tarjeta['ID_Tarjeta_NFC'] ?></td>
                <td><?= $tarjeta['NFC_Tag'] ?></td>
                <td class="actions">
                    <a href="editar_tarjeta.php?id=<?= $tarjeta['ID_Tarjeta_NFC'] ?>" class="edit"><i class="fas fa-pen fa-xs"></i></a>
                    <a href="eliminar_tarjeta.php?id=<?= $tarjeta['ID_Tarjeta_NFC'] ?>" class="trash"><i class="fas fa-trash fa-xs"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
    <div class="pagination">
        <?php if ($page > 1): ?>
        <a href="listar_tarjetas.php?page=<?= $page - 1 ?>"><i class="fas fa-angle-double-left fa-sm"></i></a>
        <?php endif; ?>
        <?php if ($page * $records_per_page < $num_tarjetas): ?>
        <a href="listar_tarjetas.php?page=<?= $page + 1 ?>"><i class="fas fa-angle-double-right fa-sm"></i></a>
        <?php endif; ?>
    </div>
</div>

<?= template_footer() ?>
