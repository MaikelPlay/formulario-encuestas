<?php
// Recibimos los datos del QUERY_STRING
$total_votos = $_GET['total_votos'];
$etiquetas = $_GET['etiquetas'];
$valores = $_GET['valores'];

// Dimensiones de la imagen
$ancho = 800;
$alto = 400;

// Creamos la imagen
$imagen = imagecreatetruecolor($ancho, $alto);

// Definimos colores
$fondo = imagecolorallocate($imagen, 255, 255, 255); // blanco
$barra = imagecolorallocate($imagen, 255, 0, 0); // rojo
$texto = imagecolorallocate($imagen, 0, 0, 0); // negro

// Llenamos el fondo de la imagen
imagefill($imagen, 0, 0, $fondo);

// Calculamos el ancho máximo de las barras
$max_valor = max($valores);

// Definir la altura de las barras y el espacio entre ellas
$espacio = 20; // Espacio entre barras
$pos_y = 50; // Posición inicial de Y (para las barras)
$barra_alto = 30; // Alto de cada barra

// Dibujamos las barras en horizontal y los textos
for ($i = 0; $i < count($etiquetas); $i++) {
    // Calculamos el ancho de la barra en función del total de votos
    $ancho_barra = ($valores[$i] / $max_valor) * ($ancho - 150);
    
    // Dibujo de la barra (en horizontal)
    imagefilledrectangle($imagen, 50, $pos_y, 50 + $ancho_barra, $pos_y + $barra_alto, $barra);
    
    // Mostramos el porcentaje dentro de la barra
    $porcentaje = ($total_votos > 0) ? ($valores[$i] / $total_votos) * 100 : 0;
    $porcentaje_texto = number_format($porcentaje, 2) . '%';
    
    // Calculamos la posición X para centrar el texto
    $text_width = imagefontwidth(5) * strlen($porcentaje_texto);
    imagestring($imagen, 5, 50 + ($ancho_barra / 2) - ($text_width / 2), $pos_y + ($barra_alto / 2) - (imagefontheight(5) / 2), $porcentaje_texto, $texto);
    
    // Mostramos la etiqueta de la respuesta al lado de la barra
    imagestring($imagen, 5, 10, $pos_y + ($barra_alto / 2) - (imagefontheight(5) / 2), $etiquetas[$i], $texto);
    
    // Avanzamos la posición en el eje Y para la siguiente barra
    $pos_y += $barra_alto + $espacio;
}

// Enviamos la cabecera para mostrar la imagen
header("Content-Type: image/png");

// Generamos la imagen
imagepng($imagen);

// Liberamos la memoria
imagedestroy($imagen);
?>
