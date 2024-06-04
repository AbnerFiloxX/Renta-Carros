<?php
define('DB_HOST', 'localhost:8889');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'renta_de_carros');

// Crear conexión
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Esta parte no es necesaria
// public function getConnection () {
//     return $this->conn;
// }
?>
