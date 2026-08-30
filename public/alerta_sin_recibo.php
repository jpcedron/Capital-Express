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
    title: "Sin recibos disponibles",
    html: `
        Aún no tienes <b>recibos de pago</b> disponibles.
        <br><br>
        Cuando se registre un pago en tu préstamo,
        aquí podrás consultar y descargar tus comprobantes.
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