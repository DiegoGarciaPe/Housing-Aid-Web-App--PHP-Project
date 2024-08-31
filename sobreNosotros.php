<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="style.css" />
    <title>Sobre Nosotros</title>
  </head>
  <body>
  <?php include 'header.php'; ?>
    <main class="mainAboutUs">
      <div class="bg-AboutUs">
        <div class="container">
          <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
            <div class="col-md-6">
              <div class="rounded-3">
                <h1 class="title-aboutUs my-2">Sobre nosotros</h1>
                <p class="lead">
                  En Oriéntame, nuestra empresa y nuestra cultura se parecen mucho
                  a nuestros servicios. Están conectados, no ensambladas, para
                  lograr una excelente experiencia.
                </p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="rounded-3">
                <img src="images/people.svg" class="imgSize-aboutUs" />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
          <div class="col-md-6">
            <div class="rounded-3">
              <img src="images/empresa.png" class="imgSize-aboutUs" />
            </div>
          </div>
          <div class="col-md-6">
            <div class="rounded-3">
              <h1 class="title-aboutUs2 my-2">
                Nuestra misión: Orientar a la gente en la búsqueda de ayudas a
                la vivienda
              </h1>
              <p class="lead">
                Creemos que no sólo es importante crecer más, sino crecer mejor.
                Y esto significa garantizar el éxito de tu empresa y también el
                de tus clientes. Todo el mundo gana.
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="bg-AboutUs">
        <div class="container">
          <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
            <div class="col-md-6">
              <div class="rounded-3">
                <h1 class="title-aboutUs2 my-2">Nuestra historia</h1>
                <p class="lead textAboutUs">
                  En 2004, cuando eran compañeros de posgrado en el MIT, Brian
                  Halligan y Dharmesh Shah se dieron cuenta de que la manera de
                  comprar estaba cambiando. La gente no quería estar recibiendo
                  publicidad constantemente, quería información útil. En 2006,
                  fundaron HubSpot para ayudar a las empresas a aprovechar ese
                  cambio para crecer mejor con el inbound marketing. Desde
                  entonces, HubSpot ha ido más allá del marketing y se ha
                  convertido en una plataforma conectada, no ensamblada, que
                  permite crear la experiencia del cliente que la gente quiere.
                  HubSpot, dirigida por la directora ejecutiva Yamini Rangan,
                  utiliza su plataforma de clientes, desarrollada en integración
                  con el Smart CRM con tecnología de inteligencia artificial, para
                  ayudar a millones de organizaciones en expansión a crecer mejor.
                </p>
              </div>
            </div>
          <div class="col-md-6">
            <div class="rounded-3">
              <img src="images/gente.png" class="imgSize-aboutUs" />
            </div>
          </div>
        </div>
      </div>
    </div>
      <div class="container counter">
        <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
          <div class="row">
            <h1 class="title-aboutUs3">Oriéntame en cifras</h1>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="contador ocultar">
                <img src="images/visitas-diarias.png" class="imgCounter">              
                <div class="contador_cantidad title-aboutUs4" data-cantidad-total="5000">0</div>
                <p class="title-aboutUs4">Visitas Diarias</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="contador ocultar">
                <img src="images/personas-ayudadas.png" class="imgCounter">                
                <div class="contador_cantidad title-aboutUs4" data-cantidad-total="10000">0</div>
                <p class="title-aboutUs4">Personas Ayudadas</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="contador ocultar">
                <img src="images/icono-españa.png" class="imgCounter">                
                <div class="contador_cantidad title-aboutUs4" data-cantidad-total="1500">0</div>
                <p class="title-aboutUs4">Ayudas en Provincias</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
    <?php include './footer.php'; ?>
    <script src="script.js"></script>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
  </body>
</html>
