<?php
// Conectar a la base de datos
$conexionBase = new mysqli('localhost', 'root', '', 'encuestas');

// Obtener el ID de la encuesta
$encuesta_id = $_GET['id'];

// Consultar la encuesta
$sql = "SELECT textoPregunta FROM encuesta WHERE id = $encuesta_id";
$result = $conexionBase->query($sql);
$pregunta = $result->fetch_assoc()['textoPregunta'];

// Consultar las respuestas de la encuesta
$sql_respuestas = "SELECT id, textoRespuesta FROM respuesta WHERE idEncuesta = $encuesta_id";
$respuestas = $conexionBase->query($sql_respuestas);

echo "<h1>" . $pregunta . "</h1>";

echo "<form action='mostrar_resultados.php' method='POST'>";
while($row = $respuestas->fetch_assoc()) {
    echo "<input type='radio' name='respuesta_id' value='" . $row['id'] . "'>" . $row['textoRespuesta'] . "<br>";
}
echo "<input type='hidden' name='encuesta_id' value='$encuesta_id'>";
echo "<input type='submit' value='Votar'>";
echo "</form>";

$conexionBase->close();
?>
