<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Capital Express - Nuevo Préstamo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="css/estilo_nuevo_prestamo.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4 mb-5">

<div class="card shadow">

<div class="card-header text-white" style="background: linear-gradient(135deg, #1a3560, #0d1b3a);">

<h2 class="mb-0">Capital Express</h2>
<small>Registro de Nuevo Préstamo</small>

</div>

<div class="card-body">

<form action="guardar.php" method="POST" id="formPrestamo">

<div class="row">

<!-- ========================= -->
<!-- DATOS CLIENTE -->
<!-- ========================= -->

<div class="col-12">

<h4 class="text-black mb-3">
<i class="bi bi-person-fill"></i> Datos del Cliente
</h4>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">
Cédula
</label>

<div class="input-group">

<input
type="text"
class="form-control"
name="cedula"
id="cedula"
required>

<button
type="button"
class="btn " style="background-color: #1a3560; color: white;"
id="buscarCliente">

🔍 Buscar

</button>

</div>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">
Estado
</label>

<input
type="text"
class="form-control"
id="estadoCliente"
value="Cliente nuevo"
readonly>

</div>

<div id="infoCliente" class="card border-0 shadow-sm mt-3 mb-4" style="display:none;">

    <div class="card-header text-white" id="colorEstado">

        <h5 class="mb-0">
            <i class="bi bi-person-fill"></i> Información del Cliente
        </h5>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-6">

                <p><strong>Nombre:</strong><br>
                <span id="txtNombre"></span></p>

                <p><strong>Teléfono:</strong><br>
                <span id="txtTelefono"></span></p>

                <p><strong>Dirección:</strong><br>
                <span id="txtDireccion"></span></p>

            </div>

            <div class="col-md-6">

                <p><strong>Estado:</strong><br>
                <span id="txtEstado"></span></p>

                <p><strong>Monto:</strong><br>
                <span id="txtMonto"></span></p>

                <p><strong>Abonado:</strong><br>
                <span id="txtAbonado"></span></p>

                <p><strong>Pendiente:</strong><br>
                <span id="txtPendiente"></span></p>

                <p><strong>Mora:</strong><br>
                <span id="txtMora"></span></p>

                <p><strong>Frecuencia:</strong><br>
                <span id="txtFrecuencia"></span></p>

                <p><strong>Fecha límite:</strong><br>
                <span id="txtFecha"></span></p>

            </div>

        </div>

    </div>

</div>

<div class="col-md-6 mb-3">

<label>Nombre</label>

<input
type="text"
class="form-control"
name="nombre"
id="nombre"
required>

</div>

<div class="col-md-6 mb-3">

<label>Teléfono</label>

<input
type="text"
class="form-control"
name="telefono"
id="telefono"
required>

</div>

<div class="col-md-6 mb-3">

<label>Dirección</label>

<input
type="text"
class="form-control"
name="direccion"
id="direccion">

</div>

<hr>

<!-- ========================= -->
<!-- DATOS PRESTAMO -->
<!-- ========================= -->

<div class="col-12">

<h4 class="text-black mb-3">
<i class="bi bi-coin"></i> Datos del Préstamo
</h4>

</div>

<div class="col-md-6 mb-3">

<label>Monto Prestado</label>

<input
type="number"
class="form-control"
name="monto"
required>

</div>

<div class="col-md-6 mb-3">

<label>Interés (%)</label>

<input
type="number"
class="form-control"
name="interes"
required>

</div>

<div class="col-md-6 mb-3">

<label>Cuotas</label>

<input
type="number"
class="form-control"
name="cuotas"
required>

</div>

<div class="col-md-6 mb-3">

<label>Frecuencia</label>

<select
name="frecuencia"
class="form-select"
required>

<option value="">Seleccione</option>
<option value="Semanal">Semanal</option>
<option value="Quincenal">Quincenal</option>

</select>

</div>

</div>

<hr>

<div class="text-end">

<button
type="submit"
class="btn btn-success"
id="btnGuardar">

Registrar préstamo

</button>

<a
href="listado.php"
class="btn btn-secondary">

Ver listado

</a>

</div>

</form>

</div>

</div>

</div>

<script src="js/nuevo_prestamo.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>