<?php
require 'conexionBBDD.php';
$conexion = conexionBBDD();

if (isset($_GET['search'])) {
    $search = $conexion->real_escape_string($_GET['search']);
    
    $sqlAyudas = "
    SELECT a.Id, a.Nombre, a.Descripcion, a.Requisitos, a.Url, GROUP_CONCAT(c.Nombre SEPARATOR ', ') AS Categoria, GROUP_CONCAT(c.Id SEPARATOR ', ') AS CategoriaIds
    FROM ayuda a
    JOIN ayuda_categoria ac ON a.Id = ac.ayuda_id
    JOIN categorias c ON ac.categoria_id = c.Id
    WHERE a.Nombre LIKE '%$search%' OR a.Descripcion LIKE '%$search%' OR a.Requisitos LIKE '%$search%'
    GROUP BY a.Id
    ORDER BY a.fecha_creacion DESC";
    
    $resultAyudas = $conexion->query($sqlAyudas);
    
    if ($resultAyudas->num_rows > 0) {
        while ($ayuda = $resultAyudas->fetch_assoc()) {
            $nombre = htmlspecialchars($ayuda['Nombre']);
            $descripcion = htmlspecialchars($ayuda['Descripcion']);
            $requisitos = htmlspecialchars($ayuda['Requisitos']);
            $url = htmlspecialchars($ayuda['Url']);
            $categoria = htmlspecialchars($ayuda['Categoria']);
            $categoriaIds = htmlspecialchars($ayuda['CategoriaIds']);

            echo "<tr data-id='{$ayuda['Id']}' data-categoria-ids='{$categoriaIds}'>";
            echo "<td class=\"url\">{$nombre}</td>";
            echo "<td class=\"url\">{$descripcion}</td>";
            echo "<td class=\"url\">{$requisitos}</td>";
            echo "<td class=\"url\">{$url}</td>";
            echo "<td>{$categoria}</td>";
            echo "<td><button type=\"button\" class=\"btn btn-outline-primary btn-sm edit-btn\">Editar</button></td>";
            echo "<td><button type=\"button\" class=\"btn btn-outline-danger btn-sm delete-btn\">Eliminar</button></td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No hay ayudas que coincidan con la búsqueda.</td></tr>";
    }
}
?>
