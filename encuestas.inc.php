<?php
class ExcepcionEnTransaccion extends Exception
{
    
}

function validarPregunta($pregunta) {
    $pregunta = trim($pregunta);
    if (empty($pregunta)) {
        throw new Exception('El texto de la pregunta no puede estar vacío.');
    }
    return addslashes($pregunta);  // Escapamos caracteres especiales como comillas simples y dobles
}

// Validar respuestas
function validarRespuestas($respuestas, $num_respuestas) {
    $respuestas_limpias = array_map('trim', $respuestas);

    // Validar que no haya respuestas vacías
    foreach ($respuestas_limpias as &$respuesta) {
        if (empty($respuesta)) {
            throw new Exception('No puede haber respuestas vacías.');
        }
        $respuesta = addslashes($respuesta);  // Escapar caracteres especiales
    }

    // Validar que el número de respuestas sea correcto
    if (count($respuestas_limpias) !== $num_respuestas) {
        throw new Exception('El número de respuestas no coincide con el número especificado.');
    }

    // Validar que no haya respuestas repetidas
    if (count($respuestas_limpias) !== count(array_unique($respuestas_limpias))) {
        throw new Exception('Las respuestas no deben estar repetidas.');
    }

    return $respuestas_limpias;
}

// Función para insertar la encuesta en la base de datos
function insertarEncuesta($db, $pregunta, $respuestas) {
    try {
        // Iniciar transacción manual
        if ($db->autocommit(false) === false) {
            throw new Exception('El motor no admite transacciones.');
        }

        // Insertar la pregunta en la tabla encuesta
        $consulta = "INSERT INTO encuesta (textoPregunta) VALUES ('$pregunta')";
        if ($db->query($consulta) === false) {
            throw new ExcepcionEnTransaccion();
        }

        // Obtener el ID de la encuesta recién insertada
        $encuesta_id = $db->insert_id;

        // Insertar cada respuesta en la tabla respuesta
        foreach ($respuestas as $respuesta) {
            $consulta = "INSERT INTO respuesta (idEncuesta, textoRespuesta, numeroRespuestas) VALUES ('$encuesta_id', '$respuesta', 0)";
            if ($db->query($consulta) === false) {
                throw new ExcepcionEnTransaccion();
            }
        }

        // Confirmar la transacción si todo sale bien
        $db->commit();
        return $encuesta_id;

    } catch (ExcepcionEnTransaccion $e) {
        // Si algo falla, revertir la transacción
        $db->rollback();
        throw $e;
    }
}
?>
