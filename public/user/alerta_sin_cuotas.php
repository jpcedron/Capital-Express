<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Capital Express</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>

Swal.fire({
    icon: "warning",
    title: "Sin plan de cuotas",
    html: `
        Actualmente no tienes un <b>préstamo activo</b>.
        <br><br>
        Por esta razón no existe un plan de cuotas
        para consultar en este momento.
    `,
    confirmButtonText: "Regresar al Panel",
    confirmButtonColor: "#1a3560",
    allowOutsideClick: false
}).then(() => {

    window.location.href = "panel_de_usuario.php";

});

</script>

</body>
</html>