function confirmarEstado(id, estado){

    let titulo = "";
    let texto = "";
    let icono = "";
    let color = "";

    if(estado === "inactivo"){

        titulo = "¿Desactivar cliente?";
        texto = "El cliente dejará de estar activo, pero conservará todo su historial de préstamos.";
        icono = "warning";
        color = "#dc3545";

    }else{

        titulo = "¿Activar cliente?";
        texto = "El cliente volverá a estar disponible para registrar nuevos préstamos.";
        icono = "question";
        color = "#198754";

    }

    Swal.fire({

        title: titulo,
        text: texto,
        icon: icono,

        showCancelButton: true,

        confirmButtonColor: color,
        cancelButtonColor: "#6c757d",

        confirmButtonText: "Sí",
        cancelButtonText: "Cancelar",

        reverseButtons: true

    }).then((result)=>{

        if(result.isConfirmed){

            window.location.href =
            "cambiar_estado_clientes.php?id="
            + id +
            "&estado=" +
            estado;

        }

    });

}





/* Eliminar cliente */

function eliminarCliente(id){

    Swal.fire({

        title:"¿Eliminar cliente?",
        text:"Esta acción eliminará completamente al cliente.",
        icon:"warning",

        showCancelButton:true,

        confirmButtonColor:"#dc3545",
        cancelButtonColor:"#6c757d",

        confirmButtonText:"Continuar",
        cancelButtonText:"Cancelar"

    }).then((resultado)=>{

        if(resultado.isConfirmed){

            Swal.fire({

                title:"Última confirmación",

                html:`
                <b>También se eliminarán:</b>

                <br><br>

                • Todos los préstamos

                <br>

                • Todos los pagos

                <br>

                • Todo el historial

                <br><br>

                <span style="color:red">
                Esta acción NO se puede deshacer.
                </span>
                `,

                icon:"error",

                showCancelButton:true,

                confirmButtonColor:"#dc3545",

                cancelButtonColor:"#6c757d",

                confirmButtonText:"Sí, eliminar todo",

                cancelButtonText:"Cancelar"

            }).then((final)=>{

                if(final.isConfirmed){

                    window.location =
                    "eliminar_cliente.php?id="+id;

                }

            });

        }

    });

}
