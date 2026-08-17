<?php 
    session_start();
    // Verificar si el usuario ha iniciado sesión
    if (!isset($_SESSION['loggedin'])) {
        // El usuario no ha iniciado sesión, redirigir a la página de inicio de sesión
      //  header("Location: index.php");
        exit();
    }
/*
Una sesión en PHP permite almacenar y recuperar datos específicos del usuario
durante varias páginas o visitas al sitio web. La función session_start() 
debe llamarse antes de utilizar cualquier otra función relacionada con sesiones, 
y generalmente se coloca al inicio del archivo PHP.

Cuando se llama a session_start(), PHP busca una cookie de sesión en la solicitud del usuario.
Si se encuentra una cookie de sesión válida, PHP reanuda la sesión existente 
y permite el acceso a los datos de la sesión. Si no se encuentra una cookie de sesión
o no existe una sesión válida, PHP crea una nueva sesión y asigna un identificador 
único a esa sesión.

Una vez que se ha iniciado la sesión con éxito utilizando session_start(),
se pueden almacenar y acceder a los datos de la sesión utilizando la variable superglobal
$_SESSION. Esta variable es un array asociativo donde se pueden guardar y recuperar
valores específicos del usuario a lo largo de su sesión en el sitio web.
*/
?>

