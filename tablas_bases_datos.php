<?php
// Conexión a MySQL
$conexion = new mysqli('localhost', 'root', '');

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Variables iniciales
$database = isset($_POST['database']) ? $_POST['database'] : '';
$table = isset($_POST['table']) ? $_POST['table'] : '';

// Función para obtener bases de datos
/*función que recibe la conexión a MySQL.
Crea una consulta SQL que selecciona el nombre de cada base de datos ( SCHEMA_NAME) desde la tabla SCHEMATAen INFORMATION_SCHEMA.
Ejecuta la consulta y devuelve el resultado de la misma con $conexion->query($sql).*/
function getDatabases($conexion) {
    $sql = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA";
    return $conexion->query($sql);
}

// Función para obtener tablas de una base de datos específica
/*getTables($conexion, $database): Defina una función que recibe la conexión y el nombre de la base de datos.
Crea una consulta SQL que selecciona los nombres de tablas ( TABLE_NAME) de la tabla TABLESen INFORMATION_SCHEMA, filtrando por la base de datos elegida ( TABLE_SCHEMA = '$database').
Ejecuta la consulta y devuelve el resultado.*/
function getTables($conexion, $database) {
    $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$database'";
    return $conexion->query($sql);
}

// Función para obtener columnas y datos de una tabla específica
/*getTableData($conexion, $database, $table): Defina una función que recibe la conexión, el nombre de la base de datos y el nombre de la tabla.
Cambia la base de datos activa a la seleccionada usando $conexion->select_db($database).
Crea una consulta SQL para seleccionar todos los datos ( *) de la tabla especificada ( $table).
Ejecuta la consulta y devuelve el resultado.*/
function getTableData($conexion, $database, $table) {
    $conexion->select_db($database);
    $sql = "SELECT * FROM $table";
    return $conexion->query($sql);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mini PHPMyAdmin</title>
</head>
<body>

<h2>Seleccionar Base de Datos</h2>
<form method="post">
    <label for="database">Base de datos:</label>
    <select name="database" id="database" onchange="this.form.submit()">
        <option value="">Seleccione una base de datos</option>
        <?php
        $databases = getDatabases($conexion);
        while ($db = $databases->fetch_assoc()) {
            $selected = ($database == $db['SCHEMA_NAME']) ? 'selected' : '';
            echo "<option value='{$db['SCHEMA_NAME']}' $selected>{$db['SCHEMA_NAME']}</option>";
        }
        ?>
    </select>
    <noscript><button type="submit">Seleccionar BD</button></noscript>
</form>

<?php if ($database): ?>
    <h2>Seleccionar Tabla de <?= htmlspecialchars($database) ?></h2>
    <form method="post">
        <input type="hidden" name="database" value="<?= htmlspecialchars($database) ?>">
        <label for="table">Tabla:</label>
        <select name="table" id="table" onchange="this.form.submit()">
            <option value="">Seleccione una tabla</option>
            <?php
            $tables = getTables($conexion, $database);
            while ($tb = $tables->fetch_assoc()) {
                $selected = ($table == $tb['TABLE_NAME']) ? 'selected' : '';
                echo "<option value='{$tb['TABLE_NAME']}' $selected>{$tb['TABLE_NAME']}</option>";
            }
            ?>
        </select>
        <noscript><button type="submit">Seleccionar Tabla</button></noscript>
    </form>
<?php endif; ?>

<?php if ($database && $table): ?>
    <h2>Contenido de la Tabla <?= htmlspecialchars($table) ?> en <?= htmlspecialchars($database) ?></h2>
    <table border="1">
        <thead>
            <tr>
                <?php
                $tableData = getTableData($conexion, $database, $table);
                $columns = $tableData->fetch_fields();
                foreach ($columns as $column) {
                    echo "<th>{$column->name}</th>";
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $tableData->fetch_assoc()) {
                echo "<tr>";
                foreach ($columns as $column) {
                    echo "<td>" . htmlspecialchars($row[$column->name]) . "</td>";
                }
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>

<?php

$conexion->close();
?>
