<?php
include 'database.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_usuario = $_SESSION['id_usuario'];
    $id_carro = $_POST['id_carro'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    $sql_carro = "SELECT costo_por_dia FROM carros WHERE id='$id_carro'";
    $result_carro = $conn->query($sql_carro);
    $row_carro = $result_carro->fetch_assoc();
    $costo_por_dia = $row_carro['costo_por_dia'];

    $diff = date_diff(date_create($fecha_inicio), date_create($fecha_fin));
    $dias = $diff->format('%a');
    $costo_total = $dias * $costo_por_dia;

    $sql_renta = "INSERT INTO rentas (id_usuario, id_carro, fecha_inicio, fecha_fin, costo_total) VALUES ('$id_usuario', '$id_carro', '$fecha_inicio', '$fecha_fin', '$costo_total')";

    if ($conn->query($sql_renta) === TRUE) {
        $id_renta = $conn->insert_id; // Obtener el ID de la última inserción
        $sql_update_carro = "UPDATE carros SET estatus='rentado' WHERE id='$id_carro'";
        $conn->query($sql_update_carro);
        echo "Renta registrada exitosamente, tu ID para devolver el coche es '$id_renta'";
    } else {
        echo "Error: " . $sql_renta . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

