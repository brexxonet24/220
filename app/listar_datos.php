<?php 
//require("loggedin.php");
include 'header.php'; 
require("conexion.php"); //Utiliza el archivo para la conexión a la base
// Obtener los datos del formulario
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$Tel  =$_POST['tel'];
$rubro = $_POST['rubro'];
$Cuit=$_POST['cuit'];
$Direccion=$_POST['direccion'];
$Localidad=$_POST['localidad'];
$Provincia=$_POST['provincia'];
$CP=$_POST['cp'];


if (!empty($nombre) && !empty($email) && !empty($rubro)) { //"EmptyLos campos no pueden estar vacíos.";
    // Sentencia SQL para insertar los datos
    $sql = "INSERT INTO Datos (Nombre, Email, Tel, Rubro, Cuit, Direccion, Localidad, Provincia, CP) VALUES ('$nombre', '$email', '$Tel', '$rubro','$Cuit', '$Direccion', '$Localidad', '$Provincia', '$CP')";
    // Resto del código para ejecutar la consulta y realizar las operaciones necesarias
    if ($conn->query($sql) === TRUE) {
        // Redirección a la página deseada después del procesamiento
        header("Location: listar.php");
    } else {
        echo "Error al insertar el registro: " . $conn->error;
    }
} 


// Consultar los datos
$sql = "SELECT * FROM Datos ORDER BY Nombre ASC";
$result = $conn->query($sql);


if ($result->num_rows > 0) {
    // Mostrar los datos en forma de tabla
    echo "<div class='container'>
    <table class='table'>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Tel</th>
            <th>Email</th>
            <th>Rubro</th>
            <th>Cuit</th>
            <th>Dirección</th>
            <th><a href='alta_datos.php' class='btn btn-success' >Agregar</a></th>
        </tr>";

while ($row = $result->fetch_assoc()) {
    $concatenacion = $row["Direccion"] . ", " . $row["Localidad"]. ", " . $row["Provincia"];
    echo "<tr>
            <td>".$row["ID"]."</td>
            <th>".$row["Nombre"]."</th>
            <td>".$row["Tel"]."</td>
            <td>".$row["Email"]."</td>
            <td>".$row["Rubro"]."</td>
            <td>".$row["Cuit"]."</td>
            <td>$concatenacion</td>
            <td>
                <a href='editar_datos.php?id=".$row["ID"]."' class='btn btn-primary'>Editar</a>
                <a href='eliminar_datos.php?id=".$row["ID"]."' class='btn btn-danger'>Eliminar</a>
            </td>
        </tr>";
}
       //cargarDireccion($concatenacion)
echo "</table>
</div>";
    
} else {
    echo "No se encontraron resultados.";
}

// Cerrar la conexión
$conn->close();
include 'footer.php'; ?>
