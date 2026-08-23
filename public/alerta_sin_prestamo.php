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
    icon: "info",
    title: "Capital Express",
    html: `
        Actualmente no tienes una <b>cartilla de préstamo</b> disponible.
        <br><br>
        Cuando se registre un préstamo activo,
        aquí podrás consultar y descargar tu cartilla de pagos.
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