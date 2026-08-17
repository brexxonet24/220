<?php
// Configuración de la conexión a la base de datos

$servername = 'localhost';
$username = 'c2560185_isft220';
$password = 'liTOludi67';
$dbname = 'c2560185_isft220';

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("La conexión falló: " . $conn->connect_error);
}
?>