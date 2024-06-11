<?php
function pdo_connect_mysql() {
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'clasesBD';
    $DATABASE_PASS = 'cl@sesBD95';
    $DATABASE_NAME = 'prototipo_tfg';
    try {
    	return new PDO('mysql:host=' . $DATABASE_HOST . ';dbname=' . $DATABASE_NAME . ';charset=utf8', $DATABASE_USER, $DATABASE_PASS);
    } catch (PDOException $exception) {
    	// If there is an error with the connection, stop the script and display the error.
    	exit('¡Conexión fallida: ' . $exception->getMessage() . '!');
    }
}
function template_header($title) {
echo <<<EOT
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>$title</title>
		<link href="../css/estilos.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body>
    <nav class="navtop">
    	<div>
    		<h1>Sistema de Gestión y Control de Asistencia</h1>
            <a href="../php/menu.php"><i class="fas fa-users"></i>Usuarios</a>
    		<a href="../php/listar_tarjetas.php"><i class="fas fa-address-card"></i>Tarjetas NFC</a>
			<a href="../seguimiento/asistencia.php"><i class="fas fa-tasks"></i>Asistencia</a>
    		<a href="../seguimiento/faltas.php"><i class="fas fa-address-book"></i>Faltas</a>
			<a href="../conexion/cerrar_sesion.php"><i class="fas fa-reply"></i>Cerrar Sesión</a>
		</div>
    </nav>
EOT;
}

function template_header2($title) {
		echo <<<EOT
		<!DOCTYPE html>
		<html>
			<head>
				<meta charset="utf-8">
				<title>$title</title>
				<link href="../css/estilos.css" rel="stylesheet" type="text/css">
				<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
			</head>
			<body>
			<nav class="navtop">
				<div>
					<h1>Sistema de Gestión y Control de Asistencia</h1>
					<a href="../php/menu_profesorado.php"><i class="fas fa-tasks"></i>Asistencia</a>
					<a href="../seguimiento/faltas_profesorado.php"><i class="fas fa-address-book"></i>Faltas</a>
					<a href="../conexion/cerrar_sesion.php"><i class="fas fa-reply"></i>Cerrar Sesión</a>
				</div>
			</nav>
		EOT;
}

function template_header3($title) {
	echo <<<EOT
	<!DOCTYPE html>
	<html>
		<head>
			<meta charset="utf-8">
			<title>$title</title>
			<link href="../css/estilos.css" rel="stylesheet" type="text/css">
			<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
		</head>
		<body>
		<nav class="navtop">
			<div>
				<h1>Sistema de Gestión y Control de Asistencia</h1>				
				<a href="../conexion/cerrar_sesion.php"><i class="fas fa-reply"></i>Cerrar Sesión</a>
			</div>
		</nav>
	EOT;
}

function template_footer() {
echo <<<EOT
    </body>
</html>
EOT;
}
?>
