<!DOCTYPE html>
<html>
<head>
    <title>Página Flotante</title>
    <style>
        /* Estilos para la página flotante */
        .pagina-flotante {
            position: fixed;  /* Establece la posición fija en la ventana del navegador */
            top: 50%;  /* Alinea la parte superior de la página flotante al 50% de la altura de la ventana */
            left: 50%;  /* Alinea la parte izquierda de la página flotante al 50% del ancho de la ventana */
            transform: translate(-50%, -50%);  /* Aplica una transformación para centrar la página flotante */
            background-color: white;  /* Establece el color de fondo de la página flotante a blanco */
            padding: 20px;  /* Agrega un espacio de relleno de 20px al contenido de la página flotante */
            border: 1px solid #ccc;  /* Establece un borde de 1px sólido de color gris claro alrededor de la página flotante */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);  /* Agrega una sombra alrededor de la página flotante para darle profundidad */
        }
    </style>
</head>
<body>
    <div class="pagina-flotante">
        <!-- Contenido de la página flotante -->
        <h1></h1>
        <p></p>
    </div>
</body>
</html>