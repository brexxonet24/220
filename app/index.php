
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><!-- Estos son caracteres especiales-->
    <meta name="author" content="xxxx">
    <meta name="keywords" content="x,x,x">
    <meta name="description" content="xxxx">
    <meta name="viewport" content="width=device-width, initial-scale=1,shrink-to-fit=no">
    <title>CRUD</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <!-- Agrega aquí tus estilos o en el archivo de estilos -->
    <style>
        
    </style>
    <!-- Agrega aquí tus etiquetas meta, enlaces a hojas de estilo CSS, etc. -->
</head>
<?php 

include 'pagina_flotante.php';
// Obtener los valores enviados por el formulario
require("conexion.php"); // Utiliza el archivo para la conexión a la base

$email = $_POST['email'];
$password = $_POST['pass'];

// Realizar la conexión a la base de datos (asumiendo que ya tienes el archivo de conexión)

// Consulta preparada para verificar si existe un usuario con el email y contraseña proporcionados
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ? AND pass = ?");
$stmt->bind_param("ss", $email, $password); //son dos parametros string, string
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    // El usuario ha iniciado sesión exitosamente
    // Puedes realizar las operaciones necesarias, como redireccionar a una página de inicio, establecer variables de sesión, etc.
   
    // Después de verificar las credenciales y confirmar que son válidas
// Establecer la variable de sesión para indicar que el usuario ha iniciado sesión
echo "¡Hola!   Ingresaste.";

session_start();
$_SESSION['loggedin'] = true;
echo '<meta http-equiv="refresh" content="0;URL=listar_datos.php">';

// Redirigir a la página deseada después de iniciar sesión correctamente
//header("Location: pagina_restringida.php");
    exit(); // Asegura que el código se detenga después de la redirección
} else {
    // Las credenciales no son válidas
    // echo "Credenciales inválidas";
}

// Cerrar la conexión a la base de datos (si es necesario)
$stmt->close();
$conn->close();
?>
    <div class="pagina-flotante">
    <form class="form" method="post" action="index.php">
    <h2 class="text-center">Iniciar sesión</h2>
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
            <label for="email">Email:</label>
            <input class="form-control" type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
            <label for="pass">Contraseña:</label>
            <input class="form-control" type="password" name="pass" id="pass" required>
            </div>
            <div class="text-center">
            <button class="btn btn-info" type="submit">Iniciar sesión</button>
            </div>
        </div>
        <div class="col-md-6">
            <img src="https://img.freepik.com/vector-gratis/fondo-paisaje-primavera-hermosa-dibujada_23-2148857331.jpg?w=2000" alt="Descripción de la imagen" class="img-fluid">
        </div>
        </div>
    </form>
    </div>
</body>
</html>

<!-- 
La diferencia entre los métodos HTTP POST y GET radica principalmente en la forma en que los datos se envían desde el cliente al servidor y en cómo los servidores procesan y responden a esas solicitudes.

El método GET se utiliza para solicitar información del servidor, donde los datos se envían en la URL como parámetros de cadena de consulta. En otras palabras, los datos del formulario se agregan al final de la URL después de un signo de interrogación (?), y se separan por el símbolo ampersand (&). Por ejemplo, una solicitud GET para un formulario de inicio de sesión podría verse así:

http://example.com/login.php?username=johndoe&password=secret
En este caso, el nombre de usuario y la contraseña se pasan como parámetros de la cadena de consulta a través de la URL. La solicitud GET es visible en la barra de direcciones del navegador, lo que significa que cualquier persona puede ver los datos enviados.

El método POST, por otro lado, se utiliza para enviar información al servidor, donde los datos del formulario se envían en el cuerpo de la solicitud HTTP. Esto significa que los datos no son visibles en la barra de direcciones del navegador. En lugar de eso, se envían de manera confidencial al servidor.

En resumen, la principal diferencia entre los métodos HTTP POST y GET es que POST se utiliza para enviar datos al servidor, mientras que GET se utiliza para solicitar datos del servidor. Además, los datos enviados a través del método POST no son visibles en la URL, lo que los hace más seguros para enviar información sensible.*/
-->