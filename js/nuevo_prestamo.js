const btnBuscar = document.getElementById("buscarCliente");
const btnGuardar = document.getElementById("btnGuardar");

btnBuscar.addEventListener("click", buscarCliente);

// También buscar al presionar Enter en la cédula
document.getElementById("cedula").addEventListener("keypress", function(e){

    if(e.key === "Enter"){
        e.preventDefault();
        buscarCliente();
    }

});

// También buscar cuando salga del campo
document.getElementById("cedula").addEventListener("blur", function(){

    if(this.value.trim() !== ""){
        buscarCliente();
    }

});

function buscarCliente(){

    const cedula = document.getElementById("cedula").value.trim();

    if(cedula === ""){

        Swal.fire({
            icon:"warning",
            title:"Atención",
            text:"Ingrese la cédula del cliente."
        });

        return;
    }

    fetch("buscar_cliente.php?cedula=" + encodeURIComponent(cedula))

    .then(response => response.json())

    .then(data => {

        limpiarFormulario();

        if(data.error){

            Swal.fire({
                icon:"error",
                title:"Error",
                text:data.mensaje
            });

            return;

        }

        if(data == null){

            document.getElementById("estadoCliente").value="Cliente nuevo";

            document.getElementById("infoCliente").style.display="none";

            btnGuardar.disabled=false;

            Swal.fire({
                icon:"info",
                title:"Cliente nuevo",
                text:"No existe un cliente registrado con esa cédula."
            });

            return;

        }

        //==========================
        // LLENAR DATOS
        //==========================

        document.getElementById("nombre").value=data.nombre;
        document.getElementById("telefono").value=data.telefono;
        document.getElementById("direccion").value=data.direccion;

        document.getElementById("infoCliente").style.display="block";

        document.getElementById("txtNombre").innerHTML=data.nombre;
        document.getElementById("txtTelefono").innerHTML=data.telefono;
        document.getElementById("txtDireccion").innerHTML=data.direccion;

        document.getElementById("txtEstado").innerHTML=data.estado;

        document.getElementById("txtMonto").innerHTML=
        "$"+Number(data.monto).toLocaleString("es-CO");

        document.getElementById("txtAbonado").innerHTML=
        "$"+Number(data.abonado).toLocaleString("es-CO");

        document.getElementById("txtPendiente").innerHTML=
        "$"+Number(data.pendiente).toLocaleString("es-CO");

        document.getElementById("txtMora").innerHTML=
        "$"+Number(data.mora).toLocaleString("es-CO");

        document.getElementById("txtFrecuencia").innerHTML=data.frecuencia;

        document.getElementById("txtFecha").innerHTML=data.fecha_limite;

        //==========================
        // COLOR DE LA TARJETA
        //==========================

        const encabezado=document.getElementById("colorEstado");

        encabezado.className="card-header text-white";

        if(data.estado==="Pagado"){

            encabezado.classList.add("bg-success");

            document.getElementById("estadoCliente").value="Cliente encontrado";

            btnGuardar.disabled=false;

            Swal.fire({
                icon:"success",
                title:"Cliente encontrado",
                text:"El cliente puede recibir un nuevo préstamo."
            });

        }

        else if(data.estado==="Activo"){

            encabezado.classList.add("bg-danger");

            document.getElementById("estadoCliente").value="Préstamo activo";

            btnGuardar.disabled=true;

            Swal.fire({
                icon:"error",
                title:"Préstamo activo",
                html:`
                    <b>Este cliente ya tiene un préstamo activo.</b><br><br>
                    Pendiente:
                    <b>$${Number(data.pendiente).toLocaleString("es-CO")}</b>
                `
            });

        }

        else if(data.estado==="Mora"){

            encabezado.className="card-header bg-warning text-dark";

            document.getElementById("estadoCliente").value="Cliente en mora";

            btnGuardar.disabled=true;

            Swal.fire({
                icon:"warning",
                title:"Cliente en mora",
                html:`
                    <b>Este cliente tiene un préstamo en mora.</b><br><br>

                    Mora acumulada:
                    <b>$${Number(data.mora).toLocaleString("es-CO")}</b>
                `
            });

        }

    })

    .catch(error=>{

        console.error(error);

        Swal.fire({
            icon:"error",
            title:"Error",
            text:"No fue posible consultar el cliente."
        });

    });

}

function limpiarFormulario(){

    document.getElementById("nombre").value="";
    document.getElementById("telefono").value="";
    document.getElementById("direccion").value="";

    document.getElementById("txtNombre").innerHTML="";
    document.getElementById("txtTelefono").innerHTML="";
    document.getElementById("txtDireccion").innerHTML="";
    document.getElementById("txtEstado").innerHTML="";
    document.getElementById("txtMonto").innerHTML="";
    document.getElementById("txtAbonado").innerHTML="";
    document.getElementById("txtPendiente").innerHTML="";
    document.getElementById("txtMora").innerHTML="";
    document.getElementById("txtFrecuencia").innerHTML="";
    document.getElementById("txtFecha").innerHTML="";

}