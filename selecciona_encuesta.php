<?php
session_start();

if (!isset($_SESSION['login'])) {
    header('Location: login_encuesta.php');
    exit();
}

$conexionBase = new mysqli('localhost', 'root', '', 'encuestas');
if ($conexionBase->connect_error) {
    die("Conexión fallida: " . $conexionBase->connect_error);
}

$sql = "SELECT id, textoPregunta FROM encuesta"; 
$result = $conexionBase->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Encuesta</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            margin-top:100px;
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
        <h1>Selecciona una encuesta</h1>
        <hr>
        <?php if ($result->num_rows > 0) : ?>
            <?php while($row = $result->fetch_assoc()) : ?>
                <p><a href="ver_encuesta.php?id=<?= $row['id'] ?>">Encuesta <?= $row['id'] ?>: <?= $row['textoPregunta'] ?></a></p>
            <?php endwhile; ?>
        <?php else : ?>
            <p>No hay encuestas disponibles.</p>
        <?php endif; ?>
        <hr>
    </div>

    <?php $conexionBase->close(); ?>
</body>
</html>
