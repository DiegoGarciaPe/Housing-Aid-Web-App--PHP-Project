<?php
require 'conexionBBDD.php';
$conexion = conexionBBDD();

$query = isset($_POST['query']) ? $_POST['query'] : '';

if ($query !== '') {
    $sql = "SELECT consultas.*, usuario.Nombre, usuario.Apellidos, usuario.Email 
            FROM consultas 
            JOIN usuario ON consultas.id_usuario = usuario.ID
            WHERE consultas.Titulo LIKE ? 
            OR consultas.Mensaje LIKE ?
            OR usuario.Nombre LIKE ?
            OR usuario.Apellidos LIKE ?
            OR usuario.Email LIKE ?
            ORDER BY fecha DESC";
    $stmt = $conexion->prepare($sql);
    $searchQuery = '%' . $query . '%';
    $stmt->bind_param("sssss", $searchQuery, $searchQuery, $searchQuery, $searchQuery, $searchQuery);
} else {
    $sql = "SELECT consultas.*, usuario.Nombre, usuario.Apellidos, usuario.Email 
            FROM consultas 
            JOIN usuario ON consultas.id_usuario = usuario.ID
            ORDER BY fecha DESC";
    $stmt = $conexion->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Mensaje</th>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Email</th>
                        <th>Superusuario Asignado</th>
                        <th>Fecha y Hora</th>
                        <th>Resuelta</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>';
    while ($consulta = $result->fetch_assoc()) {
        echo '<tr class="' . ($consulta['resuelta'] == 1 ? 'table-success' : '') . '">
                <td>' . htmlspecialchars($consulta['ID']) . '</td>
                <td>' . htmlspecialchars($consulta['Titulo']) . '</td>
                <td>' . htmlspecialchars(mb_strimwidth($consulta['Mensaje'], 0, 20, '...')) . '</td>
                <td>' . htmlspecialchars($consulta['Nombre']) . '</td>
                <td>' . htmlspecialchars($consulta['Apellidos'] ?? 'No ingresado') . '</td>
                <td>' . htmlspecialchars($consulta['Email']) . '</td>
                <td>' . htmlspecialchars($consulta['nombre_superusuario'] ?? 'No asignado') . '</td>
                <td>' . date("d/m/Y H:i", strtotime($consulta['fecha'])) . '</td>
                <td>
                    <input type="checkbox" class="resueltaCheckbox" data-id="' . $consulta['ID'] . '" ' . ($consulta['resuelta'] == 1 ? 'checked' : '') . '>
                </td>
                <td>
                    <a href="chat.php?id_consulta=' . htmlspecialchars($consulta['ID']) . '" class="btn-chatAdmin">Abrir Chat</a>
                </td>
              </tr>';
    }
    echo '</tbody>
        </table>
    </div>';
} else {
    echo '<p>No se encontraron consultas.</p>';
}

$stmt->close();
$conexion->close();
