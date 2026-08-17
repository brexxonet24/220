<?php include 'header.php';
 include 'funciones.php'; ?>
    <h2 class="text-center">Altas Datos</h2>
    <form class="form" method="post" action="listar_datos.php">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <label for="nombre">Nombre:</label>
                    <input class="form-control" type="text" name="nombre" id="nombre" required>
                    <br>
                    <label for="tel">Tel / WhatsApp:</label>
                    <input class="form-control" type="text" name="tel" id="tel" required>
                    <br>
                    <label for="email">Email:</label>
                    <input class="form-control" type="email" name="email" id="email" required>
                    <br>
                    <label for="rubro">Rubro:</label>
                    <select class="form-control" name="rubro" id="rubro" required>
                        <?php generarOpcionesCombo('SELECT DISTINCT Rubro FROM Datos WHERE Rubro IS NOT NULL AND Rubro <> "" ORDER BY Rubro ASC', 'Rubro'); ?>
                    </select>
                    <br>
                    <label for="categoria">Categoria:</label>
                    <select class="form-control" name="categoria" id="categoria" required>
                        <?php generarOpcionesCombo('SELECT DISTINCT Categoria FROM Datos WHERE Categoria IS NOT NULL AND Categoria <> "" ORDER BY Categoria ASC', 'Categoria'); ?>
                    </select>
                    <br>
                </div>
                <div class="col-md-6">
                    <label for="cuit">CUIT:</label>
                    <input class="form-control" type="text" name="cuit" id="cuit" pattern="\d{2}-\d{8}-\d{1}" title="Ingrese un CUIT\CUIL válido con el formato XX-XXXXXXXX-X" required>
                    <br>
                    <label for="direccion">Dirección:</label>
                    <input class="form-control" type="text" name="direccion" id="direccion" required>
                    <br>
                    <label for="localidad">Localidad:</label>
                    <select class="form-control" name="localidad" id="localidad" required>
                        <?php generarOpcionesCombo('SELECT DISTINCT Localidad FROM Datos WHERE Localidad IS NOT NULL AND Localidad <> "" ORDER BY Localidad ASC', 'Localidad'); ?>
                    </select>
                    <br>
                    <label for="provincia">Provincias:</label>
                    <select class="form-control" name="provincia" id="provincia" required>
                        <?php cargarProvincias();?>
                    </select>
                    <br>
                    <label for="cp">CP:</label>
                    <input class="form-control" type="text" name="cp" id="cp" required>
                    <br>
                </div>
            </div>
        </div>
        <div style="text-align: center;">
            <input class="btn btn-success" type="submit" value="Guardar">
        </div>
        <br><br>
    </form>

    <?php
      include 'footer.php';
    ?>