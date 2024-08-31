<?php

include_once 'conexionBBDD.php';

function sacarNoticias() {
    $conn = conexionBBDD();

    // Comprobar la última ejecución
    $checkQuery = "SELECT ultima_ejecucion FROM ultima_ejecucion ORDER BY id DESC LIMIT 1";
    $result = $conn->query($checkQuery);
    if (!$result) {
        echo "Error en la consulta de última ejecución: " . $conn->error;
        return;
    }
    $ultimaEjecucion = $result->fetch_assoc();

    if ($ultimaEjecucion) {
        $ultimaFecha = new DateTime($ultimaEjecucion['ultima_ejecucion']);
        $ahora = new DateTime();

        $intervalo = $ahora->diff($ultimaFecha);

        // Ejecutar solo si ha pasado un día
        if ($intervalo->days < 1) {
            echo "El script ya se ejecutó en las últimas 24 horas.";
            return;
        }
    }

    $query = urlencode('ayudas a la vivienda');  // Codificar la consulta correctamente
    $apiKey = '68cfbf8e28824e08bd7fc903143e7f20';
    $url = "https://newsapi.org/v2/everything?q={$query}&language=es&apiKey={$apiKey}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: MiAplicacionNoticias/1.0'  // Establecer un User-Agent
    ]);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpcode != 200) {
        echo "API returned HTTP status " . $httpcode . "\n";
        echo "Error en cURL: " . curl_error($ch) . "\n";
        echo "Respuesta de la API: " . $response . "\n";  // Añadir esta línea para ver la respuesta completa
        curl_close($ch);
        return;
    }

    curl_close($ch);

    $data = json_decode($response, true);
    if ($data === null || empty($data['articles'])) {
        echo "Error al decodificar los datos de la API o no se encontraron artículos";
        return;
    }

    $stmt = $conn->prepare("INSERT INTO noticias (Fuente, Autor, Titulo, Descripcion, URL, URLImagen, FechaPublicacion) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo "Error al preparar la consulta: " . $conn->error;
        return;
    }

    foreach ($data['articles'] as $article) {
        $fuente = $article['source']['name'] ?? 'Fuente desconocida';
        $autor = $article['author'] ?? 'Autor desconocido';
        $titulo = $article['title'] ?? 'Título no disponible';
        $descripcion = $article['description'] ?? 'Sin descripción';
        $url = $article['url'] ?? '#';
        $urlImagen = $article['urlToImage'] ?? 'imagen_no_disponible.jpg';
        $fechaPublicacion = isset($article['publishedAt']) ? date('Y-m-d', strtotime($article['publishedAt'])) : date('Y-m-d');

        $checkQuery = "SELECT ID FROM noticias WHERE Titulo = ?";
        $checkStmt = $conn->prepare($checkQuery);
        if (!$checkStmt) {
            echo "Error al preparar la consulta de verificación: " . $conn->error;
            return;
        }
        $checkStmt->bind_param("s", $titulo);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $checkStmt->close();

        if ($result->num_rows == 0) {
            $stmt->bind_param("sssssss", $fuente, $autor, $titulo, $descripcion, $url, $urlImagen, $fechaPublicacion);
            if (!$stmt->execute()) {
                echo "Error al insertar los datos: " . $stmt->error;
            }
        }
    }

    echo "Nuevos registros creados exitosamente";

    $stmt->close();

    // Registrar la ejecución
    $registroQuery = "INSERT INTO ultima_ejecucion (ultima_ejecucion) VALUES (NOW())";
    if (!$conn->query($registroQuery)) {
        echo "Error al registrar la última ejecución: " . $conn->error;
    }

    cerrarConexion($conn);
}

if (isset($_POST['submit'])) {
    sacarNoticias();
} else {
    // Verificar si el script debe ejecutarse automáticamente
    sacarNoticias();
}

?>
