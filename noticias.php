<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <title>Noticias</title>
</head>
<body>
    <!-- barra superior -->
    <?php include 'header.php'; ?>
    <?php include 'obtenerNoticias.php'; ?>
    <?php include 'insertarNoticias.php'; ?>

    <main class="Noticias">
        <div class="container mt-4">
            <div class="row">
                <!-- Columnas vacías de la izquierda  -->
                <div class="col-md-1"></div>

                <!-- Columna de Noticias -->
                <div class="col-md-8 px-5">
                    <h1 class="newsTitle mainTitle">Noticias de actualidad</h1>
                    <hr class="hrTitle" />
                    <hr class="hrSub" />
                    <div class="row rowNews">
                        <!-- Primera subcolumna -->
                        <div class="col-md-6 borderTint">
                            <?php
                            $noticias = obtenerNoticias();
                            if (!empty($noticias)) {
                                for ($i = 0; $i < 6; $i++) {
                                    echo '<div>';
                                    echo '<a href="' . $noticias[$i]['URL'] . '" target="_blank"><img src="' . $noticias[$i]['URLImagen'] . '" class="imgSize" /></a>';
                                    echo '<p><a href="' . $noticias[$i]['URL'] . '" class="cblack2">' . $noticias[$i]['Titulo'] . '</a></p>';
                                    echo '<p class="text-muted xsText xsText subtitle">' . $noticias[$i]['Autor'] . ' | ' . $noticias[$i]['FechaPublicacion'] . '</p>';
                                    echo '<p class="fs-6">' . (strlen($noticias[$i]['Descripcion']) > 100 ? substr($noticias[$i]['Descripcion'], 0, 100) . '...' : $noticias[$i]['Descripcion']) . '</p>';
                                    echo '<hr class="lowHr" />';
                                    echo '</div>';
                                }
                            } else {
                                echo "No hay noticias disponibles.";
                            }
                            ?>
                        </div>

                        <!-- Segunda subcolumna -->
                        <div class="col-md-6">
                            <?php   
                            if (!empty($noticias)) {
                                for ($i = 6; $i < 10; $i++) {
                                    if ($i >= count($noticias)) break; // Break if there are not enough news
                                    echo '<div>';
                                    echo '<a href="' . $noticias[$i]['URL'] . '" target="_blank"><img src="' . $noticias[$i]['URLImagen'] . '" class="imgSize" /></a>';
                                    echo '<p><a href="' . $noticias[$i]['URL'] . '" class="cblack2">' . $noticias[$i]['Titulo'] . '</a></p>';
                                    echo '<p class="text-muted xsText xsText subtitle">' . $noticias[$i]['Autor'] . ' | ' . $noticias[$i]['FechaPublicacion'] . '</p>';
                                    echo '<p class="fs-6">' . (strlen($noticias[$i]['Descripcion']) > 100 ? substr($noticias[$i]['Descripcion'], 0, 100) . '...' : $noticias[$i]['Descripcion']) . '</p>';
                                    echo '<hr class="lowHr" />';
                                    echo '</div>';
                                }
                            }
                            ?>
                            <!-- Sección de Más Noticias en la sub-columna derecha -->
                            <?php if (!empty($noticias) && count($noticias) > 10): ?>
                                <hr class="bigHr" />
                                <div>
                                    <h4 class="mainTitle">Más noticias</h4>
                                    <hr class="hrTitle" />
                                    <hr class="hrSub" />
                                    <div class="sText">
                                        <?php
                                        for ($i = 10; $i < count($noticias) && $i < 19; $i++) {
                                            echo '<p><a href="' . $noticias[$i]['URL'] . '" class="cblack">' . $noticias[$i]['Titulo'] . '</a></p>';
                                            echo '<hr />';
                                        }
                                        ?>
                                    </div>
                                    <hr class="hrSub" />
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Columna de Enlaces -->
                <div class="col-md-3 px-5">
                    <div class="mb-3">
                        <h4 class="newsTitle mainTitle">Enlaces de confianza</h4>
                        <hr class="hrTitle" />
                        <hr class="hrSub" />
                        <div class="sText">
                          <?php
                            $enlaces = obtenerEnlaces();
                            if (!empty($enlaces)) {
                                foreach ($enlaces as $enlace) {
                                    echo '<p><a href="' . $enlace['enlace'] . '" class="cblack">' . $enlace['nombre'] . '</a></p>';
                                    echo '<hr />';
                                }
                            } else {
                                echo "<p>No hay enlaces disponibles.</p>";
                            }
                          ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
