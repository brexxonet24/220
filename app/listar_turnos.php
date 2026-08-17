<?php include 'header.php'; 
require("conexion.php"); //Utiliza el archivo para la conexión a la base

//Crear la tabla si no existe


// Obtener los datos del formulario
$Nombre = $_POST['nombre'];
$Dia = $_POST['dia'];
$Hora = $_POST['hora'];

if (!empty($Nombre) && !empty($Dia) && !empty($Hora)) { //"EmptyLos campos no pueden estar vacíos.";
    // Sentencia SQL para insertar los datos
    $sql = "INSERT INTO Turnos (Nombre, Dia,Hora) VALUES ('$Nombre', '$Dia', '$Hora')";
    // Resto del código para ejecutar la consulta y realizar las operaciones necesarias
    if ($conn->query($sql) === TRUE) {
        // Redirección a la página deseada después del procesamiento
        header("Location: listar.php");
    } else {
        echo "Error al insertar el registro: " . $conn->error;
    }
} 


// Consultar los datos
$sql = "SELECT * FROM Turnos ORDER BY Dia ASC";
$result = $conn->query($sql);


if ($result->num_rows > 0) {
    // Mostrar los datos en forma de tabla
    echo "<div class='container'>
    <table class='table'>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Dia</th>
            <th>Hora</th>
        </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>".$row["Id"]."</td>
            <td>".$row["Nombre"]."</td>
            <td>".$row["Dia"]."</td>
            <td>".$row["Hora"]."</td>
            <td>
                <a href='editar_turnos.php?id=".$row["id"]."' class='btn btn-primary'>Editar</a>
                <a href='eliminar_turnos.php?id=".$row["id"]."' class='btn btn-danger'>Eliminar</a>
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
