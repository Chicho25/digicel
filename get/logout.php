<?php
	ob_start();
	session_start();
	include("include/config.php");
	include("include/defs.php");

$mensaje = "El Usuario: ".$_SESSION['USER_NAME']." ha salido del sistema ";
log_actividad(2, 7, $_SESSION['USER_ID'], $mensaje);

	session_destroy();
	$home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';
	header('Location: ' . $home_url);
?>
