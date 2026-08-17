
<?php
    function generarOpcionesCombo($sql, $tabla) {
        require("conexion.php"); //Utiliza el archivo para la conexión a la base
        //$sql = 'SELECT * FROM Datos';
        $result = $conn->query($sql);

        // Verificar si se encontraron registros
        if ($result->num_rows > 0) {
            // Generar las opciones del combo con los nombres de las provincias
            while ($fila = $result->fetch_assoc()) {
                //echo '<option value="' . $fila['Rubro'] . '">' . $fila['Rubro'] . '</option>';
                echo '<option value="' . $fila[$tabla] . '">' . $fila[$tabla] . '</option>';
            }
        }
        // Cerrar la conexión a la base de datos
       $conn->close();
    }
    function cargarDireccion($direccion){
    // Dirección a enlazar con Google Maps "123 Calle Principal, Ciudad, País";
    // URL de Google Maps con la dirección como parámetro de búsqueda
        $url = "https://www.google.com/maps/search/?api=1&query=" . urlencode($direccion);
    //<!-- Enlace a Google Maps -->
    //    echo <a href=" . $url . " target="_blank">$direccion</a>
    }
      // Función externa para cargar funciones
    function cargarProvincias() {
        // Aquí puedes obtener las provincias desde una base de datos, un archivo CSV, etc.
            $provincias = array(
                'Buenos Aires', 'Catamarca', 'Chaco', 'Chubut', 'Córdoba', 'Corrientes',
                'Entre Ríos', 'Formosa', 'Jujuy', 'La Pampa', 'La Rioja', 'Mendoza',
                'Misiones', 'Neuquén', 'Río Negro', 'Salta', 'San Juan', 'San Luis',
                'Santa Cruz', 'Santa Fe', 'Santiago del Estero', 'Tierra del Fuego', 'Tucumán'
            );
            foreach ($provincias as $provincia) {
                echo "<option value=" . $provincia . ">". $provincia ."</option>";
            }
            //return $provincias;
       }
       function cargarTipoFactura() {
        // Aquí puedes obtener las provincias desde una base de datos, un archivo CSV, etc.
            $tipoFacturas = array(
                'A', 'B', 'C');
            foreach ($tipoFacturas as $tipofactura) {
                echo "<option value=" . $tipoFactura . ">". $tipoFactura ."</option>";
            }
       }
/*
      function existeRegistro($codigo)
        {
            $sql = "SELECT * FROM Stock WHERE Codigo = '$codigo'";
            $result = $conn->query($sql);

            $conn->close();

            if ($result && $result->num_rows > 0) {
                return true;
            } else {
                return false;
            }
        }*/
?>