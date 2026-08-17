<!DOCTYPE html>
<html lang="es"> <!-- Este es una página en español -->
<head>
	<meta charset="UTF-8"><!-- Estos son caracteres especiales-->
    <meta name="author" content="xxxx">
    <meta name="keywords" content="x,x,x">
    <meta name="description" content="xxxx">
    <title>Formulario de inserción</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
</head>
<body>
    <h2>Formulario de inserción</h2>
    <div class="container">    
        <form class="form" method="post" action="listar.php">
            Nombre:<input class="form-control" type="text" name="texto1" id="texto1" required>
            Email:<input class="form-control" type="email" name="texto2" id="texto2" required><br/>
            <input class="btn btn-info" type="submit" value="Insertar">
        </form>
    </div>
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