<?php

session_start();

// Verificar que exista una sesión administrativa válida
if (
    !isset($_SESSION["admin_id"]) ||
    empty($_SESSION["admin_id"])
) {
    header("Location: login_admin.php");
    exit;
}