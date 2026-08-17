<?php

//  echo '<pre>';
//  var_dump($_SERVER['REQUEST_URI']; 
//  echo '</pre>';

?>

<?php include 'views/partials/header.view.php'; ?>

<?php include 'views/partials/navbar-brand.view.php'; ?>

<div class="container vh-100">

    <div class="d-flex flex-column" style="margin-top: 100px">

        <div class="justify-content-center text-center">

            <h5 class="display-4 fw-bold mt-3">Página no encontrada</h1>

            <p class="lead mt-3">Lo sentimos <strong>¿Quieres regresar al Sitio del ISFT 220?</strong></P>

            <a class="btn btn-outline-info btn-lg mt-3" href="/">
            <i class="fa-solid fa-arrow-left me-2"></i>
                Regresar al Home
            </a>

        </div>

    </div>

</div>

<?php include 'views/partials/footer.view.php'; ?>