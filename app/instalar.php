<?php  
require("conexion.php"); //Utiliza el archivo para la conexión a la base

//Crear la tabla si no existe
$sql = "CREATE TABLE IF NOT EXISTS Turnos (
    id_Turno INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(50) NOT NULL,
    Email VARCHAR(50) NOT NULL,
    Cel VARCHAR(50) NOT NULL,
    Horario VARCHAR(50) NOT NULL)";

if ($conn->query($sql) === false) {
    echo "Error al crear la tabla: " . $conn->error;
}
// Cerrar la conexión
$conn->close();
?>
