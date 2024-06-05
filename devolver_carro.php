<?php
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Iniciar la sesión si no está iniciada
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Verificar si la sesión del usuario está disponible
    if (isset($_SESSION['id_usuario'])) {
        $id_usuario = $_SESSION['id_usuario'];

        $id_renta = $_POST['id_renta'];
        $fecha_devolucion = $_POST['fecha_devolucion'];

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

            // Obtener el costo por día del carro
            $sql = "SELECT costo_por_dia FROM carros WHERE id='$id_carro'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $carro = $result->fetch_assoc();
                $costo_por_dia = $carro['costo_por_dia'];

                // Calcular el costo total
                $costo_total = ($dias_extra > 0 ? ($dias_extra + 1) : 1) * $costo_por_dia;

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
    } else {
        echo "La sesión del usuario no está disponible";
    }

    $conn->close();
}
?>
