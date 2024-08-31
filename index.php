<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet"
    />
    <title>Página de inicio</title>
    <link rel="stylesheet" href="style.css" />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
      rel="stylesheet"
    />
  </head>

  <body>
    <!-- barra superior -->

    <?php include 'header.php'; ?>

    <?php

    include 'conexionBBDD.php';
    $conn = conexionBBDD();

    $sql = "SELECT Nombre, Descripcion, Url FROM ayuda ORDER BY fecha_creacion DESC LIMIT 3";
    $result = $conn->query($sql); $ayudas_recientes = []; if ($result->num_rows
    > 0) { while ($row = $result->fetch_assoc()) { $ayudas_recientes[] = $row; }
    } cerrarConexion($conn); function truncateTxt($text, $length = 100) { if
    (strlen($text) <= $length) { return $text; } return substr($text, 0, $length
    - 3) . '...'; } ?>

    <!-- Contenedor con el primer texto y su imagen -->

    <main>
      <table>
        <div class="container col-xxl-8 px-4">
          <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
            <div class="col-md-10 mx-auto col-lg-6">
              <img
                src="images/ayuda.png"
                class="d-block mx-lg-auto img-fluid"
                alt="Bootstrap Themes"
                width="700"
                height="500"
                loading="lazy"
              />
            </div>
            <div class="col-lg-6">
              <h1 class="display-5 fw-bold text-body-emphasis lh-1 mb-3">
                ¡Encuentra la ayuda que mejor se adapta a ti!
              </h1>
              <p class="lead">
                Busca entre las cientos de ayudas que tenemos para ayudarte a
                tener una vida plena en unas condiciones óptimas o déjate
                asesorar por uno de nuestros expertos. ¡Estamos aquí para
                AYUDAR!
              </p>
              <a href="ayudas.php">
                <button type="button" class="btn btn-outline-success btn-lg px-4 me-md-2">
                  Ver Ayudas
                </button>                              
              </a>
            </div>
          </div>
        </div>

        <div class="pt-5 pb-5 row">
          <h1>Ayudas Recientes</h1>
        </div>

        <!-- Tres tarjetas con acceso a las ayudas recientes -->

        <div class="row categories pb-5 contenedor">
          <?php
            $indiceImg = 1;
            foreach ($ayudas_recientes as $ayuda) :
            ?>
          <a
            href="<?php echo $ayuda['Url']; ?>"
            target="_blank"
            class="cardLink"
          >
            <div class="column cardAyuda">
              <img
                src="images/Ayudas<?php echo $indiceImg ?>.png"
                class="mx-lg-auto img-fluid"
                alt="Imagen de ayuda"
                width="400"
                height="400"
              />
              <h4><?php  echo $ayuda['Nombre']; ?></h4>
              <p><?php echo truncateTxt($ayuda['Descripcion'], 100); ?></p>
            </div>
          </a>
          <?php
            $indiceImg++;
            endforeach;
            ?>
        </div>

        <div class="pt-5 pb-5 row">
          <h1>Opiniones de nuestros clientes</h1>
        </div>

        <!-- carrusel de tarjetas de opinión -->

        <div
          id="carouselExampleInterval"
          class="carousel carousel-dark slide"
          data-bs-ride="carousel"
        >
          <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="5000">
              <div class="cards-wrapper">
                <div class="card border-success rating-card mb-3">
                  <div class="card-header">Ruperta Gabilda</div>
                  <div class="card-body">
                    <h4 class="card-title">4,5/5</h4>
                    <h5 class="card-title pb-1">¡Me encanta!</h5>
                    <p class="card-text">Some quick example text</p>
                  </div>
                </div>
                <div class="card border-success rating-card mb-3">
                  <div class="card-header">Ruperta Gabilda</div>
                  <div class="card-body">
                    <h4 class="card-title">4,5/5</h4>
                    <h5 class="card-title pb-1">¡Me encanta!</h5>
                    <p class="card-text">Some quick example text</p>
                  </div>
                </div>
                <div class="card border-success rating-card mb-3">
                  <div class="card-header">Ruperta Gabilda</div>
                  <div class="card-body">
                    <h4 class="card-title">4,5/5</h4>
                    <h5 class="card-title pb-1">¡Me encanta!</h5>
                    <p class="card-text">Some quick example text</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="carousel-item" data-bs-interval="5000">
              <div class="cards-wrapper">
                <div class="card border-success rating-card mb-3">
                  <div class="card-header">Ruperta Gabilda</div>
                  <div class="card-body">
                    <h4 class="card-title">4,5/5</h4>
                    <h5 class="card-title pb-1">¡Me encanta!</h5>
                    <p class="card-text">Some quick example text</p>
                  </div>
                </div>
                <div class="card border-success rating-card mb-3">
                  <div class="card-header">Ruperta Gabilda</div>
                  <div class="card-body">
                    <h4 class="card-title">4,5/5</h4>
                    <h5 class="card-title pb-1">¡Me encanta!</h5>
                    <p class="card-text">Some quick example text</p>
                  </div>
                </div>
                <div class="card border-success rating-card mb-3">
                  <div class="card-header">Ruperta Gabilda</div>
                  <div class="card-body">
                    <h4 class="card-title">4,5/5</h4>
                    <h5 class="card-title pb-1">¡Me encanta!</h5>
                    <p class="card-text">Some quick example text</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="carousel-item">
              <div class="cards-wrapper">
                <div class="card border-success rating-card mb-3">
                  <div class="card-header">Ruperta Gabilda</div>
                  <div class="card-body">
                    <h4 class="card-title">4,5/5</h4>
                    <h5 class="card-title pb-1">¡Me encanta!</h5>
                    <p class="card-text">Some quick example text</p>
                  </div>
                </div>
                <div class="card border-success rating-card mb-3">
                  <div class="card-header">Ruperta Gabilda</div>
                  <div class="card-body">
                    <h4 class="card-title">4,5/5</h4>
                    <h5 class="card-title pb-1">¡Me encanta!</h5>
                    <p class="card-text">Some quick example text</p>
                  </div>
                </div>
                <div class="card border-success rating-card mb-3">
                  <div class="card-header">Ruperta Gabilda</div>
                  <div class="card-body">
                    <h4 class="card-title">4,5/5</h4>
                    <h5 class="card-title pb-1">¡Me encanta!</h5>
                    <p class="card-text">Some quick example text</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <button
            class="carousel-control-prev"
            type="button"
            data-bs-target="#carouselExampleInterval"
            data-bs-slide="prev"
          >
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button
            class="carousel-control-next"
            type="button"
            data-bs-target="#carouselExampleInterval"
            data-bs-slide="next"
          >
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
      </table>
    </main>

    <!-- Termina el main y empiezan los footers -->
    <?php include './footer.php'; ?>

    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
  </body>
</html>
