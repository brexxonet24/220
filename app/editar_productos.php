<?php include 'header.php'; 
require("conexion.php"); //Utiliza el archivo para la conexión a la base

// Obtener el ID del registro a editar
$id = $_GET['id'];

// Procesar la actualización del registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los nuevos valores del formulario

    $newCodigo = $_POST["codigo"];
    $newProducto = $_POST["producto"];
    $newCantidad = $_POST["cantidad"];
    $newCaracteristica = $_POST["caracteristica"];
    $newGrupo = $_POST["grupo"];
    $newFecha = $_POST["fecha"];
    $newEmail = $_POST['Email'];
    $newTel = $_POST['Tel'];
    $newRubro = $_POST['Rubro'];

    
    $sql = "UPDATE Stock SET Producto='$newProducto', Cantidad='$newCantidad', Caracteristica='$newCaracteristica', Grupo='$newGrupo' WHERE id_Stock='$id'";
    if ($conn->query($sql) === TRUE) {        
        // Redirección a la página deseada después del procesamiento
        header("Location: listar_datos.php");
        exit(); // Asegura que el código se detenga después de la redirección
    } else {
        echo "Error al actualizar el registro: " . $conn->error;
    }

    $conn->close();
}

$sql = "SELECT * FROM Stock WHERE id_Stock=$id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// Cerrar la conexión
$conn->close();
?>

<div class="container">
<h2 class="text-center">Editar Productos</h2>
    <form class="form" action="editar_productos.php?id=<?= $id ?>" method="POST">
    <label for="codigo">Código:</label>
        <input class="form-control" type="text" name="codigo" id="codigo" value="<?= $row['Codigo'] ?>" required><br>

        <label for="producto">Producto:</label>
        <input class="form-control" type="text" name="producto" id="producto" value="<?= $row['Producto'] ?>" required><br>

        <label for="cantidad">Cantidad:</label>
        <input class="form-control" type="number" name="cantidad" id="cantidad" value="<?= $row['Cantidad'] ?>" required><br>

        <label for="caracteristica">Característica:</label>
        <input class="form-control" type="text" name="caracteristica" id="caracteristica" value="<?= $row['Caracteristica'] ?>" required><br>

        <label for="grupo">Grupo:</label>
        <input class="form-control" type="text" name="grupo" id="grupo" value="<?= $row['Grupo'] ?>" required><br>

        <label for="fecha">Fecha:</label>
        <input class="form-control" type="date" name="fecha" id="fecha" value="<?= $row['Fecha'] ?>" required><br>

        <div style="text-align: center;">
            <input class="btn btn-success" type="submit" value="Guardar">
        </div>
    </form>
</div>
<?php include 'footer.php';?> 