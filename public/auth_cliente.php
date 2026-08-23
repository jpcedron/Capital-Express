<?php

session_start();

// Verificar si el cliente inició sesión
if (!isset($_SESSION["cliente_id"])) {

    header("Location: login.php");
    exit;
}