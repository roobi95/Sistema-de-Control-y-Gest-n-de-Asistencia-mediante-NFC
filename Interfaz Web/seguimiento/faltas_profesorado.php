<?php
include '../conexion/funciones.php';

// Connect to MySQL database
$pdo = pdo_connect_mysql();

// Get the page via GET request (URL param: page), if non exists default the page to 1
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// Number of records to show on each page
$records_per_page = 10;

// Initialize curso filter variable
$curso_filter = '';

// Check if curso filter is set in GET request
if (isset($_GET['curso']) && !empty($_GET['curso'])) {
    $curso_filter = "c.Nombre_Curso = :curso";      
} 

// Prepare the SQL statement and get records from our contacts table, LIMIT will determine the page
$stmt = $pdo->prepare('
    SELECT 
        u.Foto,
        u.Nombre,
        u.Apellidos,
        c.Nombre_Curso AS Curso,
        a.Fecha AS Fecha_Falta
    FROM 
        Usuarios u
    JOIN 
        Asistencia a ON u.ID_Usuario = a.ID_Usuario
    JOIN 
        Cursos c ON a.ID_Curso = c.ID_Curso
    LEFT JOIN 
        (
        SELECT 
            ID_Usuario,
            COUNT(*) AS Num_Asistencias
        FROM 
            Asistencia
        GROUP BY 
            ID_Usuario
        ) c2 ON u.ID_Usuario = c2.ID_Usuario
    WHERE 
        (c2.Num_Asistencias <= 1 OR c2.Num_Asistencias IS NULL)
        AND NOT EXISTS (
            SELECT 
                1
            FROM 
                Asistencia a2
            WHERE 
                a2.ID_Usuario = u.ID_Usuario
                AND a2.Hora_Entrada IS NOT NULL
                AND a2.Hora_Salida IS NOT NULL
        )
        /* Filtro por Curso si se ha seleccionado */
        ' . ($curso_filter !== '' ? "AND $curso_filter" : '') . '
    ORDER BY 
        u.ID_Usuario
    LIMIT 
        :current_page, :record_per_page
');

// Bind curso filter value if set
if (!empty($curso_filter)) {  
    $stmt->bindValue(':curso', $_GET['curso'], PDO::PARAM_STR);     
} 

$stmt->bindValue(':current_page', ($page-1)*$records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':record_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->execute();
$usuarios_asistencia = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the total number of records
$num_usuarios_asistencia = $pdo->query('
    SELECT COUNT(*)
    FROM 
        Usuarios u
    JOIN 
        Asistencia a ON u.ID_Usuario = a.ID_Usuario
    JOIN 
        Cursos c ON a.ID_Curso = c.ID_Curso
')->fetchColumn();
?>

<?=template_header2('Faltas')?>

<div class="content read">
    <h2>Listado de Faltas</h2>
    <form class="filter-form" method="get">        
        <select name="curso" id="curso">
            <option value="">Todos los Cursos</option>
            <!-- Aquí puedes obtener los cursos de la base de datos y mostrarlos como opciones -->
            <?php
            $stmt_cursos = $pdo->query('SELECT DISTINCT Nombre_Curso FROM Cursos');
            while ($curso = $stmt_cursos->fetch(PDO::FETCH_ASSOC)) {
                $selected = isset($_GET['curso']) && $_GET['curso'] === $curso['Nombre_Curso'] ? 'selected' : '';
                echo "<option value='{$curso['Nombre_Curso']}' $selected>{$curso['Nombre_Curso']}</option>";
            }
            ?>
        </select>
        <button type="submit">Filtrar</button>
    </form>
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
                    <td><?=$usuario_asistencia['Fecha_Falta']?></td>
                    <td class="actions">
                            <a href="editar_falta.php?id=<?= $usuario_asistencia['ID_Asistencia'] ?>" class="edit"><i class="fas fa-pen fa-xs"></i></a>
                            <a href="eliminar_falta.php?id=<?= $usuario_asistencia['ID_Asistencia'] ?>" class="trash"><i class="fas fa-trash fa-xs"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="pagination">
        <?php if ($page > 1): ?>
        <a href="menu.php?page=<?=$page-1?>"><i class="fas fa-angle-double-left fa-sm"></i></a>
        <?php endif; ?>
        <?php if ($page*$records_per_page < $num_usuarios_asistencia): ?>
        <a href="menu.php?page=<?=$page+1?>"><i class="fas fa-angle-double-right fa-sm"></i></a>
        <?php endif; ?>
    </div>
</div>

<?=template_footer()?>

