<?php
require("conexion.php"); //Utiliza el archivo para la conexión a la base

// Obtener el ID del registro a eliminar
$id = $_GET['id'];

// Eliminar el registro de la base de datos
$sql = "DELETE FROM Datos WHERE id=$id";
if ($conn->query($sql) === TRUE) {
    header("Location: listar_datos.php");
    exit(); // Asegura que el código se detenga después de la redirección
} else {
    echo "Error al eliminar el registro: " . $conn->error;
}

// Cerrar la conexión
$conn->close();
?>
