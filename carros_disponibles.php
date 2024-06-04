<?php
include 'database.php';

// Consulta SQL para seleccionar carros disponibles
$sql = "SELECT * FROM carros WHERE estatus = 'disponible'";
$result = $conn->query($sql);

// Verifica si se encontraron resultados
if ($result->num_rows > 0) {
    // Inicializa un array para almacenar los carros disponibles
    $carros_disponibles = array();
    
    // Recorre los resultados y añade cada carro disponible al array
    while ($row = $result->fetch_assoc()) {
        $carros_disponibles[] = $row;
    }
    
    // Devuelve los carros disponibles en formato JSON
    echo json_encode($carros_disponibles);
} else {
    // Si no se encontraron carros disponibles, devuelve un array vacío
    echo json_encode(array());
}

// Cierra la conexión a la base de datos
$conn->close();
?>

