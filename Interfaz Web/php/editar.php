<?php
include '../conexion/funciones.php';
$pdo = pdo_connect_mysql();
$msg = '';

// Verificar si se ha enviado el formulario de edición
if (!empty($_POST)) {
    $id = isset($_POST['id']) ? $_POST['id'] : NULL;
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $apellidos = isset($_POST['apellidos']) ? $_POST['apellidos'] : '';
    $cursoPerteneciente = isset($_POST['curso']) ? $_POST['curso'] : null;
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';
    $direccion = isset($_POST['direccion']) ? $_POST['direccion'] : '';
    $nfcTag = isset($_POST['nfc_tag']) ? $_POST['nfc_tag'] : null;
    $tipoUsuario = isset($_POST['tipo_usuario']) ? $_POST['tipo_usuario'] : '';
    $contrasena = isset($_POST['password']) ? $_POST['password'] : '';

    // Si no se sube una foto, mantener la imagen existente
    $rutaImagen = isset($_POST['ruta_imagen']) ? $_POST['ruta_imagen'] : '';

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

    // Aplicar hash a la contraseña si se proporciona
    //$contrasenaHash = !empty($contrasena) ? password_hash($contrasena, PASSWORD_DEFAULT) : null;
    $contrasenaHash = password_hash($contrasena, PASSWORD_DEFAULT); 

    // Preparar la consulta SQL para actualizar el usuario
    $stmt = $pdo->prepare('UPDATE Usuarios SET Foto = ?, Nombre = ?, Apellidos = ?, Curso_Perteneciente = ?, Email = ?, Telefono = ?, Direccion = ?, NFC_Tag = ?, Tipo_Usuario = ?, Contrasena_Hash = ? WHERE ID_Usuario = ?');

    // Si $cursoPerteneciente es null, asignar NULL a la variable
    if ($cursoPerteneciente === '') {
        $cursoPerteneciente = null;
    }

    // Si $nfcTag es null, asignar NULL a la variable
    if ($nfcTag === '') {
        $nfcTag = null;
    }

    // Ejecutar la consulta SQL
    $stmt->execute([$rutaImagen, $nombre, $apellidos, $cursoPerteneciente, $email, $telefono, $direccion, $nfcTag, $tipoUsuario, $contrasenaHash, $id]);

    $msg = '¡Usuario actualizado exitosamente!';

    // Redirigir de vuelta al menú principal
    header('Location: menu.php');
    exit;
}

// Obtener el ID del usuario a editar desde la URL
if (isset($_GET['id'])) {
    // Obtener la información del usuario de la base de datos
    $stmt = $pdo->prepare('SELECT * FROM Usuarios WHERE ID_Usuario = ?');
    $stmt->execute([$_GET['id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        exit('¡No existe usuario con ese ID!');
    }
} else {
    exit('¡ID no especificado!');
}

// Obtener la lista de cursos desde la base de datos
$stmt_cursos = $pdo->query('SELECT ID_Curso, Nombre_Curso FROM Cursos');
$cursos_disponibles = $stmt_cursos->fetchAll(PDO::FETCH_ASSOC);

// Obtener la lista de tarjetas NFC desde la base de datos
$stmt_tarjetas = $pdo->query('SELECT ID_Tarjeta_NFC, NFC_Tag FROM Tarjetas_NFC');
$tarjetas_disponibles = $stmt_tarjetas->fetchAll(PDO::FETCH_ASSOC);
?>

<?= template_header('Editar Usuario') ?>

<div class="content update">
    <h2>Editar Usuario #<?= $usuario['ID_Usuario'] ?></h2>
    <form action="editar.php?id=<?= $usuario['ID_Usuario'] ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $usuario['ID_Usuario'] ?>">
        <input type="hidden" name="ruta_imagen" value="<?= $usuario['Foto'] ?>">

        <label for="Foto">Foto:</label>
        <input type="file" name="Foto" id="Foto" placeholder="Foto" accept="image/*">

        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" value="<?= $usuario['Nombre'] ?>">

        <label for="apellidos">Apellidos:</label>
        <input type="text" name="apellidos" id="apellidos" value="<?= $usuario['Apellidos'] ?>">

        <label for="email">Email:</label>
        <input type="text" name="email" id="email" value="<?= $usuario['Email'] ?>">

        <label for="telefono">Teléfono:</label>
        <input type="text" name="telefono" id="telefono" value="<?= $usuario['Telefono'] ?>">

        <label for="direccion">Dirección:</label>
        <input type="text" name="direccion" id="direccion" value="<?= $usuario['Direccion'] ?>">

        <label for="contrasena">Contraseña:</label>
        <input type="password" name="password" id="password" placeholder="Dejar en blanco para mantener la actual">

        <label for="nfc_tag">NFC Tag:</label>
        <select name="nfc_tag" id="nfc_tag">
            <option value="">Seleccionar tarjeta NFC (opcional)</option>
            <?php foreach ($tarjetas_disponibles as $tarjeta) : ?>
                <option value="<?= $tarjeta['ID_Tarjeta_NFC'] ?>" <?= ($tarjeta['ID_Tarjeta_NFC'] == $usuario['NFC_Tag']) ? 'selected' : '' ?>>
                    <?= $tarjeta['NFC_Tag'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="curso">Curso:</label>
        <select name="curso" id="curso">
            <option value="">Seleccionar curso (opcional)</option>
            <?php foreach ($cursos_disponibles as $curso) : ?>
                <option value="<?= $curso['ID_Curso'] ?>" <?= ($curso['ID_Curso'] == $usuario['Curso_Perteneciente']) ? 'selected' : '' ?>>
                    <?= $curso['Nombre_Curso'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="tipo_usuario">Tipo de Usuario:</label>
        <select name="tipo_usuario" id="tipo_usuario">
            <option value="">Seleccionar tipo de usuario</option>
            <option value="Alumno" <?= ($usuario['Tipo_Usuario'] == 'Alumno') ? 'selected' : '' ?>>Alumno</option>
            <option value="Profesor" <?= ($usuario['Tipo_Usuario'] == 'Profesor') ? 'selected' : '' ?>>Profesor</option>
            <option value="Administrador" <?= ($usuario['Tipo_Usuario'] == 'Administrador') ? 'selected' : '' ?>>Administrador</option>
        </select>
        <label>

        <input type="submit" value="Actualizar">
    </form>
    <?php if ($msg) : ?>
        <p><?= $msg ?></p>
    <?php endif; ?>
</div>

<?= template_footer() ?>

