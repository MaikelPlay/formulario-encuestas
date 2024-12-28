<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = $_POST['userid'];
    $password = $_POST['password'];

    $db = new mysqli('localhost', 'root', '', 'encuestas');
    
    if ($db->connect_error) {
        die("Error de conexión: " . $db->connect_error);
    }

    $hashedPassword = sha1($password);

    $consulta = "SELECT login, password, tipoUsuario FROM usuario WHERE login = ? AND password = ?";
    $stmt = $db->prepare($consulta);
    $stmt->bind_param("ss", $userid, $hashedPassword);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        $_SESSION['login'] = $usuario['login'];
        $_SESSION['roles'] = $usuario['tipoUsuario'];

        header("Location: principal_encuesta.php");
        exit;
    } else {
        $error = 'Error: No existe ningún usuario con ese login/password en la BD';
    }

    $stmt->close();
    $db->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Encuestas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #f5f5f0 ;
            width: 400px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .error-message {
            color: red;
            font-weight: bold;
            margin-bottom: 15px;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        table {
            margin: 0 auto;
            border-collapse: collapse;
            width: 100%;
        }

        td {
            padding: 10px;
            color: #333;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        hr {
            margin: 20px 0;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>ACCESO APLICACIONES ENCUESTAS</h1>
        <hr>

        <?php if (!empty($error)) : ?>
            <p class="error-message"><?=$error ?></p>
        <?php endif; ?>

        <form method="POST" action="login_encuesta.php">
            <table>
                <tr>
                    <td>Usuario</td>
                    <td><input type="text" name="userid" required></td>
                </tr>
                <tr>
                    <td>Contraseña</td>
                    <td><input type="password" name="password" required></td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <hr>
                        <input type="submit" value="Entrar">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>
