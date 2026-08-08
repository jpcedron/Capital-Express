<?php

require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();

$sql = "SELECT
            id,
            nombre,
            cedula,
            telefono,
            direccion,
            estado_cliente
        FROM clientes
        ORDER BY id DESC";

$stmt = $conexion->prepare($sql);
$stmt->execute();
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Gestionar Clientes</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

    <h2>Capital Express</h2>

    <p>Gestionar Clientes</p>

    <div class="d-flex justify-content-between align-items-center mb-3">

        <a
        href="listado.php"
        class="btn btn-primary"
        >
        Volver al listado
        </a>

    </div>

    <table class="table table-bordered table-striped">

        <thead>

            <tr>

                <th>ID</th>

                <th>Nombre</th>

                <th>Cédula</th>

                <th>Teléfono</th>

                <th>Estado Cliente</th>

                <th>Acciones</th>

            </tr>

        </thead>

        <tbody>

        <?php foreach($clientes as $cliente): ?>

            <tr>

                <td>

                    <?= $cliente['id'] ?>

                </td>

                <td>

                    <?= $cliente['nombre'] ?>

                </td>

                <td>

                    <?= $cliente['cedula'] ?>

                </td>

                <td>

                    <?= $cliente['telefono'] ?>

                </td>

                <td>

                    <?php if(
                    $cliente['estado_cliente']
                    ==
                    'activo'
                    ): ?>

                        <span class="badge bg-success">

                        Activo

                        </span>

                    <?php else: ?>

                        <span class="badge bg-danger">

                        Inactivo

                        </span>

                    <?php endif; ?>

                </td>

                <td>

                    <?php if(
                    $cliente['estado_cliente']
                    ==
                    'activo'
                    ): ?>

                      <a
                        href="#"
                        class="btn btn-danger btn-sm"
                        onclick="confirmarEstado(<?= $cliente['id'] ?>,'inactivo')"
                        >
                        Desactivar
                        </a>

                    <?php else: ?>

                    <a
                    href="#"
                    class="btn btn-success btn-sm"
                    onclick="confirmarEstado(<?= $cliente['id'] ?>,'activo')"
                    >
                    Activar
                    </a>

                    <?php endif; ?>

                    <a
                    href="#"
                    class="btn btn-primary btn-sm"
                    onclick="eliminarCliente(<?= $cliente['id'] ?>)"
                    >
                    Eliminar
                    </a>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>

                 
<script src="/js/gestionar_cliente.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>