<?php
session_start();
session_unset(); // Eliminar todas las variables de sesión
session_destroy(); // Destruir la sesión activa
header("Location: ./inicioSesion.php"); // Redirigir a la página de inicio de sesión
exit();

?>