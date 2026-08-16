const btnBuscar = document.getElementById("buscarCliente");
const btnGuardar = document.getElementById("btnGuardar");

btnBuscar.addEventListener("click", buscarCliente);


// =========================================================
// BUSCAR CON ENTER
// =========================================================

document.getElementById("cedula").addEventListener("keypress", function(e) {

    if (e.key === "Enter") {

        e.preventDefault();

        buscarCliente();
    }

});


// =========================================================
// BUSCAR AL SALIR DEL CAMPO
// =========================================================

document.getElementById("cedula").addEventListener("blur", function() {

    if (this.value.trim() !== "") {

        buscarCliente();
    }

});


// =========================================================
// FUNCIÓN BUSCAR CLIENTE
// =========================================================

function buscarCliente() {

    const cedula = document
        .getElementById("cedula")
        .value
        .trim();


    // =====================================================
    // VALIDAR CÉDULA
    // =====================================================

    if (cedula === "") {

        Swal.fire({

            icon: "warning",

            title: "Atención",

            text: "Ingrese la cédula del cliente."

        });

        return;
    }


    // =====================================================
    // CONSULTAR SERVIDOR
    // =====================================================

    fetch(
        "buscar_cliente.php?cedula=" +
        encodeURIComponent(cedula)
    )

    .then(response => response.json())

    .then(data => {


        // =================================================
        // LIMPIAR INFORMACIÓN ANTERIOR
        // =================================================

        limpiarFormulario();


        // =================================================
        // ERROR DEL SERVIDOR
        // =================================================

        if (data && data.error) {

            Swal.fire({

                icon: "error",

                title: "Error",

                text: data.mensaje

            });

            return;
        }


        // =================================================
        // CLIENTE NUEVO
        // =================================================

        if (data == null) {

            document.getElementById(
                "estadoCliente"
            ).value = "Cliente nuevo";


            document.getElementById(
                "infoCliente"
            ).style.display = "none";


            btnGuardar.disabled = false;


            Swal.fire({

                icon: "info",

                title: "Cliente nuevo",

                text: "No existe un cliente registrado con esa cédula."

            });

            return;
        }


        // =================================================
        // LLENAR DATOS DEL CLIENTE
        // =================================================

        document.getElementById("nombre").value =
            data.nombre || "";

        document.getElementById("telefono").value =
            data.telefono || "";

        document.getElementById("direccion").value =
            data.direccion || "";


        document.getElementById(
            "infoCliente"
        ).style.display = "block";


        document.getElementById("txtNombre").innerHTML =
            data.nombre || "";

        document.getElementById("txtTelefono").innerHTML =
            data.telefono || "";

        document.getElementById("txtDireccion").innerHTML =
            data.direccion || "";

        document.getElementById("txtEstado").innerHTML =
            data.estado || "Sin préstamo";

        document.getElementById("txtMonto").innerHTML =
            data.monto
                ? "$" + Number(data.monto).toLocaleString("es-CO")
                : "$0";

        document.getElementById("txtAbonado").innerHTML =
            data.abonado
                ? "$" + Number(data.abonado).toLocaleString("es-CO")
                : "$0";

        document.getElementById("txtPendiente").innerHTML =
            data.pendiente
                ? "$" + Number(data.pendiente).toLocaleString("es-CO")
                : "$0";

        document.getElementById("txtMora").innerHTML =
            data.mora
                ? "$" + Number(data.mora).toLocaleString("es-CO")
                : "$0";

        document.getElementById("txtFrecuencia").innerHTML =
            data.frecuencia || "Sin préstamo";

        document.getElementById("txtFecha").innerHTML =
            data.fecha_limite || "Sin préstamo";


        // =================================================
        // REFERENCIA AL ENCABEZADO DE ESTADO
        // =================================================

        const encabezado =
            document.getElementById("colorEstado");


        encabezado.className =
            "card-header text-white";


        // =================================================
        // 1. CLIENTE INACTIVO
        // =================================================

        if (
            data.estado_cliente &&
            data.estado_cliente.toLowerCase() === "inactivo"
        ) {

            encabezado.classList.add("bg-secondary");


            document.getElementById(
                "estadoCliente"
            ).value = "Cliente inactivo";


            btnGuardar.disabled = true;


            Swal.fire({

                icon: "warning",

                title: "Cliente inactivo",

                text:
                    "Este cliente está inactivo y no puede recibir un nuevo préstamo."

            });


            return;
        }


        // =================================================
        // 2. CLIENTE ACTIVO SIN PRÉSTAMO
        // =================================================

        if (!data.prestamo_id) {

            encabezado.classList.add("bg-success");


            document.getElementById(
                "estadoCliente"
            ).value = "Cliente activo";


            btnGuardar.disabled = false;


            Swal.fire({

                icon: "success",

                title: "Cliente encontrado",

                text:
                    "El cliente está activo y puede recibir un nuevo préstamo."

            });


            return;
        }


        // =================================================
        // 3. CLIENTE ACTIVO + PRÉSTAMO PAGADO
        // =================================================

        if (data.estado === "Pagado") {

            encabezado.classList.add("bg-success");


            document.getElementById(
                "estadoCliente"
            ).value = "Cliente encontrado";


            btnGuardar.disabled = false;


            Swal.fire({

                icon: "success",

                title: "Cliente disponible",

                text:
                    "El último préstamo fue pagado. El cliente puede recibir un nuevo préstamo."

            });


            return;
        }


        // =================================================
        // 4. CLIENTE ACTIVO + PRÉSTAMO ACTIVO
        // =================================================

        if (data.estado === "Activo") {

            encabezado.classList.add("bg-danger");


            document.getElementById(
                "estadoCliente"
            ).value = "Préstamo activo";


            btnGuardar.disabled = true;


            Swal.fire({

                icon: "error",

                title: "Préstamo activo",

                html: `
                    <b>Este cliente ya tiene un préstamo activo.</b>
                    <br><br>
                    Pendiente:
                    <b>
                        $${Number(data.pendiente)
                            .toLocaleString("es-CO")}
                    </b>
                `

            });


            return;
        }


        // =================================================
        // 5. CLIENTE ACTIVO + PRÉSTAMO EN MORA
        // =================================================

        if (data.estado === "Mora") {

            encabezado.className =
                "card-header bg-warning text-dark";


            document.getElementById(
                "estadoCliente"
            ).value = "Cliente en mora";


            btnGuardar.disabled = true;


            Swal.fire({

                icon: "warning",

                title: "Cliente en mora",

                html: `
                    <b>Este cliente tiene un préstamo en mora.</b>
                    <br><br>
                    Mora acumulada:
                    <b>
                        $${Number(data.mora)
                            .toLocaleString("es-CO")}
                    </b>
                `

            });


            return;
        }

    })

    .catch(error => {

        console.error(error);


        Swal.fire({

            icon: "error",

            title: "Error",

            text:
                "No fue posible consultar el cliente."

        });

    });

}


// =========================================================
// LIMPIAR FORMULARIO
// =========================================================

function limpiarFormulario() {

    document.getElementById("nombre").value = "";
    document.getElementById("telefono").value = "";
    document.getElementById("direccion").value = "";
    document.getElementById("txtNombre").innerHTML = "";
    document.getElementById("txtTelefono").innerHTML = "";
    document.getElementById("txtDireccion").innerHTML = "";
    document.getElementById("txtEstado").innerHTML = "";
    document.getElementById("txtMonto").innerHTML = "";
    document.getElementById("txtAbonado").innerHTML = "";
    document.getElementById("txtPendiente").innerHTML = "";
    document.getElementById("txtMora").innerHTML = "";
    document.getElementById("txtFrecuencia").innerHTML = "";
    document.getElementById("txtFecha").innerHTML = "";

}