<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['login'])) {
    header("Location: login_encuesta.php");
    exit;
}

// Conectar a la base de datos
$conexionBase = new mysqli('localhost', 'root', '', 'encuestas');
$conexionBase->begin_transaction();

try {
    // Recoger el ID de la encuesta y la respuesta seleccionada
    $encuesta_id = $_POST['encuesta_id'];
    $respuesta_id = $_POST['respuesta_id'];

    // Actualizar el contador de la respuesta seleccionada
    $sql = "UPDATE respuesta SET numeroRespuestas = numeroRespuestas + 1 WHERE id = ?";
    $stmt = $conexionBase->prepare($sql);
    $stmt->bind_param("i", $respuesta_id);
    $stmt->execute();

    // Obtener la pregunta de la encuesta
    $sql_pregunta = "SELECT textoPregunta FROM encuesta WHERE id = ?";
    $stmt_pregunta = $conexionBase->prepare($sql_pregunta);
    $stmt_pregunta->bind_param("i", $encuesta_id);
    $stmt_pregunta->execute();
    $resultado_pregunta = $stmt_pregunta->get_result();
    $textoPregunta = $resultado_pregunta->fetch_assoc()['textoPregunta'];

    // Consultar las respuestas con sus respectivos números de votos
    $sql_respuestas = "SELECT textoRespuesta, numeroRespuestas FROM respuesta WHERE idEncuesta = ?";
    $stmt_respuestas = $conexionBase->prepare($sql_respuestas);
    $stmt_respuestas->bind_param("i", $encuesta_id);
    $stmt_respuestas->execute();
    $result_respuestas = $stmt_respuestas->get_result();

    // Calcular el total de votos de la encuesta
    $sql_total = "SELECT SUM(numeroRespuestas) as totalVotos FROM respuesta WHERE idEncuesta = ?";
    $stmt_total = $conexionBase->prepare($sql_total);
    $stmt_total->bind_param("i", $encuesta_id);
    $stmt_total->execute();
    $result_total = $stmt_total->get_result();
    $totalVotos = $result_total->fetch_assoc()['totalVotos'];

    $conexionBase->commit();
} catch (Exception $e) {
    $conexionBase->rollback();
    echo "Error: " . $e->getMessage();
    exit;
}

// Preparar los datos para el gráfico
$etiquetas = [];
$valores = [];
$porcentajes = [];
while ($row = $result_respuestas->fetch_assoc()) {
    $etiquetas[] = $row['textoRespuesta'];
    $valores[] = $row['numeroRespuestas'];
    $porcentajes[] = ($totalVotos > 0) ? ($row['numeroRespuestas'] / $totalVotos) * 100 : 0;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de la encuesta</title>
    <!-- Incluir la librería de Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Incluir el plugin ChartDataLabels -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .container {
            width: 80%;
            margin: 0 auto;
        }
        canvas {
            max-width: 100%;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<h1>Resultados de la Encuesta</h1>
<h2><?php echo $textoPregunta; ?></h2>
<p>Total de votaciones: <?php echo $totalVotos; ?></p>

<div class="container">
    <!-- Gráfico debajo de la tabla -->
    <canvas id="grafico"></canvas>
</div>

<script>
    var ctx = document.getElementById('grafico').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($etiquetas); ?>,
            datasets: [{
                label: 'Votos',
                data: <?php echo json_encode($valores); ?>,
                backgroundColor: 'rgba(255, 0, 0, 0.7)', // Color rojo
                borderColor: 'rgba(255, 0, 0, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y', // Para que el gráfico sea horizontal
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            var porcentaje = (tooltipItem.raw / <?php echo $totalVotos; ?>) * 100;
                            return tooltipItem.label + ': ' + tooltipItem.raw + ' votos (' + porcentaje.toFixed(2) + '%)';
                        }
                    }
                },
                datalabels: {
                    anchor: 'end',
                    align: 'end',
                    formatter: function(value, context) {
                        var percentage = (value / <?php echo $totalVotos; ?>) * 100;
                        return percentage.toFixed(2) + '%';
                    },
                    font: {
                        weight: 'bold',
                        size: 14,
                        family: 'Arial'
                    },
                    color: '#ffffff' // Color blanco para el porcentaje
                }
            },
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        },
        plugins: [ChartDataLabels]
    });
</script>

</body>
</html>

<?php
$conexionBase->close();
?>
