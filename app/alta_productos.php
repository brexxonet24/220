<?php include 'header.php'; 
require("conexion.php"); //Utiliza el archivo para la conexión a la base


// Función para guardar los datos en la tabla
function guardarDatos($codigo, $producto, $cantidad, $caracteristica, $grupo, $fecha)
{

        //$sql = "UPDATE Stock SET Producto='$producto', Cantidad='$cantidad', Caracteristica='$caracteristica', Grupo='$grupo' WHERE Codigo='$codigo'";
   
}

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los valores del formulario
    $codigo = $_POST["codigo"];
    $producto = $_POST["producto"];
    $cantidad = $_POST["cantidad"];
    $caracteristica = $_POST["caracteristica"];
    $grupo = $_POST["grupo"];
    $fecha = $_POST["fecha"];
    // Guardar los datos
    //guardarDatos($codigo, $producto, $cantidad, $caracteristica, $grupo, $fecha);
    $sql = "INSERT INTO Stock (Codigo, Producto, Cantidad, Caracteristica, Grupo, Fecha) VALUES ('$codigo', '$producto', '$cantidad', '$caracteristica', '$grupo', '$fecha')";
 
    if ($conn->query($sql) === TRUE) {
        echo "Datos guardados exitosamente";
    } else {
        echo "Error al guardar los datos: " . $conn->error;
    }

    $conn->close();
}
?>
<h2 class="text-center">Altas Productos</h2>
    <div class="container">    
        <form class="form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            Código: <input class="form-control" "text" name="codigo" required><br>
            Producto: <input class="form-control" "text" name="producto" required><br>
            Cantidad: <input class="form-control" "number" name="cantidad" required><br>
            Característica: <input class="form-control" "text" name="caracteristica" required><br>
            Grupo: <input class="form-control" ="text" name="grupo" required><br>
            Fecha: <input class="form-control" ="date" name="fecha" required><br>
            <div style="text-align: center;">
                <input class="btn btn-success" type="submit" value="Guardar">
            </div>
        </form>
    </div>
    
    <?php include 'footer.php'; 