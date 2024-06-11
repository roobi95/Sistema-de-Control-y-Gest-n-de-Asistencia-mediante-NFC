<?php
include '../conexion/funciones.php';
$msg = '';

// Función para ejecutar comandos SSH desde Ubuntu
function ssh_exec($command) {
    $output = shell_exec($command);
    return $output;
}

// Comprobar si se ha enviado el formulario para crear la tarjeta NFC
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['crear_tarjeta'])) {
    // Ejecutar el script de Python en la Raspberry Pi
    $command = 'ssh -p 22 -i /home/servidorweb/.ssh/id_rsa rcasir@192.168.1.155 "python3 /home/rcasir/Desktop/lector_nfc/lectura_tarjetas_nfc.py" 2>&1';
    $output = ssh_exec($command);

    // Mostrar mensaje de éxito o error
    if ($output === null) {
        $msg = "La tarjeta NFC fue creada exitosamente.";
    } else {
        $msg = "Error al crear la tarjeta NFC: $output";
    }
}

// Comprobar si se ha enviado el formulario para detener la ejecución del script
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['detener_tarjeta'])) {
    // Ejecutar un comando para detener el script
    $stop_command = 'ssh -p 22 -i /home/servidorweb/.ssh/id_rsa rcasir@192.168.1.155 "pkill -f lectura_tarjetas_nfc.py" 2>&1';
    $stop_output = ssh_exec($stop_command);

    // Mostrar mensaje de éxito o error
    if ($stop_output === null) {
        $msg = "La ejecución del script fue detenida correctamente.";
    } else {
        $msg = "Error al detener la ejecución del script: $stop_output";
    }
}
?>

<?= template_header('Listado de Tarjetas NFC') ?>

<div class="content update">
    <h2>Crear Tarjeta NFC</h2>
    <form action="crear_tarjeta.php" method="post">
        <input type="submit" name="crear_tarjeta" value="Crear Tarjeta NFC">
        <input type="submit" name="detener_tarjeta" value="Detener Ejecución">
    </form>
    <?php if (!empty($msg)): ?>
        <p><?= $msg ?></p>
    <?php endif; ?>
</div>

<?= template_footer() ?>
