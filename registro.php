<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Login/Registro</title>
</head>
<body>

<?php include 'header.php' ?>

<main class="background">
    <div class="form-container">
        <div class="wrapper">
            <div class="form-wrapper sign-in">
                <?php include 'formularioLogin.php' ?>
            </div>

            <div id="registro-section" class="form-wrapper sign-up">
                <?php include 'formularioRegistro.php' ?>
            </div>

        </div>
    </div>
</main>

<?php include 'footer.php' ?>


<script src="script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-KyZXEAg3QhqLMpG8r+Knujsl5+z7GtfZ7nLuFfwnqwK0sU4Z+d58z+8h/ta legido:uescofwMDwVBKTf" crossorigin="anonymous"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    if (window.location.hash === '#registro-section') {
        document.getElementById('registro-section').scrollIntoView();
    }
});
</script>
</body>
</html>