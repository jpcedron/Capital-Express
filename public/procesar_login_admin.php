<?php

session_start();

require_once "../config/conexion.php";

$conexion = (new Conexion())->conectar();


// Verificar que el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: login_admin.php");
    exit;
}


// Recibir datos
$usuario = trim($_POST["usuario"] ?? "");
$password = $_POST["password"] ?? "";


// Validar campos
if ($usuario === "" || $password === "") {

    $_SESSION["error_login_admin"] = "Debes ingresar el usuario y la contraseña.";

    header("Location: login_admin.php");
    exit;
}


// Buscar administrador
$sql = "SELECT *
        FROM administradores
        WHERE usuario = ?
        LIMIT 1";

$stmt = $conexion->prepare($sql);
$stmt->execute([$usuario]);

$admin = $stmt->fetch(PDO::FETCH_ASSOC);


// Verificar que exista
if (!$admin) {

    $_SESSION["error_login_admin"] = "El usuario o la contraseña son incorrectos.";

    header("Location: login_admin.php");
    exit;
}


// Verificar estado
if ($admin["estado"] !== "Activo") {

    $_SESSION["error_login_admin"] = "Este usuario administrador se encuentra inactivo.";

    header("Location: login_admin.php");
    exit;
}


// Verificar contraseña
if (!password_verify($password, $admin["password"])) {

    $_SESSION["error_login_admin"] = "El usuario o la contraseña son incorrectos.";

    header("Location: login_admin.php");
    exit;
}


// Regenerar ID de sesión por seguridad
session_regenerate_id(true);


// Crear sesión administrativa
$_SESSION["admin_id"] = $admin["id"];
$_SESSION["admin_usuario"] = $admin["usuario"];
$_SESSION["admin_nombre"] = $admin["nombre"];


// Redireccionar al Dashboard
header("Location: dashboard.php");
exit;