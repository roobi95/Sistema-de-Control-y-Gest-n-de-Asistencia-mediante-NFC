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
    $curso_filter = "WHERE c.Nombre_Curso = :curso";
}

// Prepare the SQL statement and get records from our contacts table, LIMIT will determine the page
$stmt = $pdo->prepare('
    SELECT u.ID_Usuario, u.Foto, u.Nombre, u.Apellidos, c.Nombre_Curso AS Curso, u.Email, u.Telefono, u.Direccion, n.NFC_Tag, u.Tipo_Usuario, u.Contrasena_Hash
    FROM Usuarios u
    LEFT JOIN Cursos c ON u.Curso_Perteneciente = c.ID_Curso
    LEFT JOIN Tarjetas_NFC n ON u.NFC_Tag = n.ID_Tarjeta_NFC
    ' . $curso_filter . '
    ORDER BY ID_Usuario 
    LIMIT :current_page, :record_per_page
');

// Bind curso filter value if set
if (!empty($curso_filter)) {
    $stmt->bindValue(':curso', $_GET['curso'], PDO::PARAM_STR);
}

$stmt->bindValue(':current_page', ($page - 1) * $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':record_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->execute();
// Fetch the records so we can display them in our template.
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the total number of contacts, this is so we can determine whether there should be a next and previous button
$num_usuarios = $pdo->query('SELECT COUNT(*) FROM Usuarios')->fetchColumn();
?>

<?= template_header('Usuarios') ?>

<div class="content read">
    <h2>Listado de Usuarios</h2>
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
    <a href="crear.php" class="create-contact">Crear Usuario</a>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <td>ID_Usuario</td>
                    <td>Foto</td>
                    <td>Nombre</td>
                    <td>Apellidos</td>
                    <td>Curso</td>
                    <td>Email</td>
                    <td>Teléfono</td>
                    <td>Dirección</td>
                    <td>NFC Tag</td>
                    <td>Tipo Usuario</td>
                    <td>Contraseña</td>
                    <td></td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario) : ?>
                    <tr>
                        <td><?= $usuario['ID_Usuario'] ?></td>
                        <td><img src="<?= $usuario['Foto'] ?>" alt="<?= $usuario['Nombre'] ?>" style="max-width: 100px; max-height: 100px;"></td>
                        <td><?= $usuario['Nombre'] ?></td>
                        <td><?= $usuario['Apellidos'] ?></td>
                        <td><?= $usuario['Curso'] ?></td>
                        <td><?= $usuario['Email'] ?></td>
                        <td><?= $usuario['Telefono'] ?></td>
                        <td><?= $usuario['Direccion'] ?></td>
                        <td><?= $usuario['NFC_Tag'] ?></td>
                        <td><?= $usuario['Tipo_Usuario'] ?></td>
                        <td><?= $usuario['Contrasena_Hash'] ?></td>
                        <td class="actions">
                            <a href="editar.php?id=<?= $usuario['ID_Usuario'] ?>" class="edit"><i class="fas fa-pen fa-xs"></i></a>
                            <a href="eliminar.php?id=<?= $usuario['ID_Usuario'] ?>" class="trash"><i class="fas fa-trash fa-xs"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="pagination">
        <?php if ($page > 1) : ?>
            <a href="menu.php?page=<?= $page - 1 ?>"><i class="fas fa-angle-double-left fa-sm"></i></a>
        <?php endif; ?>
        <?php if ($page * $records_per_page < $num_usuarios) : ?>
            <a href="menu.php?page=<?= $page + 1 ?>"><i class="fas fa-angle-double-right fa-sm"></i></a>
        <?php endif; ?>
    </div>
</div>

<?= template_footer() ?>

