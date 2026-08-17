<?php
require("conexion.php");

// Obtener el ID del registro a eliminar
$id = $_GET['id'];

// Eliminar el registro de la base de datos
$sql = "DELETE FROM usuarios WHERE id=$id";
if ($conn->query($sql) === TRUE) {
    header("Location: listar.php");
    exit(); // Asegura que el código se detenga después de la redirección
} else {
    echo "Error al eliminar el registro: " . $conn->error;
}

// Cerrar la conexión
$conn->close();
?>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
</head>
