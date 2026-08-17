<nav class="navbar navbar-expand-lg navbar-dark fixed-top">

  <div class="container">

   <a class="navbar-brand" href="/">
    ISFTº 220
  </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">

      <ul class="navbar-nav ms-auto">
        <li class="nav-item" href="/">
          <a class="nav-link active" aria-current="page" href="#"><i class="fa-solid fa-house" style="color: white;"></i></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#about">
            Nosotros
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Carreras
          </a>
          <ul class="dropdown-menu dropdown-menu-dark">
            <li>
              <a class="dropdown-item" href="#carrer-seg">
                Técnicatura en Higiene y seguridad
              </a></li>
            <li>
              <a class="dropdown-item" href="#carrer-al">
                Técnicatura en Alimentos
              </a></li>
            <li>
              <a class="dropdown-item" href="#carrer-soft">
                Técnicatura en Desarrollo de software
              </a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#gallery">
            Galería
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#location">
            Ubicación
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#contact">
            Contacto
          </a>
        </li>
      </ul>

    </div>

  </div>

</nav>

<script>

  const navEl = document.querySelector('.navbar');

  window.addEventListener('scroll', () => {

    if (window.scrollY >= 56) {

      navEl.classList.add('navbar-scrolled');

    } else if (window.scrollY < 56) {

      navEl.classList.remove('navbar-scrolled');
    }
  });
  
</script>
