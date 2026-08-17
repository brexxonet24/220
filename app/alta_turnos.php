<?php include 'header.php';
 include 'funciones.php'; ?>


    <h2 class="text-center">Altas</h2>
    <div class="container" width="10%">    
        <form class="form" method="post" action="listar_turnos.php">
        <label for="nombre">Nombre:</label>
                    <select class="form-control" name="nombre" id="nombre" required>
                        <?php generarOpcionesCombo('SELECT DISTINCT Nombre FROM Datos WHERE Nombre IS NOT NULL AND Nombre <> "" ORDER BY Nombre ASC', 'Nombre'); ?>
                    </select>            
                    <br>
                    <label for="dia">Día:</label>
                    <input class="form-control" type="date" name="dia" id="dia" value="<?php echo date('Y-m-d'); ?>" required>
                    <br>
                    <label for="hora">Hora:</label>
                    <input class="form-control" type="time" name="hora" id="hora" value="<?php echo date('H:i'); ?>" required>
                    <br>

            <input class="btn btn-info" type="submit" value="Insertar">
        </form>
    </div>

</body>

</html>
