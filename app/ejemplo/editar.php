<?php
// Configuración de la conexión a la base de datos
$servername = 'localhost';
$username = 'c2560185_isft220';
$password = 'liTOludi67';
$dbname = 'c2560185_isft220';


// Obtener el ID del registro a editar
$id = $_GET['id'];

// Procesar la actualización del registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los nuevos valores del formulario
    $newNombre = $_POST['texto1'];
    $newEmail = $_POST['texto2'];

    // Crear la conexión
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("La conexión falló: " . $conn->connect_error);
    }

    $sql = "UPDATE usuarios SET nombre='$newNombre', email='$newEmail' WHERE id=$id";
            
    if ($conn->query($sql) === TRUE) {
        //echo "Registro actualizado correctamente";
        // Redirección a la página deseada después del procesamiento
        header("Location: listar.php");
        exit(); // Asegura que el código se detenga después de la redirección
    } else {
        echo "Error al actualizar el registro: " . $conn->error;
    }

    $conn->close();
}

// Obtener los datos del registro actual
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("La conexión falló: " . $conn->connect_error);
}

$sql = "SELECT id, nombre, email FROM usuarios WHERE id=$id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// Cerrar la conexión
$conn->close();
?>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
</head>

<div class="container">
    <h2>Editar Usuario</h2>
    <form class="form" action="editar.php?id=<?= $id ?>" method="POST">
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        Nombre:<input class="form-control" type="text" name="texto1" value="<?= $row['nombre'] ?>">
        Email:<input class="form-control" type="email" name="texto2" value="<?= $row['email'] ?>"><br/>
        <input type="submit" class="btn btn-info" value="Actualizar">
    </form>
</div>
