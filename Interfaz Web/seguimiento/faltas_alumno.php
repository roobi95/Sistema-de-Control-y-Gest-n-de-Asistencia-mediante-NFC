<?php
include '../conexion/funciones.php';

// Connect to MySQL database
$pdo = pdo_connect_mysql();

// Get the page via GET request (URL param: page), if non exists default the page to 1
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// Number of records to show on each page
$records_per_page = 10;

// Prepare the SQL statement and get records from our contacts table, LIMIT will determine the page
$stmt = $pdo->prepare('
    SELECT u.Foto, u.Nombre, u.Apellidos, c.Nombre_Curso as Curso, a.Fecha, a.Hora_Entrada, a.Hora_Salida, a.ID_Asistencia
    FROM Usuarios u
    JOIN Asistencia a ON u.ID_Usuario = a.ID_Usuario
    JOIN Cursos c ON a.ID_Curso = c.ID_Curso
    WHERE (a.Hora_Entrada IS NULL OR a.Hora_Salida IS NULL) AND u.Tipo_Usuario = "Alumno"
    ORDER BY u.ID_Usuario
    LIMIT :current_page, :record_per_page
');

$stmt->bindValue(':current_page', ($page - 1) * $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':record_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->execute();
$usuarios_asistencia = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the total number of records
$num_usuarios_asistencia = $pdo->query('
    SELECT COUNT(*)
    FROM Usuarios u
    JOIN Asistencia a ON u.ID_Usuario = a.ID_Usuario
    JOIN Cursos c ON a.ID_Curso = c.ID_Curso
    WHERE (a.Hora_Entrada IS NULL OR a.Hora_Salida IS NULL) AND u.Tipo_Usuario = "Alumno"
')->fetchColumn();
?>

<?=template_header3('Faltas')?>

<div class="content read">
    <h2>Listado de Faltas</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <td>Foto</td>
                    <td>Nombre</td>
                    <td>Apellidos</td>
                    <td>Curso</td>
                    <td>Fecha Falta</td>
                    <td></td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios_asistencia as $usuario_asistencia): ?>
                <tr>
                    <td><img src="<?= $usuario_asistencia['Foto'] ?>" alt="<?= $usuario_asistencia['Nombre'] ?>" style="max-width: 100px; max-height: 100px;"></td>
                    <td><?=$usuario_asistencia['Nombre']?></td>
                    <td><?=$usuario_asistencia['Apellidos']?></td>
                    <td><?=$usuario_asistencia['Curso']?></td>
                    <td><?=$usuario_asistencia['Fecha']?></td>                    
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="pagination">
        <?php if ($page > 1): ?>
        <a href="menu.php?page=<?=$page-1?>"><i class="fas fa-angle-double-left fa-sm"></i></a>
        <?php endif; ?>
        <?php if ($page * $records_per_page < $num_usuarios_asistencia): ?>
        <a href="menu.php?page=<?=$page+1?>"><i class="fas fa-angle-double-right fa-sm"></i></a>
        <?php endif; ?>
    </div>
</div>

<?=template_footer()?>

