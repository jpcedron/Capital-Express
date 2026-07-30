<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Capital Express</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

    <div class="card shadow">

        <div class="card-header text-white" style="background: linear-gradient(135deg, #1a3560, #0d1b3a);">

            <h3>Capital Express</h3>
            <small>Finanzas con Confianza</small>

        </div>

        <div class="card-body">

            <form action="guardar.php" method="POST">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label>Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Cédula</label>
                        <input type="text" id="cedula" name="cedula" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Teléfono</label>
                        <input type="text" id="telefono" name="telefono" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Dirección</label>
                        <input type="text" id="direccion" name="direccion" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Monto Prestado</label>
                        <input type="number"  name="monto" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Interés (%)</label>
                        <input type="number" name="interes" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Cuotas</label>
                        <input type="number" name="cuotas" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Frecuencia de pago</label>
                        <select name="frecuencia" class="form-control" required>
                            <option value="">Seleccione</option>
                            <option value="Semanal">Semanal</option>
                            <option value="Quincenal">Quincenal</option>
                        </select>
                    </div>

                     

                </div>

                <button class="btn btn-success">
                    Registrar Préstamo
                </button>
                <button class="btn btn-secondary" type="button" onclick="window.location.href='listado.php'">
                    Listado
                </button>

            </form>

        </div>

    </div>

</div>

<script>

const cedula = document.getElementById("cedula");

cedula.addEventListener("blur", function(){

    if(this.value=="") return;

    fetch("buscar_cliente.php?cedula="+this.value)

    .then(res=>res.json())

    .then(data=>{

        if(!data){
            return;
        }

        if(data.estado=="Activo" || data.estado=="Mora"){

            Swal.fire({
                icon:"warning",
                title:"Cliente con préstamo activo",
                text:"Este cliente ya tiene un préstamo activo."
            });

            document.querySelector("button[type=submit]").disabled=true;

            return;

        }

        document.getElementById("nombre").value=data.nombre;
        document.getElementById("telefono").value=data.telefono;
        document.getElementById("direccion").value=data.direccion;

        Swal.fire({
            icon:"success",
            title:"Cliente encontrado",
            text:"Se cargaron automáticamente los datos del cliente."
        });

    });

});

</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11">

</script>

</body>
</html>