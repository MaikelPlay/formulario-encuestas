<?php
session_start();

if (!isset($_SESSION['login'])) {
    header('Location: login_encuesta.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal - Encuestas</title>
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
        <h1>OPCIONES DE LA APLICACIÓN ENCUESTAS</h1>
        <hr>
        <a href="selecciona_encuesta.php">Votar Encuestas</a>
        <br>
        <br>
        <?php if ($_SESSION['roles'] === 'admin') : ?>
            <a href="alta_encuesta.php">Crear Encuestas</a>
            <hr>
        <?php endif; ?>
    </div>
</body>
</html>
