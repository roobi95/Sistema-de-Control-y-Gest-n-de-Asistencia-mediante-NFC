<?php
include '../conexion/funciones.php';
$pdo = pdo_connect_mysql();
$msg = '';

// Obtener la lista de cursos desde la base de datos
$stmt_cursos = $pdo->query('SELECT ID_Curso, Nombre_Curso FROM Cursos');
$cursos_disponibles = $stmt_cursos->fetchAll(PDO::FETCH_ASSOC);

// Obtener la lista de tarjetas NFC no asignadas desde la base de datos
$sql_tarjetas_disponibles = 'SELECT ID_Tarjeta_NFC, NFC_Tag FROM Tarjetas_NFC WHERE ID_Tarjeta_NFC NOT IN (SELECT NFC_Tag FROM Usuarios WHERE NFC_Tag IS NOT NULL)';
$stmt_tarjetas = $pdo->query($sql_tarjetas_disponibles);
$tarjetas_disponibles = $stmt_tarjetas->fetchAll(PDO::FETCH_ASSOC);

if (!empty($_POST)) {
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $apellidos = isset($_POST['apellidos']) ? $_POST['apellidos'] : '';
    $cursoPerteneciente = !empty($_POST['curso']) ? $_POST['curso'] : null;
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';
    $direccion = isset($_POST['direccion']) ? $_POST['direccion'] : '';
    $nfcTag = !empty($_POST['nfc_tag']) ? $_POST['nfc_tag'] : null;
    $tipoUsuario = isset($_POST['tipo_usuario']) ? $_POST['tipo_usuario'] : '';
    $contrasena = isset($_POST['password']) ? $_POST['password'] : '';

    // Si no se sube una foto, asignar un valor predeterminado
    $rutaImagen = '';

    if (isset($_FILES['Foto']) && $_FILES['Foto']['error'] === UPLOAD_ERR_OK) {
        $carpetaImagenes = "../img/";
        $nombreImagen = $_FILES['Foto']['name'];
        $rutaTemporal = $_FILES['Foto']['tmp_name'];
        $rutaImagen = $carpetaImagenes . $nombreImagen;

        if (move_uploaded_file($rutaTemporal, $rutaImagen)) {
            // La imagen se subió con éxito
        } else {
            $msg = 'Error al mover el archivo de imagen.';
        }
    }

    // Aplicar hash a la contraseña
    $contrasenaHash = password_hash($contrasena, PASSWORD_DEFAULT);

    // Preparar la consulta SQL para insertar el nuevo usuario
    $stmt = $pdo->prepare('INSERT INTO Usuarios (Foto, Nombre, Apellidos, Curso_Perteneciente, Email, Telefono, Direccion, NFC_Tag, Tipo_Usuario, Contrasena_Hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    // Ejecutar la consulta SQL
    $stmt->execute([$rutaImagen, $nombre, $apellidos, $cursoPerteneciente, $email, $telefono, $direccion, $nfcTag, $tipoUsuario, $contrasenaHash]);

    $msg = '¡Usuario creado exitosamente!';

    // Redirigir de vuelta al menú principal
    header('Location: menu.php');
    exit;
}
?>

<?= template_header('Crear Usuario') ?>

<div class="content update">
    <h2>Crear Usuario</h2>
    <form action="crear.php" method="post" enctype="multipart/form-data">
        <label for="Foto">Foto:</label>
        <input type="file" name="Foto" id="Foto" placeholder="Foto" accept="image/*">

        <label for="nombre">Nombre: </label>
        <input type="text" name="nombre" placeholder="Nombre usuario" id="nombre">

        <label for="apellidos">Apellidos: </label>
        <input type="text" name="apellidos" placeholder="Apellido1 Apellido2" id="apellidos">

        <label for="email">Email: </label>
        <input type="text" name="email" placeholder="ejmeplo@ejemplo.com" id="email">

        <label for="telefono">Teléfono:</label>
        <input type="text" name="telefono" placeholder="xxxxxxxxx" id="telefono">

        <label for="direccion">Dirección:</label>
        <input type="text" name="direccion" placeholder="Calle Ejemplo, numero x" id="direccion">

        <label for="tipo_usuario">Tipo de Usuario:</label>
        <select type="text" name="tipo_usuario" id="tipo_usuario" placeholder="Tipo de Usuario">
            <option value="">Seleccionar tipo de usuario</option>
            <option value="Alumno">Alumno</option>
            <option value="Profesor">Profesor</option>
            <option value="Administrador">Administrador</option>
        </select>

        <label for="curso">Curso:</label>
        <select type="text" name="curso" id="curso" placeholder="Curso Perteneciente">
            <option value="">Seleccionar curso (opcional)</option>
            <?php foreach ($cursos_disponibles as $curso) : ?>
                <option value="<?= $curso['ID_Curso'] ?>"><?= $curso['Nombre_Curso'] ?></option>
            <?php endforeach; ?>
        </select>

        <label for="nfc_tag">NFC Tag:</label>
        <select name="nfc_tag" id="nfc_tag">
            <option value="">Seleccionar tarjeta NFC (opcional)</option>
            <?php foreach ($tarjetas_disponibles as $tarjeta) : ?>
                <option value="<?= htmlspecialchars($tarjeta['ID_Tarjeta_NFC']) ?>"><?= htmlspecialchars($tarjeta['NFC_Tag']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>
        <input type="submit" value="Crear">
    </form>
    <?php if ($msg) : ?>
        <p><?= $msg ?></p>
    <?php endif; ?>
</div>

<?= template_footer() ?>
