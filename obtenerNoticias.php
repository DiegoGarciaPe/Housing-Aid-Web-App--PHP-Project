<?php
include 'conexionBBDD.php';

function obtenerNoticias($limit = 25) {
    $conn = conexionBBDD();

    $sql = "SELECT Fuente, Autor, Titulo, Descripcion, URL, URLImagen, FechaPublicacion FROM noticias ORDER BY FechaPublicacion DESC LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $noticias = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $noticias[] = $row;
        }
    }

    cerrarConexion($conn);
    return $noticias;
}


function obtenerEnlaces() {
    $conn = conexionBBDD();

    $sql = "SELECT nombre, enlace FROM enlaces";
    $result = $conn->query($sql);

    $enlaces = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $enlaces[] = $row;
        }
    }

    cerrarConexion($conn);
    return $enlaces;
}
?>
