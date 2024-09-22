<?php
session_start();  // Inicia la sesión
session_destroy();  // Destruye todas las sesiones activas
header('Location: login.html');  // Redirige al login
exit();  // Termina el script
?>
