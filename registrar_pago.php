<?php

require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();

$id = $_GET['id'];

$sql = "SELECT * FROM prestamos WHERE id=?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$id]);

$prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrar Pago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<div class="container mt-5">

    <div class="card shadow">
        <div class="card-header  text-white" style="background: linear-gradient(135deg, #1a3560, #0d1b3a); ">
            <h2>Registrar Pago</h2>
        </div>

        <div class="card-body">
            <p><strong>Cliente:</strong> <?= htmlspecialchars($prestamo['nombre']) ?></p>
            <p><strong>Total:</strong> $<?= number_format($prestamo['total_pagar']) ?></p>
            <p><strong>Abonado:</strong> $<?= number_format($prestamo['abonado']) ?></p>
            <p><strong>Pendiente:</strong> $<?= number_format($prestamo['pendiente']) ?></p>

            <form action="guardar_pago.php" method="POST">
                <input type="hidden" name="prestamo_id" value="<?= $prestamo['id'] ?>">

                <label class="form-label">Valor del pago</label>
                <input 
                    type="number" 
                    name="valor_pago" 
                    class="form-control" 
                    min="1" 
                    max="<?= $prestamo['pendiente'] ?>" 
                    step="0.01" 
                    required>
                <br>

                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalConfirmacion">
                    Guardar Pago
                </button>

                <button class="btn btn-secondary" type="button" onclick="window.location.href='listado.php'">
                    Listado
                </button>

                <!-- MODAL DE BOOTSTRAP -->
                <div class="modal fade" id="modalConfirmacion" tabindex="-1" aria-labelledby="modalConfirmacionLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <!-- TÍTULO CON EL ICONO DEL CHULITO -->
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title" id="modalConfirmacionLabel">
                                    <i class="bi bi-check-circle-fill me-2"></i> Confirmar Transacción
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                ¿Estás seguro de que deseas registrar este pago para el cliente <strong><?= htmlspecialchars($prestamo['nombre']) ?></strong>?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Sí, confirmar pago</button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>