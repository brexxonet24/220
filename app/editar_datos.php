<?php include 'header.php'; 
require("conexion.php"); //Utiliza el archivo para la conexión a la base

// Obtener el ID del registro a editar
$id = $_GET['id'];

// Procesar la actualización del registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los nuevos valores del formulario
    $newNombre = $_POST['nombre'];
    $newEmail = $_POST['email'];
    $newTel = $_POST['tel'];
    $newRubro = $_POST['rubro'];
    $newCategoria = $_POST['categoria'];
    $newCUIT = $_POST['cuit'];
    $newDireccion = $_POST['direccion'];
    $newLocalidad = $_POST['localidad'];
    $newProvincia = $_POST['provincia'];
    $newCP = $_POST['cp'];


    //$sql = "UPDATE Datos SET Nombre='$newNombre', Email='$newEmail', Tel='$newTel', Rubro='$newRubro' WHERE id=$id";
    $sql = "UPDATE Datos SET Nombre='$newNombre', Tel='$newTel', Email='$newEmail', Rubro='$newRubro', Categoria='$newCategoria', CUIT='$newCUIT', Direccion='$newDireccion', Localidad='$newLocalidad', Provincia='$newProvincia', CP='$newCP' WHERE id=$id";        
    if ($conn->query($sql) === TRUE) {
        
        // Redirección a la página deseada después del procesamiento
        header("Location: listar_datos.php");
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

$sql = "SELECT * FROM Datos WHERE id=$id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// Cerrar la conexión
$conn->close();
?>

<div class="container">
<h2 class="text-center">Editar Datos</h2>
<form class="form" method="post" action="listar_datos.php">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <label for="nombre">Nombre:</label>
                <input class="form-control" type="text" name="nombre" id="nombre" value="<?= $row['Nombre'] ?>" required>
                <br>
                <label for="tel">Tel / WhatsApp:</label>
                <input class="form-control" type="text" name="tel" id="tel" value="<?= $row['Tel'] ?>" required>
                <br>
                <label for="email">Email:</label>
                <input class="form-control" type="email" name="email" id="email" value="<?= $row['Email'] ?>" required>
                <br>
                <label for="rubro">Rubro:</label>
                <input class="form-control" name="rubro" id="rubro" value="<?= $row['Rubro'] ?>">
                <br>
                <label for="categoria">Categoría:</label>
                <input class="form-control" name="categoria" id="categoria" value="<?= $row['Categoria'] ?>">
                <br>
            </div>
            <div class="col-md-6">
                <label for="cuit">CUIT:</label>
                <input class="form-control" type="text" name="cuit" id="cuit" pattern="\d{2}-\d{8}-\d{1}" title="Ingrese un CUIT\CUIL válido con el formato XX-XXXXXXXX-X" required>
                <br>
                <label for="direccion">Dirección:</label>
                <input class="form-control" type="text" name="direccion" id="direccion" value="<?= $row['Direccion'] ?>" required>
                <br>
                <label for="localidad">Localidad:</label>
                <input class="form-control" type="text" name="localidad" id="localidad" value="<?= $row['Localidad'] ?>" required>
                <br>
                <label for="provincia">Provincias:</label>
                <input class="form-control" type="text" name="provincia" id="provincia" value="<?= $row['Provincia'] ?>" required>
                <br>
                <label for="cp">CP:</label>
                <input class="form-control" type="text" name="cp" id="cp" required>
                <br>
            </div>
        </div>
    </div>
    <div style="text-align: center;">
        <input class="btn btn-success" type="submit" value="Guardar">
    </div>
    <br><br>
</form>
</div>
<?php include 'footer.php'; ?>