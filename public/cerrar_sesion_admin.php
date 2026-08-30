<?php

session_start();
// Eliminar todas las variables de sesión
$_SESSION = [];
// Destruir la sesión
session_destroy();
// Redireccionar al login administrativo
header("Location: login_admin.php");
exit;