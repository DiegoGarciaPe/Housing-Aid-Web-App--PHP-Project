<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<a href="index.php">
    <img class="imageAboveHeader" src="images/Logo.png" alt="logo Oriéntame">
</a>

<header>
    <nav class="navbar navbar-light navbar-expand-md" style="background-color: #A8D29F">
        <div class="container-fluid">

            <a class="navbar-brand" style="color: transparent" href="#">.</a>


            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menu">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="sobreNosotros.php">Sobre Nosotros</a></li>
                    <li class="nav-item"><a class="nav-link" href="ayudas.php">Ayudas</a></li>
                    <li class="nav-item"><a class="nav-link" href="contacto.php">Contacto</a></li>
                    <li class="nav-item"><a class="nav-link" href="noticias.php">Noticias</a></li>

                    <?php if (isset($_SESSION["email"])): ?>
                    <li class="nav-item"><a class="nav-link" href="dashboardUsuario.php">Mi Perfil</a></li>
                    <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="registro.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>