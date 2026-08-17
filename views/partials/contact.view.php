 <section>

 <div class="container col-xl-10 col-xxl-8 px-4 py-5">

    <div class="row align-items-center g-lg-5 py-5">

      <div class="col-lg-7 text-center text-lg-start">
        
        <?php include 'views/partials/item-contact.view.php'; ?>
      
      </div>

      <div class="col-md-10 mx-auto col-lg-5">

        <form method="post" action="https://formsubmit.co/institutosuperior220@gmail.com" class="p-4 p-md-5 border rounded-3 miform" id="contact">
          
          <div class="form-floating mb-3">

            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" required pattern="[a-zA-Z]+" title="Ingresa solamente letras en minúsculas o mayúsculas">
            <label for="floatingInput">Nombre</label>

          </div>

          <div class="form-floating mb-3">

            <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Apellido" required pattern="[a-zA-Z]+" title="Ingresa solamente letras en minúsculas o mayúsculas">
            <label for="floatingInput">Apellido</label>

          </div>

          <div class="form-floating mb-3">

            <input type="email" class="form-control" id="email" name="mail" placeholder="nombre@correo.com" required>
            <label for="floatingInput">Email</label>

          </div>

          <input type="hidden" name="_next" value="http://isft220.edu.ar/gracias">

          <div class="mb-3">

            <label for="exampleFormControlTextarea1" class="form-label"></label>
            <textarea class="form-control" id="message" name="mensaje" rows="3" placeholder="Escribinos tu consulta ..." required></textarea>

          </div>

          <button class="w-100 btn btn-lg btn-info btn-form" type="submit">Enviar</button>

        </form>

      </div>

    </div>

  </div>

</section>