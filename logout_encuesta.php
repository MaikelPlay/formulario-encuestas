<?php
session_start(); // Iniciar sesión

// Eliminar todas las variables de sesión
session_unset();

// Destruir la sesión
session_destroy();

// Redirigir al login
header('Location: login_encuesta.php');
exit();
?>
