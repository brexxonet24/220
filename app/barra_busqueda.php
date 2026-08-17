

<div class="container mt-5">
    <form method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Ingrese el texto a buscar" name="buscar">
            <select class="form-control" name="tabla">
                <option value="usuarios">Usuarios</option>
                <option value="productos">Productos</option>
                <option value="Turnos">Turnos</option>
                <!-- Agrega aquí más opciones para otras tablas -->
            </select>
            <select class="form-control" name="campo">
                <option value="nombre">Nombre</option>
                <option value="email">Email</option>
                <option value="productos">Productos</option>
                <!-- Agrega aquí más opciones para otros campos -->
            </select>
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">Buscar</button>
            </div>
        </div>
    </form>

    <?php
    require("conexion.php");

    // Obtener el valor de búsqueda, tipo de tabla y campo
    $buscar = $_GET['buscar'];
    $tabla = $_GET['tabla'];
    $campo = $_GET['campo'];

    // Consultar los datos en la tabla seleccionada y el campo especificado
    $sql = "SELECT * FROM $tabla WHERE $campo LIKE '%$buscar%' ORDER BY $campo ASC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Mostrar los datos en forma de tabla
        echo "<div class='table-responsive'> 
        <h3>Buscar en Tablas</h3>
            <table class='table'>
                <tr>
                    <th>ID</th>
                    <th>".$campo."</th>
                    <th>Acciones</th>
                </tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["id"]."</td>
                    <td>".$row["nombre"]."</td>
                    <td>
                        <a href='editar_usuarios.php?id=".$row["id"]."' class='btn btn-primary'>Editar</a>
                        <a href='eliminar_usuarios.php?id=".$row["id"]."' class='btn btn-danger'>Eliminar</a>
                    </td>
                </tr>";
        }

        echo "</table>
        </div>";
        echo "</div>";
        include 'footer.php';
        exit();
    } else {
       // echo "No se encontraron resultados.";
    }

    // Cerrar la conexión
    $conn->close();
    ?>

</div>
