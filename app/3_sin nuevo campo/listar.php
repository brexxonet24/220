<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
</head>
<?php
require("conexion.php"); //Utiliza el archivo para la conexión a la base

/*Crear la tabla si no existe
$sql = "CREATE TABLE IF NOT EXISTS usuarios (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
    pass VARCHAR(50) NOT NULL,
)";
*/
$sql = "ALTER TABLE usuarios ADD COLUMN pass VARCHAR(50) NOT NULL AFTER email";
if ($conn->query($sql) === false) {
    echo "Error al crear la tabla: " . $conn->error;
}

// Obtener los datos del formulario
$texto1 = $_POST['texto1'];
$texto2 = $_POST['texto2'];

if (!empty($texto1) && !empty($texto2)) { //"EmptyLos campos no pueden estar vacíos.";
    // Sentencia SQL para insertar los datos
    $sql = "INSERT INTO usuarios (nombre, email) VALUES ('$texto1', '$texto2')";
    // Resto del código para ejecutar la consulta y realizar las operaciones necesarias
    if ($conn->query($sql) === TRUE) {
        echo "Registro insertado correctamente";
    } else {
        echo "Error al insertar el registro: " . $conn->error;
    }
} 


// Consultar los datos
$sql = "SELECT id, nombre, email FROM usuarios";
$result = $conn->query($sql);

    
if ($result->num_rows > 0) {
    // Mostrar los datos en forma de tabla
    echo "<div class='container'>
    <table class='table'>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Acciones</th>
        </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>".$row["id"]."</td>
            <td>".$row["nombre"]."</td>
            <td>".$row["email"]."</td>
            <td>
                <a href='editar.php?id=".$row["id"]."' class='btn btn-primary'>Editar</a>
                <a href='eliminar.php?id=".$row["id"]."' class='btn btn-danger'>Eliminar</a>
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
/*La diferencia entre los métodos HTTP POST y GET radica principalmente en la forma en que los datos se envían desde el cliente al servidor y en cómo los servidores procesan y responden a esas solicitudes.

El método GET se utiliza para solicitar información del servidor, donde los datos se envían en la URL como parámetros de cadena de consulta. En otras palabras, los datos del formulario se agregan al final de la URL después de un signo de interrogación (?), y se separan por el símbolo ampersand (&). Por ejemplo, una solicitud GET para un formulario de inicio de sesión podría verse así:

http://example.com/login.php?username=johndoe&password=secret
En este caso, el nombre de usuario y la contraseña se pasan como parámetros de la cadena de consulta a través de la URL. La solicitud GET es visible en la barra de direcciones del navegador, lo que significa que cualquier persona puede ver los datos enviados.

El método POST, por otro lado, se utiliza para enviar información al servidor, donde los datos del formulario se envían en el cuerpo de la solicitud HTTP. Esto significa que los datos no son visibles en la barra de direcciones del navegador. En lugar de eso, se envían de manera confidencial al servidor.

En resumen, la principal diferencia entre los métodos HTTP POST y GET es que POST se utiliza para enviar datos al servidor, mientras que GET se utiliza para solicitar datos del servidor. Además, los datos enviados a través del método POST no son visibles en la URL, lo que los hace más seguros para enviar información sensible.*/

?>

