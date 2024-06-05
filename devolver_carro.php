<?php
include 'database.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_renta = $_POST['id_renta'];
    $fecha_devolucion = $_POST['fecha_devolucion'];
    $id_usuario = $_SESSION['id_usuario']; // Suponiendo que tienes la sesión del usuario disponible

    // Obtener la renta
    $sql = "SELECT * FROM rentas WHERE id='$id_renta' AND id_usuario='$id_usuario'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $renta = $result->fetch_assoc();
        $id_carro = $renta['id_carro'];
        $fecha_fin = $renta['fecha_fin'];

        // Calcular la diferencia de días
        $datetime1 = new DateTime($fecha_fin);
        $datetime2 = new DateTime($fecha_devolucion);
        $interval = $datetime1->diff($datetime2);
        $dias_extra = $interval->days;
        
        if ($interval->invert) {
            $dias_extra = 0; // Si la devolución es antes de la fecha de fin, no hay días extra
        }

        // Obtener el costo por día del carro
        $sql = "SELECT costo_por_dia FROM carros WHERE id='$id_carro'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $carro = $result->fetch_assoc();
            $costo_por_dia = $carro['costo_por_dia'];

            // Calcular el costo total incluyendo días extra
            $costo_total = $renta['costo_total'] + ($dias_extra * $costo_por_dia);

            // Actualizar el estado de la renta
            $sql = "UPDATE rentas SET fecha_devolucion='$fecha_devolucion', estado='devuelto', costo_total='$costo_total' WHERE id='$id_renta'";
            if ($conn->query($sql) === TRUE) {
                // Actualizar el estatus del carro
                $sql = "UPDATE carros SET estatus='disponible' WHERE id='$id_carro'";
                if ($conn->query($sql) === TRUE) {
                    echo "Carro devuelto. Costo total: $" . $costo_total;
                } else {
                    echo "Error al actualizar el estatus del carro: " . $conn->error;
                }
            } else {
                echo "Error al actualizar la renta: " . $conn->error;
            }
        } else {
            echo "Error al obtener el costo del carro: " . $conn->error;
        }
    } else {
        echo "Renta no encontrada o no tienes permiso para devolver este vehículo";
    }

    $conn->close();
}
?>
