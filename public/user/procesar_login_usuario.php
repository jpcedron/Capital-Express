<?php

session_start();

require_once "../config/conexion.php";

$conexion = (new Conexion())->conectar();

// Verificar que el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

// Verificar que los campos existan
if (!isset($_POST["cedula"], $_POST["password"])) {
    $_SESSION["error_login"] = "Debes completar todos los campos.";
    header("Location: login.php");
    exit;
}

// Recibir datos
$cedula = trim($_POST["cedula"]);
$password = $_POST["password"];

// Verificar que no estén vacíos
if ($cedula === "" || $password === "") {
    $_SESSION["error_login"] = "Debes completar todos los campos.";
    header("Location: login.php");
    exit;
}

// Buscar el cliente por cédula
$sql = "SELECT * FROM clientes WHERE cedula = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$cedula]);

$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si existe el cliente
if (!$cliente) {
    $_SESSION["error_login"] = "La cédula ingresada no se encuentra registrada.";
    header("Location: login.php");
    exit;
}

// Verificar la contraseña
if (!password_verify($password, $cliente["password"])) {
    $_SESSION["error_login"] = "La contraseña ingresada es incorrecta.";
    header("Location: login.php");
    exit;
}

// Regenerar el identificador de sesión después de autenticar
session_regenerate_id(true);

// Crear variables de sesión
$_SESSION["cliente_id"] = $cliente["id"];
$_SESSION["cliente_nombre"] = $cliente["nombre"];
$_SESSION["cliente_cedula"] = $cliente["cedula"];

// Redireccionar al panel del cliente
header("Location: panel_de_usuario.php");
exit;