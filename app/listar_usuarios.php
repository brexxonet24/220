<?php include 'header.php'; 
require("conexion.php"); //Utiliza el archivo para la conexión a la base

//Crear la tabla si no existe


// Obtener los datos del formulario
$texto1 = $_POST['texto1'];
$texto2 = $_POST['texto2'];
$texto3 = $_POST['texto3'];

if (!empty($texto1) && !empty($texto2) && !empty($texto3)) { //"EmptyLos campos no pueden estar vacíos.";
    // Sentencia SQL para insertar los datos
    $sql = "INSERT INTO usuarios (nombre, email, pass) VALUES ('$texto1', '$texto2', '$texto3')";
    // Resto del código para ejecutar la consulta y realizar las operaciones necesarias
    if ($conn->query($sql) === TRUE) {
        // Redirección a la página deseada después del procesamiento
        header("Location: listar.php");
    } else {
        echo "Error al insertar el registro: " . $conn->error;
    }
} 


// Consultar los datos
$sql = "SELECT * FROM usuarios ORDER BY nombre ASC";
$result = $conn->query($sql);


if ($result->num_rows > 0) {
    // Mostrar los datos en forma de tabla
    echo "<div class='container'>
    <table class='table'>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Password</th>
            <th>Acciones</th>
        </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>".$row["id"]."</td>
            <td>".$row["nombre"]."</td>
            <td>".$row["email"]."</td>
            <td>".$row["pass"]."</td>
            <td>
                <a href='editar_usuarios.php?id=".$row["id"]."' class='btn btn-primary'>Editar</a>
                <a href='eliminar_usuarios.php?id=".$row["id"]."' class='btn btn-danger'>Eliminar</a>
            </td>
        </tr>";
}

echo "</table>
</div>";
    
} else {
    echo "No se encontraron resultados.";
}

// Cerrar la conexión
$conn->close();

?>
<?php include 'footer.php'; ?>
