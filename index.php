<?php
// control_escolar/index.php (versión PaaS)
$host = "dbmysql-paas.mysql.database.azure.com";
$dbname = getenv('DB_NAME') ?: "control_escolar";
$user = getenv('DB_USER') ?: "appuser";
$pass = getenv('DB_PASS') ?: "AppUser!23";

// Configuración SSL (obligatoria para Azure MySQL)
$sslOptions = [
    PDO::MYSQL_ATTR_SSL_CA => '/home/site/wwwroot/DigiCertGlobalRootCA.crt.pem',
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true
];

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass, $sslOptions);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insertar'])) {
        $stmt = $conn->prepare("INSERT INTO alumnos (Num_Control, Correo, Semestre) VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['num_control'],
            $_POST['correo'],
            $_POST['semestre']
        ]);
        $mensaje = "Alumno registrado correctamente!";
    }
    
    $resultados = [];
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buscar'])) {
        $busqueda = '%'.$_POST['busqueda'].'%';
        $stmt = $conn->prepare("SELECT * FROM alumnos WHERE Num_Control LIKE ? OR Correo LIKE ?");
        $stmt->execute([$busqueda, $busqueda]);
        $resultados = $stmt->fetchAll();
    } else {
        $stmt = $conn->query("SELECT * FROM alumnos ORDER BY Num_Control");
        $resultados = $stmt->fetchAll();
    }
} catch(PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control Escolar PaaS</title>
</head>
<body>
    <div class="container">
        <h2>Computo en la Nube - Proyecto Final (PaaS)</h2>
        <p>Reynaldo Enriquez Zamorano.</p>
        <h1>Control Escolar</h1>
        
        <?php if(isset($error)): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if(isset($mensaje)): ?>
            <div class="message success"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>
        
        <div class="form-section">
            <h2>Registrar Nuevo Alumno</h2>
            <form method="post">
                <input type="hidden" name="insertar" value="1">
                <div class="form-group">
                    <label for="num_control">Número de Control:</label>
                    <input type="number" name="num_control" min="10000000" max="99999999" 
                           placeholder="" required>
                </div>
                <div class="form-group">
                    <label for="correo">Correo Electrónico:</label>
                    <input type="email" name="correo" placeholder="ejemplo@tecnm.mx" required>
                </div>
                <div class="form-group">
                    <label for="semestre">Semestre:</label>
                    <input type="number" name="semestre" min="1" max="12" placeholder="1-12" required>
                </div>
                <div>
                    <button type="submit">Guardar Alumno</button>
                </div>
            </form>
        </div>
        
        <div class="form-section">
            <h2>Buscar Alumnos</h2>
            <form method="post">
                <input type="hidden" name="buscar" value="1">
                <div class="search-box">
                    <label for="busqueda">Buscar:</label>
                    <input type="text" name="busqueda" placeholder="N° Control o Correo">
                    <button type="submit">Buscar</button>
                    <button type="button" class="secondary" onclick="window.location.href='index.php'">Mostrar Todos</button>
                </div>
            </form>
        </div>
        
        <h2>Registros de Alumnos</h2>
        <?php if(count($resultados) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>N° Control</th>
                    <th>Correo Electrónico</th>
                    <th>Semestre</th>
                    <th>Fecha de Registro</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($resultados as $alumno): ?>
                <tr>
                    <td><?= htmlspecialchars($alumno['Num_Control']) ?></td>
                    <td><?= htmlspecialchars($alumno['Correo']) ?></td>
                    <td><?= htmlspecialchars($alumno['Semestre']) ?></td>
                    <td><?= htmlspecialchars($alumno['fecha_registro']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p>No se encontraron registros de alumnos.</p>
        <?php endif; ?>
    </div>
</body>
</html>
