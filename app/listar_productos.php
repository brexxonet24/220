<?php include 'header.php'; 
require("conexion.php"); //Utiliza el archivo para la conexión a la base

// Consultar los datos
$sql = "SELECT * FROM Stock ORDER BY Codigo ASC";
$result = $conn->query($sql);


if ($result->num_rows > 0) {
    // Mostrar los datos en forma de tabla
    echo "<div class='container'>
    <table class='table'>
        <tr>
            <th>ID</th>
            <th>Cod Int</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Caracteristica</th>
            <th>Grupo</th>
            <th>Fecha</th>
        </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>".$row["id_Stock"]."</td>
                <td>".$row["Codigo"]."</td>
                <td>".$row["Producto"]."</td>
                <td>".$row["Cantidad"]."</td>
                <td>".$row["Caracteristica"]."</td>
                <td>".$row["Grupo"]."</td>
                <td>".$row["Fecha"]."</td>
                <td>
                    <a href='editar_productos.php?id=".$row["id_Stock"]."' class='btn btn-primary'>Editar</a>
                    <a href='eliminar_productos.php?id=".$row["id_Stock"]."' class='btn btn-danger'>Eliminar</a>
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