<?php

require_once "config/conexion.php";

$conexion =
(new Conexion())->conectar();

$id =
$_GET['id'];

$estado =
$_GET['estado'];

$sql = "

UPDATE prestamos

SET estado_cliente=?

WHERE id=?

";

$stmt =
$conexion->prepare($sql);

$stmt->execute([

$estado,

$id

]);

header(
"Location: gestionar_clientes.php"
);

exit;