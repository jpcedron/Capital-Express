<?php

require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();

/* Buscar todos los clientes registrados en préstamos */

$sqlPrestamos = "SELECT
                    nombre,
                    cedula,
                    telefono,
                    direccion
                 FROM prestamos";

$stmtPrestamos = $conexion->prepare($sqlPrestamos);
$stmtPrestamos->execute();

$prestamos = $stmtPrestamos->fetchAll(PDO::FETCH_ASSOC);

$nuevos = 0;

foreach ($prestamos as $prestamo) {

    // Verificar si el cliente ya existe
    $sqlExiste = "SELECT id
                  FROM clientes
                  WHERE cedula = ?";

    $stmtExiste = $conexion->prepare($sqlExiste);
    $stmtExiste->execute([$prestamo["cedula"]]);

    if (!$stmtExiste->fetch()) {

        $password = password_hash($prestamo["cedula"], PASSWORD_DEFAULT);

        $sqlInsertar = "INSERT INTO clientes
        (
            nombre,
            cedula,
            telefono,
            direccion,
            password
        )
        VALUES
        (
            ?, ?, ?, ?, ?
        )";

        $stmtInsertar = $conexion->prepare($sqlInsertar);

        $stmtInsertar->execute([
            $prestamo["nombre"],
            $prestamo["cedula"],
            $prestamo["telefono"],
            $prestamo["direccion"],
            $password
        ]);

        $nuevos++;
    }
}

echo "<h2>Sincronización terminada.</h2>";
echo "<p>Clientes agregados: <strong>$nuevos</strong></p>";