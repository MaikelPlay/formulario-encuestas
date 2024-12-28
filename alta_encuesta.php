<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['roles'] !== 'admin') {
    header('Location: login_encuesta.php');
    exit();
}

include_once 'encuestas.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = new mysqli('localhost', 'root', '', 'encuestas');  
        if ($db->connect_errno != 0) {
            throw new Exception('Error conectando: ' . $db->connect_error, $db->connect_errno);
        }

        if (isset($_POST['num_respuestas']) && !isset($_POST['pregunta'])) {
            if ($_POST['num_respuestas'] <= 0) {
                throw new Exception('Debe proporcionar un número válido de respuestas.');
            }

            $num_respuestas = (int)$_POST['num_respuestas'];
        } else {
            $num_respuestas = (int)$_POST['num_respuestas'] ?? 0;

            if (empty(trim($_POST['pregunta']))) {
                throw new Exception('El texto de la pregunta no puede estar vacío.');
            }

            $pregunta = validarPregunta($_POST['pregunta']);
            
            if (!isset($_POST['respuesta']) || count($_POST['respuesta']) < $num_respuestas) {
                throw new Exception('Debe proporcionar todas las respuestas solicitadas.');
            }

            $respuestas = validarRespuestas($_POST['respuesta'], $num_respuestas);

            $encuesta_id = insertarEncuesta($db, $pregunta, $respuestas);
            
            echo 'Encuesta registrada correctamente.<br>';
            echo '<a href="ver_encuesta.php?id=' . $encuesta_id . '">Ver encuesta</a><br>';
            echo '<a href="alta_encuesta.php">Crear nueva encuesta</a>';

            $db->close();
            exit();
        }
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();

        if (isset($db) && $db->connect_errno == 0) {
            $db->close();
        }
        exit();
    }
} 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta Encuesta</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            margin-top: 100px;
            height: 100vh;
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .header {
            position: absolute;
            top: 10px;
            right: 20px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <p>Estás logado como <strong><?php echo $_SESSION['login']; ?></strong> (<?php echo $_SESSION['roles'] === 'admin' ? 'Administrador' : 'Votante'; ?>)</p>
        <a href="logout_encuesta.php">Cerrar sesión</a>
    </div>

    <div>
        <?php if (!isset($num_respuestas)) : ?>
            <h1>ALTA ENCUESTA</h1>
            <form action="alta_encuesta.php" method="POST">
                <label for="num_respuestas">Número de respuestas de la nueva encuesta:</label>
                <input type="number" id="num_respuestas" name="num_respuestas" min="1" required>
                <input type="submit" value="Siguiente">
            </form>
        <?php else : ?>
            <h1>ALTA ENCUESTA - <?= $num_respuestas ?> Respuestas</h1>
            <form action="alta_encuesta.php" method="POST">
                <input type="hidden" name="num_respuestas" value="<?= $num_respuestas ?>">
                
                <label for="pregunta">Texto de la Pregunta:</label>
                <input type="text" id="pregunta" name="pregunta" required><br><br>

                <?php for ($i = 1; $i <= $num_respuestas; $i++) : ?>
                    <label for="respuesta<?= $i ?>">Respuesta <?= $i ?>:</label>
                    <input type="text" id="respuesta<?= $i ?>" name="respuesta[]" required><br>
                <?php endfor; ?>
                
                <br>
                <input type="submit" value="Enviar Encuesta">
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
